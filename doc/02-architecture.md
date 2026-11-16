# Architecture and code organization

## Repository layout

```
WhereTo/
├── doc/                  this documentation
├── README.md, LICENSE    GPL-3.0 licence
└── source/               the Laravel application (standard 5.x skeleton)
    ├── app/
    │   ├── Restaurant.php, RestaurantTag.php, RestaurantType.php
    │   ├── Store.php, StoreTag.php
    │   ├── Bar.php, BarTag.php
    │   ├── Karaoke.php, KaraokeTag.php
    │   ├── User.php
    │   ├── Traits/Location.php          lat/lon helpers shared by the place models
    │   ├── Tools/GeoUtils.php           distance maths, Google Maps URL parsing
    │   ├── Http/Controllers/            see below
    │   ├── Http/Middleware/             stock Laravel middleware
    │   ├── Http/Kernel.php, Console/Kernel.php, Exceptions/Handler.php
    │   └── Providers/                   stock providers (+ ide-helper registration)
    ├── bootstrap/, config/, public/, storage/
    ├── database/migrations/             17 migrations
    ├── database/seeds/                  the actual data lives here (see 03-data-model.md)
    ├── resources/views/                 Blade templates
    ├── resources/assets/                unused Mix scaffold (Vue/Sass)
    ├── routes/web.php                   all routes
    ├── tests/Unit, tests/Feature
    └── restaurants/                     stale copies of early restaurant views, not loaded by anything
```

## Place types and the controller hierarchy

The app handles four kinds of "places". Each one is a trio of model, tag model and pivot table, plus two controllers and a set of views:

| Place | Model | Tag model | Public controllers | Views |
|---|---|---|---|---|
| Restaurant | `App\Restaurant` | `App\RestaurantTag` | `RestaurantController`, `RestaurantTagController` | `resources/views/restaurants/` |
| Store | `App\Store` | `App\StoreTag` | `StoreController`, `StoreTagController` | `resources/views/stores/` |
| Bar | `App\Bar` | `App\BarTag` | `BarController`, `BarTagController` | `resources/views/bars/` |
| Karaoke | `App\Karaoke` | `App\KaraokeTag` | `KaraokeController`, `KaraokeTagController` | `resources/views/karaokes/` |

The generic logic is in the abstract `App\Http\Controllers\PlaceController`:

- `getModelName()` is abstract; the subclass returns `'Restaurant'`, `'Store'`, and so on. From that name the base class derives the model class (`App\Restaurant`), the results partial (`restaurants.results-data`) and the details views (`restaurants.details`, `restaurants.details-single`).
- `useCache()` / `cacheKey()` / `readFromDB()` run the tag query, with the result cached forever.
- `whereTags()` builds the `AND`, `OR`, `ANDNOT`, `ORNOT` tag filters with `whereHas` on the `tags` relation.
- `orderBy()` applies rating or cost ordering in SQL (only for restaurants).
- `handleDistances()`, `sortByDistance()`, `generateCurrentDistances()` compute distance from the visitor in PHP and optionally sort by it.
- `positionSpoofer()` falls back to Shibuya Station when the visitor is further than 2.5 km away or sent no position.
- `formatTags()` and `ellipsis()` are small helpers.

The four concrete place controllers are near copies of each other and each only define `results()`, `details()` and `detailsSingle()`.

The four `*TagController`s serve the filter page (all tags, cached). Only `RestaurantTagController` also has a full admin CRUD and an `autocomplete()` endpoint.

Models are equally repetitive: each place model `use`s the `Location` trait and defines `tags()`, `tagsCached()` and `attachTags()`. `Restaurant` and `User` also have a many-to-many "visited" relation with a `comment` pivot column.

## Other controllers

| Controller | Role |
|---|---|
| `SuggestionController` | Lunch suggestion. Pure PHP class (does not extend `Controller`); `isLunchTime()` is static so Blade can call it through `@inject`. |
| `RestaurantAdminController` | Resource CRUD for restaurants behind `auth`. Also referenced by routes for `export` and `importAlt`, which do not exist. |
| `RestaurantTypeController` | Non-resourceful CRUD for the `restaurant_types` lookup table. |
| `AdminPanelController` | `POST admin/debug` toggles the Debugbar on or off. |
| `Auth\*` | Stock Laravel 5.8 login, register, password reset. Both `redirectTo` values are empty strings. |

## Routes (`routes/web.php`)

Public:

