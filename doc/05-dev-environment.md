# Development environment

The app runs on Laravel 13 / PHP 8.5 / PostgreSQL 18. The podman compose environment in `dev/` provides exactly that stack; nothing needs to be installed on the host besides podman.

## Requirements

| Tool | Version | Why |
|---|---|---|
| PHP | 8.5 (8.3 minimum) | Laravel 13 requirement |
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
- Debugbar: enabled whenever `APP_DEBUG=true` and `DEBUGBAR_ENABLED` is not `false` in `.env`; it shows queries, cache hits and the many `Log::debug` calls. It is injected into the AJAX fragments too, which inflates them a lot.
- Logs: `storage/logs/laravel-YYYY-MM-DD.log`.
- Tinker: `php artisan tinker`, for example `App\Models\Restaurant::count()`.
- Style and static analysis: `vendor/bin/pint` and `vendor/bin/phpstan analyse`.

## Tests

```sh
php artisan test          # or vendor/bin/phpunit
```

`phpunit.xml` uses an in-memory SQLite database and array cache/session drivers, so the suite is isolated from the dev database. State on 2026-11-16: 53 tests, 193 assertions, all passing.

| Suite | What it covers |
|---|---|
| `Unit/GeoUtilsTest` | distances in metres, unit conversions, position and Google URL parsing |
| `Unit/RestaurantControllerTest` | distance computation and sorting, tag parsing, ellipsis, mass assignment |
| `Unit/RestaurantTagControllerTest` | tag creation and autocomplete query |
| `Unit/SuggestionControllerTest` | lunch-time window in Tokyo time |
| `Feature/PlacesTest` | per place type: filter page, results by distance, AND tag filter, details fragment and standalone page, 404; restaurant ratings and cost order; home page |
| `Feature/SuggestionTest` | empty outside lunch time, suggestion with link at lunch time |
| `Feature/AdminTest` | login/logout and throttling, guest redirect, `/register` is 404, restaurant CRUD (coordinates field, new place visible in results), tag and type edit/delete |
| `Feature/CacheSerializationTest` | forever-cache round trip through the `file` store, proving the `serializable_classes` allow-list is complete |
| `Feature/ExampleTest`, `Unit/ExampleTest` | skeleton smoke tests |

## Front-end assets

Nothing to build. Edit `public/css/style-dark.css` and `public/js/whereto.js` directly; the script tag's `?v=` query string is the file's modification time, so the browser picks up a new version on the next load. `node --check public/js/whereto.js` (and `public/sw.js`) catches syntax errors.

The service worker caches the static files: after editing the stylesheet, reload twice (the first reload serves the cached copy and fetches the new one), or tick "Update on reload" / "Bypass for network" in the browser's Application > Service Workers panel while developing. If you ever want a build step, re-add the Laravel 13 skeleton's Vite setup (`laravel/vite-plugin`, `package.json`, `vite.config.js`); those files were removed from this repository.
