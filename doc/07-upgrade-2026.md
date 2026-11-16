# Upgrading to the November 2026 stack

> **Done on 2026-11-16** (branch `upgrade-2026`): the app now runs on Laravel 13.42 / PHP 8.5.12 / PostgreSQL 18 with Composer 2, PHPUnit 12, Debugbar 4, ide-helper 3.8, Larastan 3.15. The note below is the plan that was followed; the "Outcome" section at the end records what was actually done and where it deviated.

Written on 2026-11-16. The repository is on Laravel 5.8 (July 2019) and PHP 7.1+. This note explains where the ecosystem is today, which path to take, and every change the WhereTo code needs. The application is small (9 models, 13 controllers, about 60 views, 17 migrations, no queue, no mail, no JavaScript build), so the upgrade is mostly mechanical.

## Target versions

| Component | Today (Nov 2026) | Target | Remarks |
|---|---|---|---|
| PHP | 8.5.12 is the current stable release; 8.6.0 GA is scheduled for 19 Nov 2026; 8.2 reaches end of life on 31 Dec 2026 | **8.5** | Laravel 13 declares support for PHP 8.3 to 8.5. Move to 8.6 once Laravel and the packages add it to their matrices (usually a few weeks after GA). |
| Laravel | 13.x (released 17 Mar 2026; 13.42 when the upgrade was done); 12 is on security fixes only; 11 and older are end of life | **13.x** | Laravel 14 is due in Q1 2027. |
| PostgreSQL | 18.7; 19.0 GA is scheduled for 19 Nov 2026; 14 just received its last release | **18** | Nothing in the schema is version-specific. |
| Node.js | 26 "Lithium" is Active LTS; 24 and 22 are Maintenance LTS | **26** only if you adopt Vite | Not required otherwise. |
| Composer | 2.9 | 2.x | |
| PHPUnit | 12 (Laravel 13 requires `^12.0`) or Pest 4 | PHPUnit 12 | Pest is optional. |
| Vite | 7, via `laravel/vite-plugin` | optional | Laravel Mix is abandoned in practice. |

## Choose the path: rebuild on a fresh skeleton

Two options exist:

1. **Incremental**: upgrade one major at a time (5.8 → 6 → 7 → 8 → 9 → 10 → 11 → 12 → 13), following each upgrade guide or buying one Laravel Shift per step. Eight steps for a few hundred lines of application code is not worth it.
2. **Fresh skeleton (recommended)**: `laravel new` a Laravel 13 project, then copy the application code (models, controllers, trait, `GeoUtils`, migrations, seeders, views, CSS, fonts, routes) into it and fix what the table below lists. You end up with the modern slim structure (`bootstrap/app.php`, no HTTP kernel, `bootstrap/providers.php`) for free and no leftover configuration from 2017.

The rest of this note assumes option 2. Laravel Boost's `/upgrade-laravel-v13` command and Laravel Shift only help from 12 to 13 and are not useful here.

## Step 0: capture a baseline

Before touching anything, run the legacy app once (05-dev-environment.md), seed it, and save the rendered HTML of `/restaurants/results?orderBy=ratings`, `/restaurants/results?tags=ramen&op=AND&orderBy=distance&position=35.6586,139.7016` and one details page. Diffing those outputs against the upgraded app is the cheapest regression test you will get.

## Step 1: new skeleton

```sh
composer global update laravel/installer
laravel new whereto-13 --no-interaction     # choose "no starter kit", PHPUnit, no npm
cd whereto-13
composer require laravel/tinker:^3.0
composer require --dev barryvdh/laravel-ide-helper:^3.7 larastan/larastan laravel/pint
```

Debugbar: the package historically named `barryvdh/laravel-debugbar` supports Laravel 13 from version 4.x (4.4.3 as of November 2026). Recent material indicates it is being republished as `fruitcake/laravel-debugbar` with the `Fruitcake\LaravelDebugbar` namespace; check Packagist and install whichever is current, as a dev dependency only.

Do **not** carry over: `fideloper/proxy` (built into the framework since Laravel 7), `laravelcollective/html` (abandoned, last release targets Laravel 10), `doctrine/dbal` (not needed for column changes since Laravel 11), `fzaninotto/faker` (abandoned; `fakerphp/faker` comes with the skeleton), `filp/whoops` (bundled).

## Step 2: move the code and fix it

### Models

