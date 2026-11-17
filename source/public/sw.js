/**
 * WhereTo service worker.
 *
 * A service worker is a script the browser runs in the background, between the page and the network.
 * It lets the app be installed on the home screen and keep working with a bad connection:
 *   - static files (CSS, JS, fonts, icons) are served from the cache, and refreshed in the background;
 *   - pages and data fragments go to the network first, and fall back to the last copy seen when offline.
 * Only GET requests of this site are handled. The Debugbar is left alone.
 *
 * Change CACHE_VERSION when the cached files change in a way the ?v= query strings do not cover:
 * the old cache is deleted on activation.
 * See https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API
 */

const CACHE_VERSION = 'whereto-v2'; // v2: style-dark.css became style.css (dark and light themes)

/** Files put in the cache at install time: the app shell, enough to open the app offline */
const PRECACHE = [
  '/',
  '/restaurants',
  '/stores',
  '/bars',
  '/karaokes',
  '/css/style.css',
  '/fonts/open-sans-v15-latin-regular.woff2',
  '/icons/icon-192.png',
  '/manifest.json'
];

/** Paths never touched by the worker (always straight to the network) */
const BYPASS = ['/_debugbar'];

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
 * Network-first: pages, results and details are fresh when online (a deploy may have changed them),
 * and the last copy seen is shown when offline. A page never seen offline falls back to the home page.
 */
function networkFirst(request) {
  return caches.open(CACHE_VERSION).then(cache =>
    fetch(request)
      .then(response => {
        if (response.ok) {
          cache.put(request, response.clone());
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
