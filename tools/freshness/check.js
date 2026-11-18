// npm run check [-- --kind restaurants] [--limit N] [--all]
//
// Checks the open and temporarily closed places of the seeders against Google Places, writes
// reports/<YYYY-MM-DD>T<HHMM>-<kinds>.json and prints what changed. Nothing is written to the seeders: that is apply.js.

import { mkdirSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { parseArgs } from 'node:util';
import { readAllSeeders, KINDS, DEFAULT_SEEDERS_DIR } from './lib/seeders.js';
import { classify, pickCandidate } from './lib/classify.js';
import { createPlacesClient } from './lib/places-api.js';
import { loadEnv, isMain, isoDate, formatTable, DEFAULT_REPORTS_DIR } from './lib/cli.js';

// The statuses to check again: open places, and temporarily closed ones (they may have reopened).
const CHECKED_STATUSES = [null, 'OPERATIONAL', 'CLOSED_TEMPORARILY'];

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
    minIntervalMs = 200,
    today = new Date(),
    log = console.log,
}) {
    const client = createPlacesClient({ apiKey, fetch, minIntervalMs });

    // The permanently closed and replaced places have their story already: not checked again.
    const open = readAllSeeders(seedersDir, kinds).filter((p) => CHECKED_STATUSES.includes(p.businessStatus));
    // A place written twice in a seeder (same kind, name and coordinates) is checked once.
    const seen = new Set();
    const places = open.filter((p) => {
        const key = `${p.kind}|${p.name}|${p.lat}|${p.lon}`;
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
    }).slice(0, limit);

    // The report file: date and time (so that two runs on the same day do not overwrite each other,
    // and the names sort in time order), then the kinds checked.
    const date = isoDate(today);
    const time = `${String(today.getHours()).padStart(2, '0')}${String(today.getMinutes()).padStart(2, '0')}`;
    const kindsLabel = kinds.length === Object.keys(KINDS).length ? 'all' : kinds.join('+');
    const reportPath = join(reportsDir, `${date}T${time}-${kindsLabel}.json`);
    const results = [];
    const writeReport = (extra = {}) => {
        const report = { date, generatedAt: today.toISOString(), kinds, ...extra, results };
        mkdirSync(reportsDir, { recursive: true });
        writeFileSync(reportPath, JSON.stringify(report, null, 2) + '\n');
        return report;
    };

    log(`Checking ${places.length} places with Google Places...`);
    for (const [index, place] of places.entries()) {
        let byId = Boolean(place.googlePlaceId);
        let retired = false; // true when Google no longer knows the id we stored
        let google = null;
        let found = [];
        let error = null;
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
                google = pickCandidate(place, found);
            }
        } catch (e) {
            if (FATAL_STATUSES.includes(e.status)) {
                // Keep what was done so far, then stop.
                writeReport({ partial: true, error: e.message });
                log(`Stopped after ${results.length} of ${places.length} places; partial report written to ${reportPath}`);
                throw e;
            }
            error = e.message; // a network hiccup on one place: note it and go on
        }
        const { verdict, reason, distance, idTrusted } = error
            ? { verdict: 'unsure', reason: `error: ${error}`, distance: null, idTrusted: false }
            : classify(place, google, { byId, results: found });
        const retiredNote = retired ? `stored id retired by Google, searched again; ${reason}` : reason;
        results.push({
            kind: place.kind,
            name: place.name,
            lat: place.lat,
            lon: place.lon,
            line: place.line,
            businessStatus: place.businessStatus, // ours, at the time of the check
            knownPlaceId: place.googlePlaceId,
            lookup: byId ? 'details' : 'search',
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
        });
        // A progress line every 10 places, so a long run does not look stuck.
        if ((index + 1) % 10 === 0) log(`  ${index + 1} / ${places.length}`);
    }

    const report = writeReport();

    printSummary(results, all, log);
    log(`\nReport written to ${reportPath}`);
    log('Next: npm run apply');
    return { report, reportPath };
}

// Prints the table of the places that need a look, and counts the operational ones.
function printSummary(results, all, log) {
    const shown = all ? results : results.filter((r) => r.verdict !== 'operational');
    const operational = results.filter((r) => r.verdict === 'operational').length;
    if (shown.length) {
        const rows = shown.map((r) => [
            r.kind,
            r.name,
            r.google?.name ?? '-',
            r.google?.status ?? '-',
            r.distance === null ? '-' : `${r.distance} m`,
            r.verdict,
        ]);
        log('\n' + formatTable(['kind', 'name', "Google's name", 'status', 'distance', 'verdict'], rows));
    }
    const counts = {};
    for (const r of results) counts[r.verdict] = (counts[r.verdict] ?? 0) + 1;
    log(`\n${operational} operational` + (all ? '' : ' (not listed, use --all to see them)'));
    log(Object.entries(counts).map(([verdict, n]) => `${verdict}: ${n}`).join(', '));
}

// Command line entry point.
async function main() {
    const { values } = parseArgs({
        options: {
            kind: { type: 'string' },
            limit: { type: 'string' },
            all: { type: 'boolean', default: false },
        },
    });
    if (values.kind && !(values.kind in KINDS)) {
        throw new Error(`--kind must be one of: ${Object.keys(KINDS).join(', ')}`);
    }
    const limit = values.limit === undefined ? Infinity : Number(values.limit);
    if (!Number.isInteger(limit) && limit !== Infinity) throw new Error('--limit must be a whole number');

    loadEnv();
    await runCheck({
        apiKey: process.env.GOOGLE_PLACES_API_KEY,
        kinds: values.kind ? [values.kind] : Object.keys(KINDS),
        limit,
        all: values.all,
    });
}

if (isMain(import.meta.url)) {
    main().catch((error) => {
        console.error(error.message);
        process.exitCode = 1;
    });
}
