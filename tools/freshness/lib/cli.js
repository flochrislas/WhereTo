// Small helpers shared by check.js, apply.js and names.js.

import { spawnSync } from 'node:child_process';
import { readFileSync, writeFileSync, renameSync, rmSync } from 'node:fs';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { KINDS } from './seeders.js';

// The folder of the tool (tools/freshness/).
export const TOOL_DIR = fileURLToPath(new URL('..', import.meta.url));
// The root of the repository, two levels up.
export const REPO_ROOT = fileURLToPath(new URL('../../..', import.meta.url));
// Where check writes its reports and apply reads them.
export const DEFAULT_REPORTS_DIR = fileURLToPath(new URL('../reports/', import.meta.url));
// Where names keeps the Overpass answer of the day (gitignored).
export const DEFAULT_CACHE_DIR = fileURLToPath(new URL('../cache/', import.meta.url));

// Loads tools/freshness/.env into process.env, if the file exists (Node 22 reads it natively).
export function loadEnv() {
    try {
        process.loadEnvFile(fileURLToPath(new URL('../.env', import.meta.url)));
    } catch (error) {
        if (error.code !== 'ENOENT') throw error; // no .env file: fine, the variable may be set otherwise
    }
}

// True when the module whose import.meta.url is given is the script run by `node`, false when
// it is imported (by the tests). Same idea as Python's `if __name__ == '__main__'`.
export function isMain(moduleUrl) {
    return process.argv[1] !== undefined && moduleUrl === pathToFileURL(process.argv[1]).href;
}

// Today's date as YYYY-MM-DD, in local time (toISOString would give the UTC date).
export function isoDate(date = new Date()) {
    const pad = (n) => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

// The date and time of a report file name: 2026-11-18T0930 (local time, so the names sort in time order).
export function reportStamp(date) {
    const pad = (n) => String(n).padStart(2, '0');
    return `${isoDate(date)}T${pad(date.getHours())}${pad(date.getMinutes())}`;
}

// Drops the second copy of a place written twice in a seeder (same kind, name and coordinates).
export function uniquePlaces(places) {
    const seen = new Set();
    return places.filter((p) => {
        const key = `${p.kind}|${p.name}|${p.lat}|${p.lon}`;
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
    });
}

// Reads the --kind and --limit options shared by check and names. Returns { kinds, limit }.
export function kindsAndLimit(values) {
    if (values.kind && !(values.kind in KINDS)) {
        throw new Error(`--kind must be one of: ${Object.keys(KINDS).join(', ')}`);
    }
    const limit = values.limit === undefined ? Infinity : Number(values.limit);
    if (!Number.isInteger(limit) && limit !== Infinity) throw new Error('--limit must be a whole number');
    return { kinds: values.kind ? [values.kind] : Object.keys(KINDS), limit };
}

// The real checkbox prompt. `groups` is [{ label, checked, results }]; `describe(result)` gives the
// text of a line. Returns the results ticked. Imported only when needed, so the tests (and --yes)
// never load the terminal library.
export async function checkboxPrompt(groups, { message, describe }) {
    const { default: checkbox, Separator } = await import('@inquirer/checkbox');
    const choices = groups.flatMap((g) => [
        new Separator(`--- ${g.label} (${g.results.length}) ---`),
        ...g.results.map((r) => ({ name: describe(r), value: r, checked: g.checked })),
    ]);
    return checkbox({ message, choices, pageSize: 25, loop: false });
}

// Runs a command; returns null when the program is not installed, the spawnSync result otherwise.
export function runCommand(command, args, options = {}) {
    const result = spawnSync(command, args, { encoding: 'utf8', ...options });
    if (result.error?.code === 'ENOENT') return null;
    if (result.error) throw result.error;
    return result;
}

// Writes the seeder files prepared (and verified) by planSeederEdits, logs each one, then runs
// `php -l` on them when PHP is installed. Returns the paths written. `run` is runCommand or a fake.
// The seeder on disk no longer is the text a plan was made from (saved in a text editor meanwhile).
export class SeederChangedError extends Error {}

// Writes one plan ({ filePath, before, after, changed }) safely: the new text goes to a temporary
// sibling file, `php -l` checks that file (when php is installed), and only then it is renamed over
// the seeder. A lint failure, or a seeder changed on disk since it was read (checked before the
// lint and again just before the rename), leaves the seeder byte for byte as it was.
// Returns true when the file was written, false when the plan had nothing to change.
export function writePlanSafely(plan, run) {
    if (!plan.changed) return false;
    const unchangedOnDisk = () => readFileSync(plan.filePath, 'utf8') === plan.before;
    const changed = () => new SeederChangedError(`${plan.filePath} changed on disk since it was read: nothing was written, read it again`);
    if (!unchangedOnDisk()) throw changed();
    const temporary = `${plan.filePath}.${process.pid}.tmp`;
    writeFileSync(temporary, plan.after);
    try {
        const result = run('php', ['-l', temporary]); // null: php is not installed here
        if (result && result.status !== 0) {
            throw new Error(`php -l failed on ${plan.filePath}:\n${result.stdout}${result.stderr}\nNothing was written.`);
        }
        if (!unchangedOnDisk()) throw changed();
        renameSync(temporary, plan.filePath);
    } finally {
        rmSync(temporary, { force: true }); // gone already after a rename
    }
    return true;
}

// Writes the plans that changed, one file at a time; logs each file. Returns the files written.
export function writePlans(plans, { run, log }) {
    const changedFiles = [];
    for (const plan of plans) {
        if (!writePlanSafely(plan, run)) continue;
        changedFiles.push(plan.filePath);
        log(`Written: ${plan.filePath}`);
    }
    return changedFiles;
}

// Formats rows (arrays of strings) as a text table with aligned columns.
export function formatTable(header, rows) {
    const all = [header, ...rows].map((row) => row.map((cell) => String(cell ?? '')));
    const widths = header.map((_, i) => Math.max(...all.map((row) => row[i].length)));
    const line = (row) => row.map((cell, i) => cell.padEnd(widths[i])).join('  ').trimEnd();
    return [line(all[0]), widths.map((w) => '-'.repeat(w)).join('  '), ...all.slice(1).map(line)].join('\n');
}
