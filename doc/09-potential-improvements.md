# Potential improvements

Ideas for making WhereTo better now that it runs on the 2026 stack. None of them is required: the app works as it is. They are grouped by what they improve, and each one says why it is worth doing, how to do it, and roughly how much work it is (S: an hour or two, M: a day, L: several days). Written in November 2026 against branch `upgrade-2026`.

The order inside each section is a suggested priority. If only a few are done, the "Quick wins" at the end is the list to start from.

## 1. Mobile, tablet and PC experience

The UI is already responsive (viewport meta, one `@media` breakpoint at 680px, CSS variables, system-free dark theme). These are the things that make a web app feel like a native one on a phone and still look right on a large screen.

### 1.1 Make it installable as a Progressive Web App (M) — done

- **Why**: a PWA can be added to the home screen on Android and iOS, opens full screen without the browser chrome, and keeps the page shell available offline. It is the cheapest way to be "an app" on every platform without app stores or a second code base.
- **How**: add `public/manifest.webmanifest` (name, icons, `display: standalone`, `theme_color`, `background_color`), link it from `layouts/app.blade.php`, and add a small service worker in `public/sw.js` that pre-caches the shell (`/`, `/restaurants`, the CSS, the fonts) and uses network-first for `/*/results` and `/*/details/*`. Laravel needs no change; the files are static.
- **Note**: geolocation already requires HTTPS, and so does a service worker, so the production deploy must be HTTPS anyway (see `06-deploy-production.md`).
- **Done (November 2026)**: `public/manifest.json`, `public/sw.js`, icons generated from `public/icons/icon.svg` (also a real `favicon.ico`, the old one was an empty file), registration in `whereto.js`, Nginx rule for `sw.js` in the deploy doc. Described in `04-how-it-works.md` section 8.

### 1.2 Finish the phone polish in the stylesheet (S) — done

- `<meta name="theme-color">` so the Android status bar and the Safari tab match the dark theme.
- `env(safe-area-inset-*)` padding on the header and footer for phones with a notch or a home bar; `viewport-fit=cover` in the viewport meta.
- `100dvh` instead of `100vh` for full-height panels, so the address bar of mobile browsers does not hide the bottom of the results.
- Touch targets of at least 44px for the tags and the sort buttons (`.tap`, `.sortButton`).
- `prefers-reduced-motion` guard around any transition.
- **Done (November 2026)**: all of the above in `layouts/app.blade.php` and `style-dark.css`, plus `color-scheme: dark`, `touch-action: manipulation` on the tappable elements (no double-tap zoom delay), no tap highlight, and no text inflation in landscape.

### 1.3 Add a light theme and let the system choose (S)

- **Why**: `style-dark.css` is a dark theme only. Tablets and PCs are often used in daylight.
- **How**: the colours are already CSS variables (`--color-name`, `--color-cost`, ...). Add a `@media (prefers-color-scheme: light)` block that overrides the variables, plus `color-scheme: light dark` on `:root`. Optionally a toggle stored in `localStorage`, like `orderBy` is today.

### 1.4 Use the width on tablets and PCs (M)

- **Why**: the filter panel and the results panel are shown one at a time (`showFilter()` / `showResults()` toggle `display`). On a tablet or a PC there is room for both side by side, which removes the back-and-forth.
- **How**: a second breakpoint (for example 900px) that lays `#mainFilter` and `#mainResults` out with CSS grid, and a check in `showResults()` / `showFilter()` that does nothing when both are visible. A third column could host the details instead of replacing the results.

### 1.5 Ship only the WOFF2 font (S) — done

- **Why**: `public/fonts/` carries `.svg`, `.ttf`, `.woff` and `.woff2` versions of Open Sans. Every browser that can run the `fetch()` code can use WOFF2; the others are dead weight in the repository and the deploy.
- **How**: delete the three other files and the matching `src:` entries in the `@font-face`; add `font-display: swap`.
- **Done (November 2026)**.

### 1.6 Keep the position fresh and show it (S) — done

- **Why**: `getLocation()` starts an asynchronous `getCurrentPosition()` and immediately returns the *previous* coordinates from `sessionStorage`. On the first click the position is `null` and the distances are computed from Shibuya Station; the correct distances only appear on the next refresh.
- **How**: use `navigator.geolocation.watchPosition()` once at page load, store the latest fix, and let `refreshResults()` re-run once when the first fix arrives. Show a small "distances from the station / from your position" line in `#position-report` so the visitor knows which one they see.
- **Done (November 2026)**: `startGeolocation()` in `public/js/whereto.js`, started at load on the place pages and on click on the home page; the first fix re-requests the current results. The place views also carried duplicate `#position-report` divs, removed at the same time.

