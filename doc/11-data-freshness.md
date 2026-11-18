# Data freshness: a tool to find the places that closed

Design note, November 2026. Implemented in November 2026 (steps 1 to 7 of section 7); the first real run is pending.

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

Both calls fall in the "Pro" SKU, which has 5,000 free requests per month per SKU (the old 200 dollar credit was replaced by this in March 2025). A run over all 305 places costs nothing, even the first one. Google still requires a billing account with a card on the project; set a hard quota on the key in the Google Cloud console as well, it costs nothing and prevents surprises.

**Tabelog as a second opinion** (optional, later): a closed restaurant's Tabelog page shows a "閉店" banner. There is no API; it would be a page fetch and a string match, polite (one request per second) and off by default. Useful when Google says "not found" for a restaurant that has a Tabelog page, nothing more.

## 4. What changes in the data

Two new keys in the seeders, and the matching nullable columns on the four place tables:

| Key / column | Type | Meaning |
|---|---|---|
| `business_status` | string, default `OPERATIONAL` | `OPERATIONAL`, `CLOSED_TEMPORARILY`, `CLOSED_PERMANENTLY` or `REPLACED` (another business is at the address) |
| `google_place_id` | string, nullable | Google's id of the place, written by the tool on its first run, so later runs skip the search |

An open place does not carry `business_status` in its seeder (the column defaults to `OPERATIONAL`), so the seeders only change for the places that have a story. A closed one reads:

```php
$resto = Restaurant::create([
    'name' => 'Kindenmaru',
    'business_status' => 'CLOSED_PERMANENTLY',
    'google_place_id' => 'ChIJ...',
    'location' => 'Shibuya',
    ...
```

In the app, a `BusinessStatus` enum backs the column (as `OrderBy` and `TagOperator` back the query parameters), with a `label()` for the page ("permanently closed", "temporarily closed", "replaced by another business") and an `isOpen()`.

Closed places stay in the seeders: they are the record of the visit, their page stays reachable, and the tool must know about them to avoid checking them again.

## 5. What changes in the app

**Lists, tag counts and sitemap: open places only.** A local scope `open()` (`business_status = 'OPERATIONAL'`) on the four models, applied in `PlaceSearch::find()` and in the sitemap. A local scope rather than a global one: two call sites, both explicit, and the standalone page keeps loading every place without an exception to the rule. The tag lists are cached forever and the cache is flushed by the seeder, so a reseed refreshes the counts.

**Standalone page: the place existed, and here is what became of it.** The page of a non-operational place still renders (the visit and the notes are the content), with a banner under the heading: "Permanently closed (checked November 2026)" or the temporary / replaced wording, the status in the `<title>` ("Kindenmaru, permanently closed"), and `noindex` for the permanently closed and replaced ones (there is nothing to rank; the temporarily closed keep their indexing). The Google Maps link stays: Maps shows the same status. The one-page fragment (`/restaurants/details/{id}`) is only reached from the list, so it never shows a closed place; no change there.

The "checked" month comes from the seeder's git history, not from a column: `git log -1 --format=%cs -- the seeder` is too coarse per place, so the banner simply says "closed" and the SEO description says the same. If a per-place date turns out to matter, it is one more key, later.

## 6. The tool

**Folder**: `tools/freshness/` at the root of the repository, next to `source/`, `dev/` and `e2e/`. It is not part of the Laravel app, not built into the image, not deployed.

**Language**: Node, like `e2e/`, with no framework: Node's own `fetch` for the API, `node:test` for the tests, and one small dependency for the checkbox prompt (`@inquirer/checkbox`). A PHP script would work as well; Node is chosen because the repository already has a Node tool and a `package.json` convention in `e2e/`, and because the tool must not depend on the app's `vendor/`.

**Input**: the seeders themselves. The tool reads `source/database/seeders/{Restaurants,Stores,Bars,Karaokes}TableSeeder.php` and extracts, per `create([...])` block: the kind, the name, the `business_status` and `google_place_id` if present, and the coordinates from the `fillCoordinatesFromString` line that follows. The seeders are regular enough for this (one key per line, scalar values, a few strings spanning several lines; the code in `/* */` comments is skipped; the reader checks the count of blocks against the count of `::create(` outside comments, rejects a `business_status` other than the four values, and stops on anything unexpected). No database, no PHP.

