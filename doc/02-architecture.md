# Architecture and code organization

## Repository layout

```
WhereTo/
├── doc/                  this documentation
├── dev/                  podman compose dev environment + baseline captures
├── README.md, LICENSE    GPL-3.0 licence
└── source/               the Laravel 13 application
    ├── app/
    │   ├── Models/                      Restaurant, RestaurantTag, RestaurantType, Store, StoreTag,
    │   │                                Bar, BarTag, Karaoke, KaraokeTag, User
    │   ├── Traits/Location.php          lat/lon helpers shared by the place models
    │   ├── Tools/GeoUtils.php           distance maths, position / Google Maps URL parsing
    │   ├── Enums/                       OrderBy (distance, ratings, cost) and TagOperator (AND, OR, ANDNOT, ORNOT)
    │   ├── Support/Position.php         "lat, lon" value object (parsing, distance to another position)
    │   ├── Http/Controllers/            see below
    │   └── Providers/AppServiceProvider.php
    ├── bootstrap/app.php, bootstrap/providers.php   application configuration (no Kernel classes)
    ├── config/, public/, storage/
    ├── database/migrations/             skeleton tables + 14 WhereTo tables
    ├── database/seeders/                the actual data lives here (see 03-data-model.md)
    ├── database/factories/UserFactory.php
    ├── resources/views/                 Blade templates
    ├── routes/web.php, routes/console.php
    ├── tests/Unit, tests/Feature
    ├── phpstan.neon, phpunit.xml
    └── composer.json
```

## Place types and the controller hierarchy

The app handles four kinds of "places". Each one is a trio of model, tag model and pivot table, plus two controllers and a set of views:

| Place | Model | Tag model | Public controllers | Views |
|---|---|---|---|---|
| Restaurant | `App\Models\Restaurant` | `RestaurantTag` | `RestaurantController`, `RestaurantTagController` | `resources/views/restaurants/` |
| Store | `App\Models\Store` | `StoreTag` | `StoreController`, `StoreTagController` | `resources/views/stores/` |
| Bar | `App\Models\Bar` | `BarTag` | `BarController`, `BarTagController` | `resources/views/bars/` |
| Karaoke | `App\Models\Karaoke` | `KaraokeTag` | `KaraokeController`, `KaraokeTagController` | `resources/views/karaokes/` |

The generic logic is in the abstract `App\Http\Controllers\PlaceController`:

- `getModelName()` is abstract; the subclass returns `'Restaurant'`, `'Store'`, and so on. From that name the base class derives the model class (`App\Models\Restaurant`), the results partial (`restaurants.results-data`) and the details views (`restaurants.details`, `restaurants.details-single`).
- `useCache()` / `cacheKey()` / `readFromDB()` run the tag query, with the result cached forever.
- `whereTags()` builds the `AND`, `OR`, `ANDNOT`, `ORNOT` tag filters with `whereHas` on the `tags` relation.
- `orderBy()` applies rating or cost ordering in SQL, using the columns each kind declares through `ratingColumns()` and `costColumn()` (restaurants: lunch, food and place scores and the lunch price; bars: score, place, mingling; stores and karaoke: score), with `id` as a tiebreaker so results are deterministic. The order and the tag operator travel as the `OrderBy` and `TagOperator` enums, parsed from the query string with a default for unknown values.
- `handleDistances()`, `sortByDistance()`, `generateCurrentDistances()` compute the distance from the visitor in PHP and optionally sort by it (places without coordinates are dropped when sorting by distance).
- `positionSpoofer()` turns the `position` query string into a `Position` and falls back to Shibuya Station when the visitor is further than 2.5 km away, sent no position, or sent an unparsable one.
- The details routes use route model binding: `{place}` in the URL becomes a `Restaurant`, `Store`, `Bar` or `Karaoke` argument, or a 404.
- `formatTags()` and `ellipsis()` are small helpers.

The four concrete place controllers are near copies of each other and only define `results()`, `details()` and `detailsSingle()` (the last two return 404 for unknown ids).

