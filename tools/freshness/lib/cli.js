// Small helpers shared by check.js and apply.js.

import { fileURLToPath, pathToFileURL } from 'node:url';

// The folder of the tool (tools/freshness/).
export const TOOL_DIR = fileURLToPath(new URL('..', import.meta.url));
// The root of the repository, two levels up.
export const REPO_ROOT = fileURLToPath(new URL('../../..', import.meta.url));
// Where check writes its reports and apply reads them.
export const DEFAULT_REPORTS_DIR = fileURLToPath(new URL('../reports/', import.meta.url));

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

// Formats rows (arrays of strings) as a text table with aligned columns.
export function formatTable(header, rows) {
    const all = [header, ...rows].map((row) => row.map((cell) => String(cell ?? '')));
    const widths = header.map((_, i) => Math.max(...all.map((row) => row[i].length)));
    const line = (row) => row.map((cell, i) => cell.padEnd(widths[i])).join('  ').trimEnd();
    return [line(all[0]), widths.map((w) => '-'.repeat(w)).join('  '), ...all.slice(1).map(line)].join('\n');
}
