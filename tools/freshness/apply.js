// npm run apply [-- reports/2026-11-18T0930-all.json] [--yes] [--partial]
//
// Reads a report written by check.js (the latest one by default), asks which status changes to
// keep, and writes them into the seeders. Review the result with `git diff` before committing.

import { readdirSync, readFileSync } from 'node:fs';
import { join, relative, resolve } from 'node:path';
import { parseArgs } from 'node:util';
import { planSeederEdits, KINDS, DEFAULT_SEEDERS_DIR } from './lib/seeders.js';
import { VERDICT_STATUS, SEARCH_RADIUS_METRES } from './lib/classify.js';
import { isMain, checkboxPrompt, runCommand, writePlans, DEFAULT_REPORTS_DIR, REPO_ROOT } from './lib/cli.js';

// The order and wording of the groups in the prompt. `checked`: ticked by default, and kept by
// --yes. `status`: the business_status a ticked place gets. "Replaced" hides a place on a guess
// (another name on our spot), and "not found nearby" closes it on an absence, so both are opt-in.
// `only`: the results of the verdict that go in the group (all of them without it). An open place
// is confirmed on our spot (within 50 m, or found by its id); our name 50 to 200 m away may be
// another branch of a chain while ours has closed, so that one is opt-in too.
// The report says whether the exact distance was within the radius; an older report only has the
// rounded distance, where 50 may be 50.4: that one is not taken as within.
const onOurSpot = (r) => r.idTrusted || (r.withinSearchRadius ?? (typeof r.distance === 'number' && r.distance < SEARCH_RADIUS_METRES));
const GROUPS = [
    { verdict: 'operational', only: onOurSpot, label: 'Confirmed open (business_status OPERATIONAL; a blank status means not checked)', checked: true },
    {
        verdict: 'operational',
        only: (r) => !onOurSpot(r),
        label: `Open under our name, but ${SEARCH_RADIUS_METRES} to 200 m away (unchecked: maybe another branch; tick only after a look on Maps)`,
        checked: false,
    },
    { verdict: 'closed_permanently', label: 'Permanently closed', checked: true },
    { verdict: 'closed_temporarily', label: 'Temporarily closed', checked: true },
    { verdict: 'reopened', label: 'Reopened (was temporarily closed)', checked: true },
    { verdict: 'replaced', label: 'Replaced by another business (unchecked: tick only after a look on Maps)', checked: false },
    {
        verdict: 'not_found_nearby',
        label: 'Not found within 200 m (unchecked: a ticked place is written permanently closed; look on Maps first)',
        checked: false,
        status: 'CLOSED_PERMANENTLY',
    },
].map((g) => ({ ...g, status: g.status ?? VERDICT_STATUS[g.verdict] }));

// The business_status a verdict writes when its place is ticked (undefined: not a status change).
const statusOf = (verdict) => GROUPS.find((g) => g.verdict === verdict)?.status;

// The latest report of check. The names start with the date and time (2026-11-18T0930-all.json), so
// the last in alphabetical order is the newest. The reports of names (...-names.json) are not for apply.
export function latestReport(reportsDir) {
    let files = [];
    try {
        files = readdirSync(reportsDir)
            .filter((f) => /^\d{4}-\d{2}-\d{2}T\d{4}-[a-z+]+\.json$/.test(f) && !f.endsWith('-names.json'))
            .sort();
    } catch (error) {
        if (error.code !== 'ENOENT') throw error;
    }
    if (!files.length) throw new Error(`No report in ${reportsDir}: run "npm run check" first`);
    return join(reportsDir, files.at(-1));
}

// One line of the prompt for a result: ours, then what Google has there. For a place not found
// nearby: what Google found instead, and our Google Maps link, to look at it.
export function describe(r) {
    if (r.verdict === 'not_found_nearby') {
        const instead = r.google ? `Google found "${r.google.name}" ${r.distance} m away instead` : 'Google found nothing';
        return `${r.kind.padEnd(11)} ${r.name} -> ${instead}; ${r.googleMapsLink ?? 'no Google Maps link'}`;
    }
    const google = r.google
        ? ` -> Google: "${r.google.name}", ${r.google.address ?? 'no address'} (${r.google.status ?? 'no status'}, ${r.distance} m)`
        : '';
    return `${r.kind.padEnd(11)} ${r.name}${google}`;
}

// The real prompt: a checkbox list, grouped by verdict. Space toggles, Enter confirms.
const statusPrompt = (groups) => checkboxPrompt(groups, {
    message: 'Status changes to write into the seeders (space: uncheck, enter: confirm)',
    describe,
});

