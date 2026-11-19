/**
 * WhereTo service worker.
 *
 * A service worker is a script the browser runs in the background, between the page and the network.
 * It lets the app be installed on the home screen and keep working with a bad connection:
 *   - static files (CSS, JS, fonts, icons) are served from the cache, and refreshed in the background;
 *   - pages and data fragments go to the network first, and fall back to the last copy seen when offline.
 * Only GET requests of this site are handled. The Debugbar is left alone.
 *
 * The page registers this file as /sw.js?css=<version>&js=<version>&locales=<prefixes> (whereto.js, from the data
 * attributes of <html>): the versions are the ?v= query strings the page uses for its stylesheet and script, so the
 * pre-cache holds the exact URLs the page asks for; the prefixes are the languages served under a prefix ("ja"),
 * whose pages are pre-cached too (doc/12-multilingual-ui.md). A new version of either file, or a new language, is
 * a new worker URL, hence a new install and a new cache; the old cache is deleted on activation.
 * Bump the "whereto-vN" part when the cached files change in a way the versions do not cover.
 * See https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API
 */

/** The asset versions, from the worker's own URL ('' when registered without them) */
const PARAMS = new URL(self.location.href).searchParams;
const CSS_VERSION = PARAMS.get('css') || '';
const JS_VERSION = PARAMS.get('js') || '';
/** The path prefixes of the languages other than the default one, e.g. ['/ja'] (the default one has none) */
const LOCALE_PREFIXES = (PARAMS.get('locales') || '').split(',').filter(locale => /^[a-z]{2,3}$/.test(locale)).map(locale => '/' + locale);

// v4: the pages of every language in the pre-cache
const CACHE_VERSION = 'whereto-v4-' + CSS_VERSION + '-' + JS_VERSION + '-' + LOCALE_PREFIXES.join('');

/** The pages of the app shell in the default language; the same ones exist under each language prefix */
const SHELL_PAGES = ['/', '/restaurants', '/stores', '/bars', '/karaokes'];

/** The home page of the language of a path: '/ja' for '/ja/restaurants', '/' for '/restaurants' */
function homeOf(pathname) {
  const prefix = LOCALE_PREFIXES.find(prefix => pathname === prefix || pathname.startsWith(prefix + '/'));
  return prefix || '/';
}

/** Files put in the cache at install time: the app shell in every language, enough to open the app offline */
const PRECACHE = [
  ...SHELL_PAGES,
  // The same pages under each prefix: '/ja' (the home page, no trailing slash), '/ja/restaurants'...
  ...LOCALE_PREFIXES.flatMap(prefix => SHELL_PAGES.map(page => page === '/' ? prefix : prefix + page)),
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
 * and the last copy seen is shown when offline. A page opened with the state of a filter page in its query string
 * (/restaurants?tags=ramen&place=12) falls back to the same page without it: the script restores the state from
 * the address. A page never seen falls back to the home page of its language (/ja for /ja/...).
 */
function networkFirst(request) {
  const url = new URL(request.url);
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
          if (request.mode !== 'navigate') {
            return new Response('Offline', { status: 503, statusText: 'Offline' });
          }
          // Navigation while offline: the same page without its query string, or else the app shell of the
          // page's language, instead of the browser's error page
          return cache.match(request, { ignoreSearch: true })
            .then(page => page || cache.match(homeOf(url.pathname)));
        })
      )
  );
}
