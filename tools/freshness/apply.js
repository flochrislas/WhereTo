// npm run apply [-- reports/2026-11-18T0930-all.json] [--yes] [--partial]
//
// Reads a report written by check.js (the latest one by default), asks which status changes to
// keep, and writes them into the seeders. Review the result with `git diff` before committing.

import { readdirSync, readFileSync } from 'node:fs';
import { join, relative, resolve } from 'node:path';
import { spawnSync } from 'node:child_process';
import { parseArgs } from 'node:util';
import { planSeederEdits, writeSeederEdits, KINDS, DEFAULT_SEEDERS_DIR } from './lib/seeders.js';
import { VERDICT_STATUS } from './lib/classify.js';
import { isMain, DEFAULT_REPORTS_DIR, REPO_ROOT } from './lib/cli.js';

// The order and wording of the groups in the prompt. `checked`: ticked by default, and kept by
// --yes. "Replaced" hides a place on a guess (another name on our spot), so it is opt-in.
const GROUPS = [
    { verdict: 'closed_permanently', label: 'Permanently closed', checked: true },
    { verdict: 'closed_temporarily', label: 'Temporarily closed', checked: true },
    { verdict: 'reopened', label: 'Reopened (was temporarily closed)', checked: true },
    { verdict: 'replaced', label: 'Replaced by another business (unchecked: tick only after a look on Maps)', checked: false },
];

// The latest report. The names start with the date and time (2026-11-18T0930-all.json), so the
// last in alphabetical order is the newest.
export function latestReport(reportsDir) {
    let files = [];
    try {
        files = readdirSync(reportsDir).filter((f) => /^\d{4}-\d{2}-\d{2}T\d{4}-[a-z+]+\.json$/.test(f)).sort();
    } catch (error) {
        if (error.code !== 'ENOENT') throw error;
    }
    if (!files.length) throw new Error(`No report in ${reportsDir}: run "npm run check" first`);
    return join(reportsDir, files.at(-1));
}

// One line of the prompt for a result: ours, then what Google has there.
function describe(r) {
    const google = r.google
        ? ` -> Google: "${r.google.name}", ${r.google.address ?? 'no address'} (${r.google.status ?? 'no status'}, ${r.distance} m)`
        : '';
    return `${r.kind.padEnd(11)} ${r.name}${google}`;
}

// The real prompt: a checkbox list, everything checked. Space unchecks, Enter confirms.
// Imported only when needed, so the tests (and --yes) never load the terminal library.
async function checkboxPrompt(groups) {
    const { default: checkbox, Separator } = await import('@inquirer/checkbox');
    const choices = groups.flatMap((g) => [
        new Separator(`--- ${g.label} (${g.results.length}) ---`),
        ...g.results.map((r) => ({ name: describe(r), value: r, checked: g.checked })),
    ]);
    return checkbox({
        message: 'Status changes to write into the seeders (space: uncheck, enter: confirm)',
        choices,
        pageSize: 25,
        loop: false,
    });
}

// Runs a command; returns null when the program is not installed, the spawnSync result otherwise.
function runCommand(command, args, options = {}) {
    const result = spawnSync(command, args, { encoding: 'utf8', ...options });
    if (result.error?.code === 'ENOENT') return null;
    if (result.error) throw result.error;
    return result;
}

// Runs apply. Every dependency is a parameter so the tests can fake the prompt and the commands.
// Returns { reportPath, confirmed, edits, changedFiles }.
export async function runApply({
    reportPath,
    reportsDir = DEFAULT_REPORTS_DIR,
    seedersDir = DEFAULT_SEEDERS_DIR,
    repoRoot = REPO_ROOT,
    yes = false,
    partial = false,
    prompt = checkboxPrompt,
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
    //    status the place already has (still temporarily closed) is no change.
    const isChange = (r) => r.verdict in VERDICT_STATUS && VERDICT_STATUS[r.verdict] !== (r.businessStatus ?? 'OPERATIONAL');
    const groups = GROUPS
        .map((g) => ({ ...g, results: report.results.filter((r) => r.verdict === g.verdict && isChange(r)) }))
        .filter((g) => g.results.length);
    const changes = groups.flatMap((g) => g.results);
    let confirmed = [];
    if (changes.length) {
        // --yes keeps the groups checked by default only (never "replaced").
        confirmed = yes ? groups.filter((g) => g.checked).flatMap((g) => g.results) : await prompt(groups);
    }
    log(`${confirmed.length} of ${changes.length} status changes confirmed.`);

    // 2. The edits, per kind. A Google id is only written when it is surely our place (idTrusted:
    //    exact name, or found by id) and the place is not replaced (the id would be the new business's).
    const edits = Object.fromEntries(Object.keys(KINDS).map((kind) => [kind, []]));
    const newId = (r) => (r.idTrusted && r.google?.id && r.google.id !== r.knownPlaceId ? r.google.id : undefined);
    for (const r of confirmed) {
        const fields = { businessStatus: VERDICT_STATUS[r.verdict] };
        if (r.verdict !== 'replaced' && newId(r)) fields.googlePlaceId = newId(r);
        edits[r.kind].push({ target: { name: r.name, lat: r.lat, lon: r.lon }, fields });
    }
    for (const r of report.results.filter((x) => x.verdict === 'operational' && newId(x))) {
        edits[r.kind].push({ target: { name: r.name, lat: r.lat, lon: r.lon }, fields: { googlePlaceId: newId(r) } });
    }

    // 3. Prepare and verify every file in memory first, so that an error leaves no file half done;
    //    then write them all, then check the PHP syntax.
    const plans = Object.entries(edits)
        .filter(([, list]) => list.length)
        .map(([kind, list]) => planSeederEdits(join(seedersDir, KINDS[kind]), kind, list))
        .filter((plan) => plan.changed);
    const changedFiles = [];
    for (const plan of plans) {
        writeSeederEdits(plan);
        changedFiles.push(plan.filePath);
        log(`Written: ${plan.filePath}`);
    }
    for (const file of changedFiles) {
        const result = run('php', ['-l', file]);
        if (result === null) break; // php is not installed here: skip the syntax check
        if (result.status !== 0) {
            throw new Error(`php -l failed on ${file}:\n${result.stdout}${result.stderr}\n`
                + 'The seeders were already written. Undo with: git checkout source/database/seeders');
        }
    }

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
        `${count('closed_permanently', 'closed_temporarily')} closed`,
        `${count('replaced')} replaced`,
        ...(count('reopened') ? [`${count('reopened')} reopened`] : []),
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
