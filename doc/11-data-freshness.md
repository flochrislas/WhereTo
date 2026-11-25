# Data freshness: a tool to find the places that closed

Design note, November 2026. Implemented in November 2026 (steps 1 to 7 of section 7, then 9 to 11 of section 8); the first real runs (steps 8 and 12) are pending. Section 9 adds a seeder editor (steps 13 to 17, done November 2026).

## 1. The problem

The places were visited between 2017 and 2019. Shibuya restaurants come and go: a good share of the 305 places (238 restaurants, 48 stores, 15 bars, 4 karaokes) have probably closed, moved or been replaced since. A visitor who walks to a closed restaurant does not come back, and Google does not rank a site that sends people to shuttered doors.

Two things are needed:

1. **A business status on every place**, so that the app only lists places still in business, and tells the truth on the page of a place that is not.
2. **A tool, outside the web app**, run on the dev machine from time to time: it checks every place against Google, shows a report, lets you uncheck the places you disagree with, and writes the result into the seeders. The seeders are then reviewed, committed, pushed and deployed like any other change.

## 2. Constraints that shape the design

- **The seeders are the source of truth, not the database.** `php artisan db:seed` deletes every table and reloads it from `source/database/seeders/*TableSeeder.php` (see `06-deploy-production.md`, section 9). The status therefore lives in the seeders, as one more key in the `create([...])` array of a place; the database only mirrors it. The seeder format stays what it is.
- **The tool is independent of the app.** It lives in its own folder, has its own dependencies, is never deployed, and the app does not know it exists. The only contract between the two is the shape of the seeders.
- **What we know about a place**: its name, its coordinates (every place has them, in the `fillCoordinatesFromString('lat, lon')` line of the seeder), its Google Maps link (302 of 305 places), its Tabelog link (248 places) and its website.
- **The Google Maps link cannot be queried as such.** The `0x...:0x...` pair in the URL is Google's internal feature id, which no public API accepts. Fetching the Maps page means scraping a JavaScript app behind a consent screen: fragile and against Google's terms. The supported way is the Places API.

## 3. How to verify that a place is still open

**Google Places API (New)**, which reports a `businessStatus` for every place: `OPERATIONAL`, `CLOSED_TEMPORARILY` or `CLOSED_PERMANENTLY`.

Two calls are involved, and the first one is only needed once per place:

1. **Find the place**: `POST https://places.googleapis.com/v1/places:searchText` with `textQuery` = our name, `locationBias` = a circle of 50 m around our coordinates, and the field mask `places.id,places.displayName,places.formattedAddress,places.location,places.businessStatus`. The first result within a few metres of our coordinates is the place; the tool keeps its `id` (a Google *place id*, stable, meant to be stored). A result with a different name at the same spot may mean another business is there now; the tool only calls it a replacement when that business is open and our name appears nowhere in the results (section 6), and leaves every doubtful case for a manual look.
2. **Check the place** on later runs: `GET https://places.googleapis.com/v1/places/{id}` with the mask `businessStatus,displayName,formattedAddress` (Place Details). No search, no ambiguity.

Both calls fall in the "Pro" SKU, which has 5,000 free requests per month per SKU (the old 200 dollar credit was replaced by this in March 2025). A run over all 305 places costs nothing, even the first one. Google still requires a billing account with a card on the project. The per-day quotas of the API turned out to be "not adjustable" in the console (November 2026), so there is no hard cap: the protection is the key restricted to this API, a budget alert, and the tool's own throttle (README, section 1).

**Tabelog as a second opinion** (optional, later): a closed restaurant's Tabelog page shows a "閉店" banner. There is no API; it would be a page fetch and a string match, polite (one request per second) and off by default. Useful when Google says "not found" for a restaurant that has a Tabelog page, nothing more.

## 4. What changes in the data

Two new keys in the seeders, and the matching nullable columns on the four place tables:

| Key / column | Type | Meaning |
|---|---|---|
| `business_status` | string, default `OPERATIONAL` | `OPERATIONAL`, `CLOSED_TEMPORARILY`, `CLOSED_PERMANENTLY`, `REPLACED` (another business is at the address) or `RELOCATED` (the place moved to another address; set by hand in the editor, no verdict of `check` writes it) |
| `google_place_id` | string, nullable | Google's id of the place, written by the tool on its first run, so later runs skip the search |

A place that was never checked carries no `business_status` in its seeder (the column defaults to `OPERATIONAL`, so the app treats it as open); `apply` writes `OPERATIONAL` for a place Google confirmed open, so that a blank status reads "not checked" and a written one "confirmed" (changed in November 2026: at first the open places kept no line, and the two could not be told apart in the editor). A closed one reads:

```php
$resto = Restaurant::create([
    'name' => 'Kindenmaru',
    'business_status' => 'CLOSED_PERMANENTLY',
    'google_place_id' => 'ChIJ...',
    'location' => 'Shibuya',
    ...
```

In the app, a `BusinessStatus` enum backs the column (as `OrderBy` and `TagOperator` back the query parameters), with a `label()` for the page ("permanently closed", "temporarily closed", "replaced by another business", "moved to another address") and an `isOpen()`.

Closed places stay in the seeders: they are the record of the visit, their page stays reachable, and the tool must know about them to avoid checking them again.