**Configuration**: `tools/freshness/.env` (gitignored) with `GOOGLE_PLACES_API_KEY`. The tool refuses to run without it.

Two commands, so the API calls and the decision can happen at different times:

### `npm run check` (`check.js`)

1. Reads the places to check from the seeders: the open ones, and the temporarily closed ones (they may have reopened). The permanently closed and replaced places are not checked again.
2. For each: Place Details when `google_place_id` is known (and Text Search again if Google has retired that id, which the report notes), Text Search otherwise. Throttled (a few requests per second), with `--kind restaurants` and `--limit N` for partial runs.
3. Classifies each place with a pure function (status, Google's name, distance between the two coordinates → verdict):
   - **operational**: `OPERATIONAL`, same name. Names are compared without case, spaces, punctuation, and without a "Shibuya" word and what follows it (Google adds the branch: "HUB Shibuya Center-gai" is "Hub"; "Shibuya109" is a name, not a branch); our name as whole words of Google's name also counts ("Hakata Furyu" in "Hakata Furyu Center Gai"), but such a partial match only confirms an open place: when it would change our status, the verdict is unsure. Among the results within 50 m, an exact name is preferred to a partial one, then the nearest.
   - **closed permanently**: `CLOSED_PERMANENTLY`, same name.
   - **closed temporarily**: `CLOSED_TEMPORARILY`, same name.
   - **reopened**: a place temporarily closed in our seeder that Google now reports `OPERATIONAL`, same name.
   - **replaced**: only when everything points that way, because it hides the place: the result on our spot (within 15 m) has another name, it is `OPERATIONAL`, and our name appears nowhere in the Text Search results (in a building with several tenants, our place is often a few metres from another one). A name in another script ("博多風龍" against "Hakata Furyu") cannot be compared, so it is never a replacement.
   - **unsure**: everything else: nothing found within 50 m, another name farther than 15 m, another name on our spot that is closed or has our name elsewhere in the results, no status. Reported for a manual look, never applied.
4. Writes `tools/freshness/reports/<YYYY-MM-DD>T<HHMM>-<kinds>.json` (gitignored; `<kinds>` is `all` or the `--kind` value, so the names sort in time order and two runs never overwrite each other) and prints the table: kind, name, Google's name, status, distance, verdict. Operational places are counted, not listed, unless `--all`.

An error that would fail for every place (a bad key, the API not enabled, the quota reached: HTTP 400, 401, 403 or 429) stops the run at once, but the results so far are written first, as a report marked `"partial": true` with the error. An error on a single place (the network) makes that place unsure and the run goes on.

### `npm run apply` (`apply.js`)

1. Reads the latest report (or the one given as argument). A partial report is refused unless `--partial` is given.
2. Shows every place whose verdict is a status change in a checkbox prompt, grouped by verdict, with Google's name and address on each line. Closed permanently, closed temporarily and reopened are checked; **replaced is unchecked**, to tick only after a look on Maps. Space toggles, Enter confirms. `--yes` skips the prompt and keeps the checked groups only, never replaced.
3. For each confirmed place, edits its seeder in place: inserts or updates the `'business_status' => '...',` line right after the `'name'` line of its block (for a reopened place, removes it: `OPERATIONAL` is the default), and the `'google_place_id' => '...',` line when the id is surely ours: found by its id, or a name that is exactly ours once normalised (operational places included, so the next run is Place Details only). Never for a replaced place: the id found is the new business's. Two places of the same name in a kind (the two "Joysound") are told apart by their coordinates. Every edited file is prepared and checked in memory first (the tool re-reads the new text with its own reader: every block still there, with the expected values), and only then are the files written, so an error leaves no seeder half done. The tool also runs `php -l` on each written file when `php` is on the PATH, and skips that check otherwise.
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
