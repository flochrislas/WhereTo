// npm run check [-- --kind restaurants] [--limit N] [--all] [--names]
//
// Checks the open and temporarily closed places of the seeders against Google Places, writes
// reports/<YYYY-MM-DD>T<HHMM>-<kinds>.json and prints what changed. Nothing is written to the seeders: that is apply.js.
// With --names, also compares every name_ja with Google's Japanese name (a verdict only: Google's
// name is never written nor printed).

import { mkdirSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { parseArgs } from 'node:util';
import { readAllSeeders, KINDS, DEFAULT_SEEDERS_DIR } from './lib/seeders.js';
import { classify, pickCandidate, hasNearbyMatch, SEARCH_RADIUS_METRES } from './lib/classify.js';
import { japaneseNameVerdict } from './lib/names.js';
import { createPlacesClient } from './lib/places-api.js';
import { loadEnv, isMain, isoDate, reportStamp, uniquePlaces, kindsAndLimit, formatTable, DEFAULT_REPORTS_DIR } from './lib/cli.js';

// The statuses to check again: open places, and temporarily closed ones (they may have reopened).
const CHECKED_STATUSES = [null, 'OPERATIONAL', 'CLOSED_TEMPORARILY'];

// The order of the verdicts in the table.
const VERDICT_ORDER = ['closed_permanently', 'closed_temporarily', 'reopened', 'replaced', 'not_found_nearby', 'unsure', 'operational'];

// HTTP statuses that will fail for every place (bad key, API not enabled, quota): stop at once.
const FATAL_STATUSES = [400, 401, 403, 429];

// Runs the check. Every dependency is a parameter, so the tests can run it on a temporary copy
// of a fixture seeder with a fake fetch. Returns { report, reportPath }.
// On an error that would fail for every place (bad key, quota), the results so far are written
// as a partial report before the error is thrown again.
export async function runCheck({
    apiKey,
    fetch = globalThis.fetch,
    seedersDir = DEFAULT_SEEDERS_DIR,
    reportsDir = DEFAULT_REPORTS_DIR,
    kinds = Object.keys(KINDS),
    limit = Infinity,
    all = false,
    names = false,
    minIntervalMs = 200,
    today = new Date(),
    log = console.log,
}) {
    const client = createPlacesClient({ apiKey, fetch, minIntervalMs });

    // The permanently closed and replaced places have their story already: not checked again.
    const open = readAllSeeders(seedersDir, kinds).filter((p) => CHECKED_STATUSES.includes(p.businessStatus));
    // A place written twice in a seeder (same kind, name and coordinates) is checked once.
    const places = uniquePlaces(open).slice(0, limit);

    // The report file: date and time (so that two runs on the same day do not overwrite each other,
    // and the names sort in time order), then the kinds checked.
    const date = isoDate(today);
    const kindsLabel = kinds.length === Object.keys(KINDS).length ? 'all' : kinds.join('+');
    const reportPath = join(reportsDir, `${reportStamp(today)}-${kindsLabel}.json`);
    const results = [];
    const writeReport = (extra = {}) => {
        const report = { date, generatedAt: today.toISOString(), kinds, ...extra, results };
        mkdirSync(reportsDir, { recursive: true });
        writeFileSync(reportPath, JSON.stringify(report, null, 2) + '\n');
        return report;
    };
    // Keeps what was done so far in a partial report, then stops (bad key, quota...).
    const stop = (e) => {
        writeReport({ partial: true, error: e.message });
        log(`Stopped after ${results.length} of ${places.length} places; partial report written to ${reportPath}`);
        throw e;
    };

    log(`Checking ${places.length} places with Google Places...`);
    for (const [index, place] of places.entries()) {
        let byId = Boolean(place.googlePlaceId);
        let retired = false; // true when Google no longer knows the id we stored
        let google = null;
        let found = [];
        let error = null;
        let lookup = byId ? 'details' : 'search'; // 'search+ja' when the name_ja was searched too
        try {
            if (byId) {
                google = await client.getDetails(place.googlePlaceId); // known place: no search, no ambiguity
                // Google retires ids now and then (null = HTTP 404): search by name again, like a first run.
                if (google === null) {
                    retired = true;
                    byId = false;
                }
            }
            if (!byId) {
                found = await client.searchText(place);
                lookup = 'search';
                // No result with our name within 200 m: search again with the seeder's name_ja, and
                // judge over both lists (the same place found twice is kept once).
                if (place.nameJa !== null && !hasNearbyMatch(place, found)) {
                    const more = await client.searchText({ ...place, name: place.nameJa });
                    const ids = new Set(found.map((r) => r.id));
                    found = [...found, ...more.filter((r) => !ids.has(r.id))];
                    lookup = 'search+ja';
                }
                google = pickCandidate(place, found);
            }
        } catch (e) {
            if (FATAL_STATUSES.includes(e.status)) stop(e);
            error = e.message; // a network hiccup on one place: note it and go on
        }
        const { verdict, reason, distance, idTrusted } = error
            ? { verdict: 'unsure', reason: `error: ${error}`, distance: null, idTrusted: false }
            : classify(place, google, { byId, results: found });
        const retiredNote = retired ? `stored id retired by Google, searched again; ${reason}` : reason;

        // --names: one more Details call, in Japanese, for a place that has a name_ja and whose result
        // is within 50 m (farther, it is not our place). Google's name is compared, then dropped.
        let nameJa = null; // null: no name_ja to check
        if (names && place.nameJa !== null) {
            nameJa = 'unchecked';
            if (google && distance !== null && distance <= SEARCH_RADIUS_METRES) {
                try {
                    nameJa = japaneseNameVerdict(place.nameJa, await client.getJapaneseName(google.id));
                } catch (e) {
                    if (FATAL_STATUSES.includes(e.status)) stop(e);
                    // a network hiccup: the name stays unchecked, the status verdict stands
                }
            }
        }

        results.push({
            kind: place.kind,
            name: place.name,
            lat: place.lat,
            lon: place.lon,
            line: place.line,
            businessStatus: place.businessStatus, // ours, at the time of the check
            knownPlaceId: place.googlePlaceId,
            lookup,
            retiredPlaceId: retired ? place.googlePlaceId : null,
            google: google && {
                id: google.id,
                name: google.displayName?.text ?? null,
                address: google.formattedAddress ?? null,
                status: google.businessStatus ?? null,
                lat: google.location?.latitude ?? null,
                lon: google.location?.longitude ?? null,
            },
            distance: distance === null ? null : Math.round(distance),
            verdict,
            reason: retiredNote,
            idTrusted, // true when Google's id is surely ours (exact name, or looked up by id)
            // Only with --names: 'confirmed', 'disputed', 'unchecked', or null when the place has no name_ja.
            ...(names ? { nameJa } : {}),
            // The Google Maps link, for the places to look at by hand.
            ...((names && nameJa === 'disputed') || verdict === 'not_found_nearby' ? { googleMapsLink: place.googleMapsLink } : {}),
        });
        // A progress line every 10 places, so a long run does not look stuck.
        if ((index + 1) % 10 === 0) log(`  ${index + 1} / ${places.length}`);
    }

    const report = writeReport();

    printSummary(results, all, names, log);
    log(`\nReport written to ${reportPath}`);
    log('Next: npm run apply');
    return { report, reportPath };
}

// Prints the table of the places that need a look, and counts the operational ones. With --names,
// a Japanese name column, and the disputed names listed at the end with their Google Maps link.
function printSummary(results, all, names, log) {
    // Ordered by verdict, then by reason (its numbers aside, then the distance), so that the places
    // with the same story read in a block.
    const order = (r) => VERDICT_ORDER.indexOf(r.verdict);
    const reasonKey = (r) => r.reason.replace(/\d+/g, '#');
    const shown = (all ? results : results.filter((r) => r.verdict !== 'operational'))
        .toSorted((a, b) => order(a) - order(b) || reasonKey(a).localeCompare(reasonKey(b)) || (a.distance ?? -1) - (b.distance ?? -1));
    const operational = results.filter((r) => r.verdict === 'operational').length;
    if (shown.length) {
        const rows = shown.map((r) => [
            r.kind,
            r.name,
            r.google?.name ?? '-',
            r.google?.status ?? '-',
            r.distance === null ? '-' : `${r.distance} m`,
            r.verdict,
            r.reason,
            ...(names ? [r.nameJa ?? '-'] : []),
        ]);
        const header = ['kind', 'name', "Google's name", 'status', 'distance', 'verdict', 'reason', ...(names ? ['Japanese name'] : [])];
        log('\n' + formatTable(header, rows));
    }
    const counts = {};
    for (const r of results) counts[r.verdict] = (counts[r.verdict] ?? 0) + 1;
    log(`\n${operational} operational` + (all ? '' : ' (not listed, use --all to see them)'));
    log(Object.entries(counts).map(([verdict, n]) => `${verdict}: ${n}`).join(', '));
    if (!names) return;

    const named = (verdict) => results.filter((r) => r.nameJa === verdict);
    log(`Japanese names: confirmed: ${named('confirmed').length}, disputed: ${named('disputed').length}, unchecked: ${named('unchecked').length}`);
    if (named('disputed').length) {
        log('\nJapanese names disputed by Google (check them on Maps, fix the seeder by hand if needed):');
        for (const r of named('disputed')) log(`  ${r.kind.padEnd(11)} ${r.name}: ${r.googleMapsLink ?? 'no Google Maps link'}`);
    }
}

// Command line entry point.
async function main() {
    const { values } = parseArgs({
        options: {
            kind: { type: 'string' },
            limit: { type: 'string' },
            all: { type: 'boolean', default: false },
            names: { type: 'boolean', default: false }, // also check every name_ja against Google
        },
    });
    loadEnv();
    await runCheck({
        apiKey: process.env.GOOGLE_PLACES_API_KEY,
        ...kindsAndLimit(values),
        all: values.all,
        names: values.names,
    });
}

if (isMain(import.meta.url)) {
    main().catch((error) => {
        console.error(error.message);
        process.exitCode = 1;
    });
}