## 5. What changes in the app

**Lists, tag counts and sitemap: open places only.** A local scope `open()` (`business_status = 'OPERATIONAL'`) on the four models, applied in `PlaceSearch::find()` and in the sitemap. A local scope rather than a global one: two call sites, both explicit, and the standalone page keeps loading every place without an exception to the rule. The tag lists are cached forever and the cache is flushed by the seeder, so a reseed refreshes the counts.

**Standalone page: the place existed, and here is what became of it.** The page of a non-operational place still renders (the visit and the notes are the content), with a banner under the heading: "Permanently closed (checked November 2026)" or the temporary / replaced / relocated wording, the status in the `<title>` ("Kindenmaru, permanently closed"), and `noindex` for the permanently closed, replaced and relocated ones (there is nothing to rank; the temporarily closed keep their indexing). The Google Maps link stays: Maps shows the same status. The one-page fragment (`/restaurants/details/{id}`) is only reached from the list, so it never shows a closed place; no change there.

The "checked" month comes from the seeder's git history, not from a column: `git log -1 --format=%cs -- the seeder` is too coarse per place, so the banner simply says "closed" and the SEO description says the same. If a per-place date turns out to matter, it is one more key, later.

## 6. The tool

**Folder**: `tools/freshness/` at the root of the repository, next to `source/`, `dev/` and `e2e/`. It is not part of the Laravel app, not built into the image, not deployed.

**Language**: Node, like `e2e/`, with no framework: Node's own `fetch` for the API, `node:test` for the tests, and one small dependency for the checkbox prompt (`@inquirer/checkbox`). A PHP script would work as well; Node is chosen because the repository already has a Node tool and a `package.json` convention in `e2e/`, and because the tool must not depend on the app's `vendor/`.

**Input**: the seeders themselves. The tool reads `source/database/seeders/{Restaurants,Stores,Bars,Karaokes}TableSeeder.php` and extracts, per `create([...])` block: the kind, the name, the `business_status` and `google_place_id` if present, and the coordinates from the `fillCoordinatesFromString` line that follows. The seeders are regular enough for this (one key per line, scalar values, a few strings spanning several lines; the code in `/* */` comments is skipped; the reader checks the count of blocks against the count of `::create(` outside comments, rejects a `business_status` other than the four values, and stops on anything unexpected). No database, no PHP.

**Configuration**: `tools/freshness/.env` (gitignored) with `GOOGLE_PLACES_API_KEY`. The tool refuses to run without it.

Two commands, so the API calls and the decision can happen at different times:

### `npm run check` (`check.js`)

1. Reads the places to check from the seeders: the open ones, and the temporarily closed ones (they may have reopened). The permanently closed, replaced and relocated places are not checked again.
2. For each: Place Details when `google_place_id` is known (and Text Search again if Google has retired that id, which the report notes), Text Search otherwise. Throttled (a few requests per second), with `--kind restaurants` and `--limit N` for partial runs.
3. Classifies each place with a pure function (status, Google's name, distance between the two coordinates → verdict):
   - **operational**: `OPERATIONAL`, same name. Names are compared without case, spaces, punctuation, and without a "Shibuya" word and what follows it (Google adds the branch: "HUB Shibuya Center-gai" is "Hub"; "Shibuya109" is a name, not a branch). A partial match also counts: our name as whole words of Google's ("Hakata Furyu" in "Hakata Furyu Center Gai"), Google's as whole words of ours ("Deux Magots" in "Les deux magots"), or a near miss of one letter on names of six letters or more, two on ten or more ("Yasube" / "Yasubee", "Tatsu Ichi" / "TASUICHI"). A partial match only confirms an open place: when it would change our status, the verdict is unsure. When the place has a `name_ja` (section 8) and Google's name is Japanese, the two Japanese names are compared instead (the comparison of section 8), and "confirmed" counts as an exact match. Among the results within 50 m, an exact name is preferred to a partial one, then the nearest; the same name 50 to 200 m away (our coordinates or Google's pin a little off) is still operational, with the place id not trusted (a chain may have another branch there).
   - **closed permanently**: `CLOSED_PERMANENTLY`, same name.
   - **closed temporarily**: `CLOSED_TEMPORARILY`, same name.
   - **reopened**: a place temporarily closed in our seeder that Google now reports `OPERATIONAL`, same name.
   - **replaced**: only when everything points that way, because it hides the place: the result on our spot (within 15 m) has another name, it is `OPERATIONAL`, and our name appears nowhere in the Text Search results (in a building with several tenants, our place is often a few metres from another one). A name in another script ("博多風龍" against "Hakata Furyu") cannot be compared, so it is never a replacement.
   - **not found nearby**: no result with our name within 200 m. The first real run (November 2026) showed that Google does not keep a closed place for long: it answers with the same chain in another ward or another country, or with another business on our spot. Strong evidence the place is gone, not proof: `apply` lists the group unchecked, each line with what Google found instead and our Google Maps link, so a glance at Maps decides; a ticked place becomes `CLOSED_PERMANENTLY`. Before the second search by Japanese name (section 8), this was a third of the places.
   - **unsure**: everything else: a name in another script that cannot be compared, another name on our spot that is closed, our name 50 to 200 m away with a status that would change ours, no status. Reported for a manual look, never applied.