| Change | Why |
|---|---|
| Move the nine models to `app/Models/` with namespace `App\Models` | Skeleton default since Laravel 8. Update every `App\Restaurant`, `'App\\'.$this->getModelName()` string and `@inject` path accordingly. |
| Restore `protected $fillable` on `Restaurant`, `Store`, `Bar`, `Karaoke` (or set `protected $guarded = []`) | `create($request->all())` throws `MassAssignmentException` otherwise. Seeders run unguarded, the admin controller does not. |
| Replace `\Debugbar::debug()` in `tagsCached()` with `Log::debug()` or nothing | Keeps the production build free of dev packages. |
| Optional: extract a `Place` abstract model and a `Tag` abstract model | The four place models are copy-pasted; the trait plus an abstract base removes the duplication. |

### Controllers and routes

| Change | Why |
|---|---|
| Route strings `'RestaurantController@results'` → `[RestaurantController::class, 'results']` | The controller namespace prefix on `RouteServiceProvider` is gone since Laravel 8. `Route::resource('restaurants', RestaurantAdminController::class)` likewise. |
| Replace the `array('as' => ...)` closures with `Route::view('/', 'home')->name('home')` | Lets `route:cache` work in production. |
| Delete the `clubs`, `about`, `test-html`, `restaurants/import` and `export` routes, or implement them | They reference missing code. |
| `$this->validate($request, [...])` → `$request->validate([...])` | The base `Controller` in the slim skeleton is an empty abstract class without `ValidatesRequests`. |
| `Auth::routes()` | Lives in `laravel/ui` since Laravel 6. Options: `composer require laravel/ui` and `php artisan ui:auth` (verify Laravel 13 support on Packagist first), Laravel Fortify (headless, first-party), or hand-write a login form and two routes using `Auth::attempt()`. This app only needs login and logout; drop registration and password reset entirely. |
| `request()->flash()` in `results()` | Harmless; remove it, nothing reads old input. |
| `formatTags()`: `Traversable` is resolved relative to the controller namespace and is always false | Use `\Traversable` or `is_iterable()`. |
| `ellipsis($place->points, 50)` with `points = null` | PHP 8.1+ deprecates passing null to `mb_strimwidth()`. Coalesce with `''`. |
| `GeoUtils::google2coord()` on a null link | `explode()` with null is deprecated and `$split1[1]` warns. Guard it. |
| `GeoUtils::getCoordinates()` | Non-static leftover calling Google without a key; delete it. |
| `AdminPanelController` Debugbar switch | Keep only if Debugbar stays installed; it uses the facade. |
| `SuggestionController::suggestLunch()` builds HTML in PHP | Fine, but move it to a Blade partial while you are there. |

Carbon 3 (bundled with Laravel 11+) keeps `Carbon::parse('today 11:30am', 'Asia/Tokyo')`, `now()`, `isBetween()` and `setTestNow()`, so the suggestion logic ports unchanged.

### Laravel 13 cache hardening (this matters here)

Laravel 13's `config/cache.php` ships `'serializable_classes' => false`, which refuses to unserialize PHP objects from the cache. WhereTo caches Eloquent collections everywhere (`Cache::rememberForever(...)` returning `$query->get()`). After the upgrade every cache read will fail or return nothing. Pick one:

- list the models in `serializable_classes` (`Restaurant::class, RestaurantTag::class, ...` plus `Illuminate\Database\Eloquent\Collection::class`), or
- cache arrays (`$query->get()->toArray()`) and hydrate with `Model::hydrate()` when reading, or
- drop the cache entirely: with a few hundred rows and the tag filters as SQL, PostgreSQL answers in under a millisecond.

Also check `config/session.php`: the new default `serialization => 'json'` is fine because nothing stores objects in the session.

### Migrations and seeders

| Change | Why |
|---|---|
| Keep the migration files but rewrite them as anonymous classes with `$table->id()` and `foreignId()->constrained()->cascadeOnDelete()` | Class-named migrations still work, but the skeleton style is nicer. |
| `$table->float('lat', 3, 7)` → `$table->decimal('lat', 10, 7)` | Since Laravel 11 `float()` takes a single precision argument; the old `(3, 7)` call silently means something else. `decimal` also stops the rounding you get from `float`. |
| Users table: start from the skeleton's migration and add the `level` column (or drop it, nothing reads it) | The new table has `email_verified_at`, and sessions/cache/jobs tables are created by the skeleton. |
| Delete the `create_cache_table` migration | `php artisan make:cache-table` or the skeleton already provides `cache` and `cache_locks`. |
| Move `database/seeds/*` to `database/seeders/` with namespace `Database\Seeders` | Laravel 8 layout; Composer autoloads it with PSR-4 instead of classmap. |
| `str_random()` in `RestaurantsTableSeeder` and `UserFactory` → `Str::random()` | Global string helpers were removed in Laravel 6. |
| `UserFactory`: convert to a class factory or delete it | Closure factories were removed in Laravel 8; the skeleton ships a class-based one. |

