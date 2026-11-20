import { test, after } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdtempSync, mkdirSync, cpSync, rmSync, copyFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { request as httpRequest } from 'node:http';
import { createEditorServer } from '../edit.js';
import { parseSeederFull, revisionOf } from '../lib/seeders.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;
const ok = () => ({ status: 0, stdout: 'No syntax errors', stderr: '' });

// A workspace: fixtures copied to a temporary folder (the karaoke fixture stands in for the stores
// and the bars, which have no fixture), a reports folder, a server on a free port.
async function workspace({ run = ok, write } = {}) {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-'));
    const seedersDir = join(dir, 'seeders');
    const reportsDir = join(dir, 'reports');
    cpSync(FIXTURES, seedersDir, { recursive: true });
    for (const kind of ['Stores', 'Bars']) copyFileSync(join(seedersDir, 'KaraokesTableSeeder.php'), join(seedersDir, `${kind}TableSeeder.php`));
    mkdirSync(reportsDir);
    const pageFile = join(dir, 'index.html');
    writeFileSync(pageFile, '<!doctype html><title>editor</title>');
    const server = createEditorServer({ seedersDir, reportsDir, repoRoot: dir, run, pageFile, ...(write ? { write } : {}) });
    await new Promise((resolve) => server.listen(0, '127.0.0.1', resolve));
    const base = `http://127.0.0.1:${server.address().port}`;
    after(() => server.close());
    const api = async (path, options = {}) => {
        const response = await fetch(base + path, {
            ...options,
            headers: { 'Content-Type': 'application/json', ...(options.headers ?? {}) },
            body: options.body === undefined ? undefined : JSON.stringify(options.body),
        });
        const text = await response.text();
        let body;
        try { body = JSON.parse(text); } catch { body = text; }
        return { status: response.status, body };
    };
    const seederFile = join(seedersDir, 'RestaurantsTableSeeder.php');
    const revision = () => revisionOf(readFileSync(seederFile, 'utf8'));
    return { dir, seedersDir, reportsDir, seederFile, base, port: server.address().port, api, revision };
}

test('serves the page and lists the places with a revision and the known keys per kind', async () => {
    const ws = await workspace();
    const page = await ws.api('/');
    assert.equal(page.status, 200);
    assert.match(page.body, /<title>editor<\/title>/);
    const { status, body } = await ws.api('/api/places');
    assert.equal(status, 200);
    assert.equal(body.places.length, 13 + 3 * 3);
    assert.equal(body.places[0].id, 'restaurants/0');
    assert.equal(body.revisions.restaurants, ws.revision());
    assert.deepEqual(body.keys.restaurants.slice(0, 4), ['name', 'name_ja', 'business_status', 'google_place_id']);
    // The page takes the type of each key from the server: one table, no drift.
    assert.deepEqual(body.types.location, { type: 'text', nullable: false });
    assert.deepEqual(body.types.lunch_price, { type: 'number', nullable: true });
});

test('the page cannot be framed by another site', async () => {
    const ws = await workspace();
    const response = await fetch(`${ws.base}/`);
    assert.equal(response.headers.get('x-frame-options'), 'DENY');
    assert.equal(response.headers.get('content-security-policy'), "frame-ancestors 'none'");
});

test('an edit with the current revision is written, read back and answered with the new revision', async () => {
    const ws = await workspace();
    const { status, body } = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: ws.revision(), fields: { name_ja: '博多風龍', lunch_price: 950 }, tags: ['ramen'] } });
    assert.equal(status, 200);
    assert.equal(body.place.id, 'restaurants/0');
    assert.equal(body.place.fields.find((f) => f.key === 'name_ja').value, '博多風龍');
    assert.deepEqual(body.place.tags, ['ramen']);
    assert.equal(body.revision, ws.revision());
    assert.ok(readFileSync(ws.seederFile, 'utf8').includes("'name_ja' => '博多風龍',"));
    // The next edit uses the revision just returned.
    const second = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: body.revision, lat: 35.1, lon: 139.1 } });
    assert.equal(second.status, 200);
    assert.deepEqual([second.body.place.lat, second.body.place.lon], [35.1, 139.1]);
});

test('the answer of an edit describes the text written, not a text changed outside right after it', async () => {
    // The write succeeds, then someone saves the file before the server answers: the answer must carry
    // the revision of what was written, so that the next edit is refused (409) instead of landing on a
    // block of the outside text.
    const write = (plan) => { writeFileSync(plan.filePath, plan.after + '\n// outside edit\n'); return true; };
    const ws = await workspace({ write });
    const { status, body } = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: ws.revision(), fields: { location: 'Moved' } } });
    assert.equal(status, 200);
    assert.equal(body.place.fields.find((f) => f.key === 'location').value, 'Moved');
    assert.notEqual(body.revision, ws.revision()); // the disk moved on after the write
    const next = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: body.revision, fields: { location: 'Again' } } });
    assert.equal(next.status, 409);
});

