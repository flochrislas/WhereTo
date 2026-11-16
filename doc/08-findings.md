# Findings from the November 2026 code review

Issues and surprises found while documenting the code on 2026-11-16. None of them break the main public flows (filter, results, details, suggestion). Items are grouped by how much they matter; tick them off as they get fixed.

## Breaks a feature

- [ ] **Admin restaurant create/edit throws.** `$fillable` is commented out on `Restaurant`, `Store`, `Bar` and `Karaoke`, so `Restaurant::create($request->all())` and `->update()` in `RestaurantAdminController` raise `MassAssignmentException`. Seeders are unaffected because Laravel runs them with `Model::unguarded()`. Fix: restore `$fillable` (the list is right there in a comment).
- [ ] **Missing controllers and views behind routes.** `/clubs*` (`ClubTagController`, `ClubController`), `/about` (no `about` view), `POST /admin/restaurants/import` (`importAlt`) and `/admin/restaurants/export/{type}` (`export`) all 500. The admin "Import/Export" page is linked from nowhere but exists.
- [ ] **`composer install --no-dev` crashes the app.** `config/app.php` registers `Barryvdh\Debugbar\ServiceProvider` and the four place models call `\Debugbar::debug()` in `tagsCached()`. Either install dev packages in production with `DEBUGBAR_ENABLED=false`, or remove the provider, alias and facade calls.
- [ ] **Google Analytics is dead.** `UA-132388077-1` is a Universal Analytics property; collection stopped in 2023. Replace with a GA4 tag or delete the partial.

## Security / exposure

- [ ] **Open registration.** `Auth::routes()` exposes `/register`; any visitor can create an account and reach `/admin`. The `users.level` column is never checked. Use `Auth::routes(['register' => false])` or remove auth scaffolding.
- [ ] **Seeded credentials.** Two users with password `password`, one of them a real-looking email address. Change or delete before exposing.
- [ ] **Default database password in `config/database.php`** (`1234rewq`). Harmless if `.env` overrides it, but it is in git history.
- [ ] **Debug sidebar prints config** in `layouts/html.blade.php` when `APP_DEBUG=true`; keep debug off in production.
- [ ] **Autocomplete caches by raw term.** `Cache::rememberForever($term, ...)` uses the user-supplied string as the cache key, so a term like `All-Restaurant` collides with the results cache. The endpoint is unused by the UI; delete it or prefix the key.

## Correctness / PHP 8 readiness

- [ ] `formatTags()` tests `$tags instanceof Traversable` without a leading backslash inside `App\Http\Controllers`, so it is always false. Harmless today (tags are always strings) but wrong.
- [ ] `ellipsis($place->points, 50)` passes null to `mb_strimwidth()` when `points` is null; deprecated on PHP 8.1+.
- [ ] `GeoUtils::google2coord()` explodes a possibly null link and reads `$split1[1]` without checking; warns on malformed URLs.
- [ ] `GeoUtils::getCoordinates()` is a non-static method calling the Google Geocoding API without a key. Dead code.
- [ ] `GeoUtils::withinDistanceSql()` and `flatDistance()` are marked "possibly wrong" by their own comments and are unused.
- [ ] `sortByDistance()` keys a map by the distance as a string; two places at the exact same distance overwrite each other. Rare with 7-decimal coordinates, but possible for places with identical coordinates.
- [ ] `$table->float('lat', 3, 7)`: the intention is 7 decimals, but `float` with those arguments is driver-dependent and the signature changed in Laravel 11. Use `decimal(10, 7)`.
- [ ] `Auth` controllers have `$redirectTo = ''`, so login redirects to the site root with an empty path.

## Dead code and clutter

- [ ] `source/restaurants/` is an orphaned early copy of the restaurant views.
- [ ] `resources/assets/`, `package.json`, `webpack.mix.js`, `public/js/app.js`, `public/css/app.css`: the Mix/Vue scaffold is never loaded by any view.
- [ ] `public/css/samples.css`, `tooltip-absolute.css`, `tooltip-overlay.css` are not referenced (tooltips were removed in August 2019).
- [ ] `config/app.php` declares an `Excel` alias for `Maatwebsite\Excel`, which is not installed.
- [ ] `routes/api.php` has the stock `auth:api` route; no API guard with tokens is configured.
- [ ] `user_restaurant` pivot (visited restaurants with comments) is declared on both models and used nowhere.
- [ ] `restaurant_types` table and its admin CRUD are not linked to `restaurants.type`, which is free text.
- [ ] Tag operators `OR`, `ANDNOT`, `ORNOT` are implemented but the UI hard-codes `AND`.
- [ ] Header links (Info, VIP, About, Login, Dashboard) are present but `display: none`.
- [ ] **Test suite is stale**: 7 of 19 tests fail (verified 2026-11-16). `GeoUtilsTest` expects kilometres but `distance()` returns metres and the miles/nautical variants no longer exist; both `RestaurantControllerTest` cases hit the `$fillable` exception (even `new Restaurant([...])`); `RestaurantTagControllerTest::testAutocompleteDB` asserts tag ids from an older seeding order. Tests also run against the configured database instead of an isolated one.

## Operational notes

- `composer install` fails under Composer 2 (`package:discover`: `Undefined index: name`); Laravel 5.8 predates Composer 2's `installed.json` format. Use Composer 1 until the framework is upgraded.
- With `APP_DEBUG=true` the Debugbar is injected into every AJAX fragment (results: ~650 kB, suggestion: ~1 MB). Disable it to measure real payloads.

- Every query result is cached with `rememberForever`; any data change needs `php artisan cache:clear` (the seeder does it).
- `Log::debug` is called on nearly every code path; set `APP_LOG_LEVEL=info` in production.
- `route:cache` cannot be used on the legacy app because several routes are closures.
- No `.env.example` is committed; see 05-dev-environment.md for a working one.
