# Architecture and code organization

## Repository layout

```
WhereTo/
├── doc/                  this documentation
├── dev/                  podman compose dev environment + baseline captures
├── e2e/                  browser tests (Playwright): package.json, playwright.config.js, tests/*.spec.js
├── README.md, LICENSE    GPL-3.0 licence
└── source/               the Laravel 13 application
    ├── app/
    │   ├── Models/                      Restaurant, RestaurantTag, RestaurantType, Store, StoreTag,
    │   │                                Bar, BarTag, Karaoke, KaraokeTag, and the Place interface the four kinds implement
    │   ├── Traits/Location.php          lat/lon helpers shared by the place models
    │   ├── Tools/GeoUtils.php           distance maths, position / Google Maps URL parsing
    │   ├── Enums/                       OrderBy (distance, ratings, cost) and TagOperator (AND, OR, ANDNOT, ORNOT)
    │   ├── Support/Position.php         "lat, lon" value object (parsing, distance to another position)
    │   ├── Support/StructuredData.php   the JSON-LD (schema.org) block of a place page, common fields and our review
    │   ├── Services/                    PlaceSearch (tag query, forever cache, ordering, distances) and one subclass per kind
    │   ├── Http/Controllers/            see below
    │   ├── Http/Middleware/NoIndex.php  X-Robots-Tag: noindex on the AJAX fragments
    │   └── Providers/AppServiceProvider.php
    ├── bootstrap/app.php, bootstrap/providers.php   application configuration (no Kernel classes)
    ├── config/, public/, storage/
    ├── database/migrations/             skeleton tables + 13 WhereTo tables (+ one that drops the old users tables)
    ├── database/seeders/                the actual data lives here (see 03-data-model.md)
    ├── resources/views/                 Blade templates
    ├── routes/web.php, routes/console.php
    ├── tests/Unit, tests/Feature, tests/Architecture
    ├── phpstan.neon, phpunit.xml
    └── composer.json
```

## Place types and the controller hierarchy

The app handles four kinds of "places". Each one is a trio of model, tag model and pivot table, plus two controllers and a set of views:

| Place | Model | Tag model | Public controllers | Views |
|---|---|---|---|---|
| Restaurant | `App\Models\Restaurant` | `RestaurantTag` | `RestaurantController` (+ `RestaurantTagController` for the autocomplete) | `resources/views/restaurants/` |
| Store | `App\Models\Store` | `StoreTag` | `StoreController` | `resources/views/stores/` |
| Bar | `App\Models\Bar` | `BarTag` | `BarController` | `resources/views/bars/` |
| Karaoke | `App\Models\Karaoke` | `KaraokeTag` | `KaraokeController` | `resources/views/karaokes/` |

The HTTP side is the abstract `App\Http\Controllers\PlaceController`:

- `getModelName()` is abstract; the subclass returns `'Restaurant'`, `'Store'`, and so on. From that name the base class derives the model class (`App\Models\Restaurant`), the results partial (`restaurants.results-data`) and the details views (`restaurants.details`, `restaurants.details-single`).
- `search()` is abstract too and returns the search service of the kind (`new RestaurantSearch`).
- `index()` renders the main page of the kind: the tags (`RestaurantTag::allCached()`, the tag cloud order cached forever) and the default list of places, every place nearest to the station first, already in the HTML with a real link to each standalone page. A crawler sees every place without JavaScript; the script refreshes the list once the position is known.
- `results()` reads the query string (`op`, `tags`, `position`, `orderBy`; the order and the tag operator become the `OrderBy` and `TagOperator` enums, with a default for unknown values), calls the service, shortens the "points" text and renders the results partial.
- The details routes use route model binding: `{place}` in the URL becomes a `Restaurant`, `Store`, `Bar` or `Karaoke` argument, or a 404.
- `formatTags()` and `ellipsis()` are small helpers.

The search itself is the abstract `App\Services\PlaceSearch`, with one subclass per kind that names the model and its score columns (`RestaurantSearch`: lunch, food and place scores and the lunch price; `BarSearch`: score, place, mingling; `StoreSearch` and `KaraokeSearch`: the defaults, one `score` column and no price). It knows nothing about HTTP, so it is unit-tested without requests (`tests/Unit/PlaceSearchTest.php`) and the lunch suggestion reuses it:

- `find()` / `cacheKey()` / `readFromDB()` run the tag query, with the result cached forever.
- `whereTags()` builds the `AND`, `OR`, `ANDNOT`, `ORNOT` tag filters with `whereHas` on the `tags` relation.
- `orderBy()` applies rating or cost ordering in SQL from `ratingColumns()` and `costColumn()`, with `id` as a tiebreaker so results are deterministic.
- `withDistances()`, `sortByDistance()`, `generateCurrentDistances()` compute the distance from the visitor in PHP and optionally sort by it (places without coordinates are dropped when sorting by distance).
- `resolvePosition()` turns the `position` query string into a `Position` and falls back to Shibuya Station when the visitor is further than 2.5 km away, sent no position, or sent an unparsable one.