test('a stale revision answers 409 and writes nothing', async () => {
    const ws = await workspace();
    const before = readFileSync(ws.seederFile, 'utf8');
    const { status, body } = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: 'stale', fields: { name: 'X' } } });
    assert.equal(status, 409);
    assert.match(body.error, /changed on disk|reload/i);
    assert.equal(body.revision, ws.revision());
    assert.equal(readFileSync(ws.seederFile, 'utf8'), before);
});

test('a block inserted on disk between the load and the edit is detected: neither place changes', async () => {
    const ws = await workspace();
    const loaded = ws.revision();
    // Someone pastes a new block at the top of the file in a text editor.
    const text = readFileSync(ws.seederFile, 'utf8');
    const marker = "fillCoordinatesFromString('35.658545, 139.698468');";
    const block = text.slice(text.indexOf('        $resto = Restaurant::create(['), text.indexOf(marker) + marker.length);
    writeFileSync(ws.seederFile, text.replace(block, block.replace("'Hakata Furyu'", "'Inserted'") + '\n\n' + block));
    const inserted = readFileSync(ws.seederFile, 'utf8');
    const { status } = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: loaded, fields: { business_status: 'CLOSED_PERMANENTLY' } } });
    assert.equal(status, 409);
    assert.equal(readFileSync(ws.seederFile, 'utf8'), inserted);
    const places = parseSeederFull(inserted, 'restaurants');
    assert.ok(!places.some((p) => p.index < 2 && p.fields.some((f) => f.key === 'business_status')));
});

test('a file saved during the lint is kept and answered 409', async () => {
    let seederFile;
    const run = () => { writeFileSync(seederFile, 'saved during the lint\n'); return ok(); };
    const ws = await workspace({ run });
    seederFile = ws.seederFile;
    const { status } = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: ws.revision(), fields: { name: 'X' } } });
    assert.equal(status, 409);
    assert.equal(readFileSync(seederFile, 'utf8'), 'saved during the lint\n');
});

test('bad requests answer 400 or 404 with a message and write nothing', async () => {
    const ws = await workspace();
    const before = readFileSync(ws.seederFile, 'utf8');
    const patch = (path, body) => ws.api(path, { method: 'PATCH', body });
    assert.equal((await patch('/api/places/restaurants/0', { revision: ws.revision(), fields: { name: '' } })).status, 400);
    assert.equal((await patch('/api/places/restaurants/0', { revision: ws.revision(), fields: { "'); DROP": 'x' } })).status, 400);
    assert.equal((await patch('/api/places/restaurants/0', { revision: ws.revision(), fields: { lunch_price: '900' } })).status, 400);
    assert.equal((await patch('/api/places/restaurants/0', { fields: { name: 'X' } })).status, 400); // no revision
    assert.equal((await patch('/api/places/restaurants/99', { revision: ws.revision(), fields: { name: 'X' } })).status, 404);
    assert.equal((await patch('/api/places/clubs/0', { revision: ws.revision(), fields: { name: 'X' } })).status, 404);
    const notJson = await fetch(`${ws.base}/api/places/restaurants/0`, { method: 'PATCH', headers: { 'Content-Type': 'application/json' }, body: '{not json' });
    assert.equal(notJson.status, 400);
    assert.equal((await ws.api('/api/nothing')).status, 404);
    assert.equal(readFileSync(ws.seederFile, 'utf8'), before);
});

test('a php -l failure answers 400 with the message and leaves the file', async () => {
    const ws = await workspace({ run: () => ({ status: 255, stdout: 'PHP Parse error', stderr: '' }) });
    const before = readFileSync(ws.seederFile, 'utf8');
    const { status, body } = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: ws.revision(), fields: { name: 'X' } } });
    assert.equal(status, 400);
    assert.match(body.error, /Parse error/);
    assert.equal(readFileSync(ws.seederFile, 'utf8'), before);
});

test('a missing reports folder is an empty list, and the editor still edits', async () => {
    const ws = await workspace();
    rmSync(ws.reportsDir, { recursive: true });
    const list = await ws.api('/api/reports');
    assert.equal(list.status, 200);
    assert.deepEqual(list.body.reports, []);
    const { status } = await ws.api('/api/places/restaurants/0', { method: 'PATCH', body: { revision: ws.revision(), fields: { location: 'Moved' } } });
    assert.equal(status, 200);
});

test('the listing answers the places and the revisions of every kind together', async () => {
    const ws = await workspace();
    const { body } = await ws.api('/api/places');
    assert.deepEqual(Object.keys(body.revisions).sort(), ['bars', 'karaokes', 'restaurants', 'stores']);
    assert.equal(body.revisions.restaurants, ws.revision());
});

// A raw request, for a Host header or a path that fetch would not send as given.
const raw = (port, options, body) => new Promise((resolve, reject) => {
    const req = httpRequest({ host: '127.0.0.1', port, ...options }, (res) => { res.resume(); res.on('end', () => resolve(res.statusCode)); });
    req.on('error', reject);
    req.end(body);
});