### Views

| Change | Why |
|---|---|
| `str_limit(...)` in the four `details-single` views → `Str::limit(...)` | Removed helpers. Add `use Illuminate\Support\Str;` or call `\Illuminate\Support\Str::limit`. |
| `{{ csrf_field() }}` → `@csrf` | Cosmetic. |
| `Form::open`, `Form::text`, `Form::label`... in `admin/restaurants/{create,edit,form,index}` → plain HTML | `laravelcollective/html` is abandoned and never supported Laravel 11+. Four files, each a short form. `spatie/laravel-html` or the `kkxdev/laravel-collective-html-spatie-html-bridge` shim exist if you prefer, but plain HTML is less work here. |
| `layouts/html.blade.php` loads Bootstrap 3, Bootstrap 4 alpha and jQuery from CDNs | Keep one Bootstrap (5.3) or restyle the admin with the dark theme. |
| `inline-javascript/google-analytics.blade.php` | Universal Analytics is dead; switch to a GA4 `G-XXXX` tag or delete it. |
| `@inject('suggestionController', 'App\Http\Controllers\SuggestionController')` | Works, but a view composer or passing `isLunchTime` from the route is cleaner. |
| `resources/views/admin/laravel.blade.php` and `test-html.blade.php` | Delete. |

### Configuration and environment

The skeleton's `config/` replaces the 2017 files. Carry over only these values:

| Old `.env` key | New key |
|---|---|
| `APP_LOG=daily`, `APP_LOG_LEVEL` | `LOG_CHANNEL=daily`, `LOG_LEVEL=info` |
| `CACHE_DRIVER=database` | `CACHE_STORE=database` |
| `QUEUE_DRIVER=sync` | `QUEUE_CONNECTION=sync` |
| `MAIL_DRIVER=log` | `MAIL_MAILER=log` |
| `DB_*` | unchanged; the skeleton defaults to SQLite, set `DB_CONNECTION=pgsql` |
| `POORLY_COPIED` | drop it and the check in `layouts/html` |
| `APP_NAME=WhereTo` | the new default cache prefix and session cookie derive from it (`whereto-cache-`, `whereto-session`) |

Providers: the skeleton registers only `AppServiceProvider` in `bootstrap/providers.php`. Middleware customisation (none needed here), trusted proxies and exception handling go in `bootstrap/app.php`. The `Excel` alias and the Debugbar/Collective providers from `config/app.php` must not be copied.

### Front end

Nothing in `resources/assets/` or `package.json` is used. Delete `package.json`, `webpack.mix.js`, `resources/assets/`, `public/js/app.js`, `public/css/app.css`, and the orphaned `source/restaurants/` directory. Keep `public/css/style-dark.css` and `public/fonts/`.

If you want a build step later, the Laravel 13 skeleton comes with Vite 7 and `laravel/vite-plugin`: move the inline JavaScript partials into `resources/js/app.js`, the CSS into `resources/css/app.css`, add `@vite(['resources/css/app.css', 'resources/js/app.js'])` to `layouts/app.blade.php`, and run `npm install && npm run build` with Node 26. `XMLHttpRequest` can become `fetch()` at that point, and `document.execCommand('copy')` (deprecated) can become `navigator.clipboard.writeText()`.

### Tests

- PHPUnit 12 (required by Laravel 13); test methods keep working, `phpunit.xml` moves to the skeleton version with an in-memory SQLite database by default.
- `tests/Unit/RestaurantControllerTest::testCreateFromName` and `RestaurantTagControllerTest` need `RefreshDatabase` and a seeded state; `GeoUtilsTest` and `SuggestionControllerTest` port as is.
- `tests/Feature/ExampleTest` (GET `/` is 200) is a good smoke test; add one feature test per place type that seeds two tagged places and asserts the `results` fragment order for `ratings`, `cost` and `distance`.
- Run `vendor/bin/pint` for formatting and `vendor/bin/phpstan analyse` (Larastan) to catch the PHP 8 nullability issues listed above before running the app.

## Step 3: verify

```sh
php artisan migrate:fresh --seed
php artisan test
php artisan serve
```

Compare the three saved HTML snapshots from step 0 with the upgraded output. Then follow 06-deploy-production.md with the PHP 8.5 lines.

