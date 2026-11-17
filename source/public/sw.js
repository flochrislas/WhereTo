/**
 * WhereTo service worker.
 *
 * A service worker is a script the browser runs in the background, between the page and the network.
 * It lets the app be installed on the home screen and keep working with a bad connection:
 *   - static files (CSS, JS, fonts, icons) are served from the cache, and refreshed in the background;
 *   - pages and data fragments go to the network first, and fall back to the last copy seen when offline.
 * Only GET requests of this site are handled. The Debugbar is left alone.
 *
 * The page registers this file as /sw.js?css=<version>&js=<version> (whereto.js, from the data attributes of
 * <html>): the versions are the ?v= query strings the page uses for its stylesheet and script, so the pre-cache
 * holds the exact URLs the page asks for. A new version of either file is a new worker URL, hence a new install
 * and a new cache; the old cache is deleted on activation.
 * Bump the "whereto-vN" part when the cached files change in a way the versions do not cover.
 * See https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API
 */

/** The asset versions, from the worker's own URL ('' when registered without them) */
const PARAMS = new URL(self.location.href).searchParams;
const CSS_VERSION = PARAMS.get('css') || '';
const JS_VERSION = PARAMS.get('js') || '';

const CACHE_VERSION = 'whereto-v3-' + CSS_VERSION + '-' + JS_VERSION; // v3: versioned assets in the pre-cache

/** Files put in the cache at install time: the app shell, enough to open the app offline */
const PRECACHE = [
  '/',
  '/restaurants',
  '/stores',
  '/bars',
  '/karaokes',
  '/css/style.css' + (CSS_VERSION ? '?v=' + CSS_VERSION : ''),
  '/js/whereto.js' + (JS_VERSION ? '?v=' + JS_VERSION : ''),
  '/fonts/open-sans-v15-latin-regular.woff2',
  '/icons/icon-192.png',
  '/manifest.json'
];

/** Paths never touched by the worker (always straight to the network) */
const BYPASS = ['/_debugbar'];

/**
 * How many results and details fragments the worker keeps for offline use (the pages and the static files of
 * PRECACHE are not counted). Each search or details view stores one, keyed by its full URL, which includes
 * the visitor's position, so old ones are rarely reused: they only matter for the current or last visit.
 * 20 covers a busy visit (about ten searches and five details) in about one megabyte; raise it if visitors
 * should get more of their past searches back offline, lower it to save space. The oldest ones are dropped.
 */
const MAX_FRAGMENTS = 20;

/** Install: fill the cache with the shell. skipWaiting: the new worker takes over without waiting for the tabs to close */
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_VERSION)
      .then(cache => cache.addAll(PRECACHE))
      .then(() => self.skipWaiting())
  );
});

/** Activate: drop the caches of previous versions, then control the open pages right away */
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(keys.filter(key => key !== CACHE_VERSION).map(key => caches.delete(key))))
      .then(() => self.clients.claim())
  );
});

/** Every request of the pages goes through here */
self.addEventListener('fetch', event => {
  const request = event.request;
  const url = new URL(request.url);

  // Only GET requests of this site; anything else goes straight out
  if (request.method !== 'GET' || url.origin !== self.location.origin) {
    return;
  }
  if (BYPASS.some(prefix => url.pathname.startsWith(prefix))) {
    return;
  }

  if (isStaticAsset(url.pathname)) {
    event.respondWith(staleWhileRevalidate(request));
  } else {
    event.respondWith(networkFirst(request));
  }
});

/** CSS, JS, fonts, icons: their URL changes when they change (?v=), so the cached copy is safe to use */
function isStaticAsset(pathname) {
  return /^\/(css|js|fonts|icons)\//.test(pathname) || pathname === '/manifest.json' || pathname === '/favicon.ico';
}

/**
 * Stale-while-revalidate: answer from the cache at once (fast), and fetch a fresh copy in the background
 * for the next time. First visit: no cache, wait for the network.
 */
function staleWhileRevalidate(request) {
  return caches.open(CACHE_VERSION).then(cache =>
    cache.match(request).then(cached => {
      const network = fetch(request)
        .then(response => {
          if (response.ok) {
            cache.put(request, response.clone()); // clone: a response body can be read only once
          }
          return response;
        })
        .catch(() => cached); // offline: whatever we had
      return cached || network;
    })
  );
}

/**
 * Keep at most MAX_FRAGMENTS entries stored by networkFirst (everything that is not in PRECACHE),
 * dropping the oldest ones. cache.keys() lists the entries in the order they were stored.
 */
function trimFragments(cache) {
  const shell = new Set(PRECACHE.map(path => new URL(path, self.location.origin).href));
  return cache.keys().then(requests => {
    const fragments = requests.filter(request => !shell.has(request.url) && !isStaticAsset(new URL(request.url).pathname));
    const excess = fragments.length - MAX_FRAGMENTS;
    return Promise.all(fragments.slice(0, Math.max(excess, 0)).map(request => cache.delete(request)));
  });
}

/**
 * Network-first: pages, results and details are fresh when online (a deploy may have changed them),
 * and the last copy seen is shown when offline. A page never seen offline falls back to the home page.
 */
function networkFirst(request) {
  return caches.open(CACHE_VERSION).then(cache =>
    fetch(request)
      .then(response => {
        if (response.ok) {
          cache.put(request, response.clone()).then(() => trimFragments(cache));
        }
        return response;
      })
      .catch(() =>
        cache.match(request).then(cached => {
          if (cached) {
            return cached;
          }
          // Navigation to an unknown page while offline: open the app shell instead of the browser's error page
          if (request.mode === 'navigate') {
            return cache.match('/');
          }
          return new Response('Offline', { status: 503, statusText: 'Offline' });
        })
      )
  );
}
