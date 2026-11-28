# WhereTo, the stand-alone Android app

An APK that holds the WhereTo site **and its data**, installed by hand, that works with no server and no network
(airplane mode included). The design is in [`doc/14-android-app.md`](../../doc/14-android-app.md) (section "A
stand-alone APK that needs no server") and the plan it was built from in
[`doc/16-standalone-apk-plan.md`](../../doc/16-standalone-apk-plan.md).

```sh
cd tools/android-app
npm run build                          # export, tests, APK: ends with build/whereto-<date>.apk
npm run install-apk                    # on the phone plugged in by USB (waits for it)
```

## How it works

1. **`npm run export`** (`export.mjs`) asks Laravel, in the dev container, for every page and fragment of the site,
   in English and Japanese, and writes them as files into `export/`: `php artisan app:export-static`. The command
   first rebuilds a database of its own, `whereto_export`, from the seeders (never the dev database: see
   "The export database" below), and renders the pages in a stand-alone mode (root-relative URLs, no "Copy URL",
   the suggestion control always present and hidden). It also writes `export/app-data/`: per kind, the ids of the
   places in the two orders the server returns them, their tags and coordinates, the results fragment with every
   row, the suggestion sentences, and `meta.json` with the date of the export (the app's version).
   Right after, `php artisan app:export-reference` writes `export-reference.json` next to `export/` (not inside: it
   does not go into the APK): the site's own answers to the requests of `test/parity-requests.json`, from the same
   database.
2. In the app, `whereto-local.js` (in `source/public/js/`) answers what depends on the visitor: the results (tags,
   operator, order, distances from the phone) and the lunch suggestion, decided by the phone's clock in Tokyo time.
   Everything else is a file.
3. **`npm test`** runs the Node tests (`test/`: the path rule, and the pure functions of `whereto-local.js` against
   the PHP rules), then the browser spec `e2e/tests/standalone.spec.js` on the preview of `export/`: pages, places,
   Back, the suggestion at 09:00 and 12:00 (Playwright's clock), location denied, and the **parity** of every
   request of the table with the site's answer. Any request outside the preview fails a test.
4. **`npm run apk`** (`build-apk.sh`) copies `export/` into the Android project and builds the APK in a container
   (`Containerfile`): only podman and `adb` are needed on the laptop.

## Prerequisites

- Node 22 or later (no npm dependency for the tool itself; the browser spec uses the Playwright of `e2e/`:
  `cd ../../e2e && npm install && npx playwright install chromium` once).
- podman, and the dev containers up: `cd dev && podman compose up -d` (see `doc/05-dev-environment.md`).
- For the phone: `adb` (`sudo apt install adb`; `install-apk.sh` also finds the one of an Android SDK, in
  `~/Android/Sdk/platform-tools/` or under `ANDROID_HOME`, when it is not in the `PATH`), USB debugging on the phone (Settings > About phone > tap "Build
  number" seven times, then Settings > System > Developer options > USB debugging), and a current "Android System
  WebView" (updated from the Play Store; the app needs Android 7 or later).

## The commands

| Command | What it does |
|---|---|
| `npm run export` | rebuilds `whereto_export` from the seeders and writes `export/` and `export-reference.json` |
| `npm test` | Node tests, then the browser spec on the preview (needs an export) |
| `npm run test:node` | the Node tests only (`node --test test/*.test.mjs`) |
| `npm run preview` | the app in a desktop browser: open http://localhost:8030/ (`node preview.mjs <port>`; port 0: any) |
| `npm run apk` | the APK from the current `export/`, in `build/` |
| `npm run install-apk` | installs the most recent APK of `build/` on the phone plugged in by USB (`./install-apk.sh [apk]`); it waits for the phone, and installs over the app already there, keeping its saved theme and choices |
| `npm run build` | export, test, apk |

The tests run by hand, and before every APK through `npm run build`: they are not part of the CI (decided in
November 2026).

## The preview

`preview.mjs` serves `export/` on 127.0.0.1 with the path rule of the Android app (`lib/resolve.mjs`: the query
string dropped, `..` refused, the file, else `<path>/index.html`, else a real 404). Open it as
`http://localhost:<port>/`, not `http://127.0.0.1:<port>/`: the page accepts geolocation without HTTPS on the host
`localhost` only. It is the app as the phone runs it, minus the Android bridge (`window.WhereToApp`): the buttons of
a place open new tabs, and Back is the browser's.

## Updating the data

The data is in the APK: a corrected restaurant reaches the phone with a new build and a new install.

```sh
# edit the seeders (source/database/seeders/), then
npm run build
npm run install-apk
```

The version of the app is the date of the export (Settings > Apps > WhereTo shows it), and its version code grows
with it, so a newer build installs over an older one.

## The export database

`app:export-static` drops and rebuilds a whole database, so it only ever works on the `export` connection of
`config/database.php`: the dev server and user, but the fixed database name `whereto_export` and no `DB_URL`.
Before dropping anything it checks, on that very connection, that the driver is PostgreSQL and that the server says
`select current_database()` is `whereto_export`, and it refuses a cached configuration (`php artisan config:clear`).
`export.mjs` creates the empty database once (`CREATE DATABASE whereto_export` in the `db` container). The dev
database and its cache are not touched: the export runs with the array cache and session.

## Troubleshooting

- **`INSTALL_FAILED_UPDATE_INCOMPATIBLE` / signatures do not match**: the APK was signed with another key than the
  installed one. The builds are signed with the key in `.keystore/` (made once by `build-apk.sh`, ignored by git):
  keep that folder. If it was lost, uninstall the app first (`adb uninstall io.github.flochrislas.whereto`; its
  saved theme and choices go with it).
- **`INSTALL_FAILED_VERSION_DOWNGRADE`**: the APK's export is older than the installed one (the version is the date
  of the export). Install with `adb install -r -d`, or uninstall first.
- **The first build is slow**: it builds the Android image (about 1.3 GB) and downloads Gradle and the libraries,
  kept in the podman volume `whereto-android-gradle` (`podman volume rm whereto-android-gradle` to start over).
  `./build-apk.sh [export-dir]` builds an APK from another export folder.
- **Installing from a file on the phone** rather than `adb`: Android asks to allow "Install unknown apps" for the
  app that opens the APK (Files, the browser).
- **"Cannot reach the export database"**: the dev containers are down, or `whereto_export` does not exist:
  `npm run export` creates it.
- **"Another export is running"**: one export runs at a time (they share the export database). If none is running,
  an earlier one was killed: remove the folder `.export.lock`. A failed export never replaces the previous `export/`
  and its reference answers: the new pair is swapped in only once complete.
- **"The configuration is cached"**: run `php artisan config:clear` in the app container.
- **The dev containers are not running**: `cd dev && podman compose up -d`.
- **A parity test fails**: the app's search and the site's disagree on that request; the message shows the two
  answers, reduced to what the page shows. Fix `whereto-local.js` (or the PHP, if the site is wrong), never the
  reference.
