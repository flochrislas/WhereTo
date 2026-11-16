# Technologies

Everything below comes from `source/composer.json`, `source/composer.lock`, `source/package.json` and the views. Versions are the ones actually locked, not the ones you would get today.

## Back end

| Component | Version in the repo | Notes |
|---|---|---|
| PHP | `>= 7.1.3` required | The code uses scalar type hints, return types and `void`, so 7.1 really is the floor. It was last run on PHP 7.x. |
| Laravel framework | 5.8.29 (`5.8.*`) | Released 2019, end of life since 2020. |
| Symfony components | 4.3.3 | Pulled in by Laravel. |
| Database | PostgreSQL (default connection `pgsql`) | Config defaults: database `whereto`, user `wheretouser`. MySQL, SQLite and SQL Server connections are present in `config/database.php` but unused. |
| Cache | `database` driver by default | A `cache` table is created by a migration. The app relies heavily on `Cache::rememberForever`. |
| Session | `file` driver by default | |
| Queue / mail / broadcasting | Laravel defaults, unused | No jobs, mails or events are defined. |
| Logging | `daily` channel (`APP_LOG`) with `debug` level | Debug logging is verbose all over the controllers. |

### Composer packages (production)

| Package | Locked | Role |
|---|---|---|
| `laravel/framework` | 5.8.29 | Framework |
| `laravelcollective/html` | 5.8.0 | `Form::` / `Html::` Blade helpers, used only in the admin restaurant CRUD views |
| `laravel/tinker` | 1.0.8 | REPL |
| `fideloper/proxy` | 4.1.0 | `TrustProxies` middleware (built into Laravel since 7.x) |
| `nesbot/carbon` | 2.21 | Date handling, used by the lunch-time suggestion |

### Composer packages (development only)

| Package | Locked | Role |
|---|---|---|
| `barryvdh/laravel-debugbar` | ^3.2.4 | Debug bar; also used as a logging sink from models (`\Debugbar::debug`), and toggled from the admin panel |
| `barryvdh/laravel-ide-helper` | ^2.6 | IDE stubs, registered in `AppServiceProvider` outside production |
| `doctrine/dbal` | ~2.3 | Needed by ide-helper and column changes |
| `filp/whoops` | ^2.0 | Error pages |
| `fzaninotto/faker` | ^1.4 | Factories (abandoned project) |
| `mockery/mockery` | ^1.0 | Tests |
| `phpunit/phpunit` | ^7.5 | Tests |

Note that `config/app.php` also declares an `Excel` alias pointing at `Maatwebsite\Excel`, but that package is not installed. The alias is lazy so it only fails if `Excel::` is used, which nothing does.

## Front end

The public UI is deliberately minimal and framework-free:

- Blade templates under `resources/views/`.
- One hand-written stylesheet, `public/css/style-dark.css` (dark theme, CSS variables, CSS grid for the home page buttons and a flex "table" for results). Open Sans is self-hosted in `public/fonts/`.
- Vanilla JavaScript inlined in Blade partials under `resources/views/inline-javascript/` (`XMLHttpRequest`, `navigator.geolocation`, `localStorage` / `sessionStorage`). No jQuery, no Vue on public pages.
- Google Analytics (`gtag.js`, property `UA-132388077-1`) is injected only when `APP_ENV=production`. Universal Analytics properties stopped collecting data in 2023, so this is dead.

The admin pages use the older `layouts/html.blade.php` layout which loads Bootstrap 3.3.7, Bootstrap 4 alpha and jQuery 3.1.1 from CDNs.

### Node / Laravel Mix

`package.json` is the stock Laravel 5.5 scaffold: Laravel Mix 1.x, webpack, Vue 2, jQuery, Bootstrap-Sass, Axios, Lodash. `webpack.mix.js` compiles `resources/assets/js/app.js` and `resources/assets/sass/app.scss` into `public/js/app.js` and `public/css/app.css`. Neither compiled file is referenced by any view, so the Node toolchain is effectively unused. You can deploy without ever running `npm`.

## Tooling and hosting hints found in the repo

- `public/.htaccess` (Apache) and `public/web.config` (IIS) are the stock Laravel rewrite rules.
- `server.php` and `artisan serve` for local use.
- `phpunit.xml` runs `tests/Unit` and `tests/Feature` with array cache and session drivers.
- `.gitignore` mentions Homestead and Rocketeer, suggesting Homestead (Vagrant) was once the dev box. No Docker, CI or deployment scripts are committed.
- The IDE comments mention Eclipse and PhpStorm.
