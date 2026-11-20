// npm run names [-- --kind restaurants] [--limit N] [--dry-run] [--yes] [--refresh]
//
// Finds a Japanese name for the open and temporarily closed places of the seeders that have none,
// from OpenStreetMap (one Overpass request, cached for the day), writes
// reports/<YYYY-MM-DD>T<HHMM>-names.json, asks which names to keep and writes them into the seeders
// as 'name_ja'. Then: npm run check -- --names.

import { mkdirSync, writeFileSync } from 'node:fs';
import { join, relative } from 'node:path';
import { parseArgs } from 'node:util';
import { readAllSeeders, planSeederEdits, KINDS, DEFAULT_SEEDERS_DIR } from './lib/seeders.js';
import { fetchOsmElements, DEFAULT_OVERPASS_URL } from './lib/overpass.js';
import { matchPlace, resolveClaims } from './lib/names.js';
import {
    loadEnv, isMain, isoDate, reportStamp, uniquePlaces, kindsAndLimit, formatTable, checkboxPrompt, runCommand, writePlans,
    DEFAULT_REPORTS_DIR, DEFAULT_CACHE_DIR, REPO_ROOT,
} from './lib/cli.js';

// The business statuses of the places that get a Japanese name (null is open, the default).
const NAMED_STATUSES = [null, 'OPERATIONAL', 'CLOSED_TEMPORARILY'];

// The credit the ODbL asks for, wherever the names are shown.
export const ATTRIBUTION = '© OpenStreetMap contributors (ODbL)';

// The groups of the prompt. Only the English matches are checked: the first real run (November 2026)
// showed that "nearest" is wrong far more often than right in Shibuya (a dentist, a bank, a parking lot
// for a restaurant). It stays in the table as a hint, unchecked. The "none" places are only listed.
const GROUPS = [
    { match: 'matched', label: 'Matched by their English name', checked: true },
    { match: 'nearest', label: 'Nearest element on the spot (a guess, usually wrong: tick only after a look)', checked: false },
];

// One line of the prompt: ours, then the OpenStreetMap element.
export function describe(r) {
    const osmName = r.elementName ?? r.nameJa; // an element may only have a name:ja
    return `${r.kind.padEnd(11)} ${r.name} -> ${r.nameJa} (${r.element}, "${osmName}", ${r.distance} m)`;
}

const namesPrompt = (groups) => checkboxPrompt(groups, {
    message: 'Japanese names to write into the seeders (space: uncheck, enter: confirm)',
    describe,
});