test('lists the reports newest first and maps one to the places', async () => {
    const ws = await workspace();
    const result = { kind: 'restaurants', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468, verdict: 'closed_permanently', google: { id: 'ChIJx', name: 'Furyu', address: 'Tokyo', status: 'CLOSED_PERMANENTLY' }, distance: 2 };
    writeFileSync(join(ws.reportsDir, '2026-11-18T0930-all.json'), JSON.stringify({ date: '2026-11-18', results: [result] }));
    writeFileSync(join(ws.reportsDir, '2026-11-19T2000-names.json'), JSON.stringify({ date: '2026-11-19', source: 'OpenStreetMap', results: [{ ...result, match: 'matched', nameJa: '博多風龍', element: 'node/1' }] }));
    writeFileSync(join(ws.reportsDir, 'notes.txt'), 'not a report');
    const list = await ws.api('/api/reports');
    assert.deepEqual(list.body.reports, [
        { file: '2026-11-19T2000-names.json', kind: 'names', date: '2026-11-19' },
        { file: '2026-11-18T0930-all.json', kind: 'check', date: '2026-11-18' },
    ]);
    const mapped = await ws.api('/api/reports/2026-11-18T0930-all.json');
    assert.equal(mapped.status, 200);
    assert.equal(mapped.body.kind, 'check');
    assert.equal(mapped.body.byId['restaurants/0'].verdict, 'closed_permanently');
    assert.deepEqual(mapped.body.unmatched, []);
    assert.equal((await ws.api('/api/reports/..%2Fpackage.json')).status, 404); // fetch would fold a plain ../ before sending
    assert.equal(await raw(ws.port, { path: '/api/reports/../package.json' }), 404);
    assert.equal((await ws.api('/api/reports/missing.json')).status, 404);
});

test('regression: a renamed and moved place keeps its block, and a report button still writes to it', async () => {
    const ws = await workspace();
    const result = { kind: 'restaurants', name: 'Go Go Curry', lat: 35.657333, lon: 139.703654, verdict: 'replaced', google: { id: 'ChIJreplaced' }, distance: 3 };
    writeFileSync(join(ws.reportsDir, '2026-11-18T0930-all.json'), JSON.stringify({ date: '2026-11-18', results: [result] }));
    const mapped = (await ws.api('/api/reports/2026-11-18T0930-all.json')).body;
    assert.equal(mapped.byId['restaurants/2'].verdict, 'replaced');

    const patch = (body) => ws.api('/api/places/restaurants/2', { method: 'PATCH', body });
    const renamed = await patch({ revision: ws.revision(), fields: { name: 'Go! Go! Curry' } });
    assert.equal(renamed.status, 200);
    const moved = await patch({ revision: renamed.body.revision, lat: 35.6574, lon: 139.7037 });
    assert.equal(moved.status, 200);
    // The "Replaced" button of the row, built from the mapping made at load time, targets the id.
    const closed = await patch({ revision: moved.body.revision, fields: { business_status: 'REPLACED' } });
    assert.equal(closed.status, 200);

    const places = parseSeederFull(readFileSync(ws.seederFile, 'utf8'), 'restaurants');
    const block = places[2];
    assert.equal(block.name, 'Go! Go! Curry');
    assert.deepEqual([block.lat, block.lon], [35.6574, 139.7037]);
    assert.equal(block.fields.find((f) => f.key === 'business_status').value, 'REPLACED');
    assert.ok(!places.some((p, i) => i !== 2 && p.fields.some((f) => f.key === 'business_status' && f.value === 'REPLACED')));
    // The same report loaded again no longer finds the renamed place.
    const again = (await ws.api('/api/reports/2026-11-18T0930-all.json')).body;
    assert.equal(again.byId['restaurants/2'], undefined);
    assert.equal(again.unmatched.length, 1);
});

test('refuses another host or origin', async () => {
    const ws = await workspace();
    assert.equal(await raw(ws.port, { path: '/api/places', headers: { Host: 'evil.example' } }), 403);
    const body = JSON.stringify({ revision: ws.revision(), fields: { name: 'X' } });
    assert.equal(await raw(ws.port, { method: 'PATCH', path: '/api/places/restaurants/0', headers: { 'Content-Type': 'application/json', Origin: 'http://evil.example' } }, body), 403);
    const sameOrigin = await fetch(`${ws.base}/api/places`, { headers: { Origin: ws.base } });
    assert.equal(sameOrigin.status, 200);
});

test('the diff route runs git diff on the seeders', async () => {
    const ran = [];
    const run = (command, args, options) => { ran.push([command, args, options?.cwd]); return command === 'git' ? { status: 0, stdout: 'diff --git a/x', stderr: '' } : ok(); };
    const ws = await workspace({ run });
    const { status, body } = await ws.api('/api/diff');
    assert.equal(status, 200);
    assert.equal(body, 'diff --git a/x');
    assert.deepEqual(ran.at(-1), ['git', ['diff', '--', 'source/database/seeders'], ws.dir]);
});
