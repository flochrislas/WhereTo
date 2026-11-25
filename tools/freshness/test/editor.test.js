import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { join } from 'node:path';
import { parseSeederFull } from '../lib/seeders.js';
import { keyType, knownKeys, validateEdit, reportKind, mapReport, OPTIONAL_KEYS, reportLabel } from '../lib/editor.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;
const places = parseSeederFull(readFileSync(join(FIXTURES, 'RestaurantsTableSeeder.php'), 'utf8'), 'restaurants');

test('the type comes from the key, not from the current value', () => {
    assert.deepEqual(keyType('official_website', null), { type: 'url', nullable: true });
    assert.deepEqual(keyType('visit_date', '2017-09-05 13:00:00'), { type: 'datetime', nullable: true });
    assert.deepEqual(keyType('lunch_price', 900), { type: 'number', nullable: true });
    assert.deepEqual(keyType('score_food', 2), { type: 'number', nullable: false });
    assert.deepEqual(keyType('visited', true), { type: 'boolean', nullable: false });
    assert.deepEqual(keyType('experience', 'x'), { type: 'longtext', nullable: false });
    assert.deepEqual(keyType('business_status', undefined), { type: 'status', nullable: true });
    assert.deepEqual(keyType('google_place_id', undefined), { type: 'id', nullable: true });
    assert.deepEqual(keyType('name_ja', undefined), { type: 'text', nullable: true });
    // An unknown key: from its value.
    assert.deepEqual(keyType('mystery', 3), { type: 'number', nullable: true });
    assert.deepEqual(keyType('mystery', null), { type: 'text', nullable: true });
});

test('knownKeys lists the keys of the kind, name first, the optional ones next, always', () => {
    const keys = knownKeys('restaurants', places);
    assert.deepEqual(keys.slice(0, 4), ['name', ...OPTIONAL_KEYS]);
    assert.ok(keys.includes('tabelog_link') && keys.includes('score_food'));
    assert.equal(new Set(keys).size, keys.length);
    assert.deepEqual(knownKeys('bars', []), ['name', ...OPTIONAL_KEYS]);
});

test('validateEdit checks the type of each key, then the low-level rules', () => {
    assert.doesNotThrow(() => validateEdit({ fields: { lunch_price: 0 } }));
    assert.doesNotThrow(() => validateEdit({ fields: { lunch_price: null } }));
    assert.doesNotThrow(() => validateEdit({ fields: { official_website: null } }));
    assert.doesNotThrow(() => validateEdit({ fields: { visit_date: '2017-09-05 13:00:00' } }));
    assert.throws(() => validateEdit({ fields: { lunch_price: '900' } }), /lunch_price.*number/);
    assert.throws(() => validateEdit({ fields: { score_food: null } }), /score_food.*cannot be null/);
    assert.throws(() => validateEdit({ fields: { visited: 'yes' } }), /visited.*true or false/);
    assert.throws(() => validateEdit({ fields: { visit_date: '2017-09-05T13:00' } }), /visit_date.*YYYY-MM-DD HH:MM:SS/);
    assert.throws(() => validateEdit({ fields: { official_website: 'not a url' } }), /official_website.*http/);
    assert.throws(() => validateEdit({ fields: { location: null } }), /location.*cannot be null/);
    assert.throws(() => validateEdit({ fields: { name: '' } }), /name/); // from checkPlaceEdit
    // The whole-number columns: a score is a smallint, the price an integer.
    assert.doesNotThrow(() => validateEdit({ fields: { score_food: 5, score: 0, lunch_price: 40000 } }));
    assert.throws(() => validateEdit({ fields: { score_food: 40000 } }), /score_food.*whole number from -32768 to 32767/);
    assert.throws(() => validateEdit({ fields: { score: 2.5 } }), /score.*whole number/);
    assert.throws(() => validateEdit({ fields: { lunch_price: 900.5 } }), /lunch_price.*whole number/);
    assert.throws(() => validateEdit({ fields: { lunch_price: 3e9 } }), /lunch_price.*whole number/);
});

test('reportKind tells a names report from a check report', () => {
    assert.equal(reportKind({ source: 'OpenStreetMap', results: [] }), 'names');
    assert.equal(reportKind({ results: [] }), 'check');
});

test('mapReport attaches each result to a place id, by kind, name and coordinates, always', () => {
    const result = (name, lat, lon, extra = {}) => ({ kind: 'restaurants', name, lat, lon, verdict: 'operational', ...extra });
    const report = { results: [
        result('Hakata Furyu', 35.658545, 139.698468, { verdict: 'closed_permanently' }),
        result("Mc Donald's", 35.661000, 139.698000), // the second of two with that name
        result('Usagi', 35.662000, 139.700500), // two blocks, same name, same coordinates
        result('Go Go Curry', 35.0, 139.0), // a unique name, but the place moved since the report: no match
        result('Nowhere', 35, 139),
        { kind: 'karaokes', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468 }, // another kind
    ] };
    const { kind, byId, unmatched } = mapReport(report, places);
    assert.equal(kind, 'check');
    assert.equal(byId['restaurants/0'].verdict, 'closed_permanently');
    assert.equal(byId['restaurants/2'], undefined);
    assert.equal(byId['restaurants/3'], undefined);
    assert.equal(byId['restaurants/4'].name, "Mc Donald's");
    assert.equal(byId['restaurants/11'].name, 'Usagi');
    assert.equal(byId['restaurants/12'].name, 'Usagi');
    assert.deepEqual(unmatched.map((r) => `${r.kind}/${r.name}`), ['restaurants/Go Go Curry', 'restaurants/Nowhere', 'karaokes/Hakata Furyu']);
});

test('reportLabel tells the reports apart: time, kinds, type, Tabelog, partial', () => {
    assert.equal(reportLabel('2026-11-19T2001-all.json', { date: '2026-11-19' }), '2026-11-19 20:01  all  check');
    assert.equal(reportLabel('2026-11-19T1916-restaurants.json', { date: '2026-11-19' }), '2026-11-19 19:16  restaurants  check');
    assert.equal(reportLabel('2026-11-19T2000-names.json', { date: '2026-11-19', source: 'OpenStreetMap' }), '2026-11-19 20:00  names');
    assert.equal(reportLabel('2026-11-25T1700-names.json', { date: '2026-11-25', source: 'Tabelog (tabelog.com)' }), '2026-11-25 17:00  names from Tabelog');
    assert.equal(reportLabel('2026-11-20T1918-all+tabelog.json', { date: '2026-11-19', tabelog: { from: '2026-11-19T2001-all.json' } }), '2026-11-20 19:18  all  check + Tabelog (on the 2026-11-19 20:01 check)');
    assert.equal(reportLabel('2026-11-18T0930-bars.json', { date: '2026-11-18', partial: true }), '2026-11-18 09:30  bars  check, partial');
    assert.equal(reportLabel('odd.json', { date: '2026-11-18' }), 'odd.json  check'); // a name the tool did not write
});
