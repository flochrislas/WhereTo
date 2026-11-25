// npm run edit [-- --port 8020]
//
// A local page to edit the place seeders: lists the places, filters them, edits any field of one,
// and shows a report of check or names next to each row. Listens on 127.0.0.1 only. Every request
// reads the seeders again; a revision per file (SHA-1 of its text) refuses an edit made from a page
// that saw an older text. See doc/11-data-freshness.md, section 9.

import { createServer } from 'node:http';
import { readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { parseArgs } from 'node:util';
import { parseSeederFull, revisionOf, planPlaceEdit, KINDS, DEFAULT_SEEDERS_DIR } from './lib/seeders.js';
import { validateEdit, mapReport, knownKeys, reportKind, KEY_TYPES, reportLabel } from './lib/editor.js';
import { writePlanSafely, SeederChangedError, runCommand, isMain, DEFAULT_REPORTS_DIR, REPO_ROOT } from './lib/cli.js';

const DEFAULT_PAGE = fileURLToPath(new URL('./editor/index.html', import.meta.url));
const DEFAULT_PORT = 8020;
// The names of the reports of check and names: 2026-11-18T0930-all.json, 2026-11-19T2000-names.json.
const REPORT_RE = /^\d{4}-\d{2}-\d{2}T\d{4}-[a-z+]+\.json$/;

// An error with the HTTP status to answer.
class HttpError extends Error {
    constructor(status, message, extra = {}) {
        super(message);
        this.status = status;
        this.extra = extra;
    }
}

// Reads the JSON body of a request; 400 when it is not JSON or not an object.
async function readJson(request) {
    let text = '';
    for await (const chunk of request) text += chunk;
    try {
        const body = JSON.parse(text);
        if (!body || typeof body !== 'object' || Array.isArray(body)) throw new Error();
        return body;
    } catch {
        throw new HttpError(400, 'the body must be a JSON object');
    }
}

// Creates the server (not listening). seedersDir, reportsDir, repoRoot, run, write and pageFile can
// be replaced in the tests.
export function createEditorServer({
    seedersDir = DEFAULT_SEEDERS_DIR,
    reportsDir = DEFAULT_REPORTS_DIR,
    repoRoot = REPO_ROOT,
    run = runCommand,
    write = writePlanSafely,
    pageFile = DEFAULT_PAGE,
} = {}) {
    const seederFile = (kind) => join(seedersDir, KINDS[kind]);
    // One read per seeder and per operation: the places and the revision come from the same text,
    // so an index of the answer always belongs to the text of its revision.
    const readKind = (kind) => {
        const text = readFileSync(seederFile(kind), 'utf8');
        return { text, revision: revisionOf(text), places: parseSeederFull(text, kind) };
    };

    // The page is only for this machine: another Host, or another Origin on a request that writes,
    // is a web page open in the same browser trying its luck.
    function checkOrigin(request) {
        const host = request.headers.host ?? '';
        if (!/^(127\.0\.0\.1|localhost)(:\d+)?$/.test(host)) throw new HttpError(403, 'this page only answers on 127.0.0.1');
        const origin = request.headers.origin;
        if (origin !== undefined && origin !== `http://${host}`) throw new HttpError(403, 'refused: another origin');
    }

    const routes = {
        'GET /': () => ({ type: 'text/html; charset=utf-8', text: readFileSync(pageFile, 'utf8'), frame: false }),

        'GET /api/places': () => {
            const read = Object.keys(KINDS).map(readKind);
            const places = read.flatMap((r) => r.places);
            const revisions = Object.fromEntries(Object.keys(KINDS).map((kind, i) => [kind, read[i].revision]));
            const keys = Object.fromEntries(Object.keys(KINDS).map((kind) => [kind, knownKeys(kind, places)]));
            return { json: { places, revisions, keys, types: KEY_TYPES } };
        },

        'PATCH /api/places/:kind/:index': async (request, { kind, index }) => {
            if (!(kind in KINDS) || !/^\d+$/.test(index)) throw new HttpError(404, `no place ${kind}/${index}`);
            const body = await readJson(request);
            if (typeof body.revision !== 'string') throw new HttpError(400, 'the revision is missing');
            const edit = { fields: body.fields, tags: body.tags, lat: body.lat, lon: body.lon };
            try {
                validateEdit(edit);
            } catch (error) {
                throw new HttpError(400, error.message);
            }
            const file = seederFile(kind);
            const stale = () => new HttpError(409, 'the seeder changed on disk: reload the page', { revision: readKind(kind).revision });
            const current = readKind(kind); // the one text this edit is checked on and built from
            if (current.revision !== body.revision) throw stale();
            const position = Number(index);
            if (!current.places[position]) throw new HttpError(404, `no place ${kind}/${index}`);
            let plan;
            try {
                plan = planPlaceEdit(file, kind, position, edit, current.text);
                write(plan, run);
            } catch (error) {
                if (error instanceof SeederChangedError) throw stale();
                throw new HttpError(400, error.message);
            }
            // The answer describes the text written, not the file as it is now: an outside save right
            // after the write must make the page's next edit a 409, not a hit on a moved block.
            return { json: { place: parseSeederFull(plan.after, kind)[position], revision: revisionOf(plan.after) } };
        },

        'GET /api/reports': () => {
            let files = [];
            try {
                files = readdirSync(reportsDir);
            } catch (error) {
                if (error.code !== 'ENOENT') throw error; // no report yet: an empty list
            }
            const reports = files
                .filter((f) => REPORT_RE.test(f))
                .sort()
                .reverse()
                .map((file) => {
                    const report = JSON.parse(readFileSync(join(reportsDir, file), 'utf8'));
                    return { file, kind: reportKind(report), date: report.date, label: reportLabel(file, report) };
                });
            return { json: { reports } };
        },

        'GET /api/reports/:file': (request, { file }) => {
            if (!REPORT_RE.test(file)) throw new HttpError(404, 'no such report');
            let report;
            try {
                report = JSON.parse(readFileSync(join(reportsDir, file), 'utf8'));
            } catch {
                throw new HttpError(404, 'no such report');
            }
            const places = Object.keys(KINDS).flatMap((k) => readKind(k).places);
            const { kind, byId, unmatched } = mapReport(report, places);
            return { json: { report: { date: report.date, kinds: report.kinds, source: report.source ?? null }, kind, byId, unmatched } };
        },

        'GET /api/diff': () => {
            const result = run('git', ['diff', '--', 'source/database/seeders'], { cwd: repoRoot });
            return { type: 'text/plain; charset=utf-8', text: result?.stdout ?? '' };
        },
    };

    // Matches "PATCH /api/places/restaurants/3" against the routes; returns the handler and the parameters.
    function match(method, path) {
        for (const [pattern, handler] of Object.entries(routes)) {
            const [m, p] = pattern.split(' ');
            if (m !== method) continue;
            const names = [];
            const re = new RegExp('^' + p.replace(/:(\w+)/g, (_, name) => { names.push(name); return '([^/]+)'; }) + '$');
            const found = path.match(re);
            if (found) return { handler, params: Object.fromEntries(names.map((n, i) => [n, decodeURIComponent(found[i + 1])])) };
        }
        return null;
    }

    return createServer(async (request, response) => {
        const send = (status, type, text, frame = true) => {
            // The page must not be framed by another site, which could steer clicks onto its buttons.
            const noFrame = frame ? {} : { 'X-Frame-Options': 'DENY', 'Content-Security-Policy': "frame-ancestors 'none'" };
            response.writeHead(status, { 'Content-Type': type, 'Cache-Control': 'no-store', ...noFrame });
            response.end(text);
        };
        try {
            checkOrigin(request);
            const path = new URL(request.url, 'http://127.0.0.1').pathname; // a /../ is folded here: it cannot leave the route
            const found = match(request.method, path);
            if (!found) throw new HttpError(404, `no route ${request.method} ${path}`);
            const answer = await found.handler(request, found.params);
            if (answer.json !== undefined) send(200, 'application/json; charset=utf-8', JSON.stringify(answer.json));
            else send(200, answer.type, answer.text, answer.frame);
        } catch (error) {
            const status = error instanceof HttpError ? error.status : 500;
            send(status, 'application/json; charset=utf-8', JSON.stringify({ error: error.message, ...(error.extra ?? {}) }));
        }
    });
}

// Command line entry point.
function main() {
    const { values } = parseArgs({ options: { port: { type: 'string', default: String(DEFAULT_PORT) } } });
    const port = Number(values.port);
    if (!Number.isInteger(port) || port <= 0) throw new Error('--port must be a whole number');
    const server = createEditorServer();
    server.listen(port, '127.0.0.1', () => {
        console.log(`Seeder editor: http://127.0.0.1:${port}/  (Ctrl+C to stop)`);
    });
}

if (isMain(import.meta.url)) {
    try {
        main();
    } catch (error) {
        console.error(error.message);
        process.exitCode = 1;
    }
}
