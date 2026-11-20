# Freshness: find the places that closed

A small Node tool, run by hand on the dev machine from time to time. It checks every open or temporarily closed place of the seeders against Google Places, shows what closed, reopened or was replaced, and writes the confirmed changes into `source/database/seeders/*TableSeeder.php`. A second command fills the Japanese names of the places (`name_ja`) from OpenStreetMap, and `check --names` has Google confirm them. The design is in [`doc/11-data-freshness.md`](../../doc/11-data-freshness.md) (sections 6 and 8).

It is not part of the Laravel app: it is never built into the image nor deployed, and the app does not know it exists. The only contract between the two is the shape of the seeders.

Requirements: Node 22 or later (it uses the built-in `fetch`, `node:test` and `process.loadEnvFile`). PHP is optional (used for a `php -l` syntax check after an edit, skipped when `php` is not on the PATH).

```sh
cd tools/freshness
npm ci          # one dependency: @inquirer/checkbox, for the checkbox prompt
npm test        # no network, no key needed
```

## 1. Get a Google Places API key (once)

1. Open the [Google Cloud console](https://console.cloud.google.com/) and create a project (for instance `whereto-freshness`).
2. Attach a billing account to the project. Google requires one, with a card, even though a full run stays inside the free tier: Text Search and Place Details with the fields this tool asks for are "Pro" SKUs, with 5,000 free requests per month each, and a run is about 300 requests.
3. **APIs & Services > Library**: search for **Places API (New)** and click **Enable**. (Not the legacy "Places API".)
4. **APIs & Services > Credentials > Create credentials > API key**. Then edit the key:
   - **API restrictions > Restrict key**, and tick **Places API (New)** only.
   - Application restrictions: none is needed for a key that only lives on your machine (an IP restriction works too if your address is fixed).
5. **Limit the spending.** The per-day request quotas of Places API (New) (**APIs & Services > Places API (New) > Quotas & System Limits**) are marked "not adjustable" by Google (seen in November 2026), so a key cannot be capped there. What remains: the API restriction of step 4 (the key is useless for anything else), a **budget alert** under **Billing > Budgets & alerts** (an e-mail at, say, 1 dollar; Google does not stop the requests by itself), and the tool's own throttle (5 requests per second, and one run is about 300 requests, 600 with `--names`, far from the 5,000 free ones per month). Keep the key in `.env` only and revoke it in the console if it ever leaks.

## 2. Configure

```sh
cp .env.example .env
# then edit .env:  GOOGLE_PLACES_API_KEY=AIza...
```

`.env` is gitignored; never commit the key. The tool refuses to run without it. (The variable can also come from the shell environment instead of the file.)

`npm run names` needs no key. It asks the public Overpass server `https://overpass-api.de/api/interpreter`; to use a mirror instead, add `OVERPASS_URL=https://.../api/interpreter` to `.env`.

## 3. `npm run check`

```sh
npm run check                        # every open place of the four kinds
npm run check -- --kind restaurants  # one kind: restaurants, stores, bars or karaokes
npm run check -- --limit 10          # the first 10 places only (a first try)
npm run check -- --all               # list the operational places too
npm run check -- --names             # also check every name_ja against Google (section 6)
```

(`--` passes the options through npm to the script.)

For each place without a `business_status` in its seeder, or with `CLOSED_TEMPORARILY` (it may have reopened), the tool asks Google (the permanently closed and replaced places are not checked again):

- **Place Details** when the place already has a `google_place_id` (written by a previous `apply`): no search, no ambiguity. If Google has retired that id (HTTP 404), the tool searches by name again and the report says so (`retiredPlaceId`); `apply` then stores the new id;
- **Text Search** otherwise: our name, biased to a 50 m circle around our coordinates, in English. When the place has a `name_ja` in its seeder (from `npm run names`, section 6) and no result with our name is within 200 m, a second Text Search with the `name_ja` (same circle, still in English), and the place is judged over both lists; the report says `lookup: "search+ja"`. This one is on by default: it only reads our own seeder, and costs one more request for those places only.

It sends about 5 requests per second, then classifies each place:

| Verdict | When | What `apply` does |
|---|---|---|
| `operational` | Google says `OPERATIONAL` for our name within 50 m; or for our name 50 to 200 m away (reason "same name 97 m away": coordinates a bit off, or another branch of a chain) | writes the `google_place_id` if the id is surely ours (see below; never for a result 50 to 200 m away) |
| `closed_permanently` | Google says `CLOSED_PERMANENTLY` for our name | `business_status` `CLOSED_PERMANENTLY` (+ id) |
| `closed_temporarily` | Google says `CLOSED_TEMPORARILY` for our name | `business_status` `CLOSED_TEMPORARILY` (+ id) |
| `reopened` | our place is `CLOSED_TEMPORARILY` and Google says `OPERATIONAL` for our name | removes the `business_status` line (+ id) |
| `replaced` | all of: no result with our name within 200 m, the result within 15 m has another name, it is `OPERATIONAL`, and our name is nowhere in the search results, even far away | `business_status` `REPLACED`, only if you tick it (no id: the one found is the new business's) |
| `not_found_nearby` | no result with our name within 200 m, and not replaced: nothing found ("no match within 200 m, nothing found"), our name only farther ("..., nearest same name 1745 m away"), another business on our spot that is closed or not sure enough to be a replacement ("..., other businesses on the spot"), or another name 15 m or more away ("..., nearest result 30 m away") | `business_status` `CLOSED_PERMANENTLY`, only if you tick it after a look on Maps (no id) |
| `unsure` | anything else: a partial name within 50 m when Google says closed, our name 50 to 200 m away but not open, a name in another script within 50 m (Japanese only, and no `name_ja` that confirms it), no status, a network error | nothing: look by hand |

Names are compared without case, spaces, punctuation or accents, and without a "Shibuya" word and anything after it ("HUB Shibuya Center-gai" is "Hub"; "Shibuya109" stays, and a name that would shrink below 3 characters, "Le Shibuya", is kept whole). Equal names are an exact match. A partial match is any of: our name (3 characters or more) as whole words of Google's name ("Hakata Furyu" in "Hakata Furyu Center Gai"; "Bar" does not match "Barbacoa"); Google's name (3 characters or more) as whole words of ours ("Deux Magots" in "Les deux magots"); a near miss, on the whole names or on those words: one letter apart for names of 6 characters or more, two for 10 or more ("Tatsu Ichi" / "TASUICHI", "Disc Union" / "Disk Union", "Yasube" / "Tsukemen Yasubee"). When the place has a `name_ja` and Google's name has kana or kanji, the two are compared like `check --names` does (section 6), and a confirmed Japanese name is an exact match. Google's pick order: our name within 50 m (the nearest exact name, else the nearest partial one), else our name within 200 m (same order), else the nearest result. A partial match only confirms an open place: if Google says closed (or open, for a temporarily closed place), the verdict is `unsure`, since it may be a sister business ("The HUB British Pub" for "Hub"). Nor is a partial match sure enough to store the id: the `google_place_id` is written only for a place found by its stored id or whose name is exactly ours once normalised.

The result goes to `reports/<YYYY-MM-DD>T<HHMM>-<kinds>.json` (gitignored), for instance `reports/2026-11-18T0930-all.json` or `reports/2026-11-18T1405-bars.json` (`<kinds>` is `all` or the `--kind` value). The names sort in time order, and `apply` takes the newest. A table is printed: kind, name, Google's name, status, distance, verdict, reason; the rows are ordered by verdict (closed permanently, closed temporarily, reopened, replaced, not found nearby, unsure, operational), then by reason, so that the places with the same story read in a block. The operational places are counted, not listed, unless `--all`.

A bad key, a disabled API or an exhausted quota (HTTP 400, 401, 403 or 429) stops the run at once; the places checked so far are still written, in a report marked `"partial": true` with the error. A network error on one place is reported as `unsure` and the run goes on.

## 4. `npm run apply`

```sh
npm run apply                               # the latest report
npm run apply -- reports/2026-11-18T0930-all.json   # a given report
npm run apply -- --yes                              # no prompt: keep the checked groups (never "replaced" nor "not found nearby")
npm run apply -- --partial                          # accept a partial report (refused otherwise)
```

The places whose verdict changes the status are shown in a checkbox list, grouped by verdict, with Google's name and address on each line. Permanently closed, temporarily closed and reopened are checked; **replaced and not found nearby are unchecked**: tick a place only after looking at it on Maps. A not-found-nearby line shows what Google found instead (name and distance) and the place's Google Maps link from the seeder; a ticked one is written `CLOSED_PERMANENTLY`, without an id. Space toggles, Enter confirms, Ctrl+C cancels without writing anything.

The tool then edits the seeders in place, one line per key, right after the `'name'` line of the place, with the same indentation:

```php
$resto = Restaurant::create([
    'name' => 'Kindenmaru',
    'business_status' => 'CLOSED_PERMANENTLY',
    'google_place_id' => 'ChIJ...',
    'location' => 'Shibuya',
```

For a reopened place, the `business_status` line is removed instead. Nothing else in the file changes. A place that appears twice in a seeder (same name, same coordinates) gets the same lines in both blocks; two places with the same name at different spots (the two "Joysound" karaokes) are told apart by their coordinates. Every edited seeder is prepared in memory first and read back with the tool's own reader (every place still there, with the expected values); only when all of them pass are the files written, each one logged. Each file is then written to a temporary copy, checked with `php -l` if PHP is installed, and renamed over the seeder only when it passes (on a failure nothing is written); the tool then prints `git diff --stat source/database/seeders`.

Running `apply` twice on the same report changes nothing the second time.

## 5. After apply

```sh
git diff source/database/seeders                          # review every change
cd dev && podman compose exec app php artisan db:seed     # see it in the local app
git commit -am "Places checked on 2026-11-18: 12 closed, 3 replaced"
git push                                                  # then deploy, which reseeds (doc/06-deploy-production.md, section 9)
```

`git checkout source/database/seeders` undoes the whole edit. The `unsure` places are left alone: check them on Maps and edit their seeder by hand if needed (add `'business_status' => 'CLOSED_PERMANENTLY',` after the `'name'` line).

## 6. Japanese names: `npm run names`, and `check --names`

A Japanese place page is only indexed once the place has a `name_ja`. Two sources, with different rights:

- **OpenStreetMap** may be copied (Open Database License, ODbL), **on one condition: the credit "© OpenStreetMap contributors"** wherever the names are shown. The app's info page carries it; keep it there as long as names from OpenStreetMap are in the seeders. The report of `names` carries it too.
- **Google Places** may only be consulted: its terms forbid storing anything but the place id. **The `--names` pass never writes, prints or keeps a Japanese name from Google**, not even in the local report: it compares Google's name with ours and keeps the verdict only. (The status pass asks Google for English names; when Google has none, its `displayName` may come back in Japanese, and that name is in the status report and table as it always was.)

Only `names` writes `name_ja`, and Google is only asked for Japanese names with `check --names`. Plain `check` does read the `name_ja` already in the seeders, as a second search key (section 3). The flow, `names` first because it is free and its names help the status pass find the places:

```sh
npm run names                    # 1. one request to OpenStreetMap, a checkbox prompt, name_ja written in the seeders
npm run check                    # 2. the status pass; the name_ja is a second search key when the English name finds nothing nearby
npm run apply                    # 3. the status changes (permanently closed and replaced places keep their name_ja, their pages are not indexed)
npm run check -- --names         # 4. the verification: every name_ja against Google's Japanese name
# 5. fix by hand, in the seeders, the names the report disputes or could not fill, with Google Maps open
git diff source/database/seeders ; cd dev && podman compose exec app php artisan db:seed ; git commit ; git push ; deploy
```

`names` offers a name to the places without a status and to the temporarily closed ones (their pages stay indexable), never to the permanently closed or replaced ones. Steps can be repeated: `names` only looks at the places without `name_ja`, and `check --names` verifies whichever exist. Do not push before step 4: a `name_ja` makes the Japanese page indexable, so Google's check comes first.

### `npm run names`

```sh
npm run names                        # the open and temporarily closed places without name_ja, all four kinds
npm run names -- --kind bars         # one kind
npm run names -- --limit 10          # the first 10 places only
npm run names -- --dry-run           # the table only: no report, no seeder written
npm run names -- --yes               # no prompt: keep the English matches only
npm run names -- --refresh           # ask OpenStreetMap again even if today's answer is cached
```

1. **One request to the Overpass API** for all the places: every node and way with an `amenity`, `shop`, `leisure` or `craft` tag, in the box around the places plus 100 m (a few thousand elements for Shibuya; the unnamed ones are dropped on our side: a `name` filter on the server made the query time out). The query, with the box filled in:

   ```
   [out:json][timeout:90][bbox:south,west,north,east];
   (
     nw["amenity"];
     nw["shop"];
     nw["leisure"];
     nw["craft"];
   );
   out center tags;
   ```

   The answer is saved in `cache/overpass-<YYYY-MM-DD>.json` (gitignored) and reused for the rest of the day when its box covers the places asked for, so running the command again costs the shared server nothing. The request names the tool in its `User-Agent`, as the Overpass usage policy asks.
2. **Matching**, per place, among the named elements within 50 m (a way by its centre):

   | Match | When | Proposed |
   |---|---|---|
   | `matched` | an element whose `name:en`, or whose `name` when it has Latin letters, matches our name with the strict rules: exact, or our name as whole words of theirs, spelled the same (none of the looser rules of the status pass, section 3: a name written from a guess would be wrong on the page). Exact wins over partial, then the nearest | yes, checked |
   | `nearest` | no English name matches, but exactly one named element is within 15 m and it has a Japanese name. Right in a street of single shops, a guess in a building with several tenants | yes, unchecked: the first real run showed the guess wrong far more often than right in Shibuya (a dentist, a bank, a parking lot); tick one only after a look at the element |
   | `none` | anything else: nothing named within 50 m, two or more elements within 15 m, only Latin names, an element that matches our English name but has no Japanese name (our place is mapped, without the name we need: a neighbour would be a wrong guess), or a `nearest` element that several of our places claim (two restaurants in one building, one of them mapped) | no: listed, fill it by hand |

   The Japanese name is `name:ja` when it contains kana or kanji, else `name` when it does (a `name:ja` of "HUB" is no Japanese name); an element with neither cannot give a name and is skipped. It is kept as written: a branch such as 渋谷店 is part of the name on the sign. A `matched` element claimed by two places stays matched: its English name says which one it is.
3. Writes `reports/<YYYY-MM-DD>T<HHMM>-names.json` (`apply` ignores these) and prints the table: kind, name, Japanese name, source element (`node/123`, `way/456`: open `https://www.openstreetmap.org/node/123` to see it), distance, match.
4. The checkbox prompt, grouped by match; then, for each confirmed place, the line `'name_ja' => '...',` right after the `'name'` line of its block, with the same in-memory check, write and `php -l` as `apply`:

   ```php
   $resto = Restaurant::create([
       'name' => 'Hakata Furyu',
       'name_ja' => '博多風龍',
       'business_status' => 'CLOSED_TEMPORARILY',
   ```

### `npm run check -- --names`

With `--names` (and only then), every place that has a `name_ja` and was found by the status pass (by its id or by a search, within 50 m) gets one more Place Details request, in Japanese, for its name only (same Pro SKU: a full run is then about 600 requests, still inside the 5,000 free ones). The two names are compared after folding the width (ＡＢＣ to ABC, half-width katakana to full width), dropping spaces (full-width ones too), punctuation and middle dots (・), and dropping a trailing branch from both: a last word ending in 店 or starting with 渋谷, or for a name without spaces a trailing 渋谷…店 ("ラーメン二郎 渋谷店", "ラーメン二郎渋谷店" and "ラーメン二郎（渋谷店）" are all "ラーメン二郎": brackets separate words). A last word ending in 商店, 飯店, 酒店, 書店 or 売店 is part of the name, not a branch ("らーめん 山田商店" is not "らーめん 渋谷店"). When Google's name has no kana or kanji (a Latin brand), the two are compared like the status pass compares names instead: "JOYSOUND 渋谷店" against "JOYSOUND" is confirmed, "ゴーゴーカレー" against "Go! Go! Curry" disputed.

| `nameJa` | When |
|---|---|
| `confirmed` | equal after that |
| `disputed` | different (for a Latin name from Google: different once compared like the status pass does) |
| `unchecked` | the place was not found within 50 m, Google has no name for it, or the request failed |

The verdict goes into each result of the report as `nameJa` (`null` for a place without `name_ja`) and into a `Japanese name` column of the table; the disputed places are listed together at the end with their Google Maps link (also in the report, as `googleMapsLink`), which is where the hand check happens. A disputed name does not change the status verdict, and `apply` ignores `nameJa`. Without the flag: no extra request, no `nameJa` field, the same output as before.

## Files

| File | Role |
|---|---|
| `check.js` | the `check` command (and `--names`) |
| `apply.js` | the `apply` command |
| `names.js` | the `names` command |
| `lib/seeders.js` | reads the places from the seeders, and edits a place: the `name_ja`, `business_status` and `google_place_id` keys for `apply` and `names` (prepare and verify in memory, then write) |
| `lib/classify.js` | the verdict: name comparison (near misses, the Japanese comparison), distance (haversine), rules above |
| `lib/names.js` | the OpenStreetMap matcher (and the Japanese comparison, re-exported from `lib/classify.js`) |
| `lib/overpass.js` | the Overpass request, with its daily cache |
| `lib/places-api.js` | the Google calls, with the key, the field masks and the throttling |
| `lib/cli.js` | small shared helpers (`.env`, dates, the text table, the checkbox prompt, writing the seeders: `php -l` on a copy, then the rename) |
| `test/` | `node --test` tests, on fixture seeders in `test/fixtures/` with a fake `fetch`; one test also reads the four real seeders (read-only) |
