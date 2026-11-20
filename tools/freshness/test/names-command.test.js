// names.js end to end, on a temporary copy of the fixture seeders, with a fake Overpass server.
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdtempSync, cpSync, existsSync, readdirSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { runNames, describe } from '../names.js';
import { latestReport } from '../apply.js';
import { readSeeder } from '../lib/seeders.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;
const TODAY = new Date(2026, 10, 18, 9, 30); // 18 November 2026 (months count from 0)

// An Overpass node `north` metres north of the given coordinates.
const node = (id, tags, lat, lon, north = 0) => ({ type: 'node', id, lat: lat + north / 111195, lon, tags: { amenity: 'restaurant', ...tags } });

// What the fake Overpass server knows around the fixture restaurants.
const ELEMENTS = [
    node(1, { name: '博多風龍', 'name:en': 'Hakata Furyu' }, 35.658545, 139.698468, 10), // matched, exact
    { type: 'way', id: 2, center: { lat: 35.657333, lon: 139.703654 }, tags: { shop: 'deli', name: 'ゴーゴーカレー 渋谷店', 'name:en': 'Go Go Curry Shibuya' } }, // matched, a way
    node(3, { name: 'スパゲッティーのパンチョ' }, 35.6605, 139.6985, 5), // nearest: alone on the spot
    node(4, { name: 'うさぎ', 'name:en': 'Usagi' }, 35.662, 139.7005, 3), // matched: both copies of Usagi
    node(5, { name: 'タイ料理' }, 35.6578, 139.7012, 4), // Krung Siam: two candidates on the spot
    node(6, { name: 'ソムタム' }, 35.6578, 139.7012, 9),
    node(7, { name: 'Il Ritrovo' }, 35.6601, 139.6971, 4), // Latin only: no Japanese name
    node(8, { name: '天狗酒場', 'name:en': 'Tengu Sakaba' }, 35.6588, 139.6978, 2), // the temporarily closed one
    node(9, { name: '金田丸' }, 35.655166, 139.700365, 1), // Kindenmaru is permanently closed: never offered
];

// A fake Overpass that answers `elements` (ELEMENTS by default) and counts the calls.
function fakeOverpass(elements = ELEMENTS) {
    const server = { calls: 0 };
    server.fetch = async () => {
        server.calls++;
        return new Response(JSON.stringify({ osm3s: { copyright: 'ODbL' }, elements }), { status: 200 });
    };
    return server;
}

// A fresh temporary seeders, reports and cache folder.
function workspace() {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-names-'));
    const seedersDir = join(dir, 'seeders');
    cpSync(FIXTURES, seedersDir, { recursive: true });
    return { seedersDir, reportsDir: join(dir, 'reports'), cacheDir: join(dir, 'cache') };
}

// Runs names on the restaurants of a workspace, silently, with php and git "not installed".
async function names(ws, server, extra = {}) {
    return runNames({ fetch: server.fetch, today: TODAY, kinds: ['restaurants'], run: () => null, log: () => {}, ...ws, ...extra });
}