The four `*TagController`s serve the filter page (all tags, cached). `RestaurantTagController` also has an `autocomplete()` endpoint.

Models are equally repetitive: each place model `use`s the `Location` trait, declares `$fillable`, and defines `tags()`, `tagsCached()` and `attachTags()`.

## Other controllers

| Controller | Role |
|---|---|
| `SuggestionController` | Lunch suggestion; reuses the restaurant search through a `RestaurantController` (the one exception listed in the architecture tests). `isLunchTime()` is static so Blade can call it through `@inject`. Renders `suggestions.suggestion-text`. |

## Routes (`routes/web.php`)

All routes point at controller classes or `Route::view`, so `route:cache` works.

Public:

| Method and path | Handler | Purpose |
|---|---|---|
| `GET /` | view `home` | Home page with four big buttons and the suggestion box |
| `GET /info/main`, `/vip/main` | views | Placeholder pages |
| `GET /suggestions` | `SuggestionController@suggest` | HTML fragment with a lunch suggestion |
| `GET /restaurants` | `RestaurantTagController@restaurantsFilter` | Filter page listing all tags |
| `GET /restaurants/results` | `RestaurantController@results` | HTML fragment with the result rows (AJAX) |
| `GET /restaurants/details/{id}` | `RestaurantController@details` | HTML fragment with the details (AJAX) |
| `GET /restaurant/details/{id}` | `RestaurantController@detailsSingle` | Full standalone details page, shareable URL |
| `GET /tags/autocomplete` | `RestaurantTagController@autocomplete` | JSON for tag autocomplete (not used by the UI) |
| same four for `/stores`, `/bars`, `/karaokes` | `Store*`, `Bar*`, `Karaoke*` | |
| `GET /up` | framework | Health check |

Every route is a `GET`: the app has no login and no admin (removed in November 2026; the data is edited in the seeders and the site is read-only). There are no accounts, so there is nothing to brute-force and nothing to leak.

Removed from the legacy routes: `/about`, `/test-html`, `/clubs*`, `/register`, `/password/*`, and the whole `/login`, `/logout` and `/admin/*` set (restaurant, tag and type CRUD, Debugbar switch, import/export).

## Views

- `layouts/app.blade.php` is the public layout: the stylesheet (dark by default, light with `data-theme="light"` on `<html>`, set before the first paint by a tiny inline script), the theme-color and color-scheme metas, CSRF meta, the `public/js/whereto.js` script tag (deferred, versioned by file modification time), `layouts.header`, `#position-report` for geolocation messages, content, `layouts.footer`.
- Each place folder has: `main` (page shell that includes `filter` and `results`; its `#content` div carries the `data-results-url` attribute the JavaScript reads), `filter` (tag cloud), `results` (sort buttons and column headers), `results-data` (the rows, returned by AJAX), `details` (fragment) and `details-single` (standalone page wrapping `details`).
- `public/manifest.json`, `public/sw.js` (service worker) and `public/icons/` make the app installable on a phone's home screen and usable offline; see `04-how-it-works.md` section 8.
- The JavaScript is not in the views: it lives in `public/js/whereto.js`, one file with a section per concern (geolocation, panels, results, tag filter, details, suggestion). The views call its global functions from `onclick` attributes and pass it the two server URLs through `data-results-url` and `data-suggestion-url` attributes. All requests use `fetch()`.
- `suggestions/suggestion.blade.php` renders the suggestion box only at lunch time; `suggestions/suggestion-text.blade.php` is the sentence returned by `/suggestions`.


## Known gaps

See `08-findings.md` for the full checklist with what was fixed in the upgrade. Still open by design:

- The four place sets are copy-pasted (consolidation was explicitly left out of the upgrade).
- Tag operators `OR`, `ANDNOT`, `ORNOT` exist server-side but the UI always sends `AND`.
- `restaurant_types` is seeded but not used by any page (`restaurants.type` is free text).
- Hidden header links (Info, VIP) are still `display: none`.
