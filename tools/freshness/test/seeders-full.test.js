import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { join } from 'node:path';
import { parseSeederFull, readAllSeedersFull, revisionOf } from '../lib/seeders.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;
const restoText = readFileSync(join(FIXTURES, 'RestaurantsTableSeeder.php'), 'utf8');

test('the full reader gives every key with its type, the tags and the coordinates', () => {
    const places = parseSeederFull(restoText, 'restaurants');
    assert.equal(places.length, 13);
    const [furyu] = places;
    assert.equal(furyu.id, 'restaurants/0');
    assert.equal(furyu.index, 0);
    assert.equal(furyu.line, 30);
    assert.equal(furyu.name, 'Hakata Furyu');
    assert.deepEqual([furyu.lat, furyu.lon], [35.658545, 139.698468]);
    assert.deepEqual(furyu.tags, ['ramen', 'japanese']);
    assert.deepEqual(furyu.fields.map((f) => f.key), [
        'name', 'location', 'type', 'lunch_price', 'points', 'visited', 'visit_date', 'google_maps_link', 'tabelog_link', 'score_lunch', 'score_food',
    ]);
    const value = (key) => furyu.fields.find((f) => f.key === key).value;
    assert.equal(value('lunch_price'), 900);
    assert.equal(value('visited'), true);
    assert.equal(value('tabelog_link'), null);
    assert.equal(value('points'), "A line with 'quotes', commas, and => arrows inside.");
});

test('the full reader keeps a multi-line string as one value with newlines', () => {
    const zanmai = parseSeederFull(restoText, 'restaurants').find((p) => p.name === 'Sushi Zanmai');
    const points = zanmai.fields.find((f) => f.key === 'points').value;
    assert.ok(points.startsWith('Spans several lines, like a few real ones:\n          <ul>'));
    assert.ok(points.endsWith("It's fine."));
});

test('two places with the same name get two ids, and a commented-out block gets none', () => {
    const places = parseSeederFull(restoText, 'restaurants');
    const usagi = places.filter((p) => p.name === 'Usagi');
    assert.deepEqual(usagi.map((p) => p.id), ['restaurants/11', 'restaurants/12']);
    assert.ok(!places.some((p) => p.name === 'M-SPO cafe dining'));
});

test('the full reader keeps the tags as written, case included, and reads every kind', () => {
    const places = readAllSeedersFull(FIXTURES, ['restaurants', 'karaokes']); // the fixtures have two kinds
    const karaokekan = places.find((p) => p.kind === 'karaokes' && p.name === 'Karaoke-kan');
    assert.deepEqual(karaokekan.tags, ['karaokekan', 'private rooms', 'JOYSOUND', 'DAM']);
    assert.equal(karaokekan.id, 'karaokes/0');
});

test('the revision is the SHA-1 of the text', () => {
    assert.equal(revisionOf('abc'), 'a9993e364706816aba3e25717850c26c9cd0d89d');
    assert.notEqual(revisionOf(restoText), revisionOf(restoText + '\n'));
});