test('names matches the open and temporarily closed places without name_ja, writes the report, and --yes writes the matched ones only', async () => {
    const ws = workspace();
    const server = fakeOverpass();
    const { report, reportPath, changedFiles } = await names(ws, server, { yes: true });
    assert.equal(server.calls, 1);
    assert.equal(reportPath, join(ws.reportsDir, '2026-11-18T0930-names.json'));
    assert.deepEqual(JSON.parse(readFileSync(reportPath, 'utf8')), report);
    assert.match(report.source, /© OpenStreetMap contributors/);

    // 13 blocks, minus the permanently closed one (Kindenmaru), minus the second Usagi.
    // The temporarily closed one (Tengu Sakaba) is looked up: its pages stay indexable.
    const match = Object.fromEntries(report.results.map((r) => [`${r.name}@${r.lat}`, [r.match, r.nameJa, r.element]]));
    assert.deepEqual(match, {
        'Hakata Furyu@35.658545': ['matched', '博多風龍', 'node/1'],
        'Go Go Curry@35.657333': ['matched', 'ゴーゴーカレー 渋谷店', 'way/2'],
        "Mc Donald's@35.659": ['none', null, null],
        "Mc Donald's@35.661": ['none', null, null],
        'Il Ritrovo@35.6601': ['none', null, null],
        'Bistro Shibuya@35.656': ['none', null, null],
        'Sushi Zanmai@35.6595': ['none', null, null],
        'Pancho@35.6605': ['nearest', 'スパゲッティーのパンチョ', 'node/3'],
        'Krung Siam@35.6578': ['none', null, null],
        'Tengu Sakaba@35.6588': ['matched', '天狗酒場', 'node/8'],
        'Usagi@35.662': ['matched', 'うさぎ', 'node/4'],
    });

    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    assert.deepEqual(changedFiles, [file]);
    const text = readFileSync(file, 'utf8');
    assert.ok(text.includes("            'name' => 'Go Go Curry',\n            'name_ja' => 'ゴーゴーカレー 渋谷店',\n"));
    const nameJa = (name) => readSeeder(file, 'restaurants').filter((p) => p.name === name).map((p) => p.nameJa);
    assert.deepEqual(nameJa('Hakata Furyu'), ['博多風龍']);
    assert.deepEqual(nameJa('Pancho'), [null]); // a nearest guess: not written by --yes
    assert.deepEqual(nameJa('Usagi'), ['うさぎ', 'うさぎ']);
    assert.deepEqual(nameJa('Krung Siam'), [null]);
    assert.deepEqual(nameJa('Tengu Sakaba'), ['天狗酒場']); // temporarily closed: named too
    assert.deepEqual(nameJa('Kindenmaru'), [null]); // permanently closed: not looked up

    // A second run only looks at the places still without a name, from the cache: no request, no change.
    const again = await names(ws, server, { yes: true, today: new Date(2026, 10, 18, 10, 0) });
    assert.equal(server.calls, 1);
    assert.equal(again.report.results.length, 7); // the 6 without a candidate, and the nearest guess left unwritten
    assert.deepEqual(again.changedFiles, []);
    assert.equal(readFileSync(file, 'utf8'), text);
    // The names reports are not for apply.
    assert.throws(() => latestReport(ws.reportsDir), /npm run check/);
});

test('names writes the Overpass cache and reuses it the same day, unless --refresh', async () => {
    const ws = workspace();
    const server = fakeOverpass();
    await names(ws, server, { dryRun: true });
    const cache = JSON.parse(readFileSync(join(ws.cacheDir, 'overpass-2026-11-18.json'), 'utf8'));
    assert.equal(cache.elements.length, ELEMENTS.length);
    assert.match(cache.query, /nw\["amenity"\]/);
    await names(ws, server, { dryRun: true });
    assert.equal(server.calls, 1);
    await names(ws, server, { dryRun: true, refresh: true });
    assert.equal(server.calls, 2);
});

test('names --dry-run prints the table and writes no report and no seeder', async () => {
    const ws = workspace();
    const lines = [];
    const before = readFileSync(join(ws.seedersDir, 'RestaurantsTableSeeder.php'), 'utf8');
    const out = await names(ws, fakeOverpass(), { dryRun: true, yes: true, log: (l) => lines.push(l) });
    const text = lines.join('\n');
    assert.match(text, /kind\s+name\s+Japanese name\s+source element\s+distance\s+match/);
    assert.match(text, /restaurants\s+Hakata Furyu\s+博多風龍\s+node\/1\s+10 m\s+matched/);
    assert.match(text, /matched: 4, nearest: 1, none: 6/);
    assert.equal(out.reportPath, null);
    assert.ok(!existsSync(ws.reportsDir));
    assert.equal(readFileSync(join(ws.seedersDir, 'RestaurantsTableSeeder.php'), 'utf8'), before);
});

