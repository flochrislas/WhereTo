# Technologies

State after the November 2026 upgrade (branch `upgrade-2026`). Versions come from `source/composer.json` / `composer.lock` and the dev containers. For the pre-upgrade stack see the history of this file on `master`.

## Back end

| Component | Version | Notes |
|---|---|---|
| PHP | 8.5 (`^8.3` required by Laravel 13) | Dev and production images use `php:8.5-cli` / `php8.5-fpm`. |
| Laravel framework | 13.x (13.42 locked on 2026-11-16) | Slim skeleton: `bootstrap/app.php`, no HTTP kernel, `bootstrap/providers.php`. |
| Database | PostgreSQL 18 | Default connection `pgsql`; SQLite in-memory for tests. |
| Cache | `database` store, `rememberForever` | The cached Eloquent collections are allow-listed in `config/cache.php` (`serializable_classes`). |
| Session | `file` driver in dev; `database` works too (the skeleton ships the table) | |
| Queue / mail / broadcasting | unused | `sync` queue, `log` mailer. |
| Logging | `daily` channel | Set `LOG_LEVEL=info` in production; the controllers log at debug level a lot. |

### Composer packages (production)

| Package | Role |
|---|---|
| `laravel/framework` `^13.17` | Framework |
| `laravel/tinker` `^3.0` | REPL |

`nesbot/carbon` 3 (lunch-time logic) and `fruitcake/php-cors` come with the framework.

### Composer packages (development only)

| Package | Role |
|---|---|
| `fruitcake/laravel-debugbar` `^4.4` | Debugbar (renamed from `barryvdh/laravel-debugbar` in 2026). Toggle with `DEBUGBAR_ENABLED`. |
| `barryvdh/laravel-ide-helper` `^3.8` | IDE stubs (`php artisan ide-helper:generate`), auto-discovered, dev only |
| `larastan/larastan` `^3.15` | PHPStan for Laravel, level 5, config in `phpstan.neon` |
| `laravel/pint` | Code style (Laravel preset) |
| `phpunit/phpunit` `^12.5`, `mockery/mockery`, `fakerphp/faker`, `laravel/pail`, `laravel/pao`, `nunomaduro/collision` | Skeleton defaults |

Removed during the upgrade: `laravelcollective/html` (abandoned), `fideloper/proxy` (built into the framework), `doctrine/dbal`, `fzaninotto/faker`, `filp/whoops`.

## Front end

Unchanged in spirit: no build step, no framework.

- Blade templates under `resources/views/`.
- One stylesheet, `public/css/style.css`, with a dark palette (default) and a light one as CSS variables, switched by `data-theme` on `<html>`; self-hosted Open Sans (WOFF2 only) in `public/fonts/`.
- Progressive Web App: `public/manifest.json` (name, icons from `public/icons/`, standalone display) and a service worker `public/sw.js` that pre-caches the app shell, serves static files from the cache and falls back to the last seen pages and results when offline. Registered by `whereto.js`; needs HTTPS or localhost.
- Vanilla JavaScript in one static file, `public/js/whereto.js`, loaded once by the public layout with `defer` and a cache-busting `?v=` query string (the file's modification time). No build step. It uses `fetch()`, `navigator.geolocation.watchPosition()` and the Clipboard API (with a fallback for non-secure contexts).
- No third-party front-end library and no CDN: the admin and login pages, which loaded Bootstrap 3 and jQuery, were removed in November 2026 (the seeders are the only way to edit the data).
- Google Analytics was removed (the Universal Analytics property was dead).

The Laravel 13 skeleton's Vite files (`package.json`, `vite.config.js`, `.npmrc`, `resources/js`, `resources/css`) were deleted; no view calls `@vite`.

## Tooling

- `dev/`: podman compose environment (PHP 8.5 image, PostgreSQL 18, Composer 2), see `05-dev-environment.md`.
- `phpunit.xml`: skeleton version, SQLite in-memory, array cache and session drivers. The database, cache and session entries carry `force="true"` so that variables exported into a container cannot override them.
- `pint.json` is not needed (Laravel preset by default); run `vendor/bin/pint`.
- `phpstan.neon`: Larastan level 5 over `app/`, `database/`, `routes/`.
- `public/.htaccess` and `public/index.php`: stock Laravel 13.