// Runs apply. Every dependency is a parameter so the tests can fake the prompt and the commands.
// Returns { reportPath, confirmed, edits, changedFiles }.
export async function runApply({
    reportPath,
    reportsDir = DEFAULT_REPORTS_DIR,
    seedersDir = DEFAULT_SEEDERS_DIR,
    repoRoot = REPO_ROOT,
    yes = false,
    partial = false,
    prompt = statusPrompt,
    run = runCommand,
    log = console.log,
}) {
    reportPath ??= latestReport(reportsDir);
    const report = JSON.parse(readFileSync(reportPath, 'utf8'));
    if (report.partial && !partial) {
        throw new Error(`${reportPath} is a partial report (the check stopped: ${report.error}). Run the check again, or pass --partial to apply it anyway.`);
    }
    log(`Report: ${reportPath} (${report.results.length} places checked on ${report.date}${report.partial ? ', PARTIAL' : ''})`);

    // 1. The status changes, grouped by verdict, and which ones to keep. A verdict that gives the
    //    status the place already has (still temporarily closed, still open) is no change; an open
    //    place without a status line gets its OPERATIONAL written (confirmed, as against not checked).
    const isChange = (r) => statusOf(r.verdict) !== undefined && statusOf(r.verdict) !== r.businessStatus;
    const groups = GROUPS
        .map((g) => ({ ...g, results: report.results.filter((r) => r.verdict === g.verdict && (g.only?.(r) ?? true) && isChange(r)) }))
        .filter((g) => g.results.length);
    const changes = groups.flatMap((g) => g.results);
    let confirmed = [];
    if (changes.length) {
        // --yes keeps the groups checked by default only (never "replaced", "not found nearby", nor an
        // open place found farther than 50 m).
        confirmed = yes ? groups.filter((g) => g.checked).flatMap((g) => g.results) : await prompt(groups);
    }
    log(`${confirmed.length} of ${changes.length} status changes confirmed.`);

    // 2. The edits, per kind. A Google id is only written when it is surely our place (idTrusted:
    //    exact name, or found by id) and the place is not replaced (the id would be the new business's;
    //    a place not found nearby never has a trusted id either).
    const edits = Object.fromEntries(Object.keys(KINDS).map((kind) => [kind, []]));
    const newId = (r) => (r.idTrusted && r.google?.id && r.google.id !== r.knownPlaceId ? r.google.id : undefined);
    for (const r of confirmed) {
        const fields = { businessStatus: statusOf(r.verdict) };
        if (r.verdict !== 'replaced' && newId(r)) fields.googlePlaceId = newId(r);
        edits[r.kind].push({ target: { name: r.name, lat: r.lat, lon: r.lon }, fields });
    }
    // An operational place left unticked (or already OPERATIONAL) still gets its new id.
    for (const r of report.results.filter((x) => x.verdict === 'operational' && newId(x) && !confirmed.includes(x))) {
        edits[r.kind].push({ target: { name: r.name, lat: r.lat, lon: r.lon }, fields: { googlePlaceId: newId(r) } });
    }

    // 3. Prepare and verify every file in memory first, so that an error leaves no file half done;
    //    then write them all, then check the PHP syntax.
    const plans = Object.entries(edits)
        .filter(([, list]) => list.length)
        .map(([kind, list]) => planSeederEdits(join(seedersDir, KINDS[kind]), kind, list));
    const changedFiles = writePlans(plans, { run, log });

    const unsure = report.results.filter((r) => r.verdict === 'unsure');
    if (unsure.length) log(`${unsure.length} places are unsure (not changed): look at them by hand, see the report.`);

    if (!changedFiles.length) {
        log('The seeders are already up to date: nothing to do.');
        return { reportPath, confirmed, edits, changedFiles };
    }

    // 4. Show what changed and what to do next.
    const diff = run('git', ['diff', '--stat', '--', relative(repoRoot, seedersDir) || '.'], { cwd: repoRoot });
    if (diff?.status === 0 && diff.stdout.trim()) log('\n' + diff.stdout.trimEnd());
    const count = (...verdicts) => confirmed.filter((r) => verdicts.includes(r.verdict)).length;
    const summary = [
        `${count('closed_permanently', 'closed_temporarily', 'not_found_nearby')} closed`,
        `${count('replaced')} replaced`,
        ...(count('reopened') ? [`${count('reopened')} reopened`] : []),
        ...(count('operational') ? [`${count('operational')} confirmed open`] : []),
    ].join(', ');
    log(`
Next steps:
  git diff source/database/seeders                          # review every change
  cd dev && podman compose exec app php artisan db:seed     # see it in the local app
  git commit -am "Places checked on ${report.date}: ${summary}"
  git push                                                  # then deploy, which reseeds
Undo with: git checkout source/database/seeders`);
    return { reportPath, confirmed, edits, changedFiles };
}

// Command line entry point.
async function main() {
    const { values, positionals } = parseArgs({
        options: {
            yes: { type: 'boolean', default: false },
            partial: { type: 'boolean', default: false }, // accept a report from a check that stopped
        },
        allowPositionals: true, // the report file, optional
    });
    await runApply({ reportPath: positionals[0] && resolve(positionals[0]), yes: values.yes, partial: values.partial });
}

if (isMain(import.meta.url)) {
    main().catch((error) => {
        // Ctrl+C in the prompt: leave quietly, nothing was written.
        if (error.name === 'ExitPromptError') return console.log('Cancelled, nothing written.');
        console.error(error.message);
        process.exitCode = 1;
    });
}
