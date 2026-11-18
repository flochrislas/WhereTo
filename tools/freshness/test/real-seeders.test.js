// Reads the four real seeders of the app (read-only) to prove the reader handles them.
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readAllSeeders, DEFAULT_SEEDERS_DIR } from '../lib/seeders.js';

test('the reader reads every place of the real seeders, with coordinates', () => {
    const places = readAllSeeders(DEFAULT_SEEDERS_DIR);
    const count = (kind) => places.filter((p) => p.kind === kind).length;
    assert.equal(count('restaurants'), 238);
    assert.equal(count('stores'), 48);
    assert.equal(count('bars'), 15);
    assert.equal(count('karaokes'), 4);
    for (const p of places) {
        assert.ok(Number.isFinite(p.lat) && Number.isFinite(p.lon), `${p.kind} ${p.name} has no coordinates`);
        assert.ok(p.lat > 35.6 && p.lat < 35.7 && p.lon > 139.6 && p.lon < 139.8, `${p.kind} ${p.name} is not in Shibuya`);
    }
});
