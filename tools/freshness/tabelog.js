// npm run tabelog [-- reports/2026-11-19T2001-all.json]
//
// A second opinion for the places Google was unsure about or could not find: reads their Tabelog
// page (the tabelog_link of the seeder, restaurants and bars) and, when Tabelog says the place is
// closed, changes the verdict to closed_permanently or closed_temporarily. Writes a new report,
// reports/<now>-<kinds>+tabelog.json, next to the input; apply and the editor take it as any
// check report. Nothing is written to the seeders: that is apply.js.

import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { basename, join } from 'node:path';
import { parseArgs } from 'node:util';
import { readAllSeeders, KINDS, DEFAULT_SEEDERS_DIR } from './lib/seeders.js';
import { fetchTabelogStatus, tabelogVerdict, tabelogJapaneseLink } from './lib/tabelog.js';
import { latestReport } from './apply.js';
import { isMain, reportStamp, formatTable, DEFAULT_REPORTS_DIR } from './lib/cli.js';

// The verdicts that get a second opinion from Tabelog.
const FLAGGED = ['unsure', 'not_found_nearby'];

// The order of the statuses in the table and the counts.
const STATUS_ORDER = ['closed', 'temporarily_closed', 'moved', 'open', 'error'];

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

// A place of the seeders, by kind, name and coordinates: how a report row names a place.
const placeKey = (p) => `${p.kind}|${p.name}|${p.lat}|${p.lon}`;

// Runs the pass. Every dependency is a parameter, so the tests can run it on a copy of a fixture
// seeder with a fake fetch. Returns { report, reportPath }.
export async function runTabelog({
    reportPath,
    reportsDir = DEFAULT_REPORTS_DIR,
    seedersDir = DEFAULT_SEEDERS_DIR,
    fetch = globalThis.fetch,
    minIntervalMs = 1000, // one page a second: a polite pace for a site that did not ask for us
    today = new Date(),
    log = console.log,
}) {
    reportPath ??= latestReport(reportsDir);
    const input = JSON.parse(readFileSync(reportPath, 'utf8'));
    if (input.source) throw new Error(`${reportPath} is a names report: give a check report (npm run check)`);
    log(`Report: ${reportPath} (${input.results.length} places checked on ${input.date})`);

    // The Tabelog links come from the seeders of the kinds the report covers, not from the report.
    const kinds = (input.kinds ?? Object.keys(KINDS)).filter((k) => k in KINDS);
    const links = new Map(readAllSeeders(seedersDir, kinds).map((p) => [placeKey(p), p.tabelogLink]));

    const flagged = input.results.filter((r) => FLAGGED.includes(r.verdict));
    const toCheck = flagged.filter((r) => links.get(placeKey(r)));
    const missing = flagged.filter((r) => !links.has(placeKey(r))).length;
    log(`${flagged.length} places are unsure or not found nearby; ${toCheck.length} have a Tabelog link. Reading their pages, one a second...`);

    const results = [];
    let checked = 0;
    for (const r of input.results) {
        const link = links.get(placeKey(r)) ?? null;
        if (!FLAGGED.includes(r.verdict) || !link) {
            // A report that already went through this pass keeps what Tabelog said then: the link
            // and the status behind a closed verdict.
            results.push({ ...r, tabelog: r.tabelog ?? null });
            continue;
        }
        if (checked > 0) await sleep(minIntervalMs);
        checked += 1;
        let tabelog;
        try {
            // The Japanese page, whatever the language of the link: its wording is the one we know.
            tabelog = { link, status: await fetchTabelogStatus(tabelogJapaneseLink(link) ?? link, { fetch }) };
        } catch (e) {
            tabelog = { link, status: 'error', error: e.message }; // one bad page: note it and go on
        }
        const change = tabelogVerdict(tabelog.status);
        results.push({
            ...r,
            ...(change ? { verdict: change.verdict, reason: `${change.reason}; Google: ${r.reason}` } : {}),
            tabelog,
        });
        if (checked % 10 === 0) log(`  ${checked} / ${toCheck.length}`);
    }

    // The new report: the input, its rows with the Tabelog field, and where it comes from. The
    // date of the Google check stays (apply prints it); the file name is stamped now.
    const kindsLabel = basename(reportPath).match(/^\d{4}-\d{2}-\d{2}T\d{4}-([a-z]+)/)?.[1] ?? 'all';
    const outPath = join(reportsDir, `${reportStamp(today)}-${kindsLabel}+tabelog.json`);
    const report = {
        ...input,
        tabelog: { from: basename(reportPath), checkedAt: today.toISOString(), checked },
        results,
    };
    mkdirSync(reportsDir, { recursive: true });
    writeFileSync(outPath, JSON.stringify(report, null, 2) + '\n');

    printSummary(results, flagged.length, missing, log);
    log(`\nReport written to ${outPath}`);
    log('Next: npm run apply (the closed places are ticked), or load it in npm run edit');
    return { report, reportPath: outPath };
}

// The table of the places read on Tabelog, by status, and the counts.
function printSummary(results, flaggedCount, missing, log) {
    const read = results.filter((r) => r.tabelog).toSorted((a, b) => STATUS_ORDER.indexOf(a.tabelog.status) - STATUS_ORDER.indexOf(b.tabelog.status));
    if (read.length) {
        const rows = read.map((r) => [r.kind, r.name, r.tabelog.status, r.verdict, r.tabelog.error ?? r.tabelog.link]);
        log('\n' + formatTable(['kind', 'name', 'Tabelog', 'verdict', 'link'], rows));
    }
    const count = (status) => read.filter((r) => r.tabelog.status === status).length;
    log(`\n${read.length} checked on Tabelog: ${count('closed')} closed, ${count('temporarily_closed')} temporarily closed, ${count('moved')} moved, ${count('open')} open, ${count('error')} error`);
    const noLink = flaggedCount - read.length - missing;
    if (noLink) log(`${noLink} of ${flaggedCount} flagged places had no Tabelog link (stores and karaokes have none): look by hand.`);
    if (missing) log(`${missing} place${missing === 1 ? ' of the report is' : 's of the report are'} not in the seeders any more (renamed or moved since the check): left as they are.`);
}

// Command line entry point.
if (isMain(import.meta.url)) {
    const { positionals } = parseArgs({ allowPositionals: true }); // the report file, optional
    runTabelog({ reportPath: positionals[0] }).catch((e) => {
        console.error(e.message);
        process.exit(1);
    });
}