The four concrete place controllers are near copies of each other and only define `getModelName()`, `search()`, `details()` and `detailsSingle()` (`index()` and `results()` are in the base class) (the last two return 404 for unknown ids).

`RestaurantTagController` only has the `autocomplete()` endpoint; the tag lists of the four kinds are `allCached()` static methods on the tag models.

Models are equally repetitive: each place model `use`s the `Location` trait, implements the `Place` interface (`position()`, `currentDistance()`, `setCurrentDistance()`, provided by the trait; this is what `PlaceSearch` needs from a place), declares `$fillable`, and defines `tags()`, `tagsCached()` and `attachTags()`.

## Other controllers

| Controller | Role |
|---|---|
| `SitemapController` | `robots.txt` (a route, so the sitemap line carries this deployment's URL) and `sitemap.xml` (cached forever, flushed by the seeder). |
| `SuggestionController` | Lunch suggestion; reuses `RestaurantSearch`. `isLunchTime()` is static so Blade can call it through `@inject`. Renders `suggestions.suggestion-text`. |

## Routes (`routes/web.php`)

All routes point at controller classes or `Route::view`, so `route:cache` works.

Public:

| Method and path | Handler | Purpose |
|---|---|---|
| `GET /` | view `home` | Home page with four big buttons and the suggestion box |
| `GET /info/main`, `/vip/main` | views | Placeholder pages |
| `GET /suggestions` | `SuggestionController@suggest` | HTML fragment with a lunch suggestion |
| `GET /restaurants` | `RestaurantController@index` | Main page: all the tags and the default list of places |
| `GET /restaurants/results` | `RestaurantController@results` | HTML fragment with the result rows (AJAX) |
| `GET /restaurants/details/{id}` | `RestaurantController@details` | HTML fragment with the details (AJAX) |
| `GET /restaurant/details/{id}` | `RestaurantController@detailsSingle` | Full standalone details page, shareable URL |
| `GET /robots.txt`, `GET /sitemap.xml` | `SitemapController` | For the crawlers: allow everything and list every page (home, main pages, standalone place pages with the visit date); the sitemap is cached forever |
| `GET /tags/autocomplete` | `RestaurantTagController@autocomplete` | JSON for tag autocomplete (not used by the UI) |
| same four for `/stores`, `/bars`, `/karaokes` | `Store*`, `Bar*`, `Karaoke*` | |
| `GET /up` | framework | Health check |

Every route is a `GET`: the app has no login and no admin (removed in November 2026; the data is edited in the seeders and the site is read-only). There are no accounts, so there is nothing to brute-force and nothing to leak.

Removed from the legacy routes: `/about`, `/test-html`, `/clubs*`, `/register`, `/password/*`, and the whole `/login`, `/logout` and `/admin/*` set (restaurant, tag and type CRUD, Debugbar switch, import/export).

## Views

- `layouts/app.blade.php` is the public layout: `<title>`, meta description, canonical URL, Open Graph and Twitter tags all built from the `title` and `description` sections each view declares (and a visually hidden `<h1>` from the `heading` section, else the title); the stylesheet (dark by default, light with `data-theme="light"` on `<html>`, set before the first paint by a tiny inline script), the theme-color and color-scheme metas, CSRF meta, the `public/js/whereto.js` script tag (deferred, versioned by file modification time), `layouts.header`, `#position-report` for geolocation messages, content, `layouts.footer`.
- Each place folder has: `main` (page shell that includes `filter` and `results`; its `#content` div carries the `data-results-url` attribute the JavaScript reads), `filter` (tag cloud), `results` (sort buttons and column headers), `results-data` (the rows, returned by AJAX), `details` (fragment) and `details-single` (standalone page wrapping `details`, plus the `<x-structured-data>` component: the JSON-LD block built by the model's `structuredData()`).
- `public/manifest.json`, `public/sw.js` (service worker) and `public/icons/` make the app installable on a phone's home screen and usable offline; see `04-how-it-works.md` section 8.
- The JavaScript is not in the views: it lives in `public/js/whereto.js`, one file with a section per concern (geolocation, panels, results, tag filter, details, suggestion). The views call its global functions from `onclick` attributes and pass it the two server URLs through `data-results-url` and `data-suggestion-url` attributes. All requests use `fetch()`.
- `suggestions/suggestion.blade.php` renders the suggestion box only at lunch time; `suggestions/suggestion-text.blade.php` is the sentence returned by `/suggestions`.


## Known gaps

See `08-findings.md` for the full checklist with what was fixed in the upgrade. Still open by design:

- The four place sets are copy-pasted (consolidation was explicitly left out of the upgrade).
- Tag operators `OR`, `ANDNOT`, `ORNOT` exist server-side but the UI always sends `AND`.
- `restaurant_types` is seeded but not used by any page (`restaurants.type` is free text).
- Hidden header links (Info, VIP) are still `display: none`.