## 2. Front-end code

### 2.1 Move the inline JavaScript to static files (M) — done

- **Why**: the six `inline-javascript/*.blade.php` partials are injected into every page, so the browser downloads and parses them on each navigation and cannot cache them. They are also hard to lint and impossible to unit test. This does not need a build step, which was a deliberate choice of the upgrade.
- **How**: move them to `public/js/whereto.js` (one file, or one per concern) loaded with `<script type="module" defer>`. The only Blade values they need (`resultsUrl`, the CSRF token) can be passed through `data-*` attributes on `#app` or the existing `window.Laravel` object. Add a far-future `Cache-Control` for `/js` and `/css` in Nginx and a `?v=` query string from `filemtime()` (or Laravel's `Vite::asset` replacement, `asset()` with a version config) so updates are picked up.
- **Bonus**: run ESLint or Biome on the file in the dev container; add a Playwright smoke test (see 5.2).
- **Done (November 2026)**: one classic script `public/js/whereto.js` (not an ES module, because the views call its functions from `onclick` attributes), `defer`, `?v=` from `filemtime()`, URLs passed through `data-results-url` on `#content` and `data-suggestion-url` on `#suggestion`. The Nginx cache headers and the linter are still to do.

### 2.2 Keep HTML fragments, or go to a JSON API (M to L)

- **Today**: the results and details endpoints return Blade-rendered HTML fragments that the JavaScript injects with `innerHTML`. This is simple, fast on first paint, and works without a JS framework.
- **Option A (recommended)**: keep the fragments, and adopt [htmx](https://htmx.org/) (one 14 kB script, no build step). Attributes such as `hx-get="/restaurants/results" hx-target="#results" hx-include="[name=position]"` would replace most of `filter.blade.php` and `main.blade.php`. The stale-response guard comes for free (`hx-sync`).
- **Option B**: return JSON (`PlaceResource` API resources) and render client-side with a small framework (Vue, Svelte or Alpine). This gives client-side sorting without a round trip and makes a native wrapper (Capacitor) easier later, but it needs Node and a build step, which the project decided against. Only worth it if the UI is going to grow a lot.
- **Option C**: Livewire 4 (first-party Laravel). Interactivity written in PHP, no Node required, but every tag click goes through the server and the page weight grows. Good fit for the admin, less so for the public filter.

### 2.3 Accessibility (S) — done

- The tags are `<div>` elements with `onclick`; make them `<button type="button">` so they work with the keyboard and screen readers, and use `aria-pressed` for the selected state.
- Put `aria-live="polite"` on `#results` and `#position-report` so updates are announced.
- Give the three sort buttons `aria-pressed` reflecting the stored `orderBy`.
- **Done (November 2026)**: tags are `<button type="button">` with `aria-pressed`, the sort buttons carry `data-order-by` and `aria-pressed`, `#results`, `#position-report` and `#suggestion` are `aria-live="polite"`, the result name cells are `role="button" tabindex="0"` with Enter/Space handled in `whereto.js`, visible focus outlines, and the suggestion button is a real button.

### 2.4 Admin pages on the same stack as the public pages (S)

- **Why**: `layouts/html.blade.php` loads Bootstrap 3.4 and jQuery from a CDN for the admin and login pages only. Bootstrap 3 is unmaintained and jQuery is unused by the app code.
- **How**: either upgrade to Bootstrap 5.3 (no jQuery, has a built-in dark mode: `data-bs-theme="dark"`) or restyle the few admin tables and forms with `style-dark.css` and drop the CDN entirely. The second option also removes the only third-party requests the app makes.

## 3. Back-end architecture

### 3.1 Collapse the four copy-pasted place sets (L)

- **Why**: restaurants, stores, bars and karaokes each have their own model, tag model, pivot, migration pair, seeder pair, controller pair, and four views. `RestaurantController`, `StoreController`, `BarController` and `KaraokeController` are byte-for-byte identical apart from the model name. Any change has to be made four times. This was kept on purpose during the upgrade ("port and fix only") and is the biggest structural improvement left.
- **How, least invasive**: keep the four tables, but have one `PlaceController` whose model is chosen from the route: `Route::get('{kind}/results', [PlaceController::class, 'results'])->whereIn('kind', ['restaurants', 'stores', 'bars', 'karaokes'])`, with a `PlaceKind` PHP enum mapping the URL segment to the model class, the tag model, the views and the route names. The views can be shared with `@include` and a `$kind` variable; only the restaurant row has the cost and lunch columns, which a `@if` handles.
- **How, full**: one `places` table with a `kind` column and nullable restaurant-only columns, one `tags` table with a `kind` column, one pivot. The queries become simpler and tags could even be shared across kinds. This needs a data migration from the four existing tables and changes the cache keys.
- **Either way**: the admin CRUD, which today exists only for restaurants, becomes available for the other three kinds for free.

### 3.2 Extract the search into a service class (M)

- **Why**: `PlaceController` holds the query building, the caching, the distance computation and the HTTP handling. `SuggestionController` instantiates `new RestaurantController` just to call `useCache()` and `handleDistances()`, which couples two HTTP controllers together.
- **How**: an `App\Services\PlaceSearch` class (or one per kind, built from the enum of 3.1) with `search(tags, op, orderBy, limit): Collection` and `withDistances(Collection, ?Position): Collection`. Controllers become thin: read the request, call the service, return a view. Testing the search no longer needs HTTP requests.

### 3.3 Use the framework's building blocks (S each)

- **Form Requests** (`php artisan make:request StoreRestaurantRequest`) instead of inline `$request->validate()` arrays in the four admin controllers; the rules are then shared between `store()` and `update()`.
- **Route model binding**: `Route::get('restaurant/details/{restaurant}')` with a `Restaurant $restaurant` parameter replaces the `findOrFail` calls, and gives the 404 for free.
- **PHP enums** for `orderBy` (`distance`, `ratings`, `cost`) and `op` (`AND`, `OR`, `ANDNOT`, `ORNOT`), validated with `Rule::enum()` in the request instead of being passed around as free strings.
- **A `Position` value object** (`lat`, `lon`, `fromString()`, `distanceTo()`) instead of the `"lat,lon"` string that goes through `positionSpoofer()`, `google2coord()` and the cache key logic. The `Location` trait would then take a `Position`.
- **Authorization**: `users.level` exists but nothing reads it. A `Gate::define('admin', fn (User $u) => $u->level >= 10)` and `->can('admin')` on the admin route group would make the column meaningful.

### 3.4 Seed data as data, not as PHP (M)

- **Why**: `RestaurantsTableSeeder.php` is more than 5000 lines of PHP arrays. It is the real "database" of the project, and editing it by hand is error prone; the admin edits the live DB instead, which diverges from the seeder.
- **How**: move the data to `database/data/restaurants.csv` (or JSON) and make the seeder read it with `fgetcsv`. Then add an `artisan whereto:export` command that writes the live tables back to those files, so the repository stays the source of truth and a fresh deploy reproduces production. This replaces the Excel import/export that was dropped in the upgrade.

### 3.5 Distances in the database, when the data grows (L, only if needed)

- **Today**: every search loads the cached collection and computes the Haversine distance for each place in PHP, then sorts. With a few hundred places and a forever cache this is cheap, and not worth changing now.
- **If the data grows** (thousands of places, or a second city): PostgreSQL's `earthdistance` extension, or PostGIS with a `geography(Point)` column and a GiST index, lets the DB do `ORDER BY ST_Distance(...) LIMIT 50`. The cache would then be keyed by a rounded position (100 m grid) instead of being position-independent.

## 4. Performance and operations

### 4.1 Production checklist that costs nothing (S) — done

- `php artisan optimize` after each deploy (config, routes, views, events cached). The upgrade removed all route closures, so `route:cache` works now.
- OPcache with `opcache.validate_timestamps=0` in production (and `opcache.preload` if the host allows it).
- Nginx: `gzip`/`brotli` on, `Cache-Control: public, max-age=31536000, immutable` for `/css`, `/js`, `/fonts`, HTTP/2 or HTTP/3.
- `SESSION_DRIVER=database` or `redis` only if there are several web nodes; the file driver is fine for one server.
- **Done (November 2026)** in `06-deploy-production.md`: gzip, HTTP/2, immutable one-year cache for the static files (the stylesheet link now carries a `?v=` like the script), an OPcache section, and a restart of PHP-FPM in the update steps. HTTP/3 and Brotli are left out: they need extra Nginx modules.

### 4.2 Browser caching for the fragments (S)

- **Why**: the forever cache makes the server fast, but the client still downloads the same results fragment on every click.
- **How**: an `ETag` built from the cache key plus the last admin flush time (store a `places.version` value in the cache and bump it in the `Cache::flush()` calls) lets the browser get a `304 Not Modified` for repeated searches. Laravel's `SetCacheHeaders` middleware (`cache.headers:etag;public;max_age=60`) does most of it.

### 4.3 Tagged cache instead of flushing everything (S)

- **Why**: every admin write calls `Cache::flush()`, which also drops sessions if they ever share the store, and the next visitor pays for a full rebuild.
- **How**: with the `redis` or `database` cache store, `Cache::tags(['places'])->rememberForever(...)` and `Cache::tags(['places'])->flush()` only drop the place data. Not possible with the `file` store, so this comes together with moving the cache to Redis or the `cache` table.

### 4.4 FrankenPHP or Octane (M, optional)

- **Why**: keeping the framework booted between requests removes most of the per-request cost. For an app this size it is a luxury, but it also simplifies the deployment to a single binary with built-in HTTPS and HTTP/3.
- **How**: `composer require laravel/octane` with the FrankenPHP server, or the official `dunglas/frankenphp` image in the `dev/` compose file, replacing `php artisan serve`. The `Cache::rememberForever` logic is unaffected; the only thing to check is that nothing keeps state in static properties.

### 4.5 Observability (S)

- Laravel's health endpoint (`withRouting(health: '/up')`) for uptime monitoring.
- `LOG_CHANNEL=daily` with a retention, and a `LOG_STACK` that also ships to the host's journal.
- Laravel Pulse or Telescope in the dev environment only, to see slow queries and cache hit rates when working on 3.5.

## 5. Quality, testing and delivery

### 5.1 Continuous integration (S) — done

- **Why**: Pint, PHPStan and the 53 tests are run by hand in the container. A GitHub Actions workflow runs them on every push and on the pull request, with a PostgreSQL 18 service so the tests exercise the real database instead of SQLite.
- **How**: `.github/workflows/ci.yml` with `shivammathur/setup-php` (PHP 8.5, `pdo_pgsql`), `composer install`, `vendor/bin/pint --test`, `vendor/bin/phpstan`, `php artisan test`. Add Dependabot or Renovate for Composer updates.
- **Done (November 2026)**: `.github/workflows/ci.yml` (Pint, PHPStan, `node --check`, tests on SQLite and on a PostgreSQL 18 service) and `.github/dependabot.yml` (weekly, Composer updates grouped).

### 5.2 Browser tests for the JavaScript (M)

- **Why**: the tests cover the HTTP layer only; nothing checks that a tag click updates the results or that the back button works.
- **How**: Playwright (already used on this machine) against the `dev/` stack, with the geolocation faked (`context.grantPermissions(['geolocation'])`, `setGeolocation`). Five scenarios cover the whole UI: home, tag filter, each sort, details and back, suggestion with the clock set to lunch time. Laravel Dusk is the first-party alternative but it needs Chrome in the PHP image.

### 5.3 Raise PHPStan to level 7 or 8 (M)

- Level 5 passes today. Going higher mostly means declaring array shapes and nullable returns, which doubles as documentation for someone learning PHP.

### 5.4 Architecture tests (S)

- Pest's or PHPUnit's `arch()` style tests (via `pestphp/pest-plugin-arch`, or hand-written reflection checks) can enforce "controllers do not instantiate other controllers" and "models do not use the Request", which guards the refactors of section 3.

## 6. Product ideas

Small features that the current data already supports.

- **Results counter**: done (November 2026), a "N results" line above the list in the four `results-data` fragments.
- **OR filter**: `whereTags()` already implements `OR`, `ANDNOT` and `ORNOT`; a single toggle next to the selected tags would expose it.
- **Opening hours and "open now"**: a `hours` JSON column per place and a filter that uses the same Tokyo clock as the lunch suggestion.
- **Japanese UI**: Laravel localisation (`lang/ja.json`) with the browser language as default; the data already has Japanese names.
- **Share and deep links**: the copy-URL button exists; add Open Graph tags to the details pages so a shared link shows the place name and type, and a `sitemap.xml` route for the details pages.
- **Freshen ratings**: an artisan command that queries the Google Places API for each `google_maps_link` and updates `score_*` or a new `google_rating` column, run monthly. Requires an API key and a cost check.
- **Second area**: the Shibuya Station fallback and the 100 best restaurants limit are hard-coded; an `areas` table (name, centre, radius) would make "enjoyShibuya" the first of several.

## Quick wins

The items that are an hour or two each and improve the phone experience the most:

1. ~~1.2 phone polish in the stylesheet~~ done
2. ~~1.5 WOFF2 only~~ done
3. ~~1.6 position freshness~~ done
4. ~~2.3 accessibility~~ done
5. ~~4.1 production checklist~~ done
6. ~~5.1 CI workflow~~ done
7. ~~6 results counter~~ done

1.1 (PWA) and 2.1 (JS to static files) are done. All the quick wins are done. Next: 3.1 (one place set) as the structural project if the app keeps evolving.
