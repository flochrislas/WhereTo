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
- `orderBy()` applies rating or cost ordering in SQL (restaurants only), with `id` as a tiebreaker so results are deterministic.
- `handleDistances()`, `sortByDistance()`, `generateCurrentDistances()` compute the distance from the visitor in PHP and optionally sort by it (places without coordinates are dropped when sorting by distance).
- `positionSpoofer()` falls back to Shibuya Station when the visitor is further than 2.5 km away, sent no position, or sent an unparsable one.
- `formatTags()` and `ellipsis()` are small helpers.

The four concrete place controllers are near copies of each other and only define `results()`, `details()` and `detailsSingle()` (the last two return 404 for unknown ids).

The four `*TagController`s serve the filter page (all tags, cached). `RestaurantTagController` also has the admin CRUD and an `autocomplete()` endpoint.

Models are equally repetitive: each place model `use`s the `Location` trait, declares `$fillable`, and defines `tags()`, `tagsCached()` and `attachTags()`. `Restaurant` and `User` also have a many-to-many "visited" relation with a `comment` pivot column.

## Other controllers

| Controller | Role |
|---|---|
| `SuggestionController` | Lunch suggestion. Plain class; `isLunchTime()` is static so Blade can call it through `@inject`. Renders `suggestions.suggestion-text`. |
| `RestaurantAdminController` | Resource CRUD for restaurants behind `auth`; parses the "lat, lon" form field and flushes the cache after every write. |
| `RestaurantTypeController` | Non-resourceful CRUD for the `restaurant_types` lookup table (`PUT` update, `DELETE` delete); flushes the cache after every write. |
| `AdminPanelController` | `POST admin/debug` toggles the Debugbar when it is installed. |
| `Auth\LoginController` | Hand-written login/logout (`Auth::attempt`). `POST /login` is throttled to 5 attempts per minute. No registration, no password reset. |

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

Auth: `GET /login` (named `login`), `POST /login`, `POST /logout` (named `logout`).

Admin, under `/admin` with the `auth` middleware: dashboard view, `POST debug`, `Route::resource('restaurants')`, the seven `restaurantType/*` routes, `Route::resource('restaurantTag')`.

Removed from the legacy routes: `/about`, `/test-html`, `/clubs*`, `/admin/laravel`, `/admin/restaurantsFiles`, restaurant import/export, `/register`, `/password/*`.

## Views

- `layouts/app.blade.php` is the public layout: dark stylesheet, CSRF meta, `layouts.header`, `#position-report` for geolocation messages, content, `layouts.footer`, geolocation script.
- `layouts/html.blade.php` is the Bootstrap layout used by the admin and login pages.
- Each place folder has: `main` (page shell that includes `filter` and `results`, plus a `resultsUrl` JS variable), `filter` (tag cloud), `results` (sort buttons and column headers), `results-data` (the rows, returned by AJAX), `details` (fragment) and `details-single` (standalone page wrapping `details`).
- `inline-javascript/` holds the JavaScript, one partial per concern: `main`, `filter`, `results` (clipboard), `details`, `geolocalisation`, `suggestion`. All requests use `fetch()`.
- `suggestions/suggestion.blade.php` renders the suggestion box only at lunch time; `suggestions/suggestion-text.blade.php` is the sentence returned by `/suggestions`.
- `admin/` has the restaurant, tag and type CRUD screens and the dashboard. Restaurant forms are plain HTML (`admin/restaurants/form.blade.php` loops over a field list).
- `auth/login.blade.php` is the login form.


## Known gaps

See `08-findings.md` for the full checklist with what was fixed in the upgrade. Still open by design:

- The four place sets are copy-pasted (consolidation was explicitly left out of the upgrade).
- Tag operators `OR`, `ANDNOT`, `ORNOT` exist server-side but the UI always sends `AND`.
- `restaurant_types` and the `user_restaurant` pivot are not used by any page.
- Hidden header links (Info, VIP, Login, Dashboard) are still `display: none`.