test('names shows matched checked and nearest unchecked, lists the none ones, and keeps what is ticked', async () => {
    const ws = workspace();
    let shown;
    const lines = [];
    // The fake prompt unticks the nearest guess.
    const prompt = async (groups) => {
        shown = groups.map((g) => [g.match, g.checked, g.results.map((r) => r.name)]);
        return groups.flatMap((g) => g.results).filter((r) => r.match !== 'nearest');
    };
    const ran = [];
    const run = (command, args) => {
        ran.push([command, args[0]]);
        return { status: 0, stdout: '', stderr: '' };
    };
    await names(ws, fakeOverpass(), { prompt, run, log: (l) => lines.push(l) });
    assert.deepEqual(shown, [
        ['matched', true, ['Hakata Furyu', 'Go Go Curry', 'Tengu Sakaba', 'Usagi']],
        ['nearest', false, ['Pancho']],
    ]);
    assert.match(lines.join('\n'), /6 places without a name found[\s\S]*Krung Siam: 2 named elements within 15 m/);
    assert.match(lines.join('\n'), /npm run check -- --names/);
    assert.deepEqual(ran, [['php', '-l'], ['git', 'diff']]);
    const places = readSeeder(join(ws.seedersDir, 'RestaurantsTableSeeder.php'), 'restaurants');
    assert.equal(places.find((p) => p.name === 'Pancho').nameJa, null);
    assert.equal(places.find((p) => p.name === 'Hakata Furyu').nameJa, '博多風龍');
});

test('names stops if php -l fails, and the seeder is left as it was', async () => {
    const ws = workspace();
    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    const before = readFileSync(file, 'utf8');
    const failing = (command) => (command === 'php' ? { status: 255, stdout: 'Parse error', stderr: '' } : { status: 0, stdout: '' });
    await assert.rejects(names(ws, fakeOverpass(), { yes: true, run: failing }), /php -l failed[\s\S]*nothing was written/i);
    assert.equal(readFileSync(file, 'utf8'), before); // the lint runs on a copy
});

test('names --limit and --kind', async () => {
    const ws = workspace();
    const server = fakeOverpass();
    const { report } = await names(ws, server, { limit: 2, dryRun: true });
    assert.deepEqual(report.results.map((r) => r.name), ['Hakata Furyu', 'Go Go Curry']);
    const karaokes = await names(ws, server, { kinds: ['karaokes'], dryRun: true });
    assert.deepEqual(karaokes.report.results.map((r) => r.name), ['Karaoke-kan', 'Joysound', 'Joysound']);
    assert.ok(readdirSync(ws.cacheDir).length === 1);
});

test('names drops a nearest guess that two places claim, and keeps an English match', async () => {
    const ws = workspace();
    // Move Sushi Zanmai 10 m north of Bistro Shibuya: two restaurants in one building.
    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    writeFileSync(file, readFileSync(file, 'utf8').replace("fillCoordinatesFromString('35.659500, 139.699500')", "fillCoordinatesFromString('35.656090, 139.699000')"));
    const izakaya = node(1, { name: '居酒屋 一' }, 35.656, 139.699, 5); // mapped once, 5 m from both
    const { report } = await names(ws, fakeOverpass([izakaya]), { dryRun: true });
    const get = (name) => report.results.find((r) => r.name === name);
    assert.deepEqual([get('Bistro Shibuya').match, get('Bistro Shibuya').reason], ['none', 'node/1 claimed by 2 places']);
    assert.deepEqual([get('Sushi Zanmai').match, get('Sushi Zanmai').nameJa], ['none', null]);

    const named = node(1, { name: 'すし三昧', 'name:en': 'Sushi Zanmai' }, 35.656, 139.699, 5);
    const again = await names(ws, fakeOverpass([named]), { dryRun: true, refresh: true });
    const match = (name) => again.report.results.find((r) => r.name === name).match;
    assert.deepEqual([match('Sushi Zanmai'), match('Bistro Shibuya')], ['matched', 'none']);
});

test('the prompt line shows the OpenStreetMap name, or the Japanese name when the element has only name:ja', () => {
    const r = { kind: 'restaurants', name: 'Pancho', nameJa: 'パンチョ', element: 'node/3', distance: 5 };
    assert.match(describe({ ...r, elementName: 'Spaghetti Pancho' }), /Pancho -> パンチョ \(node\/3, "Spaghetti Pancho", 5 m\)/);
    assert.doesNotMatch(describe({ ...r, elementName: null }), /null/);
    assert.match(describe({ ...r, elementName: null }), /"パンチョ"/);
});
