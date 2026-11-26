import { test } from 'node:test';
import assert from 'node:assert/strict';
import { mkdtempSync, existsSync, readFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { boundingBox, buildQuery, fetchOsmElements, USER_AGENT } from '../lib/overpass.js';

const places = [{ lat: 35.65, lon: 139.69 }, { lat: 35.66, lon: 139.70 }];
const TODAY = new Date(2026, 10, 18, 9, 30);

// A fake Overpass server that records the calls and answers with `body` (or the given status).
function fakeOverpass(body = { elements: [{ type: 'node', id: 1, lat: 35.655, lon: 139.695, tags: { name: '店' } }] }, status = 200) {
    const calls = [];
    const fetch = async (url, options) => {
        calls.push({ url, options });
        return new Response(JSON.stringify(body), { status });
    };
    return { fetch, calls };
}

test('the bounding box covers the places plus 100 m', () => {
    const box = boundingBox(places);
    assert.equal(box.south, 35.649101); // 100 m ~ 0.000899 degree of latitude
    assert.equal(box.north, 35.660899);
    assert.ok(box.west < 139.689 && box.west > 139.6888); // a little more in longitude, at this latitude
    assert.ok(box.east > 139.701 && box.east < 139.7012);
});

test('the query asks for every named business in the box, with the centre of ways and relations', () => {
    assert.equal(buildQuery({ south: 1, west: 2, north: 3, east: 4 }), [
        '[out:json][timeout:90][bbox:1,2,3,4];',
        '(',
        '  nw["amenity"];',
        '  nw["shop"];',
        '  nw["leisure"];',
        '  nw["craft"];',
        ');',
        'out center tags;',
    ].join('\n'));
});

test('one POST with a User-Agent, then the cache of the day, unless --refresh', async () => {
    const cacheDir = join(mkdtempSync(join(tmpdir(), 'freshness-osm-')), 'cache');
    const server = fakeOverpass();
    const first = await fetchOsmElements({ places, cacheDir, fetch: server.fetch, today: TODAY, url: 'https://mirror.example/api/interpreter' });
    assert.equal(first.fromCache, false);
    assert.equal(first.elements.length, 1);
    assert.equal(first.cachePath, join(cacheDir, 'overpass-2026-11-18.json'));
    const [call] = server.calls;
    assert.equal(call.url, 'https://mirror.example/api/interpreter');
    assert.equal(call.options.method, 'POST');
    assert.equal(call.options.headers['User-Agent'], USER_AGENT);
    assert.match(USER_AGENT, /github\.com\/flochrislas\/WhereTo/);
    assert.equal(new URLSearchParams(call.options.body).get('data'), buildQuery(boundingBox(places)));
    assert.ok(call.options.signal instanceof AbortSignal); // a timeout
    assert.deepEqual(JSON.parse(readFileSync(first.cachePath, 'utf8')).elements, first.elements);

    // Same day, a box inside the cached one: no request.
    const again = await fetchOsmElements({ places: [places[0]], cacheDir, fetch: server.fetch, today: TODAY });
    assert.deepEqual([again.fromCache, again.elements.length, server.calls.length], [true, 1, 1]);
    // A box the cache does not cover, --refresh, or another day: a new request.
    await fetchOsmElements({ places: [...places, { lat: 35.67, lon: 139.71 }], cacheDir, fetch: server.fetch, today: TODAY });
    await fetchOsmElements({ places, cacheDir, fetch: server.fetch, today: TODAY, refresh: true });
    await fetchOsmElements({ places, cacheDir, fetch: server.fetch, today: new Date(2026, 10, 19) });
    assert.equal(server.calls.length, 4);
    assert.ok(existsSync(join(cacheDir, 'overpass-2026-11-19.json')));
});

test('an Overpass error throws and leaves no cache', async () => {
    const cacheDir = join(mkdtempSync(join(tmpdir(), 'freshness-osm-')), 'cache');
    await assert.rejects(fetchOsmElements({ places, cacheDir, fetch: fakeOverpass({}, 429).fetch, today: TODAY }), /Overpass API: HTTP 429/);
    // A query that timed out on the server answers 200 with a remark and partial data.
    const timedOut = fakeOverpass({ elements: [], remark: 'runtime error: Query timed out in "query" at line 3' });
    await assert.rejects(fetchOsmElements({ places, cacheDir, fetch: timedOut.fetch, today: TODAY }), /runtime error/);
    assert.ok(!existsSync(join(cacheDir, 'overpass-2026-11-18.json')));
});
