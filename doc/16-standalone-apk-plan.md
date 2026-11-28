# Implementation plan: a stand-alone Android APK

Written in November 2026 for the agent that will implement it, and revised after two reviews. The idea is described in `14-android-app.md` (section "A stand-alone APK that needs no server"); this document is the how.

## 1. The goal

One command builds an APK that holds the WhereTo site **and its data**. It is installed by hand and works with no server and no network:

```
cd tools/android-app
npm run build                      # ends with build/whereto-<date>.apk
adb install -r build/whereto-<date>.apk
```

The app must look and behave like the website: the four kinds of place, the tag filter with its four operators, the three sort orders, the details of a place, English and Japanese, the dark and light themes, distances from the phone, the lunch suggestion.

Not in scope: a Play Store release, the Trusted Web Activity of `14-android-app.md`, an iPhone app, updating the data without reinstalling.

## 2. Rules of this repository

- **Never commit, push, merge or rebase** unless the user asks in the conversation. Leave the work in the working tree and say it is ready.
- Run commands in the sandbox by default. `podman`, `curl` to localhost and Playwright need to run outside it.
- Run `shellcheck` on every shell script written. Lint changed PHP files with `php -l`.
- Never add the root dotfiles, the two `.env.example` files or `tools/.mcp.json`: they are artefacts of the sandbox.
- Follow the style of the code around: comments explain why, in plain English; every feature has its section in `doc/` and its tests.
- The existing suites must stay green: `php artisan test` and PHPStan in the dev container (see `05-dev-environment.md`), `npx playwright test` in `e2e/`, `node --test` in `tools/freshness/`.

## 3. What the code does today (checked in November 2026)

Read these before changing anything; the plan relies on them.

**The page asks the server for three kinds of fragment**, all in `source/public/js/whereto.js`, all with the same two lines (`fetch(url)` then `response.ok ? response.text() : Promise.reject(response.status)`):

| Function | URL | Answer |
|---|---|---|
| `loadAllResults()` | `<results-url>?position=<lat,lon or null>&orderBy=…` | the results fragment, every place |
| `ajaxResults(op, tags, orderBy)` | `<results-url>?op=…&tags=a,b&orderBy=…&position=…` | the results fragment, filtered |
| `ajaxDetails(url)` | `<details-url>` with the id, e.g. `/restaurants/details/12` | the details fragment; a 404 closes the panel |
| `ajaxSuggestion()` | `<suggestion-url>?position=…` | one sentence, or an empty string outside lunch time |

**URLs.** The script never builds a path: the URLs come from `data-results-url`, `data-details-url`, `data-suggestion-url` and `data-twin-base`, written by Blade. Every internal link and fragment URL of the views goes through `App\Support\LocaleUrl` (`route()`, `twin()`), which calls `route(..., absolute: true)`: **they are absolute**, with the scheme and host of the request. The assets go through `asset()` (seven calls in the views, one of them the `og:image`, which nothing loads), absolute too; the icon sprite is already root-relative (`Tags::sprite()`). Japanese pages live under `/ja/`.

**The search** is `App\Services\PlaceSearch` (subclasses `RestaurantSearch`, `StoreSearch`, `BarSearch`, `KaraokeSearch`):

