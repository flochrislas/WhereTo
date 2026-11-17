# WhereTo

[![CI](https://github.com/flochrislas/WhereTo/actions/workflows/ci.yml/badge.svg)](https://github.com/flochrislas/WhereTo/actions/workflows/ci.yml)

A small Laravel website that helps you pick a place in Shibuya, Tokyo: restaurants for lunch, stores, bars and karaoke. Places are tagged, scored and geolocated; the site filters by tag, sorts by rating, cost or walking distance from your position, and suggests a lunch spot around noon.

Personal project, started in 2017, upgraded in November 2026 to Laravel 13 / PHP 8.5 / PostgreSQL 18.

## Layout

| Folder | Content |
|---|---|
| [`source/`](source/) | The Laravel application |
| [`dev/`](dev/) | Podman compose development environment (app on http://localhost:8010) |
| [`doc/`](doc/) | Documentation: technologies, architecture, data model, request flows, dev setup, production deployment, upgrade notes, findings, potential improvements |
| [`.github/`](.github/) | CI workflow (Pint, PHPStan, tests on SQLite and PostgreSQL 18) and Dependabot |

## Quick start

```sh
cd dev
podman compose up -d        # builds the image, installs dependencies, migrates and seeds on first run
```

Then open http://localhost:8010. To run without containers, follow [`doc/05-dev-environment.md`](doc/05-dev-environment.md).

Tests, code style and static analysis, from `source/`:

```sh
php artisan test
vendor/bin/pint
vendor/bin/phpstan analyse
```

## License

GPL-3.0, see [LICENSE](LICENSE).