| Method and path | Handler | Purpose |
|---|---|---|
| `GET /` | closure, `home` view | Home page with four big buttons and the suggestion box |
| `GET /info/main`, `/vip/main`, `/about`, `/test-html` | closures | Placeholder pages (`about` has no view and will 500) |
| `GET /suggestions` | `SuggestionController@suggest` | HTML fragment with a lunch suggestion |
| `GET /restaurants` | `RestaurantTagController@restaurantsFilter` | Filter page listing all tags |
| `GET /restaurants/results` | `RestaurantController@results` | HTML fragment with the result rows (AJAX) |
| `GET /restaurants/details/{id}` | `RestaurantController@details` | HTML fragment with the details (AJAX) |
| `GET /restaurant/details/{id}` | `RestaurantController@detailsSingle` | Full standalone details page, shareable URL |
| `GET /tags/autocomplete` | `RestaurantTagController@autocomplete` | JSON for tag autocomplete (not used by the current UI) |
| same four for `/stores`, `/bars`, `/karaokes` | `Store*`, `Bar*`, `Karaoke*` | |
| `GET /clubs…` | `ClubTagController`, `ClubController` | Controllers do not exist; these routes 500 |

Admin, under `/admin` with the `auth` middleware:

| Path | Handler |
|---|---|
| `GET /admin` | `admin.dashboard` view |
| `POST /admin/debug` | Debugbar switch |
| `resource /admin/restaurants` | `RestaurantAdminController` |
| `GET/POST /admin/restaurantType/...` | `RestaurantTypeController` |
| `resource /admin/restaurantTag` | `RestaurantTagController` |
| `GET /admin/restaurantsFiles`, `/admin/restaurants/export/{type}`, `POST /admin/restaurants/import` | import/export page; the handlers are missing |
| `GET /admin/laravel` | the stock Laravel welcome view |

`Auth::routes()` adds `/login`, `/register`, `/logout`, `/password/*`. `routes/api.php` only contains the stock `auth:api` user route, which is unusable because no API guard is configured with tokens.

## Views

- `layouts/app.blade.php` is the public layout: dark stylesheet, CSRF meta, `layouts.header`, `#position-report` for geolocation messages, content, `layouts.footer`, and the geolocation script. Google Analytics is included only in production.
- `layouts/html.blade.php` is the Bootstrap layout used by admin and auth pages.
- Each place folder has: `main` (page shell that includes `filter` and `results`, plus a `resultsUrl` JS variable), `filter` (tag cloud), `results` (sort buttons and column headers), `results-data` (the rows, returned by AJAX), `details` (fragment) and `details-single` (standalone page wrapping `details`).
- `inline-javascript/` holds the JavaScript, one partial per concern: `main` (showing/hiding panels, "Show All"), `filter` (tag selection and AJAX search), `results`, `details` (AJAX details, back button), `geolocalisation`, `suggestion`, `google-analytics`.
- `suggestions/suggestion.blade.php` injects `SuggestionController` and renders the suggestion box only at lunch time.
- `admin/` has the restaurant, tag and type CRUD screens, the dashboard and the import/export page.

The home page links Stores, Bars and Karaoke with a `locked` CSS class and an "alpha version" ribbon; only Lunch is considered finished.

## Known gaps and dead code

Keep these in mind when deploying or upgrading; none of them break the main flows.

- Routes for clubs, restaurant import/export and `/about` point at code or views that do not exist.
- `source/restaurants/` is an orphaned early copy of the restaurant views.
- `resources/assets/` and `package.json` (Vue, Mix) are unused; `public/js/app.js` and `public/css/app.css` are never loaded.
- `$fillable` is commented out on all four place models. Seeding still works because Laravel runs seeders with mass-assignment protection disabled (`Model::unguarded()`), but `Restaurant::create($request->all())` in `RestaurantAdminController@store` and `update` throws a `MassAssignmentException`, so creating or editing a restaurant from the admin is broken. Both `RestaurantControllerTest` cases fail for the same reason, since even `new Restaurant([...])` goes through `fill()`. Restore `$fillable` on `Restaurant` if you want the admin CRUD back.
- `GeoUtils::getCoordinates()` is a non-static leftover that calls the Google Geocoding API without a key.
- `config/database.php` ships a default PostgreSQL password. Always override it from `.env`.
- Header links to login, dashboard, info and VIP are present but hidden with `display: none`.
- Debug logging is called on nearly every request path; `APP_LOG_LEVEL` should be `info` or higher in production.
