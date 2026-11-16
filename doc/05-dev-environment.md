# Development environment

This note describes running the application **as it is committed** (Laravel 5.8, PHP 7.x). If you intend to modernise it, read 07-upgrade-2026.md first and set up PHP 8.5 instead; the old stack is only worth installing to take a snapshot of the working behaviour before upgrading.

## Requirements for the legacy stack

| Tool | Version | Why |
|---|---|---|
| PHP | 7.2, 7.3 or 7.4 | 7.1 is the declared minimum; 7.4 is the last version that runs Laravel 5.8 without patches. PHP 8 breaks Laravel 5.8 and its dependencies. |
| PHP extensions | `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo` | Laravel 5.8 requirements plus PostgreSQL |
| Composer | **1.x** | Composer 2 changed the `installed.json` format and Laravel 5.8's `package:discover` crashes on it (`Undefined index: name`). Use Composer 1, or upgrade first. |
| PostgreSQL | any supported version (18 today) | Default connection. SQLite also works for a quick look (`DB_CONNECTION=sqlite`). |
| Node.js | not needed | The Mix build output is not referenced by any view |

Because PHP 7.4 reached end of life in November 2022, do not install it on your workstation. Run it in a container instead.

## Option A: podman compose (recommended, ready to use)

A complete environment is committed in `dev/`: PHP 7.4 app image, PostgreSQL 18, automatic `composer install`, `.env` creation, migrations and seeding on first start. See [`dev/README.md`](../dev/README.md).

```sh
cd dev
podman compose up -d
# then open http://localhost:8010
```

Docker users can run the same `compose.yaml` with `docker compose`.

## Option B: native PHP on the host

If you already have a PHP 7.4 (for example through `phpenv`, `phpbrew` or Ubuntu's `ondrej/php` PPA):

```sh
sudo apt install php7.4-cli php7.4-pgsql php7.4-mbstring php7.4-xml php7.4-bcmath php7.4-curl php7.4-zip
```

Then follow the steps below with `php7.4` instead of `php`.

## First run

All commands run from `source/`.

1. Create the environment file. No `.env.example` is committed, so write one:

   ```ini
   APP_NAME=WhereTo
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000
   APP_LOG=daily
   APP_LOG_LEVEL=debug
   POORLY_COPIED=nope

   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=whereto
   DB_USERNAME=wheretouser
   DB_PASSWORD=change-me

   CACHE_DRIVER=database
   SESSION_DRIVER=file
   QUEUE_DRIVER=sync
   MAIL_DRIVER=log
   DEBUGBAR_ENABLED=true
   ```

   `.env` is git-ignored. For SQLite instead, set `DB_CONNECTION=sqlite`, `DB_DATABASE=/absolute/path/source/database/database.sqlite` and `touch` that file.

2. Install dependencies and generate the key:

   ```sh
   composer install
   php artisan key:generate
   ```

3. Create the schema and load the data:

   ```sh
   php artisan migrate
   php artisan db:seed      # deletes and reloads every table, then flushes the cache
   ```

   The seeders create two users (`florent.redares@gmail.com` and `user2@test.rt`, password `password`) for the admin.

4. Serve:

   ```sh
   php artisan serve
   ```

   Open http://localhost:8000. Geolocation works on `localhost` without HTTPS. To test from a phone on the same network you need HTTPS, for example with a `mkcert` certificate behind Caddy, or use the "position too far" fallback which uses Shibuya Station.

5. Optional: regenerate IDE helpers (dev only):

   ```sh
   php artisan ide-helper:generate
   php artisan ide-helper:models -N
   ```

## Day-to-day

- Changed seed data: `php artisan db:seed` (it clears the cache). Changed only code: `php artisan cache:clear` if query results are involved.
- Debugbar: enabled whenever `APP_DEBUG=true` and `DEBUGBAR_ENABLED` is not `false`; it shows queries, cache hits and the many `Log::debug` calls.
- Logs: `storage/logs/laravel-YYYY-MM-DD.log`.
- Tinker: `php artisan tinker`, for example `App\Restaurant::count()`.

## Tests

```sh
vendor/bin/phpunit
```

`phpunit.xml` sets array cache and session drivers but does not override the database, so the tests that touch Eloquent hit the configured database. Result of the suite as committed, run on 2026-11-16 in the `dev/` containers: 19 tests, 12 pass, 7 fail. All failures are stale tests rather than environment problems:

| Test | Result | Cause |
|---|---|---|
| `Unit/SuggestionControllerTest` (2) | pass | |
| `Unit/GeoUtilsTest` other cases | pass | |
| `Unit/GeoUtilsTest::testMiles`, `testN`, `testKm`, `testDistanceSmall` | fail | Expect kilometres but `GeoUtils::distance()` was changed to return metres (and the miles/nautical variants were dropped) after the tests were written |
| `Unit/RestaurantControllerTest::testCreateFromName`, `testGenerateCurrentDistances` | error | `MassAssignmentException`: `Restaurant::$fillable` is commented out, so even `new Restaurant([...])` throws |
| `Unit/RestaurantTagControllerTest::testAutocompleteDB` | fail | Asserts hard-coded tag ids (9, 21) that depend on an old seeding order; labels match |
| `Feature/ExampleTest` | pass | GET `/` returns 200 |

Composer note: the dev image ships Composer 1 because Laravel 5.8's `package:discover` fails with `Undefined index: name` under Composer 2.

For an isolated run, add to `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

and use `RefreshDatabase` in the tests that need tables.

## Front-end assets

Nothing to build. Edit `public/css/style-dark.css` and the Blade partials under `resources/views/inline-javascript/` directly. If you want to revive Laravel Mix, note that `package.json` pins Mix 1.x which needs Node 8-10; replace it during the upgrade (07-upgrade-2026.md) rather than trying to run it.
