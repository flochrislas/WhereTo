# WhereTo documentation

WhereTo (branded "FindItFast / enjoyShibuya" in the UI) is a small Laravel web app that helps you pick a place in Shibuya, Tokyo: restaurants for lunch, stores, bars and karaoke. Places are tagged, scored and geolocated; the UI filters by tag, sorts by rating, cost or walking distance from the visitor's GPS position, and shows a lunch suggestion around noon.

The application code lives in `source/`. These notes were written in November 2026 and describe the app after its upgrade to Laravel 13 / PHP 8.5 (branch `upgrade-2026`); before that, the last functional commit dated from January 2020.

| Document | What it covers |
|---|---|
| [01-technologies.md](01-technologies.md) | Languages, framework, packages, front-end stack, versions pinned in the lock files |
| [02-architecture.md](02-architecture.md) | Directory layout, classes, routes, views, and known gaps / dead code |
| [03-data-model.md](03-data-model.md) | Database tables, relationships, seed data, caching keys |
| [04-how-it-works.md](04-how-it-works.md) | Request flows: home page, filtering, results, details, suggestion, caching, offline |
| [05-dev-environment.md](05-dev-environment.md) | Setting up a local development environment and running the tests |
| [06-deploy-production.md](06-deploy-production.md) | Deploying on a production server (Nginx + PHP-FPM + PostgreSQL) |
| [07-upgrade-2026.md](07-upgrade-2026.md) | Upgrading from Laravel 5.8 / PHP 7.1 to Laravel 13 / PHP 8.5 and the rest of the 2026 stack |
| [08-findings.md](08-findings.md) | Bugs, security issues, dead code and quirks found during the review, as a checklist |
| [09-potential-improvements.md](09-potential-improvements.md) | Ideas for the future: mobile/tablet/PC experience, front-end and back-end architecture, performance, testing, product features |