// Runs names. Every dependency is a parameter, so the tests can run it on a temporary copy of a
// fixture seeder with a fake fetch and a fake prompt. Returns { report, reportPath, confirmed, changedFiles }.
export async function runNames({
    fetch = globalThis.fetch,
    overpassUrl = DEFAULT_OVERPASS_URL,
    seedersDir = DEFAULT_SEEDERS_DIR,
    reportsDir = DEFAULT_REPORTS_DIR,
    cacheDir = DEFAULT_CACHE_DIR,
    repoRoot = REPO_ROOT,
    kinds = Object.keys(KINDS),
    limit = Infinity,
    dryRun = false,
    yes = false,
    refresh = false,
    today = new Date(),
    prompt = namesPrompt,
    run = runCommand,
    log = console.log,
}) {
    // The places without a Japanese name, but the permanently closed and replaced ones (their pages
    // are not indexed). A temporarily closed place gets one: its pages stay indexable, like check checks it.
    const all = readAllSeeders(seedersDir, kinds);
    const places = uniquePlaces(all.filter((p) => NAMED_STATUSES.includes(p.businessStatus) && p.nameJa === null)).slice(0, limit);
    if (!places.length) {
        log('Every place to name already has a Japanese name: nothing to do.');
        return { report: null, reportPath: null, confirmed: [], changedFiles: [] };
    }

    // 1. One request for all of them (or the cache of the day), then the matching, place by place.
    const { elements, cachePath } = await fetchOsmElements({ places, cacheDir, url: overpassUrl, fetch, today, refresh, log });
    // A guess on an element that several of our places claim is no longer a guess worth writing.
    const results = resolveClaims(places.map((p) => ({ kind: p.kind, name: p.name, lat: p.lat, lon: p.lon, line: p.line, ...matchPlace(p, elements) })));

    // 2. The report (not in a dry run) and the table.
    const report = { date: isoDate(today), generatedAt: today.toISOString(), kinds, source: ATTRIBUTION, overpassCache: cachePath, results };
    const reportPath = join(reportsDir, `${reportStamp(today)}-names.json`);
    if (!dryRun) {
        mkdirSync(reportsDir, { recursive: true });
        writeFileSync(reportPath, JSON.stringify(report, null, 2) + '\n');
    }
    const rows = results.map((r) => [r.kind, r.name, r.nameJa ?? '-', r.element ?? '-', r.distance === null ? '-' : `${r.distance} m`, r.match]);
    log('\n' + formatTable(['kind', 'name', 'Japanese name', 'source element', 'distance', 'match'], rows));
    const count = (match) => results.filter((r) => r.match === match).length;
    log(`\nmatched: ${count('matched')}, nearest: ${count('nearest')}, none: ${count('none')}. Names ${ATTRIBUTION}.`);
    if (dryRun) {
        log('Dry run: no report, no seeder written.');
        return { report, reportPath: null, confirmed: [], changedFiles: [] };
    }
    log(`Report written to ${reportPath}`);

    // 3. Which names to keep. --yes keeps the checked group only (the English matches).
    const groups = GROUPS.map((g) => ({ ...g, results: results.filter((r) => r.match === g.match) })).filter((g) => g.results.length);
    const proposed = groups.flatMap((g) => g.results);
    const none = results.filter((r) => r.match === 'none');
    if (none.length) {
        log(`\n${none.length} places without a name found (fill them by hand, with Google Maps open):`);
        for (const r of none) log(`  ${r.kind.padEnd(11)} ${r.name}: ${r.reason}`);
    }
    const confirmed = !proposed.length ? [] : yes ? proposed.filter((r) => r.match === 'matched') : await prompt(groups);
    log(`${confirmed.length} of ${proposed.length} Japanese names confirmed.`);

    // 4. Prepare and verify every seeder in memory, then write them all and check the PHP syntax.
    const plans = Object.keys(KINDS)
        .map((kind) => [kind, confirmed.filter((r) => r.kind === kind)])
        .filter(([, list]) => list.length)
        .map(([kind, list]) => planSeederEdits(join(seedersDir, KINDS[kind]), kind,
            list.map((r) => ({ target: { name: r.name, lat: r.lat, lon: r.lon }, fields: { nameJa: r.nameJa } }))));
    const changedFiles = writePlans(plans, { run, log });
    if (!changedFiles.length) {
        log('No seeder changed.');
        return { report, reportPath, confirmed, changedFiles };
    }

    // 5. Show what changed and what to do next. Not pushed yet: Google's check comes first.
    const diff = run('git', ['diff', '--stat', '--', relative(repoRoot, seedersDir) || '.'], { cwd: repoRoot });
    if (diff?.status === 0 && diff.stdout.trim()) log('\n' + diff.stdout.trimEnd());
    log(`
Next steps:
  git diff source/database/seeders     # review every name
  npm run check -- --names             # Google's second opinion on every name_ja, before anything is pushed
Undo with: git checkout source/database/seeders`);
    return { report, reportPath, confirmed, changedFiles };
}

// Command line entry point.
async function main() {
    const { values } = parseArgs({
        options: {
            kind: { type: 'string' },
            limit: { type: 'string' },
            'dry-run': { type: 'boolean', default: false }, // print the table, write nothing
            yes: { type: 'boolean', default: false }, // no prompt: keep the English matches only
            refresh: { type: 'boolean', default: false }, // ignore today's Overpass cache
        },
    });
    loadEnv();
    await runNames({
        ...kindsAndLimit(values),
        overpassUrl: process.env.OVERPASS_URL || DEFAULT_OVERPASS_URL,
        dryRun: values['dry-run'],
        yes: values.yes,
        refresh: values.refresh,
    });
}

if (isMain(import.meta.url)) {
    main().catch((error) => {
        // Ctrl+C in the prompt: leave quietly, nothing was written.
        if (error.name === 'ExitPromptError') return console.log('Cancelled, nothing written.');
        console.error(error.message);
        process.exitCode = 1;
    });
}
