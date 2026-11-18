# Development environment

The app runs on Laravel 13 / PHP 8.5 / PostgreSQL 18. The podman compose environment in `dev/` provides exactly that stack; nothing needs to be installed on the host besides podman.

## Requirements

| Tool | Version | Why |
|---|---|---|
| PHP | 8.5 (8.4 minimum) | Laravel 13 needs 8.3; the test dependencies (PHPUnit 13) need 8.4 |
| PHP extensions | `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `bcmath`, `intl`, `zip` | Laravel requirements plus PostgreSQL and Composer |
| Composer | 2.x | |
| PostgreSQL | 18 (any supported version works) | Default connection. SQLite also works for a quick look. |
| Node.js | not needed | No build step; the skeleton's Vite files were removed. |

## Option A: podman compose (recommended, ready to use)

A complete environment is committed in `dev/`: PHP 8.5 app image, PostgreSQL 18, automatic `composer install`, `.env` creation, migrations and seeding on first start. See [`dev/README.md`](../dev/README.md).

```sh
cd dev
podman compose up -d
# then open http://localhost:8010
```

Docker users can run the same `compose.yaml` with `docker compose`.

## Option B: native PHP on the host

```sh
sudo apt install php8.5-cli php8.5-pgsql php8.5-mbstring php8.5-xml php8.5-bcmath php8.5-curl php8.5-zip php8.5-intl composer
```

On Ubuntu the `php8.5` packages come from the `ppa:ondrej/php` repository. Then follow the steps below.

## First run

All commands run from `source/`.

1. Create the environment file from the dev template (`source/.env.example` has the same keys with empty values):

   ```sh
   cp ../dev/laravel.env .env      # then set DB_HOST=127.0.0.1 and DB_PORT=5433 or your own values
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
- Debugbar: enabled whenever `APP_DEBUG=true` and `DEBUGBAR_ENABLED` is not `false` in `.env`; it shows queries, cache hits and the many `Log::debug` calls. It is injected into the AJAX fragments too, which inflates them a lot.
- Logs: `storage/logs/laravel-YYYY-MM-DD.log`.
- Tinker: `php artisan tinker`, for example `App\Models\Restaurant::count()`.
- Style and static analysis: `vendor/bin/pint` and `vendor/bin/phpstan analyse`.

## Tests

```sh
php artisan test          # or vendor/bin/phpunit
```

`phpunit.xml` uses an in-memory SQLite database and array cache/session drivers, so the suite is isolated from the dev database. State on 2026-11-17: 74 tests, 534 assertions, all passing.

The same checks run on GitHub for every push to `master` and every pull request (`.github/workflows/ci.yml`): Pint, PHPStan, `node --check` on the two scripts, and the suite twice, on SQLite and on a PostgreSQL 18 service. To run the suite against PostgreSQL locally, create a `whereto_test` database and export the `DB_*` variables; they win over `phpunit.xml`:

```sh
podman compose exec db psql -U wheretouser -d whereto -c "CREATE DATABASE whereto_test;"
podman compose exec -e DB_CONNECTION=pgsql -e DB_HOST=db -e DB_DATABASE=whereto_test -e DB_USERNAME=wheretouser -e DB_PASSWORD=whereto app php artisan test
```

Dependabot (`.github/dependabot.yml`) opens a weekly pull request for Composer and GitHub Actions updates.

| Suite | What it covers |
|---|---|
| `Unit/GeoUtilsTest` | distances in metres, unit conversions, position and Google URL parsing |
| `Unit/RestaurantControllerTest` | tag parsing, ellipsis, mass assignment |
| `Unit/PlaceSearchTest` | distance computation and sorting, position fallback to the station, cache keys per kind and parameters |
| `Unit/RestaurantTagControllerTest` | tag creation and autocomplete query |
| `Unit/SuggestionControllerTest` | lunch-time window in Tokyo time |
| `Feature/PlacesTest` | per place type: filter page, results by distance, AND tag filter, details fragment and standalone page, 404; restaurant ratings and cost order; home page; `/login` and `/admin` are 404 |
| `Feature/SuggestionTest` | empty outside lunch time, suggestion with link at lunch time |
| `Architecture/ArchitectureTest` | rules about the code's shape, checked with reflection and file scans: controllers extend `Controller` (place ones `PlaceController`) and never build another controller or a query (the search services and the models do that); models, services and support classes ignore the HTTP request; enums are string-backed with `fromRequest()`; no `dd()`/`dump()`/Debugbar/`env()` outside `config/`; no `$request->all()`; every application route is a named `GET`; every `onclick` in the views names a function of `whereto.js`; only the layout has an inline `<script>`. Run alone with `php artisan test --testsuite=Architecture` |
| `Feature/CacheSerializationTest` | forever-cache round trip through the `file` store, proving the `serializable_classes` allow-list is complete |
| `Feature/ExampleTest`, `Unit/ExampleTest` | skeleton smoke tests |

## Browser tests

The PHPUnit suite stops at the HTTP layer. The scenarios in `e2e/tests/` drive the real pages in Chromium with Playwright: home page and theme switch, tag filter with "all / any" and the keyboard, the three sort orders, details and BACK (and the details tags), the standalone page, and the service worker offline. They run twice, as a phone (Pixel 7) and as a desktop (1280px, three panels side by side), with the GPS faked near the station.

They need Node 22 and the app running (the dev stack by default, `BASE_URL` for another):

```sh
cd e2e
npm install                      # once: the Playwright test runner
npx playwright install chromium  # once: the browser build the runner wants
npm test                         # BASE_URL=http://localhost:8010 npm test to be explicit
npm run test:headed              # watch the browser
npx playwright show-trace test-results/<test>/trace.zip   # the trace of a failed test
```

CI runs them in a second job after the PHP checks, against a `php artisan serve` on the seeded PostgreSQL database, and keeps the HTML report as an artifact when something fails. `e2e/node_modules`, `test-results` and `playwright-report` are ignored by git.

## Front-end assets

Nothing to build. Edit `public/css/style.css` and `public/js/whereto.js` directly; the script tag's `?v=` query string is the file's modification time, so the browser picks up a new version on the next load. `node --check public/js/whereto.js` (and `public/sw.js`) catches syntax errors.

The service worker caches the static files: after editing the stylesheet, reload twice (the first reload serves the cached copy and fetches the new one), or tick "Update on reload" / "Bypass for network" in the browser's Application > Service Workers panel while developing. If you ever want a build step, re-add the Laravel 13 skeleton's Vite setup (`laravel/vite-plugin`, `package.json`, `vite.config.js`); those files were removed from this repository.