4. Writes `tools/freshness/reports/<YYYY-MM-DD>T<HHMM>-<kinds>.json` (gitignored; `<kinds>` is `all` or the `--kind` value, so the names sort in time order and two runs never overwrite each other) and prints the table: kind, name, Google's name, status, distance, verdict, reason, ordered by verdict then reason so that the places to look at read in blocks. Operational places are counted, not listed, unless `--all`.

An error that would fail for every place (a bad key, the API not enabled, the quota reached: HTTP 400, 401, 403 or 429) stops the run at once, but the results so far are written first, as a report marked `"partial": true` with the error. An error on a single place (the network) makes that place unsure and the run goes on.

### `npm run apply` (`apply.js`)

1. Reads the latest report (or the one given as argument). A partial report is refused unless `--partial` is given.
2. Shows every place whose verdict is a status change in a checkbox prompt, grouped by verdict, with Google's name and address on each line. Closed permanently, closed temporarily and reopened are checked; **replaced and not found nearby are unchecked**, to tick only after a look on Maps. Space toggles, Enter confirms. `--yes` skips the prompt and keeps the checked groups only, never the unchecked ones.
3. For each confirmed place, edits its seeder in place: inserts or updates the `'business_status' => '...',` line right after the `'name'` line of its block (`OPERATIONAL` for an operational or reopened place; only the editor's "(none)" removes the line), and the `'google_place_id' => '...',` line when the id is surely ours: found by its id, or a name that is exactly ours once normalised (operational places included, so the next run is Place Details only). Never for a replaced place: the id found is the new business's. Two places of the same name in a kind (the two "Joysound") are told apart by their coordinates. Every edited file is prepared and checked in memory first (the tool re-reads the new text with its own reader: every block still there, with the expected values), and only then are the files written, so an error leaves no seeder half done. The tool also runs `php -l` on a temporary copy of each file when `php` is on the PATH, and renames the copy over the seeder only when it passes (a failure, or a seeder changed on disk meanwhile, leaves the file as it was); without PHP the check is skipped.
4. Prints `git diff --stat source/database/seeders` and the next steps.

### What you do next

```sh
git diff source/database/seeders            # review
cd dev && podman compose exec app php artisan db:seed   # see it in the local app
git commit -am "Places checked on 2026-11-18: 12 closed, 3 replaced"
git push                                    # then deploy, which reseeds (06-deploy-production.md, section 9)
```

The seeder edit is the one delicate part, and it is also what makes the tool reviewable: every change is a plain git diff of the seeders before anything reaches a database, and `git checkout` undoes it.

### Tests (`node --test`)

- The seeder reader, on a fixture seeder: every block found, keys read, a name with an escaped quote, a place already closed, an unknown status rejected; and once over the four real seeders, read-only.
- The classifier: one case per verdict, from hand-written API responses, plus the traps of "replaced": a building with several tenants, a Japanese-only name, a short chain name with its branch ("Hub").
- The seeder editor, on a fixture: insert, update, idempotent second run, then the reader reads back what was written.
- The commands against a fake `fetch` (no network in tests).

## 7. Order of work

| # | Step | Where | Size |
|---|---|---|---|
| 1 | **Done (November 2026).** `business_status` and `google_place_id` columns, `BusinessStatus` enum, `$fillable`, `open()` scope in the search and the sitemap, tests | `source/` | M |
| 2 | **Done (November 2026).** Banner, title and `noindex` on the standalone page of a non-operational place, tests | `source/` | S |
| 3 | **Done (November 2026).** Docs: `03-data-model.md` (the columns), `02-architecture.md` (the `tools/` folder) | `doc/` | S |
| 4 | **Done (November 2026).** Seeder reader and classifier, with tests | `tools/freshness/` | M |
| 5 | **Done (November 2026).** `check` with the Places API client, the report and the table | `tools/freshness/` | S |
| 6 | **Done (November 2026).** Seeder editor and `apply` with the checkbox prompt, with tests | `tools/freshness/` | M |
| 7 | **Done (November 2026).** `tools/freshness/README.md`: Google Cloud setup (project, Places API (New), key restricted to that API, quota), `.env`, the two commands | `tools/freshness/` | S |
| 8 | First real run, review the report, apply, reseed locally, commit the seeders, deploy | you | |

Steps 1 to 3 and 4 to 7 are independent; the app does not need the tool to exist, and the tool only needs the seeder format.

Later, if useful: the Tabelog second opinion; the replacement's name from the report used to add the new place; `regularOpeningHours` from the same API (an Enterprise field, 1,000 free per month, still enough for a monthly run).

## 8. Japanese names: `names` from OpenStreetMap, checked by the Google pass

Added November 2026, after the Japanese UI (`12-multilingual-ui.md`, step 8). A Japanese place page is only worth indexing once the place has a Japanese name, and three hundred names are too many to type. Two sources, with different rights:

- **OpenStreetMap** may be copied: its data is under the Open Database License (ODbL), which allows storing and publishing it with the attribution "© OpenStreetMap contributors" where it is used (the info page gets that line; the seeders being public in this repository satisfies the share-alike clause for the names taken). Shibuya is well mapped: most restaurants and shops carry a Japanese `name`, many an English `name:en`.
- **Google Places** may only be consulted: its terms forbid storing anything but the place id (section 3 and `12-multilingual-ui.md`). The names pass therefore never writes, prints or keeps the Japanese name it asks Google for. It only compares it with ours and reports a verdict. (The status pass shows Google's name in its report as before, for the comparison with ours; that name is Japanese only when Google has no English one.)

**Off by default.** The status pass stays what it is: `npm run check` neither reads OpenStreetMap nor asks Google for a Japanese name unless it is given the `--names` flag, and the Japanese names are only ever written by the separate `npm run names` command. A routine status run costs and does exactly what it did before this section.

### The flow

```sh
npm run names                    # 1. one request to OpenStreetMap, a checkbox prompt, name_ja written in the seeders
npm run check && npm run apply   # 2. the status pass; a place not found under its English name is searched under its name_ja too
npm run check -- --names         # 3. the status pass again, this time also checking every name_ja against Google
npm run edit                     # 4. fix by hand what the report disputes or could not fill, on the page with the report loaded (section 9)
cd dev && podman compose exec app php artisan db:seed ; git commit ; git push ; deploy
```

The Japanese name comes first because it helps the status pass: Google often indexes a place under its Japanese name only, and our English transcription does not find it. With a `name_ja` in the seeder, `check` runs a second Text Search with it when the English one finds no match within 200 m, and compares Japanese names when Google's is Japanese; this is a search with our own data, on by default, not the Google verification of step 3, which stays behind `--names`. Steps 1 and 3 can be repeated: `names` only fills the places without a status and without `name_ja` (the open and temporarily closed ones, whose English page is indexable), and `check --names` verifies whichever names exist. The seed is not pushed between 1 and 3: a `name_ja` makes the Japanese page of the place indexable, so Google's check comes before production.

### What changes in the data

| Key / column | Type | Meaning |
|---|---|---|
| `name_ja` | string, nullable | The Japanese name of the place, shown first on the `/ja` pages (heading, list rows, title). Absent for a place that has none yet, whose Japanese page stays `noindex` (`Place::hasContentIn('ja')` becomes "`name_ja` is set") |

In the seeder, one more line right after `'name'`:

```php
$resto = Restaurant::create([
    'name' => 'Hakata Furyu',
    'name_ja' => '博多風龍',
    'business_status' => 'CLOSED_TEMPORARILY',
    ...
```

Written by the tool, edited by hand when the tool is wrong: the diff of the seeders stays the review. The lines the tool writes keep one order in a block: `name`, `name_ja`, `business_status`, `google_place_id`.

### `npm run names` (`names.js`)

1. **One query to the Overpass API** (`https://overpass-api.de/api/interpreter`, no key, no account; another server can be set in `.env` as `OVERPASS_URL`): every node and way with an `amenity`, `shop`, `leisure` or `craft` tag in the bounding box of our places plus 100 m, the unnamed ones dropped on our side (a `name` filter on the server, one of its biggest indexes, and the relations made the query time out on every server in November 2026; this form answers in under a minute). That is one request of a few thousand elements for the whole of Shibuya, instead of three hundred `around` queries, and it is polite to a shared free server. The answer is saved in `tools/freshness/cache/overpass-<date>.json` (gitignored) and reused for a day, so re-running the command costs nothing.
2. **Matching**, a pure function per place, among the elements within 50 m (ways and relations by their centre):
   - **matched**: an element whose `name:en` (or whose `name` when it contains Latin letters, as "JOYSOUND 渋谷店") matches our name with the strict rules of the status pass (exact, or our name as whole words of theirs, spelled the same: the near misses of section 6 would let "Cafe" match "Suzu Cafe" and write a wrong name). An exact match is preferred to a partial one, then the nearest. When the matching element has no Japanese name, the place is **none**: we know which one it is, and it is not the neighbour.
   - **nearest**: no English name matches, but one element within 15 m has a Japanese name and no other named element is within 15 m. The right one in a street of single shops, a guess in a building with several tenants: it is written, and flagged for the Google check. An element that two of our places would both take as nearest is given to neither (one of them is wrong, and the pages would carry the same name).
   - **none**: everything else (nothing named within 50 m, two candidates on the spot, only Latin names). Listed for a manual look.
   - The Japanese name is `name:ja` when it contains kana or kanji, else `name` when it does; an element without one is skipped. It is kept as written (branch suffixes such as 渋谷店 are part of the name on the sign).
3. Writes `reports/<date>T<time>-names.json` (the naming of `check`; `apply` ignores these reports) and prints the table: kind, name, Japanese name, source element, distance, match. Then the checkbox prompt as `apply`: matched names checked, nearest ones unchecked (the first real run, November 2026, showed the guess wrong far more often than right in Shibuya: a dentist, a bank, a parking lot for a restaurant; 43 matched, 23 nearest of which 2 right), `--yes` keeps the matched ones only. For each confirmed place, inserts the `'name_ja' => '...',` line after the `'name'` line of its block, with the same in-memory check, write and `php -l` as `apply`. `--kind` and `--limit` as for `check`; `--dry-run` prints the table and writes neither report nor seeder (the Overpass cache is still written, so a real run right after costs no request).

### `npm run names -- --tabelog`: the names from the Tabelog pages

Added on 2026-11-25: OpenStreetMap named 43 places, and about 200 restaurants and bars were left without `name_ja`, nearly all of them with a `tabelog_link`. The Tabelog page of a place carries its Japanese name, so `--tabelog` replaces step 1 and 2 above: one page per place (the Japanese page, whatever the language of the link), one a second, the name taken from the page's structured data.

The name on Tabelog ends with the branch (博多風龍 渋谷店, バルッチャ道玄坂), which we do not want in `name_ja`: the site shows one place, not a chain. The branch is dropped by `withoutBranch` (`lib/tabelog.js`), a pure function of the name and of the place names the page gives (address, title, breadcrumb), so nothing about Shibuya is written in the code. Its rules and its known misses are in `tools/freshness/README.md`, section 6. A name left without kana or kanji is its own match kind, `latin`, unticked in the prompt.

The rest is unchanged: the same report shape (so the editor loads it), the same prompt, the same safe write, and `check --names` afterwards. `--closed` (both sources) also names the permanently closed, replaced and relocated places, whose pages are not indexed but still show a name. Only the name and the place names of a page are kept (`cache/tabelog-<date>.json`), as for the status pass of section 10.

### What changes in `npm run check`, with `--names` only

Without the flag, nothing: no extra request, no new column, the report as before. With `--names`, for every place that has a `name_ja` and whose Google result is within 50 m, the status pass makes one more Place Details request with `languageCode=ja` and the mask `displayName` (the same Pro SKU: a full run becomes about 600 requests, still well inside the 5,000 free ones). The two Japanese names are compared after folding the width (ＡＢＣ to ABC, half-width katakana to full), dropping spaces, brackets, punctuation and middle dots, and dropping a trailing branch (a last word ending in 店, except the usual name endings 商店, 飯店, 酒店, 書店, 売店, or starting with 渋谷) from both sides:

- **confirmed**: equal after that;
- **disputed**: different. When Google's name has no kana or kanji (it only knows an English name, as "JOYSOUND"), the two are compared as English names instead, with the normalisation of the status pass;
- **unchecked**: the place was not found or has no Google name.

The report gets a `nameJa` field with that verdict (`null` for a place without `name_ja`, absent without the flag) and a `Japanese name` column in the table; the disputed places are listed together at the end, and in the report, with their Google Maps link, which is where the hand check happens. Google's Japanese name itself is never written anywhere, not even in the local report. A disputed name does not change the status verdict: those are two separate questions.

### What changes in the app

`name_ja` column (nullable, in a new migration, the four tables), in `$fillable`; `hasContentIn('ja')` reads it, so the Japanese page of a named place becomes indexable, gets its `hreflang` pair and enters the sitemap, as designed. The `/ja` pages show `name_ja` first and the English name after it in the list rows, the heading and the title; the English pages do not change. The structured data of the Japanese page gets the Japanese `name`. The info page carries "© OpenStreetMap contributors" with a link to the licence, in both languages. `03-data-model.md` gets the column and the attribution obligation.

### Tests

- The matcher, from a hand-written Overpass answer: exact and partial English match, nearest with a single candidate, two candidates on the spot (none), a Latin-only name (skipped), `name:ja` preferred to `name`, a way with a centre.
- The Japanese comparison: identical, width and spaces, branch suffix (ラーメン二郎 渋谷店), different names, Google's name in Latin only.
- The seeder editor: insert `name_ja` after `name`, idempotent, read back.
- `names` and `check --names` against a fake `fetch`, including the Overpass cache; and `check` without the flag makes no Japanese request and writes no `nameJa` field.
- App: a place with `name_ja` on `/ja` (heading, title, indexable, in the sitemap, in the JSON-LD), one without (unchanged).

### Order of work

| # | Step | Where | Size |
|---|---|---|---|
| 9 | **Done (November 2026).** `name_ja` column, `hasContentIn`, display on the `/ja` pages, attribution on the info page, tests, docs | `source/`, `doc/` | S/M |
| 10 | **Done (November 2026).** Overpass client with its cache, matcher, `names` command, seeder edit of `name_ja`, tests, README section | `tools/freshness/` | M |
| 11 | **Done (November 2026).** Japanese name comparison in `check --names` (second Details call, verdict, report and table), tests | `tools/freshness/` | S |
| 12 | The flow above, by hand: status pass, `names`, `check --names`, manual fixes, reseed, commit, deploy | you | |

## 9. The seeder editor: `npm run edit`

Added November 2026. Editing three hundred places in four PHP files is slow: finding a block, keeping the quotes right, checking the result. And the reports of `check` and `names` are read in a terminal, away from the data they talk about. The editor is a local page that lists the places, filters them, edits any field of one in place, and shows the results of a report next to each row, with one-click buttons for what the report suggests. It is the hand part of the flow in section 8, made fast.

**What it is not.** Not a part of the Laravel app (the app's admin writes to the database, which the next reseed wipes; the seeders are the source of truth). Not a database: it writes the seeder files, one line at a time, through the same reader and line editor as `apply` and `names`. Not deployed: it listens on the laptop only. It does not add or delete places: a block is still written by hand in the seeder.

### The command

```sh
cd tools/freshness && npm run edit        # prints http://127.0.0.1:8020/ ; Ctrl+C stops it
```

`edit.js` starts a server with Node's `http` module (no new dependency), bound to `127.0.0.1` only, port `8020` (`--port` to change it). It serves one page, `editor/index.html` (plain HTML and JavaScript, no framework, no build, as the app's own client), and a small JSON API. Nothing is cached: every request reads the seeders again, so an edit made in a text editor at the same time is seen by the next request.

### The places and their id

The reader of `lib/seeders.js` reads every key of a block but only exposes five. It gets a second, full shape, `readSeederFull`: every key with its typed value (string, number, boolean, null; a multi-line string stays one value with newlines), the tags of the `attachTags([...])` line, the coordinates, and the line of the block. The short shape used by `check`, `apply` and `names` does not change.

**Id.** The editor identifies a place by its kind and its position among the blocks of its seeder, zero-based: `restaurants/12`. The editor never adds or removes blocks, so the position does not move during a session, whereas the line number moves when a line is inserted, and the name and the coordinates are precisely what the editor lets you change. Every edit targets an id; the server resolves it by reading the file again at that moment. Two places with the same name (the two Joysound karaokes) have two ids.

**Revision.** The position only holds while nobody adds, removes or comments out a block in a text editor meanwhile; then the same index would silently point at another place. So every answer that lists places carries, per seeder, a revision: the SHA-1 of the file's text. A `PATCH` must send the revision it was built from; the server reads the file, compares, and answers `409 Conflict` with "the seeder changed on disk, reload the page" when it differs, writing nothing. A successful `PATCH` answers the place and the revision of the text it wrote (not of the file as it is a moment later: an outside save right after the write must make the next edit a 409). While the page reloads, a save from the still-visible old form is refused unsent, and the new places and revisions are installed together just before the rows are redrawn; a reload that brings a changed seeder also drops the saves queued before it (their block index belongs to the old text), and the answer of a save that was in flight across a reload updates nothing; two reloads at once are one reload, and saves stay refused until its rows are redrawn; when the selected report cannot be fetched during a reload, the rows are redrawn without it and the error is shown, and every reload keeps the open form on its place, found again by name and coordinates. The page queues its saves per seeder so that each one uses the revision returned by the previous one (two quick blurs on two fields do not race each other). A 409 empties that queue: the edits still waiting are never sent, since their block index may be wrong now; the page reloads, lists the lost edits in a notice ("Hakata Furyu: location = First") and marks their fields, and the open form follows its place, found again by name and coordinates, not its position. Each operation of the server reads a seeder once: the places and the revision of an answer come from the same text, and an edit is built from the very text whose revision was checked.

### Editing

A field saves when you leave it (blur, or Enter in a single-line field); the row then shows "saved", or the server's error in red and the old value back. No save button: the review is `git diff`, as for the other commands, and the page has a "Diff" button that shows the current diff of the seeders without leaving it. Undo is a hand edit, or reverting the hunk in a diff tool (`git checkout -p source/database/seeders` asks hunk by hunk): the seeders usually carry other uncommitted changes, from `apply` or `names`, that a whole-file checkout would discard too.

The line editor of `lib/seeders.js` generalises from its three known keys to any key:

- an existing key rewrites its line (or its lines, for a multi-line string), keeping the indentation;
- a new key is inserted before the closing `]);`, except `name_ja`, `business_status` and `google_place_id`, which keep their places after `name` (section 8);
- the type of a key is decided by the key, not by the current value, because a value may be `null` (`official_website`, `visit_date` are in some blocks): `lib/editor.js` holds, per kind, the known keys with their type: text, long text (`points`, `experience`, `description`), number (`lunch_price`, the scores; `lunch_price` nullable, `0` distinct from `null`), boolean (`visited`), date and time (`visit_date`, `'2017-09-05 13:00:00'`, kept as the text it is, no time zone conversion, no time dropped), URL (`google_maps_link`, `tabelog_link`, `official_website`), status (`business_status`), id (`google_place_id`). An unknown key takes the type of its value, or text. The page takes that table from the server (`GET /api/places` carries it), so the two sides cannot drift, and offers the matching input; an emptied input writes `null` for a nullable key (dates, URLs, `name_ja`, `google_place_id`, `lunch_price`) and `''` for the other text keys; an emptied or unparseable number box for a key that cannot be null is refused on the page, nothing sent. `name_ja` and `google_place_id` cleared remove their line instead (absent is their null, section 8); `business_status` is a selector whose `OPERATIONAL` deletes the line, as today. The "add a key" list of a row always offers `name_ja`, `business_status` and `google_place_id`, plus the other known keys of the kind the block lacks;
- the tags rewrite the `attachTags([...])` line from a comma-separated list (trimmed, case kept as `JOYSOUND` shows, empty ones dropped; the line is added after the block when the place had none);
- the coordinates rewrite the `fillCoordinatesFromString('lat, lon')` line (a pasted "lat, lon, zoom" keeps only the first two numbers; both must be numbers, an empty part is refused on the page, an explicit 0 is a number);
- `name` must stay a non-empty string; `business_status` one of the known values; a Google id letters, digits, `-` and `_`; a string must not contain control characters other than newlines. Anything else is refused with a message and nothing is written.

Every write goes through `planSeederEdits`: the file is edited in memory, read back with the reader, and compared with what was expected before the disk is touched. Then, unlike `apply` today (which writes first and lints after), the editor writes the new text to a sibling temporary file, runs `php -l` on it when `php` is installed, and only then renames it over the seeder; a lint failure deletes the temporary file and leaves the original byte for byte as it was. A text editor can save while `php -l` runs: the writer reads the seeder once, builds the new text from exactly those bytes, and just before the rename reads the seeder again and compares it with them; a difference is a 409 too, nothing is replaced, the outside edit is kept. The reader reads each `'key' => value` line on its own and accepts a missing comma after the last key, which is how a block whose last line has no comma could read back fine and still be broken PHP: the writer, when it inserts a line in such a block, adds the missing comma on the last key's line (a comment may sit between it and the closing). `apply` and `names` move to the same temporary-file write (`writePlans` in `lib/cli.js`).

### The page

- **Filter bar:** a text box searched in the name, the Japanese name, the location, the type and the tags; a kind selector (all, restaurants, stores, bars, karaokes); a status selector (all, open, temporarily closed, permanently closed, replaced, relocated). When a report is loaded, a verdict selector too. The count of rows shown is displayed.
- **Rows:** one line per place: kind, name, Japanese name, location, type, status, tags, a Maps link (our `google_maps_link`, or a search at our coordinates). Clicking a row opens it as a form with every key of the block in the seeder's order, plus the tags and the coordinates. Fields are editable right there; a key the block does not have yet can be added from a list of the keys the other blocks of that kind use.
- **Report columns** (see below) between the name and the fields, and the one-click buttons.
- No login, no CSRF: the server only listens on the loopback address. The page refuses to load from another origin (the API checks the `Host` header and the `Origin` of state-changing requests), and refuses to be framed (`X-Frame-Options: DENY`), so a web page open in the same browser can neither edit the seeders nor steer clicks onto the buttons.

### Reports

A selector lists the files of `reports/`, newest first, `check` and `names` reports alike (the file name says which). Loading one maps each result to a place once, by kind, name and coordinates, all three (a same-name place at another spot, or one moved since the report, gets no result and no button), and attaches it to the place's id; the mapping is kept in the page, so a renamed place keeps its report columns. A place without a result shows "not in the report"; a result without a place (renamed since) is counted at the top.

For a `check` report, each row gets: the verdict as a coloured badge (operational green, closed red, replaced and not found orange, unsure grey), the reason, what Google found (its name, address, status, distance), and two links: our place on Maps and Google's place (by its id). When the report was made with `--names`, a fifth column shows the Japanese name verdict (confirmed, disputed, unchecked), disputed in orange, and it is a filter too: an operational place with a disputed name is precisely a row to open and fix. Buttons: **Closed** (writes `business_status` `CLOSED_PERMANENTLY`), **Closed for now** (`CLOSED_TEMPORARILY`, offered first when the verdict is `closed_temporarily`), **Replaced**, **Relocated** (`RELOCATED`: the place moved to another address), **Open** (removes the status), **Keep id** (writes `google_place_id` from the result, for a place found under a name the matcher did not pair; the next `check` then looks it up by id). Google's name and address are shown to be read while you type, never written by a button: Google's terms (section 3). A button carries only the name of its action; the value it writes is taken from the report held in the page, not from the HTML.

For a `names` report, each row gets the OpenStreetMap candidate (its Japanese name, its English name, the distance, the match kind and the reason, a link to the element) and one button: **Use this name** (writes `name_ja`), shown only when the result carries a name: a `none` result has no candidate, and the button would otherwise erase a name typed by hand after the report was loaded. The same rule for **Keep id**: shown only when Google's result has an id. OpenStreetMap's data may be copied (section 8).

### Files

- `tools/freshness/edit.js`: the server, the routes (`GET /` the page, `GET /api/places`, `PATCH /api/places/:kind/:index` with `{ revision, fields?, tags?, lat?, lon? }` (409 on a stale revision), `GET /api/reports`, `GET /api/reports/:file`, `GET /api/diff`), `--port`.
- `tools/freshness/editor/index.html`: the page.
- `tools/freshness/lib/seeders.js`: `readSeederFull`, the general line editor, the tags and coordinates lines.
- `tools/freshness/lib/editor.js`: pure functions: the report mapping (results to ids), the validation of an edit, the known keys per kind with their types.
- `tools/freshness/lib/cli.js`: `writePlans` lints a temporary file before renaming it over the seeder (shared with `apply` and `names`).
- `package.json`: the `edit` script. README: a section. This note: this section.

### Tests (`node --test`)

On a copy of a small seeder in a temporary folder:

- the full reader: every key with its type, a multi-line string, the tags, the coordinates, a block in a comment skipped, two places with the same name get two ids;
- the editor: rewrite a string, a number, a boolean; add a key at the end; the three known keys keep their places; remove `name_ja` and `google_place_id`; set and remove the status; a multi-line string written back and read back; tags rewritten, tags added to a place without; coordinates rewritten from "lat, lon" and from "lat, lon, zoom"; a refused value leaves the file untouched; idempotent (the same edit twice changes nothing);
- the report mapping: a `check` and a `names` report against a list of places, a result with no place, two places with the same name told apart by coordinates;
- the routes, with the server started on a free port in the test: the page is served, the places are listed, an edit is written and read back, a bad edit answers 400 with the message and writes nothing, a request from another origin is refused;
- the revision: a `PATCH` with a stale revision answers 409 and writes nothing; a block inserted in the file between the load of a report and the press of its button is detected (409), and neither place changes;
- the write: a `php -l` failure (fake `run`) leaves the original byte for byte, and a key inserted after a last line without a comma gives that line its comma;
- the types: `official_website` `null` to a URL and back to `null`; a date cleared; a number refused when not a number;
- **regression:** load a report, rename a place, change its latitude, check that both edits land in the same block, and that a report button still writes to that block.

The page gets three Playwright tests in `e2e/tests/editor.spec.js`, which start the server on a copy of the fixtures (the app does not need to run): a field saved on blur with no `reports/` folder yet; two queued edits with a block inserted on disk in between (the first answers 409, the second is never sent, nothing is written); a report button whose value holds an apostrophe. The rest is checked by hand (filter, open a row, load today's report, see the diff).

### Order of work

| # | Step | Where | Size |
|---|---|---|---|
| 13 | **Done (November 2026).** Full reader and general line editor in `lib/seeders.js`, tags and coordinates, the trailing comma, the lint-then-rename write, tests | `tools/freshness/lib/` | M |
| 14 | **Done (November 2026).** `lib/editor.js`: report mapping, key types and validation, tests | `tools/freshness/lib/` | S |
| 15 | **Done (November 2026).** `edit.js`: server, routes, revisions, tests including the regression | `tools/freshness/` | M |
| 16 | **Done (November 2026).** `editor/index.html`: the page, its Playwright tests, a hand check | `tools/freshness/editor/`, `e2e/tests/` | M |
| 17 | **Done (November 2026).** README section, `package.json` script | `tools/freshness/` | S |

## 10. A second opinion from Tabelog: `npm run tabelog`

Added November 2026. The first real run of `check` (section 7) left 127 places unsure or not found nearby, a third of the restaurants: Google does not keep a closed place for long, and answers with the same chain elsewhere or another business on the spot. Each of those needed a look on Maps. Tabelog, the Japanese restaurant guide, keeps the page of a closed restaurant and marks it: 【閉店】 before the name in the title, and a warning block under the name (class `rst-status-alert`, "このお店は現在閉店しております。"); 【休業中】 for a temporary closure, 【移転】 for a move. Most restaurants and bars of the seeders carry a `tabelog_link` (107 of the 127 flagged places). So one page read settles most of what Google could not.

### The command

```sh
cd tools/freshness && npm run tabelog [-- reports/2026-11-19T2001-all.json]
```

`tabelog.js` reads a check report (the newest by default, a names report is refused), takes the rows whose verdict is `unsure` or `not_found_nearby`, finds each place's `tabelog_link` in the seeders (matched by kind, name and coordinates, like the editor's report mapping), and fetches the page, one a second, with the tool's user agent. `lib/tabelog.js` reads the status from the title bracket and the alert block only (the "report a closure" button on every page says the same words and is ignored), in Japanese and in English (the `/en/` pages): `closed`, `temporarily_closed`, `moved`, else `open`; a non-200 answer or a network error gives `error` for that place and the run goes on.

### What changes in the report

A new file, `reports/<now>-<kinds>+tabelog.json`, next to the input, which stays as it was. The header keeps the input's `date` (`apply` prints it) and adds `tabelog: { from, checkedAt, checked }`. The rows are the input's, in order, each with a `tabelog` field: `null` when not read, else `{ link, status }` (`error` adds the message). A `closed` page changes the verdict to `closed_permanently`, a `temporarily_closed` page to `closed_temporarily`, and the reason becomes "Tabelog says permanently closed; Google: <Google's reason>". `moved`, `open` and `error` leave the verdict: Google's doubt stands, for a look by hand (a moved restaurant is a closed one for the app, but the editor decides that).

Nothing else changes: `apply` reads the new report as any check report (it is the newest, so it is the default; the closed places are ticked), the editor lists it and shows the Tabelog link and status next to the Google links. No Google id is written for a place closed by Tabelog (`idTrusted` is false on those rows). Only the status is kept from Tabelog, never a name, a rating or an address.

### Tests (`node --test`)

`test/tabelog.test.js`: the status from two saved pages (`test/fixtures/tabelog-closed.html`, `tabelog-open.html`, trimmed to the title and the alert block as Tabelog writes them) and from their variants (temporary closure, move, English); the fetch (user agent, non-200); the command end to end on a copy of the fixture seeders with links added, a hand-made check report and a fake fetch: which pages are read and in what order, the new file name, the untouched input, the changed and unchanged rows, the summary; the default report and the refusal of a names report; a row that is not in the seeders any more.

### Order of work

| # | Step | Where | Size |
|---|---|---|---|
| 18 | **Done (November 2026).** `lib/tabelog.js`, `tabelog.js`, tests, README section, the link in the editor | `tools/freshness/` | S |
