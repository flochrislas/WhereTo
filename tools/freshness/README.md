# Freshness: find the places that closed

A small Node tool, run by hand on the dev machine from time to time. It checks every open or temporarily closed place of the seeders against Google Places, shows what closed, reopened or was replaced, and writes the confirmed changes into `source/database/seeders/*TableSeeder.php`. The design is in [`doc/11-data-freshness.md`](../../doc/11-data-freshness.md).

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
5. **Cap the quota**, so a bug or a leaked key cannot cost anything: **APIs & Services > Places API (New) > Quotas & System Limits**, and set the per-day request limits (for instance "SearchTextRequest per day" and "GetPlaceRequest per day") to 1,000. Optionally, add a budget alert under **Billing > Budgets & alerts**.

## 2. Configure

```sh
cp .env.example .env
# then edit .env:  GOOGLE_PLACES_API_KEY=AIza...
```

`.env` is gitignored; never commit the key. The tool refuses to run without it. (The variable can also come from the shell environment instead of the file.)

## 3. `npm run check`

```sh
npm run check                        # every open place of the four kinds
npm run check -- --kind restaurants  # one kind: restaurants, stores, bars or karaokes
npm run check -- --limit 10          # the first 10 places only (a first try)
npm run check -- --all               # list the operational places too
```

(`--` passes the options through npm to the script.)

For each place without a `business_status` in its seeder, or with `CLOSED_TEMPORARILY` (it may have reopened), the tool asks Google (the permanently closed and replaced places are not checked again):

- **Place Details** when the place already has a `google_place_id` (written by a previous `apply`): no search, no ambiguity. If Google has retired that id (HTTP 404), the tool searches by name again and the report says so (`retiredPlaceId`); `apply` then stores the new id;
- **Text Search** otherwise: our name, biased to a 50 m circle around our coordinates, in English.

It sends about 5 requests per second, then classifies each place:

| Verdict | When | What `apply` does |
|---|---|---|
| `operational` | Google says `OPERATIONAL` for our name within 50 m | writes the `google_place_id` if the id is surely ours (see below) |
| `closed_permanently` | Google says `CLOSED_PERMANENTLY` for our name | `business_status` `CLOSED_PERMANENTLY` (+ id) |
| `closed_temporarily` | Google says `CLOSED_TEMPORARILY` for our name | `business_status` `CLOSED_TEMPORARILY` (+ id) |
| `reopened` | our place is `CLOSED_TEMPORARILY` and Google says `OPERATIONAL` for our name | removes the `business_status` line (+ id) |
| `replaced` | all of: the result within 15 m has another name, it is `OPERATIONAL`, and our name is nowhere in the search results | `business_status` `REPLACED`, only if you tick it (no id: the one found is the new business's) |
| `unsure` | anything else: nothing found, nearest result farther than 50 m, another name 15 to 50 m away, another name on our spot that is closed or has our name elsewhere in the list, a name in another script (Japanese only), no status | nothing: look by hand |

Names are compared without case, spaces, punctuation or accents, and without a "Shibuya" word and anything after it ("HUB Shibuya Center-gai" is "Hub"; "Shibuya109" stays, and a name that would shrink below 3 characters, "Le Shibuya", is kept whole). Our name as whole words of Google's name also counts ("Hakata Furyu" in "Hakata Furyu Center Gai"; "Bar" does not match "Barbacoa"). Among the results within 50 m, the nearest exact name wins over any partial one. A partial match only confirms an open place: if Google says closed (or open, for a temporarily closed place), the verdict is `unsure`, since it may be a sister business ("The HUB British Pub" for "Hub"). Nor is a partial match sure enough to store the id: the `google_place_id` is written only for a place found by its stored id or whose name is exactly ours once normalised.

The result goes to `reports/<YYYY-MM-DD>T<HHMM>-<kinds>.json` (gitignored), for instance `reports/2026-11-18T0930-all.json` or `reports/2026-11-18T1405-bars.json` (`<kinds>` is `all` or the `--kind` value). The names sort in time order, and `apply` takes the newest. A table is printed: kind, name, Google's name, status, distance, verdict. The operational places are counted, not listed, unless `--all`.

A bad key, a disabled API or an exhausted quota (HTTP 400, 401, 403 or 429) stops the run at once; the places checked so far are still written, in a report marked `"partial": true` with the error. A network error on one place is reported as `unsure` and the run goes on.

## 4. `npm run apply`

```sh
npm run apply                               # the latest report
npm run apply -- reports/2026-11-18T0930-all.json   # a given report
npm run apply -- --yes                              # no prompt: keep the checked groups (never "replaced")
npm run apply -- --partial                          # accept a partial report (refused otherwise)
```

The places whose verdict changes the status are shown in a checkbox list, grouped by verdict, with Google's name and address on each line. Permanently closed, temporarily closed and reopened are checked; **replaced is unchecked**: tick a place only after looking at it on Maps. Space toggles, Enter confirms, Ctrl+C cancels without writing anything.

The tool then edits the seeders in place, one line per key, right after the `'name'` line of the place, with the same indentation:

```php
$resto = Restaurant::create([
    'name' => 'Kindenmaru',
    'business_status' => 'CLOSED_PERMANENTLY',
    'google_place_id' => 'ChIJ...',
    'location' => 'Shibuya',
```

For a reopened place, the `business_status` line is removed instead. Nothing else in the file changes. A place that appears twice in a seeder (same name, same coordinates) gets the same lines in both blocks; two places with the same name at different spots (the two "Joysound" karaokes) are told apart by their coordinates. Every edited seeder is prepared in memory first and read back with the tool's own reader (every place still there, with the expected values); only when all of them pass are the files written, each one logged. Then the tool runs `php -l` on them if PHP is installed (on a failure the files are already written: `git checkout source/database/seeders` undoes them), and prints `git diff --stat source/database/seeders`.

Running `apply` twice on the same report changes nothing the second time.

## 5. After apply

```sh
git diff source/database/seeders                          # review every change
cd dev && podman compose exec app php artisan db:seed     # see it in the local app
git commit -am "Places checked on 2026-11-18: 12 closed, 3 replaced"
git push                                                  # then deploy, which reseeds (doc/06-deploy-production.md, section 9)
```

`git checkout source/database/seeders` undoes the whole edit. The `unsure` places are left alone: check them on Maps and edit their seeder by hand if needed (add `'business_status' => 'CLOSED_PERMANENTLY',` after the `'name'` line).

## Files

| File | Role |
|---|---|
| `check.js` | the `check` command |
| `apply.js` | the `apply` command |
| `lib/seeders.js` | reads the places from the seeders, and edits the two keys of a place (prepare and verify in memory, then write) |
| `lib/classify.js` | the verdict: name comparison, distance (haversine), rules above |
| `lib/places-api.js` | the two Google calls, with the key, the field masks and the throttling |
| `lib/cli.js` | small shared helpers (`.env`, dates, the text table) |
| `test/` | `node --test` tests, on fixture seeders in `test/fixtures/` with a fake `fetch`; one test also reads the four real seeders (read-only) |