- `find($tags, $op, $orderBy, $limit)`: only the places of the `open` scope; the tag filter (`whereTags`); then `ORDER BY`: the cost column first when the order is `cost` and the kind has one (restaurants: `lunch_price`), then each rating column descending (restaurants: `score_lunch`, `score_food`, `score_place`; bars: `score`, `score_place`, `score_mingling`; others: `score`), then `id`. Results are cached forever (`Cache::rememberForever`).
- The four operators: `AND` has all the tags (the count of matching tags equals the count asked), `OR` has any, `ANDNOT` has none, `ORNOT` has at least one tag outside the list. No tags: every place. An unknown `op` is `AND`; an unknown `orderBy` is `distance` (`TagOperator::fromRequest`, `OrderBy::fromRequest`). `tags` is split on commas after `", "` became `","` (`PlaceController::formatTags`); the test before the split is PHP's `empty()`, so `tags=0` is "no tags", while `tags=,` is two empty tags.
- `resolvePosition()`: the given `lat, lon`, or Shibuya Station (`35.658789, 139.701262`) when it is missing, `null`, invalid, or more than 2,500 m from the station. What counts as valid is decided by `Position::fromString` and `GeoUtils` (extra parts are ignored, the numbers pass PHP's `is_numeric`): read them, PHP accepts forms that `parseFloat` and `Number` treat differently.
- `withDistances()`: fills `currentDistance` (metres) on each place; when the order is `distance`, drops the places without coordinates and sorts by distance (a stable sort over the order of `find`).
- The distance is `GeoUtils::equirectangularApprox` × 1000, **not Haversine** (a comment in `Position.php` says Haversine; the code is the truth). The row shows `GeoUtils::walkingTime($distance / 1000)`: `ceil(km / 3 * 60)` minutes. **A place without coordinates has no distance and its row shows 0** in the `ratings` and `cost` orders.

**The results fragment** (`resources/views/<kind>s/results-data.blade.php`): a `.resultsCount` sentence (`1 result` or `:count results`, translated), a `.resultTable` of pairs `.resultsCounter` + `.resultRow`, and `#resultsEnd` (`End of :count amazing results`, the number in `<strong>`). Only the walking time in `.distance .oneDigit` depends on the position. The four views are near copies.

**The details fragment and the standalone page of a place do not depend on the position** (no `currentDistance` in those views). They are the same for every visitor.

**The buttons of a place.** In the details views (`<kind>s/details.blade.php`, `details-single.blade.php`) Google Maps, Tabelog and Website are **buttons calling `window.open(url, '_blank')`**, not links, and the Website one is printed even when `official_website` is empty (14 restaurants). "Copy URL" calls `copyStringToClipboard()` with `LocaleUrl::route(...)`, the address of the place on the site.

**The suggestion.** `SuggestionController`: outside 11:30:00 to 14:00:00 in `Asia/Tokyo` (both ends included: `Carbon::isBetween`), an empty string. Otherwise `find(null, And, Ratings, 100)`, sorted by distance **which drops the places without coordinates**, the 35 nearest of what remains, one at random, rendered by `suggestions/suggestion-text.blade.php`. **The button itself is decided when Blade renders**: `suggestions/suggestion.blade.php` prints `#suggestion` and its button only `@if (isLunchTime())`, and nothing otherwise. A page exported at 09:00 has no button, whatever the time on the phone.

**The state of a page is in its address.** `syncState()` writes the tags, operator, order and open place in the query string with `history.replaceState` (no history entry), and `restoreState()` reads them back on load. So reloading the current URL restores the page; but **opening a place adds nothing to the history**: the browser's Back leaves the page. The page's own "BACK" button calls `hideDetails()`.

**The layout** (`layouts/app.blade.php`) loads `css/style.css`, `js/whereto.js` and the sprite with `?v=<mtime>`, prints the translated messages in `window.whereToStrings` and a `csrf-token` meta (a new value for each session, so two exports of the same data would differ), and `whereto.js` registers `/sw.js` at its end. The theme is the one saved in `localStorage`, else the system's (`prefers-color-scheme`). `whereto.js` shows the "location needs HTTPS" message when the page is neither on `https:` nor on the host `localhost`.

**The seeders flush the cache.** `DatabaseSeeder` ends with `Cache::flush()`, on whatever store is the default at that moment.

**The dev environment** (`dev/compose.yaml`): the `app` container mounts `../source` at `/app` and serves on host port 8010 only; `db` is PostgreSQL 18. `dev/laravel.env` has `APP_DEBUG=true` and `DEBUGBAR_ENABLED=true`: Debugbar adds its markup to the HTML responses of the web server. It turns itself off in console commands (`runningInConsole`), so an Artisan export should not carry it; the export checks that anyway. `config/database.php` reads `DB_URL` besides `DB_HOST`, `DB_DATABASE`…, and a cached configuration (`php artisan config:cache`) ignores the environment altogether.

**Ids are not stable between databases.** The seeders start with `DB::table(...)->delete()`, and PostgreSQL sequences keep advancing: the same seeders give different ids in the dev database (seeded several times) and in a fresh one. Ids appear in the links and in the fragment URLs.

## 4. The design, and why

1. **Laravel exports the site as files.** A new Artisan command sends requests to the application from inside (no HTTP server) and writes each answer to disk. Every page and fragment is therefore exactly what the site serves, in both languages, and a change to a view reaches the app with the next export. No view is rewritten.
2. **Root-relative URLs in stand-alone mode.** In that mode the views print `/ja/restaurants`, `/css/style.css`, `/restaurants/details/12`, with no scheme and no host. The same exported files then work at any origin: the Android app's, and the desktop preview's on `127.0.0.1`. The preview and the tests run on **the exact files that go into the APK**.
3. **Details and pages are plain files.** `/restaurants/details/12` is the file `restaurants/details/12/index.html`; the page fetches it as it does today. No JavaScript is added for them.
4. **Only what depends on the visitor is computed in the phone**: the results, the suggestion, and whether the suggestion button shows. A new script, `whereto-local.js`, does it from data the export prepares. It does not re-implement the SQL ordering: the export gives it the ids **already in the two orders the server would return** (by ratings, by cost). The script filters by tags, computes distances, sorts by distance when asked, and assembles the fragment from rows the server rendered.
5. **`whereto.js` gets one seam.** Its four `fetch` calls go through one function. On the website nothing changes. In the app, that function asks `whereto-local.js` first.
6. **The Android app is a single screen holding a WebView**, written by hand (one activity), not Capacitor. The site has many pages at paths without an extension (`/ja/restaurants`), and the app must map each to `…/index.html`: with `androidx.webkit.WebViewAssetLoader` and a custom path handler that is a few lines under our control, and the build needs only the Android SDK and Gradle. The loader serves the files at `https://appassets.androidplatform.net/`, a secure origin, which geolocation requires. [Android's guide to loading in-app content](https://developer.android.com/develop/ui/views/layout/webapps/load-local-content)
7. **One database for the export and for its reference.** The export is made from a database freshly seeded for it, and the answers it is compared with come from the same database, through the same application code, in the same run. The dev database is not involved, neither as a source nor as a reference.
8. **The tool is `tools/android-app/`**, with a small Node part (the build script, the preview server, the tests) and the Android project. The Android build runs in a container, so only podman and `adb` are needed on the host.

## 5. The work, in phases

Each phase ends with every existing test passing. Do them in order. **Phases 1 to 4 build a thin slice end to end** (every page and place, no search yet) and stop at a check on a real phone: the origin, the routing of paths to files and the Back behaviour are the decisions everything else rests on, and they are cheap to validate before the search exists.

**In the slice the lists are empty, and that is expected.** Until Phase 5 nothing answers `/<kind>s/results` and `/suggestions`: those requests get a 404 and the list shows its error message. The tests and the phone check of Phase 4 therefore open places by their address, and do not look at the lists.

### Phase 1: one seam in `whereto.js`

- Add `loadFragment(url)`: returns a promise of the HTML text, rejected with the HTTP status on an error, exactly what the two repeated lines do today. Replace the four call sites with it.
- In `loadFragment`, before `fetch`: `if (window.whereToLocal && window.whereToLocal.handles(url)) return window.whereToLocal.load(url);`. `whereToLocal` does not exist on the website, so nothing changes there.
- Skip the service worker registration when `document.documentElement.dataset.standalone` is set (the app has no use for it and ships no `sw.js`).
- At the end of `syncState()`: `if (window.whereToLocal) window.whereToLocal.stateChanged();`. `syncState()` runs each time a place opens or closes; the app uses this to know whether Back has something to close (Phase 4). Check that every path that opens or closes a place does call it.
- Update the header comment of `whereto.js`.

Done when: `npx playwright test` passes unchanged.

### Phase 2: the stand-alone mode of the views

A config value `whereto.standalone` (new `config/whereto.php`, default `null`, no `.env` entry): only the export command sets it, at run time, to the export date. When it is set:

- **URLs are root-relative.** `LocaleUrl::route()` and `LocaleUrl::twin()` default to `absolute: false`. The seven `asset()` calls of the views go through one small helper (for example `App\Support\PublicUrl::asset()`) that returns `/css/style.css` in this mode and `asset()` otherwise. The canonical, Open Graph, hreflang and JSON-LD URLs stay as they are: nothing loads them.
- **The layout** adds `data-standalone="<export date>"` on `<html>`, loads `js/whereto-local.js` with `defer` **before** `whereto.js`, and prints neither the `<link rel="manifest">` nor the `csrf-token` meta (check first that no script reads it).
- **`source/public/js/whereto-local.js` is created now, as a stub**, because the layout loads it and Phase 4 relies on it: `window.whereToLocal = { handles: () => false, load, stateChanged }`, `window.whereToBack` and the replacement of `window.open` (both specified in Phase 4). Phase 5 fills `handles` and `load`.
- **The buttons of a place.** "Copy URL" is not printed: the app has no address worth sharing (its pages are root-relative, and the site may not be deployed). The Website button is not printed when `official_website` is empty.
- **The suggestion control is always printed, hidden.** `suggestions/suggestion.blade.php` prints `#suggestion` with its button and the `hidden` attribute, whatever the time; `whereto-local.js` shows or hides it (Phase 5). On the website the view is unchanged.
- **A row can be found by its place.** Add `data-place-id="{{ $place->id }}"` to the `.resultRow` of the four `results-data.blade.php` views, in both modes (the variable is `$restaurant`, `$store`, `$bar`, `$karaoke`).
- **Nothing is added to the footer.** The date of the data is the app's version (Phase 4), shown by Android in the app's information.

Tests (feature tests, with the config set in the test): in stand-alone mode a main page has no `http` in its `href`, `src`, `data-*-url`, `data-twin-base` and `onclick` attributes other than the known external links, the home page has the hidden suggestion control at any time of day, and a details fragment has no "Copy URL" button and no Website button for a place without a website; in normal mode none of the additions appears and the URLs are absolute as today.

### Phase 3: the export command, first slice

`php artisan app:export-static {dir}` in `source/app/Console/Commands/ExportStatic.php`. This phase exports pages, details and assets; the search data comes in Phase 5.

**Three pieces, so that each can be tested**:

- `App\Support\ExportGuard`: the check of the database, with no side effect;
- `App\Services\StaticExporter`: writes the files from **whatever database is the default connection**. It never migrates or seeds;
- the command: prepares the run, calls the guard, rebuilds the export database, calls the exporter.

**The order of the command matters. Nothing is written anywhere before step 4.**

1. **Refuse a `dir` that exists and is not empty**, before touching any database.
2. **Prepare the run** (`StaticExporter::prepare()`, also used by the reference command of Phase 6): `config(['cache.default' => 'array', 'session.driver' => 'array', 'whereto.standalone' => <date>])`. This comes before the seeding because `DatabaseSeeder` calls `Cache::flush()` (section 3): with the real store still the default, an export would empty the dev cache. The `array` cache also keeps the export from reading stale search results (the search caches forever) or leaving entries behind; the `array` session keeps thousands of internal requests from writing a session file each. Check that both take effect when set at run time in a console command (no store resolved earlier).
3. **The database guard, the part to get right.** The command re-creates a database, so it must be impossible to aim it at the dev one:
   - it uses a **dedicated connection**, `export`, defined in `config/database.php`: PostgreSQL, the host and credentials of the dev environment's variables, and a **fixed database name `whereto_export`**, with no `url` key. It does not read `DB_DATABASE` or `DB_URL`;
   - it aborts when the configuration is cached (`app()->configurationIsCached()`): a cached configuration may describe another connection than the files do;
   - it runs `select current_database()` **on that connection** and aborts unless the driver is `pgsql` and the answer is `whereto_export`. The check and the migration use the same connection object, so what is checked is what is rebuilt. `ExportGuard` has the decision as a pure function (driver, database name, configuration cached: allowed, or the reason why not) and one method that reads these from a connection. **There is no exception for tests**: the tests never go through the rebuild (see below).
4. `migrate:fresh --force --database=export` with `--seed` (an option `--seeder=<class>` replaces the default seeder), then `export` becomes the default connection for the rest of the run. Check that the seeders and the models use the default connection, so that they follow.
5. `StaticExporter::export($dir)`, described next.

Creating the empty database `whereto_export` once is the tool's job (Phase 4, `CREATE DATABASE` in the `db` container, as `05-dev-environment.md` does for `whereto_test`); the command only says how when it is missing.

The exporter:

1. Create `dir`.
2. Check its preparation was done (stand-alone mode and the `array` stores are set), and fail otherwise.
3. For each language (`LocaleUrl::locales()`), request these paths through the HTTP kernel (`app(Kernel::class)->handle(Request::create($path))`, then `terminate`), require a 200, and write the body to `<dir>/<path>/index.html` (the home page: `<dir>/index.html` and `<dir>/ja/index.html`):
   - the home page, `info/main`, `vip/main`;
   - the four main pages (`restaurants`, `stores`, `bars`, `karaokes`);
   - for **every** place of the four kinds, open or not: the details fragment (`<kind>s/details/<id>`) and the standalone page (`<kind>/details/<id>`).
   Build the paths with the route names, not by hand.
4. Copy `public/css`, `public/js`, `public/fonts`, `public/icons` and `public/favicon.ico` into `<dir>`. **Not** `index.php`, `sw.js` or `manifest.json`.
5. Write `<dir>/app-data/meta.json`: the export date (ISO, with the time) and the number of places per kind.
6. Print one line per kind and the total number of files.

Things to check while writing it:

- The locale must be right for each internal request. The routes set it with the `locale:<code>` middleware precisely because one process may answer several requests (comment in `routes/web.php`).
- `?v=<mtime>` on the assets stays: the app and the preview ignore query strings when they look for a file.
- No exported file contains `phpdebugbar`. Debugbar is off in console commands today; if that check ever fails, disable it in `prepare()`.

Tests. The suite runs on SQLite and on PostgreSQL (`whereto_test`), and the command always rebuilds `whereto_export` from the full seeders, so the tests are split the same way as the code:

- `tests/Unit/ExportGuardTest.php`, no database: the pure decision. `pgsql` + `whereto_export` is allowed; `pgsql` + `whereto`, `pgsql` + `whereto_test`, `sqlite` + anything and a cached configuration are refused.
- `tests/Feature/StaticExporterTest.php`, on the test database of either kind, with a few places made by the test (open, closed, without coordinates, without website): it calls `prepare()` and `export()` directly. The files exist for both languages; no exported file has an internal absolute URL in an attribute that is loaded or followed, nor `phpdebugbar`; a closed place has its details file; no `sw.js`; a key put in the real cache store before the run is still there after, and the store holds nothing new; no session file was written.
- `tests/Feature/ExportStaticCommandTest.php`: a non-empty directory is refused before any query (both databases). **PostgreSQL only** (skipped on SQLite): with the `export` connection's database name overridden to the test database, the command aborts, and the tables and rows of the test database are still there; a cached configuration aborts the same way. The full run of the command (rebuild, seeders) is exercised by the tool, not by PHPUnit.

### Phase 4: the tool, the preview, the Android shell, and the first check on a phone

`tools/android-app/`:

| File | Role |
|---|---|
| `package.json` | scripts `export`, `preview`, `test`, `apk`, `build` (= export, test, apk); no dependency unless one is really needed |
| `export.mjs` | makes `export/`: see below |
| `preview.mjs` | a static server on 127.0.0.1 that serves `export/` **with the rules of the Android path handler**: the app in a desktop browser |
| `lib/resolve.mjs` | the one function that maps a request path to a file or to a 404, shared by the preview and its tests, and mirrored by the Java handler |
| `test/` | `node --test` for `resolve` and, from Phase 5, for the pure functions of `whereto-local.js` |
| `build-apk.sh` | builds the APK in the container |
| `Containerfile` | the Android build image |
| `android/` | the Android project |
| `README.md` | how to build, install, preview, update the data |
| `.gitignore` | `export/`, `export-reference.json`, `build/`, `.keystore/`, `android/app/src/main/assets/site/`, Gradle outputs |

`export.mjs`, run on the host:

1. Check that the dev containers are up (`podman compose -f dev/compose.yaml ps`), with a clear message otherwise.
2. Create the database `whereto_export` in the `db` container if it does not exist.
3. Run `php artisan app:export-static storage/app/static-export` in the `app` container (empty that folder first; `/app` is the mounted `source/`), then move the result to `tools/android-app/export/`. No `DB_*` override is passed: the command's own connection and guard decide.
4. From Phase 6, ask the same container for the reference answers (see there).
5. Print the summary of `meta.json`.

**The path rule**, in `lib/resolve.mjs` and in the Java handler: drop the query string; refuse `..`; if `<path>` is a file, serve it; else if `<path>/index.html` exists, serve it as `text/html`; else 404. MIME types from the extension, including `image/svg+xml` and the fonts.

**The preview's address.** `preview.mjs` listens on `127.0.0.1` and on a port given as argument (`0`: any free port, printed on start). It must be opened as `http://localhost:<port>/`, not `http://127.0.0.1:<port>/`: `whereto.js` accepts geolocation without HTTPS on the host `localhost` only (section 3).

**The Android app**: a minimal project in `tools/android-app/android/`, Java (no Kotlin plugin needed), one activity. **Check the current versions and API details in the official Android documentation before writing: this part of the plan was written from memory and was not verified.** In particular: the Android Gradle Plugin, Gradle, JDK and `compileSdk` versions that go together; the `WebViewAssetLoader` API; and each point marked "verify".

- **Assets**: `build-apk.sh` copies `export/` to `android/app/src/main/assets/site/`.
- **`MainActivity`** extends `androidx.activity.ComponentActivity` (the back API below is AndroidX; the dependencies are `androidx.activity` and `androidx.webkit`). A full-screen `WebView` with JavaScript, DOM storage (the theme and the banner use `localStorage`) and geolocation enabled; it loads `https://appassets.androidplatform.net/`.
- **Serving the files**: a `WebViewAssetLoader` with one custom `PathHandler` on `/` implementing the path rule, called from `WebViewClient.shouldInterceptRequest`.
  - **The handler never returns `null`.** A `null` means "not mine" and the request falls through to the real network. For a path with no file it returns a `WebResourceResponse` with status 404 and the reason phrase `Not Found` (the constructor rejects an empty phrase) and an empty body. The page relies on a real 404 to close the details of an unknown place.
  - `shouldInterceptRequest` answers the same 404 to any request for another host: the app loads nothing from outside.
  - Verify what the handler receives for `/`, `/ja/restaurants` and `/css/style.css?v=1` (the path with or without its leading slash, with or without the query), and write the handler from what is observed.
- **Navigation** (`shouldOverrideUrlLoading`): **return `false` for the app's own origin**, so that its pages load in the WebView; for anything else, start an `ACTION_VIEW` intent, catch `ActivityNotFoundException` (no app for that link: do nothing), and return `true`. This covers the plain links (Facebook, Instagram, the Maps links of the rows). Verify that links with `target="_blank"` reach it with multiple windows left unsupported, which is the default.
- **`window.open`, through a bridge.** The buttons of a place call `window.open(url, '_blank')` (section 3), which a WebView without multiple windows does not handle usefully. The activity exposes one object to the page, `WhereToApp` (`addJavascriptInterface`, methods annotated `@JavascriptInterface`), and `whereto-local.js` replaces `window.open` when that object exists: `WhereToApp.openExternal(url)`. The Java side accepts `http:` and `https:` URLs only, ignores anything else (including an empty string), and starts the same intent. In the desktop preview the object does not exist and `window.open` is left alone. Only the app's own files run in this WebView, so the bridge is not exposed to foreign pages; keep it to the two methods named in this plan. The methods are called on a background thread: post to the main thread.
- **Location**: `ACCESS_FINE_LOCATION` and `ACCESS_COARSE_LOCATION` in the manifest; a `WebChromeClient` whose `onGeolocationPermissionsShowPrompt` asks the Android runtime permission the first time and then answers the callback. Denied: the page already falls back to the station.
- **Theme.** A WebView tells the page `prefers-color-scheme: light` unless the app's own theme follows the system, so with a fixed light theme the app would always start light. Give the activity a day/night theme (a platform `DayNight` theme, or AppCompat's if the `minSdk` requires it; verify which) and check on the phone that a first launch in system dark mode shows the dark theme, and that switching the system theme while the app is open is followed.
- **Back, explicitly.** Opening a place adds no history entry (section 3), so `WebView.goBack()` alone would leave the list page when the user means to close the details. The modern back API (`OnBackPressedCallback` on `getOnBackPressedDispatcher()`) wants to know **before** the gesture whether the app will handle it, so the app cannot ask the page at that moment (`evaluateJavascript` answers later). The page tells the app instead:
  1. `whereToLocal.stateChanged()` (called by `syncState()`, Phase 1) calls `WhereToApp.setPlaceOpen(<boolean>)` when the bridge exists: `true` when `whereToBack()` would close something;
  2. the activity keeps one callback, **enabled when a place is open or the WebView can go back**. Update it from `setPlaceOpen` and from `WebViewClient.doUpdateVisitedHistory` (a new page starts with no place open until it reports otherwise);
  3. when the callback runs: a place is open, `evaluateJavascript("whereToBack()")`; else `goBack()`;
  4. when it is disabled the system does its default: it leaves the app.
  `whereToBack()` is defined in `whereto-local.js`: with a place open it calls `hideDetails()` and returns `true`; otherwise `false`. **Optional, only if it is easy:** on a phone the filter panel and the results are two views of the same page, and Back could also go from the results to the filters. Do it if `whereto.js` already has one function that shows the filters and one test that tells which view is shown, so that `whereToBack()` and `setPlaceOpen` (then better named for "something to go back to") cover it in a few lines, with a browser test. If it needs new state or changes to the layout code, leave it out and say so in the report.
- **Restoration, explicitly.** Two different events:
  - *a configuration change* (rotation, dark mode, font size): the activity is not recreated, the WebView keeps everything. In the manifest: `android:configChanges="orientation|screenSize|smallestScreenSize|screenLayout|keyboard|keyboardHidden|navigation|uiMode|density|fontScale|locale|layoutDirection"`;
  - *the process is killed in the background* and the user comes back: restore the WebView's history with `saveState` / `restoreState` in `onSaveInstanceState` / `onCreate` (verify that it works with pages served by the asset loader). If nothing can be restored, load the last URL, saved on pause. Either way the page rebuilds itself from its address: the tags, operator, order and open place are in the query string (`restoreState()` in `whereto.js`).
  Test it with the developer option "Don't keep activities".
- **No `INTERNET` permission**, if the app works without it (verify: the asset loader intercepts every request of the app's own origin, and the external links are opened by other apps). An app that cannot reach the network is a simple thing to say about privacy.
- **Identity**: application id `io.github.flochrislas.whereto` (decided by the user; changing it later makes a second app), label "WhereTo", the launcher icon from `source/public/icons/`, the status bar colours of the two themes.
- **Version**: `versionName` is the export date; `versionCode` a number that grows with it (for example minutes since a fixed date), so that a newer build always installs over an older one.
- **Signing**: a debug build, signed with a keystore generated once by `build-apk.sh` into `tools/android-app/.keystore/` (ignored by git) and reused. A keystore that changes between builds makes `adb install -r` fail with a signature mismatch. Never commit it.

`Containerfile` and `build-apk.sh`: the image has a JDK, the Android command-line tools, the platform and build-tools the project compiles with, licences accepted at build time, versions pinned. The script checks `export/` exists, copies the assets, runs the Gradle wrapper's `assembleDebug` in the container with the project mounted and a named volume for the Gradle cache, copies the APK to `build/whereto-<date>.apk`, prints its size and the `adb install -r` line. With rootless podman, check that the files written through the mount belong to the user (`--userns=keep-id` is the usual answer). `shellcheck` it.

**Browser tests on the preview** (`e2e/tests/standalone.spec.js`, skipped unless `STANDALONE_DIR` is set):

- the spec starts its own `preview.mjs` on that directory, on port `0`, and reads the port it prints; the tests use `http://localhost:<port>` and never the `BASE_URL` of the other specs. One server per worker is enough;
- it runs in the Chromium projects only (`phone`, `desktop`): the target is Android's WebView, which is Chromium. Skip it in the Safari projects;
- on every test it **fails if the page requests anything outside the preview's origin** (listen to the page's requests; the external links are never followed). The 404 of the results and suggestion requests are expected in this phase;
- `playwright.config.js` grants the geolocation permission and a position to every test. A test that needs it denied must make its own context with `permissions: []`.

In this phase: the home page and a main page load in both languages with their styles and icons; a place opens by `?place=<id>` and from its standalone page, with no "Copy URL" button; `whereToBack()` returns `true` then `false`; `window.open` is the browser's own (no bridge); an unknown details URL answers 404.

**Stop here and check on a real phone, in airplane mode**, with what the slice has: the home page and the restaurants page open in both languages (the list empty, as said above); a place opens by its address (`/restaurants?place=<id>`); the Google Maps button of a place opens Maps; Back closes the details, then goes to the previous page, then leaves the app; the app starts dark when the system is dark. Report the result to the user before going on. If the origin, the 404, the theme or the Back behaviour does not work as planned, fix the plan first.

### Phase 5: the search data and the local script

**More data from the export**, under `<dir>/app-data/`:

- `<kind>.json`, one per kind, the same for every language:
  ```json
  { "byRatings": [12, 7, 31], "byCost": [7, 12, 31],
    "places": { "12": { "tags": ["ramen", "noodles"], "lat": 35.66, "lon": 139.70 } } }
  ```
  `byRatings` is the ids of `find(null, And, Ratings)`, `byCost` those of `find(null, And, Cost)`, in order. **`byCost` is written only for a kind that has a cost column** (today the restaurants); the script uses its presence, not the name of the kind. `places` has the places of `byRatings` only (the open ones); `lat` and `lon` are `null` for a place without coordinates. Tags are the English labels, the values the filter sends.
- `<locale>/<kind>-results.html`: the body of `<kind>s/results?orderBy=ratings`, the fragment with every row.
- `<locale>/suggestions.json`: `{ "<id>": "<sentence html>" }` for the 100 ids of `find(null, And, Ratings, 100)` of the restaurants, each rendered with `view('suggestions.suggestion-text', ['place' => $place])` in that language (`app()->setLocale()` around the direct render).
- `<locale>/strings.json`: `{ "one": "1 result", "many": ":count results", "end": "End of :count amazing results" }`, translated with `__()`.

**`source/public/js/whereto-local.js`**, plain browser JavaScript like `whereto.js` (no build step, no module syntax). The stub of Phase 2 already defines `window.whereToLocal = { handles(url), load(url), stateChanged() }`, `window.whereToBack` and the replacement of `window.open`; this phase writes `handles` and `load`.

- URLs are root-relative now: resolve them with `new URL(url, location.href)` before reading the path and the query.
- `handles(url)`: true when the path, without the language prefix, is `/<kind>s/results` or `/suggestions`. Everything else (the details) goes to `fetch`.
- `load(url)` returns a promise of the HTML text. It takes the language from `document.documentElement.lang` and loads the files of `/app-data/` with `fetch` on first use, then keeps them.

Results, for `op`, `tags`, `orderBy`, `position` read from the query string with the same defaults as the server (section 3):

1. Start from `byCost` when `orderBy` is `cost` **and the kind has a `byCost`**, else from `byRatings`.
2. Read the tags as the server does: an empty value or `0` is no tags; otherwise `", "` becomes `","` and the string is split on commas, empty parts kept. Filter with the four operators. Reproduce the server exactly, including `AND` comparing the count of matching tags with the count asked.
3. Resolve the position (missing, `null`, invalid, or further than 2,500 m from the station: the station), accepting and refusing the same strings as `Position::fromString` (section 3). Compute the distance of each place with the formula of `GeoUtils::equirectangularApprox`, radians computed as PHP's `deg2rad` does (`degrees * Math.PI / 180`). A place without coordinates has **no distance** (`null`, never `NaN`).
4. When `orderBy` is `distance`: drop the places without a distance, sort by distance. `Array.prototype.sort` is stable, as PHP's sort is, so equal distances keep the ratings order.
5. Assemble the fragment: parse `<kind>-results.html` once (a `<template>` element) into a map from `data-place-id` to the pair `.resultsCounter` + `.resultRow`; clone the pairs in order; write the walking time (`Math.ceil(metres / 1000 / 3 * 60)`, and `0` for a place without a distance, as the site shows) in `.distance .oneDigit` of each; set `.resultsCount` from `strings.json` (`one` when the count is 1, else `many` with the number) and `#resultsEnd` (the sentence cut at `:count`, the number in `<strong>`, as the view does).

**The suggestion, decided on the device**:

- *Whether the control shows.* On load, on `pageshow`, when the app comes back to the front (`visibilitychange`), and once a minute while the page is visible: remove or set the `hidden` attribute of `#suggestion` according to the time in `Asia/Tokyo` (from 11:30:00 to 14:00:00, both included, to the second, as the server; use `Intl.DateTimeFormat` with that time zone, not the phone's zone). A suggestion already shown stays until the control is hidden.
- *The suggestion itself* (`/suggestions`): outside lunch time, an empty string, as the server. Otherwise the first 100 ids of the restaurants' `byRatings`, **without the places that have no coordinates**, sorted by distance, the 35 nearest, one at random, its sentence from `suggestions.json`. No candidate: an empty string.

Keep the pure parts (query parsing, tag filter, distance, ordering, lunch time) in small functions that take their data, the clock and the random source as arguments, so that the Node tests can call them (load the file in a `vm` context).

Tests: extend `StaticExporterTest` (the ids of `byRatings` and `byCost`, no `byCost` for a kind without a cost, a closed place in no index, `null` coordinates, the per-language files). Node tests for the pure functions: the edges of lunch time (11:29:59, 11:30:00, 14:00:00, 14:00:01 Tokyo, from a phone in another time zone); the tags `0`, `,` and `a, b`; positions that PHP accepts or refuses (take the cases from the PHP tests of `Position` and `GeoUtils`); a place without coordinates in each order and in the suggestion; an injected random.

### Phase 6: parity with the site, and the browser scenarios

**The reference comes from the export's own database, in the same run.** A second command, `php artisan app:export-reference {requests} {output}`, reads a JSON file of request paths with their query strings and writes, as a JSON file, the body the application answers to each, dispatched through the HTTP kernel like the export. Both are files under `storage/app/` (which the container shares with the host), not standard input and output: warnings printed by PHP must not end up in the JSON. It applies the same guard and makes the same `export` connection the default **without rebuilding it**, so the ids are those of the export. It prepares the run like the export (`array` cache and session: with the real cache it would answer with search results cached from the dev database) **except the stand-alone mode, left off**: this is the site's answer. `export.mjs` calls it right after the export and moves the result to `tools/android-app/export-reference.json`, **next to** `export/`, not inside (it must not go into the APK); the file is ignored by git.

This replaces a comparison with the dev site at `BASE_URL`, which would be wrong twice: its ids differ (section 3), and its pages carry Debugbar.

**What is compared.** Not raw HTML: the local script builds its fragment through the DOM, which serialises differently from Blade's text, and the two modes differ on purpose in their URLs (relative and absolute). For each request, both HTML texts are parsed in the page and reduced to the same structure, which must be equal:

- the count sentence and the end sentence, as text;
- for each row, in order: the place id, the name and type texts, the walking time, the texts of the score cells, the **path** of the standalone link and of the details URL in `onclick` (origin removed), the Maps link.

**The table of requests** (one JSON file in `tools/android-app/test/`, used by both the reference command and the spec): the four kinds; both languages; no tags; one tag; two tags with each of the four operators; a tag no place has (zero results); the three orders; `cost` on a kind without a price; no position, `position=null`, a position near the station, a position 10 km away, a malformed position, a position with a third part (`lat,lon,15`); `tags=0` and `tags=,`; an unknown `op` and `orderBy`. Take the tag labels from the export's data, not from memory. If the seeders have a place without coordinates, the table covers it through the `ratings` order; if they have none, say so in the test, the Node tests cover the case.

**Scenarios through the UI of the preview**, with the request guard of Phase 4 on:

- filter by a tag, open a place, close it with the page's BACK button; `whereToBack()` returns `true` then `false`;
- switch to Japanese from a filtered page with a place open: the filter and the place follow;
- the sort buttons; a place opened by `?place=<id>`; an unknown id closes the panel;
- **the same export at 09:00 and at 12:00 Tokyo time** (Playwright's clock): at 09:00 the home page shows no suggestion control; at 12:00 it shows it, a click gives a sentence that links to one of the exported restaurants, and "Try again" works; with the clock moved from 11:00 to 12:00 while the page stays open, the control appears;
- location denied (a context of its own with `permissions: []`, see Phase 4): the page says distances are from the station.

`npm test` in the tool runs the Node tests, then this spec with `STANDALONE_DIR` set.

### Phase 7: documentation

- `tools/android-app/README.md`: prerequisites (podman, the dev containers, `adb`, USB debugging on the phone), the commands, how to update the data (rebuild and reinstall), the preview, troubleshooting (signature mismatch, "install unknown apps", the export database).
- `04-how-it-works.md`: how the stand-alone app gets its fragments (`loadFragment`, `whereto-local.js`, `app-data/`), the stand-alone mode of the views, the Back and restoration rules.
- Update the section of `14-android-app.md` to what was built; add this document and the tool to `doc/README.md` and to the root `README.md` if it lists the tools.
- `05-dev-environment.md`: the `export` connection and its guard, the new tests and how to run them.
- **No CI for now** (decided by the user): the tool's tests are run by hand, with `npm test` in `tools/android-app/`, and `npm run build` runs them before the APK. Say so in the README.

## 6. Risks and how the plan answers them

| Risk | Answer |
|---|---|
| The export rebuilds the dev database | a dedicated connection with a fixed database name, `current_database()` checked on that same connection, a cached configuration refused, no exception for tests; a PostgreSQL test aims the command at another database and finds it intact |
| The export empties or pollutes the dev cache | the `array` cache and session are set before the guard and the seeding (the seeders flush the cache); a test checks the real store before and after |
| A request of the app reaches the network | the path handler answers 404 itself and never falls through; other hosts get the same 404; no `INTERNET` permission if verified |
| A button of a place does nothing | `window.open` goes through the app's bridge; empty websites and "Copy URL" are not printed; checked on the phone in Phase 4 |
| The app is always in the light theme | a day/night theme for the activity; checked on the phone in Phase 4 |
| The exported pages point at a host that is not there | root-relative URLs in stand-alone mode; a feature test on the attributes; the preview spec fails on any request outside its origin |
| The app's search drifts from the site's | the orders come from the server; the parity test compares with the application's own answers on the same database |
| The comparison fails for reasons that are not differences | ids from one database for both sides; the reference does not read the real cache; structures compared, not raw HTML |
| The suggestion never shows, or shows at the wrong time | the control is exported hidden and shown by the device's clock in Tokyo time; tested at 09:00 and 12:00 on one export |
| Back leaves the list instead of closing a place | the page tells the app when a place is open, and the app's back callback is enabled accordingly; tested through `whereToBack()` and on the phone |
| The page is lost when Android kills the app | history restored, else the last URL reloaded; the page rebuilds from its query string |
| The export reads stale results from the cache | the `array` cache store for the run |
| A float differs between PHP and JavaScript | only the walking time (a `ceil`) and the order of two places at almost the same distance could show it; the parity test would, and then the case is documented, not hidden |
| The APK does not update | `versionCode` from the export date, one stable keystore |
| A WebView too old for the page's JavaScript | set `minSdk` to a version whose WebView is updatable (Android 7 or later) and say in the README that the "Android System WebView" must be current |
| The Android details written from memory are wrong | Phase 4 starts by checking them in the official documentation, and ends with a check on a phone before the rest is built |

## 7. Acceptance, on a real phone

Install the APK, then turn on airplane mode before the first launch.

1. The home page opens; each of the four kinds lists its places.
2. A filter with two tags gives, with each of the four operators, the same places in the same order as the preview on the desktop.
3. The three sort buttons work; the walking times are plausible.
4. A place opens with its details, its tags and their icons. Android Back closes the details, then returns to the home page, then leaves the app.
5. The language switch shows the Japanese pages, with the Japanese names; the theme button works and the choice survives a restart.
6. On a filtered Japanese page with a place open: rotate the phone, nothing is lost. Then, with "Don't keep activities" on, go to the home screen and come back: the same filter and the same place are shown.
7. With location allowed (airplane mode off, GPS on), the page says distances are from the position when in Shibuya, and from the station elsewhere or when location is denied.
8. The Google Maps link of a row and the Google Maps button of a place both open the Maps app; a place without a website has no Website button; no place has a "Copy URL" button.
9. An APK built in the morning shows no suggestion control then, and shows it between 11:30 and 14:00 Tokyo time without a rebuild, also when the app was left open; the suggestion links to a place.
10. A new build made after a seeder change installs over the old one with `adb install -r`, and Android's information on the app shows the new date as its version.
11. With the system in dark mode and the app's data cleared, the app starts in the dark theme.

## 8. Decisions of the user

- The application id is `io.github.flochrislas.whereto`.
- No "Data of <date>" line in the footer.
- The tool's tests do not run in CI at this point.
- "Copy URL" is hidden in the app.
- On a phone, Android's Back going from the results back to the filters is **not required, and welcome if it is easy**: see the Back rules of Phase 4.

## 9. Not verified

Neither the author of this plan nor its reviewers could check these; the implementer must, in Phase 4, and report what was found:

- the versions of the Android Gradle Plugin, Gradle, the JDK and `compileSdk` that go together;
- that the app works without the `INTERNET` permission;
- that `saveState` / `restoreState` restore the history of pages served by the asset loader;
- how links with `target="_blank"` behave with multiple windows unsupported;
- the flow of the location permission prompt (the Android runtime permission, then the page's);
- what path the asset loader hands to a handler registered on `/`;
- that the day/night theme of the activity is what `prefers-color-scheme` follows, and that a change of system theme reaches the page with `uiMode` in `configChanges`.
