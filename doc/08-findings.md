# Findings from the November 2026 code review

Issues and surprises found while documenting the legacy code on 2026-11-16. Ticked items were fixed by the Laravel 13 upgrade the same day (branch `upgrade-2026`); the notes in *italics* say how.

## Breaks a feature

- [x] **Admin restaurant create/edit throws.** *`$fillable` restored on the four place models; covered by `AdminTest`.* `$fillable` is commented out on `Restaurant`, `Store`, `Bar` and `Karaoke`, so `Restaurant::create($request->all())` and `->update()` in `RestaurantAdminController` raise `MassAssignmentException`. Seeders are unaffected because Laravel runs them with `Model::unguarded()`. Fix: restore `$fillable` (the list is right there in a comment).
- [x] **Missing controllers and views behind routes.** *Routes removed.* `/clubs*` (`ClubTagController`, `ClubController`), `/about` (no `about` view), `POST /admin/restaurants/import` (`importAlt`) and `/admin/restaurants/export/{type}` (`export`) all 500. The admin "Import/Export" page is linked from nowhere but exists.
- [x] **`composer install --no-dev` crashes the app.** *No runtime reference to dev packages any more.* `config/app.php` registers `Barryvdh\Debugbar\ServiceProvider` and the four place models call `\Debugbar::debug()` in `tagsCached()`. Either install dev packages in production with `DEBUGBAR_ENABLED=false`, or remove the provider, alias and facade calls.
- [x] **Google Analytics is dead.** *Partial removed.* `UA-132388077-1` is a Universal Analytics property; collection stopped in 2023. Replace with a GA4 tag or delete the partial.

## Security / exposure

- [x] **Open registration.** *Gone with the whole login and admin (November 2026).* `Auth::routes()` exposes `/register`; any visitor can create an account and reach `/admin`. The `users.level` column is never checked. Use `Auth::routes(['register' => false])` or remove auth scaffolding.
- [x] **Seeded credentials.** *Gone: the users table, the seeder and the login were removed with the admin (November 2026).* Two users with password `password`, one of them a real-looking email address. Change or delete before exposing.
- [x] **Default database password in `config/database.php`** *Skeleton config, no password.* (`1234rewq`). Harmless if `.env` overrides it, but it is in git history.
- [x] **Debug sidebar prints config** *Removed from the layout.* in `layouts/html.blade.php` when `APP_DEBUG=true`; keep debug off in production.
- [x] **Autocomplete caches by raw term.** *Key is now `autocomplete-<md5>`.* `Cache::rememberForever($term, ...)` uses the user-supplied string as the cache key, so a term like `All-Restaurant` collides with the results cache. The endpoint is unused by the UI; delete it or prefix the key.

## Correctness / PHP 8 readiness

- [x] `formatTags()` tests `$tags instanceof Traversable` without a leading backslash inside `App\Http\Controllers`, so it is always false. Harmless today (tags are always strings) but wrong.
- [x] `ellipsis($place->points, 50)` passes null to `mb_strimwidth()` when `points` is null; deprecated on PHP 8.1+.
- [x] `GeoUtils::google2coord()` explodes a possibly null link and reads `$split1[1]` without checking; warns on malformed URLs.
- [x] `GeoUtils::getCoordinates()` is a non-static method calling the Google Geocoding API without a key. Dead code.
- [ ] `GeoUtils::withinDistanceSql()` and `flatDistance()` are marked "possibly wrong" by their own comments and are unused.
- [x] `sortByDistance()` keys a map by the distance as a string; two places at the exact same distance overwrite each other. Rare with 7-decimal coordinates, but possible for places with identical coordinates.
- [x] `$table->float('lat', 3, 7)`: the intention is 7 decimals, but `float` with those arguments is driver-dependent and the signature changed in Laravel 11. Use `decimal(10, 7)`.
- [x] `Auth` controllers have `$redirectTo = ''`, so login redirects to the site root with an empty path.

## Dead code and clutter

- [x] `source/restaurants/` is an orphaned early copy of the restaurant views.
- [x] `resources/assets/`, `package.json`, `webpack.mix.js`, `public/js/app.js`, `public/css/app.css`: the Mix/Vue scaffold is never loaded by any view.
- [x] `public/css/samples.css`, `tooltip-absolute.css`, `tooltip-overlay.css` are not referenced (tooltips were removed in August 2019).
- [x] `config/app.php` declares an `Excel` alias for `Maatwebsite\Excel`, which is not installed.
- [x] `routes/api.php` has the stock `auth:api` route; no API guard with tokens is configured.
- [x] `user_restaurant` pivot (visited restaurants with comments) is declared on both models and used nowhere. *Removed with the users table.*
- [ ] `restaurant_types` table is not linked to `restaurants.type`, which is free text. *The CRUD is gone; the table and its seeder stay.*
- [x] Tag operators `OR`, `ANDNOT`, `ORNOT` are implemented but the UI hard-codes `AND`. *The UI has "Match all / any" buttons since November 2026; `ANDNOT` and `ORNOT` stay server-side only.*
- [ ] Header links (Info, VIP) are present but `display: none`. *Login and Dashboard are gone.*
- [x] **Test suite is stale** *Rewritten and extended: 53 green tests on SQLite in-memory, including a serializing-cache round trip.*: 7 of 19 tests fail (verified 2026-11-16). `GeoUtilsTest` expects kilometres but `distance()` returns metres and the miles/nautical variants no longer exist; both `RestaurantControllerTest` cases hit the `$fillable` exception (even `new Restaurant([...])`); `RestaurantTagControllerTest::testAutocompleteDB` asserts tag ids from an older seeding order. Tests also run against the configured database instead of an isolated one.

## Operational notes

- **Legacy `BarController` hard-coded an "office" position** instead of the visitor's (debug leftover found while diffing the upgrade against the baseline). *Fixed: bars use the real position like the other place types.*
- **Equal scores sorted in arbitrary order** (PostgreSQL ties). *Fixed: `id` tiebreaker.*
- (legacy) `composer install` fails under Composer 2 (`package:discover`: `Undefined index: name`); Laravel 5.8 predates Composer 2's `installed.json` format. Use Composer 1 until the framework is upgraded.
- With `APP_DEBUG=true` the Debugbar is injected into every AJAX fragment (results: ~650 kB, suggestion: ~1 MB). Disable it to measure real payloads.

- Every query result is cached with `rememberForever`; the admin controllers and the seeder flush it, any other data change needs `php artisan cache:clear`.
- (legacy) The admin tag/type edit forms posted with `method="restaurantTag"` (so GET) and delete was a plain link; edit and delete never worked. *Fixed: `PUT`/`DELETE` forms, covered by tests.*
- (legacy) The admin restaurant form had no coordinates field in the port, so new places never appeared in distance-sorted lists. *Fixed: "lat, lon" field.*
- (legacy) Login lost its 5-per-minute throttle when `AuthenticatesUsers` went away. *Fixed: `throttle:5,1` on `POST /login`.*
- Tags cached from a `belongsToMany` carry a `Pivot` object; it must be in `cache.serializable_classes` or it comes back as `__PHP_Incomplete_Class`. *Fixed and tested.*
- `Log::debug` is called on nearly every code path; set `LOG_LEVEL=info` in production.
- (legacy) `route:cache` could not be used because several routes were closures. *Fixed: all routes are controller or view routes.*
- `dev/laravel.env` is the reference environment file for the container; `source/.env.example` has the same keys with empty secrets.