## Step 4: optional modernisation once it runs

- Replace the per-request PHP distance sort with SQL: PostgreSQL's `earthdistance` extension or PostGIS `ST_DistanceSphere` make "nearest first" a `ORDER BY` clause and let you drop the forever-cache.
- Collapse the four copy-pasted controller/model/view sets into one parameterised `PlaceController` with a `places` config array.
- Replace the stock auth with Fortify and remove open registration.
- Use `Illuminate\Support\Number` / `Str` helpers instead of `number_format` and `mb_strimwidth`.
- Add a GitHub Actions workflow running `composer install`, Pint, PHPStan and `php artisan test` on PHP 8.5.

## Outcome (2026-11-16)

Path taken: fresh `laravel/laravel ^13` skeleton created with Composer in the PHP 8.5 container, legacy files restored from `master` into it, then ported file by file. Decisions: port and fix only (no consolidation of the four place sets), no build step, hand-written login, forever-cache kept with `serializable_classes`, PHPUnit 12.

What was verified against the legacy app (baseline captures in `dev/baseline/`, taken from the PHP 7.4 stack before the switch):

- Seeded row counts identical for every table.
- Tag clouds of the four filter pages identical.
- 12 of the 16 captured result/details fragments byte-identical. The 4 others differ for understood reasons: two restaurant lists swap rows that have equal scores/prices (PostgreSQL returned ties in arbitrary order; the new code adds `id` as a tiebreaker), and the two bar lists show different walking minutes because the legacy `BarController` hard-coded an "office" position instead of using the visitor's (a debug leftover, now fixed).
- `php artisan test`: 53 tests, 193 assertions, green. Pint and Larastan (level 5) clean.
- `route:cache`, `config:cache`, `view:cache` work; the app boots with `composer install --no-dev`.

Deviations from the plan above:

- Migrations keep their original file names but were rewritten as anonymous classes with `id()`, `decimal(10,7)` coordinates and `foreignId()->constrained()->cascadeOnDelete()` pivots; the skeleton's users migration got the `level` column.
- `GeoUtils::toPositionArray()` validates its input but still accepts a trailing third value, because several seed entries pass `'lat,lon,zoom'` strings (Ichiran would otherwise have fallen back to the Google link centre).
- `sortByDistance()` now sorts a collection by `currentDistance` instead of a string-keyed map (no more collisions for places at the same distance).
- The Debugbar package is `fruitcake/laravel-debugbar` 4.x; `AdminPanelController` only touches it when the container binding exists.
- Pint reformatted the seeders (short array syntax, unused imports removed), so git shows them as new files rather than renames of `database/seeds/*`.
- The skeleton's Vite files (`package.json`, `vite.config.js`, `.npmrc`, `resources/js`, `resources/css`), `AGENTS.md` and `welcome.blade.php` were deleted; `.env.example` is a WhereTo template (same keys as `dev/laravel.env`, empty secrets).
- A code review of the port added: `throttle:5,1` on `POST /login` (the legacy trait had a lockout), a "lat, lon" field on the admin restaurant form and a cache flush in the three admin controllers (otherwise a new place never showed up), working `PUT`/`DELETE` forms for tag and type edit/delete (broken since 2019), `Pivot` in `cache.serializable_classes` with a test through the `file` store, and no more `DB_*` variables in `dev/compose.yaml` (Laravel reads `$_SERVER` before `$_ENV`, so they overrode `phpunit.xml` and the tests wrote a SQLite file named `whereto` into `source/`).

## References (consulted 2026-11-16)

- Laravel release notes and support policy: https://laravel.com/framework/docs/releases and https://laravel-news.com/laravel-releases
- Laravel 13 upgrade guide: https://laravel.com/docs/13.x/upgrade
- Laravel 11 slim skeleton and migration of Kernel/providers: https://laravel.com/framework/docs/11.x/releases and https://kawax.biz/en/advanced/app-structure-migration
- PHP supported versions: https://www.php.net/supported-versions.php and https://php.watch/versions
- PostgreSQL 19 schedule: https://www.postgresql.org/about/news/postgresql-19-beta-4-released-3386/
- Node.js releases: https://nodejs.org/en/about/previous-releases
- laravelcollective/html abandonment: https://laravel-news.com/collective-html-abandoned and https://packagist.org/packages/laravelcollective/html
- Debugbar and ide-helper compatibility: https://laravelshift.com/can-i-upgrade-laravel/barryvdh/laravel-debugbar and https://github.com/barryvdh/laravel-ide-helper/releases