// ---- The general editor (task 2)
import { mkdtempSync, cpSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { editPlaceText, planPlaceEdit, checkPlaceEdit } from '../lib/seeders.js';

function tempFixtures() {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-'));
    cpSync(FIXTURES, dir, { recursive: true });
    return dir;
}
const field = (place, key) => place.fields.find((f) => f.key === key)?.value;
const reread = (text, index) => parseSeederFull(text, 'restaurants')[index];

test('edits a string, a number and a boolean in place, keeping the other lines', () => {
    const out = editPlaceText(restoText, 'restaurants', 0, { fields: { location: 'Shibuya, Dogenzaka', lunch_price: 950, visited: false } });
    const furyu = reread(out, 0);
    assert.equal(field(furyu, 'location'), 'Shibuya, Dogenzaka');
    assert.equal(field(furyu, 'lunch_price'), 950);
    assert.equal(field(furyu, 'visited'), false);
    assert.ok(out.includes("            'lunch_price' => 950,\n"));
    // Only three lines differ.
    const diff = out.split('\n').filter((l, i) => l !== restoText.split('\n')[i]);
    assert.equal(diff.length, 3);
});

test('edits a string with quotes and backslashes, round trip', () => {
    const value = "Mc Donald's \\ C:\\path 'quoted'";
    const out = editPlaceText(restoText, 'restaurants', 0, { fields: { points: value } });
    assert.equal(field(reread(out, 0), 'points'), value);
});

test('adds a new key at the end of the block, with a comma on the line before when it had none', () => {
    const noComma = restoText.replace("            'score_food' => 2,\n        ]);", "            'score_food' => 2\n        ]);");
    assert.equal(field(reread(noComma, 0), 'score_food'), 2); // the reader accepts it
    const out = editPlaceText(noComma, 'restaurants', 0, { fields: { score_price: 1 } });
    assert.ok(out.includes("            'score_food' => 2,\n            'score_price' => 1,\n        ]);"));
    assert.equal(reread(out, 0).fields.at(-1).key, 'score_price');
});

test('the three known keys keep their places after the name', () => {
    const out = editPlaceText(restoText, 'restaurants', 0, { fields: { google_place_id: 'ChIJnew', business_status: 'CLOSED_TEMPORARILY', name_ja: '博多風龍' } });
    assert.ok(out.includes("'name' => 'Hakata Furyu',\n            'name_ja' => '博多風龍',\n            'business_status' => 'CLOSED_TEMPORARILY',\n            'google_place_id' => 'ChIJnew',\n            'location'"));
});

test('null removes name_ja, google_place_id and the status; OPERATIONAL is written like any status', () => {
    const kindenmaru = 1; // has a status and an id
    const out = editPlaceText(restoText, 'restaurants', kindenmaru, { fields: { google_place_id: null, business_status: null } });
    const place = reread(out, kindenmaru);
    assert.equal(field(place, 'google_place_id'), undefined);
    assert.equal(field(place, 'business_status'), undefined);
    const open = editPlaceText(restoText, 'restaurants', kindenmaru, { fields: { business_status: 'OPERATIONAL' } });
    assert.equal(field(reread(open, kindenmaru), 'business_status'), 'OPERATIONAL');
    const withJa = editPlaceText(restoText, 'restaurants', 0, { fields: { name_ja: '博多風龍' } });
    const without = editPlaceText(withJa, 'restaurants', 0, { fields: { name_ja: null } });
    assert.equal(without, restoText);
});

test('null on another key writes null, and a value replaces a null', () => {
    const out = editPlaceText(restoText, 'restaurants', 0, { fields: { tabelog_link: 'https://tabelog.com/x', visit_date: null } });
    const place = reread(out, 0);
    assert.equal(field(place, 'tabelog_link'), 'https://tabelog.com/x');
    assert.equal(field(place, 'visit_date'), null);
    assert.ok(out.includes("            'visit_date' => null,\n"));
});

test('a multi-line string is written back over its lines and read back whole', () => {
    const zanmai = 7;
    const text = 'Line one\nLine two\nLine three';
    const out = editPlaceText(restoText, 'restaurants', zanmai, { fields: { points: text } });
    assert.equal(field(reread(out, zanmai), 'points'), text);
    assert.equal(reread(out, zanmai).fields.length, reread(restoText, zanmai).fields.length);
    const back = editPlaceText(out, 'restaurants', zanmai, { fields: { points: 'One line' } });
    assert.ok(back.includes("            'points' => 'One line',\n            'visited' => true,"));
});

test('tags are rewritten, trimmed and kept in case, and added to a place without', () => {
    const out = editPlaceText(restoText, 'restaurants', 0, { tags: [' ramen', 'JOYSOUND', '', 'ドリンク'] });
    assert.deepEqual(reread(out, 0).tags, ['ramen', 'JOYSOUND', 'ドリンク']);
    assert.ok(out.includes("        $resto->attachTags(['ramen', 'JOYSOUND', 'ドリンク']);\n        $resto->fillCoordinatesFromString('35.658545, 139.698468');"));
    const noTags = restoText.replace("        $resto->attachTags(['ramen', 'japanese']);\n        $resto->fillCoordinatesFromString('35.658545, 139.698468');", "        $resto->fillCoordinatesFromString('35.658545, 139.698468');");
    assert.equal(reread(noTags, 0).tags, null);
    const added = editPlaceText(noTags, 'restaurants', 0, { tags: ['new'] });
    assert.deepEqual(reread(added, 0).tags, ['new']);
    assert.ok(added.includes("        ]);\n        $resto->attachTags(['new']);\n        $resto->fillCoordinatesFromString('35.658545, 139.698468');"));
});

test('coordinates are rewritten from two numbers, and a zoom level is dropped', () => {
    const out = editPlaceText(restoText, 'restaurants', 0, { lat: 35.1, lon: 139.2 });
    assert.deepEqual([reread(out, 0).lat, reread(out, 0).lon], [35.1, 139.2]);
    assert.ok(out.includes("        $resto->fillCoordinatesFromString('35.1, 139.2');"));
    const krung = 9; // '35.6578,139.7012,17' in the fixture
    const fixed = editPlaceText(restoText, 'restaurants', krung, { lat: 35.6578, lon: 139.7012 });
    assert.ok(fixed.includes("fillCoordinatesFromString('35.6578, 139.7012');"));
});

test('a bad edit is refused before anything is touched', () => {
    const refused = (edit, re) => assert.throws(() => checkPlaceEdit(edit), re);
    refused({ fields: { name: '' } }, /name/);
    refused({ fields: { name: null } }, /name/);
    refused({ fields: { business_status: 'GONE' } }, /business status/);
    refused({ fields: { google_place_id: 'has space' } }, /place id/);
    refused({ fields: { name_ja: '' } }, /name_ja/);
    refused({ fields: { 'bad key': 'x' } }, /key/);
    refused({ fields: { location: 'tab\there' } }, /control/);
    refused({ fields: { location: { a: 1 } } }, /value/);
    refused({ fields: { lunch_price: Infinity } }, /number/);
    refused({ lat: 91, lon: 0 }, /latitude/);
    refused({ lat: 0, lon: 'x' }, /longitude/);
    refused({ tags: 'ramen' }, /tags/);
    refused({ tags: ['a,b'] }, /tags/);
    refused({}, /nothing to edit/);
    assert.doesNotThrow(() => checkPlaceEdit({ fields: { points: 'two\nlines' } }));
});

test('planPlaceEdit verifies the result in memory and reports what changed, writing nothing', () => {
    const dir = tempFixtures();
    const file = join(dir, 'RestaurantsTableSeeder.php');
    const plan = planPlaceEdit(file, 'restaurants', 2, { fields: { name: 'Go! Go! Curry' } });
    assert.equal(plan.filePath, file);
    assert.equal(plan.before, restoText);
    assert.equal(plan.changed, true);
    assert.equal(reread(plan.after, 2).name, 'Go! Go! Curry');
    assert.equal(readFileSync(file, 'utf8'), restoText);
    const same = planPlaceEdit(file, 'restaurants', 2, { fields: { name: 'Go Go Curry' } });
    assert.equal(same.changed, false);
    assert.throws(() => planPlaceEdit(file, 'restaurants', 99, { fields: { name: 'x' } }), /no block 99/);
    // The text given is the one edited, whatever the file holds now.
    const given = planPlaceEdit(file, 'restaurants', 0, { fields: { name: 'Y' } }, restoText.replace("'Hakata Furyu'", "'X'"));
    assert.equal(given.before, restoText.replace("'Hakata Furyu'", "'X'"));
    assert.equal(reread(given.after, 0).name, 'Y');
});

test('an edit is idempotent', () => {
    const edit = { fields: { name_ja: 'テスト', lunch_price: 1000 }, tags: ['a', 'b'], lat: 35.5, lon: 139.5 };
    const once = editPlaceText(restoText, 'restaurants', 3, edit);
    assert.equal(editPlaceText(once, 'restaurants', 3, edit), once);
});

test('the comma goes on the last key line even when a comment line sits before the closing', () => {
    const commented = restoText.replace("            'score_food' => 2,\n        ]);", "            'score_food' => 2\n            // 'old' => 1,\n        ]);");
    const out = editPlaceText(commented, 'restaurants', 0, { fields: { score_price: 1 } });
    assert.ok(out.includes("            'score_food' => 2,\n            // 'old' => 1,\n            'score_price' => 1,\n        ]);"));
});

test('a key from the prototype chain, or fields that are not a plain object, are refused', () => {
    for (const key of ['constructor', 'toString', '__proto__', 'hasOwnProperty']) {
        assert.throws(() => checkPlaceEdit({ fields: { [key]: 'x' } }), /key/);
    }
    assert.throws(() => checkPlaceEdit({ fields: 'ab' }), /fields/);
    assert.throws(() => checkPlaceEdit({ fields: ['a'] }), /fields/);
});
