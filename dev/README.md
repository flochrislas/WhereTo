# WhereTo dev environment (podman compose)

Runs the app (Laravel 13 on PHP 8.5) plus PostgreSQL 18 in two rootless containers. Everything is driven from this folder.

| Service | Image | Host port |
|---|---|---|
| `app` | `localhost/whereto-app:php8.5`, built from `Containerfile` | http://localhost:8010 |
| `db` | `docker.io/library/postgres:18` | `localhost:5433` (user `wheretouser`, password `whereto`, database `whereto`) |

Ports 8000 and 5432 are deliberately avoided because other projects on this laptop use them.

## Files

- `compose.yaml`: the two services, a named volume for the database and one for the Composer cache.
- `Containerfile`: `php:8.5-cli` with `pdo_pgsql`, `bcmath`, `zip`, `intl`, `pcntl` and Composer 2.
- `entrypoint.sh`: on every start it creates `source/.env` from `laravel.env` if missing, runs `composer install` if `vendor/` is missing, generates `APP_KEY` if empty, waits for PostgreSQL, runs migrations, and seeds once (marker file `source/storage/.dev-seeded`). Then it starts `php artisan serve`.
- `laravel.env`: template for `source/.env` (dev credentials only, Laravel 13 key names).
- `baseline/`: reference output of the legacy (Laravel 5.8) app captured before the upgrade: HTML fragments, a data dump and row counts. `capture.sh` reproduces the capture from the running app: `podman compose exec -T app sh -s /tmp/out < baseline/capture.sh`, then copy `/tmp/out` out of the container and `diff` against `baseline/`.

`source/` is bind-mounted at `/app`, so edits on the host are live. Rootless podman maps the container's root to your user, so `vendor/` and `storage/` stay owned by you.

## Commands

```sh
cd dev
podman compose up -d            # start (first run builds the image and installs dependencies, 1-2 min)
podman compose logs -f app      # watch the bootstrap / request log
podman compose down             # stop, keep the database
podman compose down -v          # stop and wipe the database and Composer cache
podman compose build --no-cache # rebuild the image after editing Containerfile or entrypoint.sh
```

Inside the app container:

```sh
podman compose exec app php artisan db:seed        # reload all data from the seeders (clears the cache)
podman compose exec app php artisan cache:clear
podman compose exec app php artisan tinker
podman compose exec app php artisan test           # SQLite in-memory, isolated from the dev database
podman compose exec app vendor/bin/pint            # code style
podman compose exec app vendor/bin/phpstan analyse # static analysis
podman compose exec app composer require vendor/pkg
podman compose exec app bash
```

Database from the host:

```sh
psql -h localhost -p 5433 -U wheretouser whereto    # password: whereto
```

## Resetting

- Data only: `podman compose exec app php artisan migrate:fresh --seed`.
- Everything: `podman compose down -v`, `rm -f ../source/storage/.dev-seeded ../source/.env`, `rm -rf ../source/vendor`, `podman compose up -d`.

## Notes

- Geolocation works in the browser on `http://localhost`, so "near first" sorting can be tested without HTTPS. Non-localhost origins (another device) need HTTPS; the server falls back to Shibuya Station.
- The admin is at http://localhost:8010/admin with `florent.redares@gmail.com` / `password` (seeded).
- Database settings live only in `source/.env`. Do not export `DB_*` variables into the container: Laravel reads `$_SERVER` before `$_ENV`, so they would override the `phpunit.xml` settings and the tests would write a SQLite file named after the database into `source/`.
- With `APP_DEBUG=true` the Debugbar is injected into every response, including the AJAX fragments. Set `DEBUGBAR_ENABLED=false` in `source/.env` to see realistic payload sizes (an environment variable on `artisan serve` is not forwarded).
