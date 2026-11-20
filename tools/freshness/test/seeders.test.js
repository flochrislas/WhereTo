import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdtempSync, cpSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { parseSeeder, readSeeder, editSeederText, applySeederEdits, planSeederEdits, phpString } from '../lib/seeders.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;
const restoText = readFileSync(join(FIXTURES, 'RestaurantsTableSeeder.php'), 'utf8');

// Copies the fixtures to a fresh temporary folder, so a test can edit them.
function tempFixtures() {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-'));
    cpSync(FIXTURES, dir, { recursive: true });
    return dir;
}

test('the reader finds every active block and skips the commented-out ones', () => {
    const places = parseSeeder(restoText, 'restaurants');
    assert.equal(places.length, 13);
    assert.deepEqual(places[0], {
        kind: 'restaurants',
        name: 'Hakata Furyu',
        nameJa: null,
        businessStatus: null,
        googlePlaceId: null,
        googleMapsLink: 'https://www.google.com/maps/place/0',
        lat: 35.658545,
        lon: 139.698468,
        line: 30,
    });
    assert.ok(!places.some((p) => p.name === 'M-SPO cafe dining' || p.name === 'xxxx'));
});

test('the reader unescapes quotes and reads an already closed place', () => {
    const places = parseSeeder(restoText, 'restaurants');
    assert.equal(places.filter((p) => p.name === "Mc Donald's").length, 2);
    const kindenmaru = places.find((p) => p.name === 'Kindenmaru');
    assert.equal(kindenmaru.businessStatus, 'CLOSED_PERMANENTLY');
    assert.equal(kindenmaru.googlePlaceId, 'ChIJclosedKindenmaru');
    assert.equal(places.find((p) => p.name === 'Il Ritrovo').googlePlaceId, 'ChIJknownRitrovo');
});

test('the reader reads a string that spans several lines', () => {
    const places = parseSeeder(restoText, 'restaurants');
    assert.ok(places.some((p) => p.name === 'Sushi Zanmai'));
    // The editor still inserts after the name line, never inside the string.
    const out = editSeederText(restoText, 'restaurants', { name: 'Sushi Zanmai' }, { googlePlaceId: 'ChIJsushi' });
    assert.ok(out.includes("'name' => 'Sushi Zanmai',\n            'google_place_id' => 'ChIJsushi',\n"));
});

test('the reader ignores a zoom level after the coordinates, like the app', () => {
    const krung = parseSeeder(restoText, 'restaurants').find((p) => p.name === 'Krung Siam');
    assert.deepEqual([krung.lat, krung.lon], [35.6578, 139.7012]);
});

test('the reader stops when a block cannot be read', () => {
    const broken = restoText.replace("'lunch_price' => 900,", "'lunch_price' => 900 + 1,");
    assert.throws(() => parseSeeder(broken, 'restaurants'), /line 34/);
});

test('the reader stops when a ::create( is not a block it understands', () => {
    const odd = restoText.replace("$resto = Restaurant::create([\n            'name' => 'Usagi',", "$resto = Restaurant::create(['name' => 'Usagi',");
    assert.throws(() => parseSeeder(odd, 'restaurants'), /create/);
});

test('the reader rejects an unknown business_status, naming the file, line and place', () => {
    const dir = tempFixtures();
    const file = join(dir, 'RestaurantsTableSeeder.php');
    writeFileSync(file, restoText.replace("'business_status' => 'CLOSED_PERMANENTLY',", "'business_status' => 'CLOSED',"));
    assert.throws(() => readSeeder(file, 'restaurants'), (error) => {
        assert.ok(error.message.startsWith(file));
        assert.match(error.message, /line 48: unknown business_status 'CLOSED' for the place 'Kindenmaru'/);
        return true;
    });
});

test('the reader stops when a block has no coordinates', () => {
    const noCoords = restoText.replace("        $resto->fillCoordinatesFromString('35.660500, 139.698500');\n", '');
    assert.throws(() => parseSeeder(noCoords, 'restaurants'), /Pancho.*coordinates/);
});

test('the editor inserts both keys right after the name line', () => {
    const out = editSeederText(restoText, 'restaurants', { name: 'Go Go Curry' }, {
        businessStatus: 'CLOSED_PERMANENTLY',
        googlePlaceId: 'ChIJgogo',
    });
    assert.ok(out.includes(
        "            'name' => 'Go Go Curry',\n" +
        "            'business_status' => 'CLOSED_PERMANENTLY',\n" +
        "            'google_place_id' => 'ChIJgogo',\n" +
        "            'location' => 'Shibuya',\n",
    ));
    // Two lines added, nothing else touched.
    assert.equal(out.split('\n').length, restoText.split('\n').length + 2);
    const removed = out.replace("            'business_status' => 'CLOSED_PERMANENTLY',\n            'google_place_id' => 'ChIJgogo',\n", '');
    assert.equal(removed, restoText);
});

test('the editor inserts the place id after an existing status', () => {
    const out = editSeederText(restoText, 'restaurants', { name: 'Tengu Sakaba' }, { googlePlaceId: 'ChIJtengu' });
    assert.ok(out.includes(
        "            'name' => 'Tengu Sakaba',\n" +
        "            'business_status' => 'CLOSED_TEMPORARILY',\n" +
        "            'google_place_id' => 'ChIJtengu',\n",
    ));
});

test('the editor updates existing lines in place', () => {
    const out = editSeederText(restoText, 'restaurants', { name: 'Kindenmaru' }, {
        businessStatus: 'REPLACED',
        googlePlaceId: 'ChIJother',
    });
    assert.equal(out.split('\n').length, restoText.split('\n').length);
    assert.ok(out.includes("'name' => 'Kindenmaru',\n            'business_status' => 'REPLACED',\n            'google_place_id' => 'ChIJother',\n"));
});

test('the editor is idempotent', () => {
    const fields = { businessStatus: 'CLOSED_PERMANENTLY', googlePlaceId: 'ChIJgogo' };
    const once = editSeederText(restoText, 'restaurants', { name: 'Go Go Curry' }, fields);
    const twice = editSeederText(once, 'restaurants', { name: 'Go Go Curry' }, fields);
    assert.equal(twice, once);
});

test('the editor removes the status line when the place is operational again', () => {
    const out = editSeederText(restoText, 'restaurants', { name: 'Tengu Sakaba' }, { businessStatus: 'OPERATIONAL' });
    assert.ok(!out.includes('CLOSED_TEMPORARILY'));
    assert.equal(out.split('\n').length, restoText.split('\n').length - 1);
});

test('the editor tells two places of the same name apart by their coordinates', () => {
    const target = { name: "Mc Donald's", lat: 35.661, lon: 139.698 };
    const out = editSeederText(restoText, 'restaurants', target, { businessStatus: 'CLOSED_PERMANENTLY' });
    const places = parseSeeder(out, 'restaurants').filter((p) => p.name === "Mc Donald's");
    assert.deepEqual(places.map((p) => p.businessStatus), [null, 'CLOSED_PERMANENTLY']);
    // Without coordinates, the name alone is ambiguous.
    assert.throws(() => editSeederText(restoText, 'restaurants', { name: "Mc Donald's" }, { businessStatus: 'REPLACED' }), /2 places/);
});

test('the editor edits both copies of a place that is in the seeder twice', () => {
    const target = { name: 'Usagi', lat: 35.662, lon: 139.7005 };
    const out = editSeederText(restoText, 'restaurants', target, { businessStatus: 'CLOSED_PERMANENTLY', googlePlaceId: 'ChIJusagi' });
    const usagis = parseSeeder(out, 'restaurants').filter((p) => p.name === 'Usagi');
    assert.equal(usagis.length, 2);
    for (const u of usagis) assert.deepEqual([u.businessStatus, u.googlePlaceId], ['CLOSED_PERMANENTLY', 'ChIJusagi']);
    assert.equal(out.split('\n').length, restoText.split('\n').length + 4);
});

test('the editor refuses an unknown place, status or id', () => {
    assert.throws(() => editSeederText(restoText, 'restaurants', { name: 'Nowhere' }, { businessStatus: 'REPLACED' }), /No place/);
    assert.throws(() => editSeederText(restoText, 'restaurants', { name: 'Usagi' }, { businessStatus: 'GONE' }), /status/);
    assert.throws(() => editSeederText(restoText, 'restaurants', { name: 'Usagi' }, { googlePlaceId: "x'); evil();" }), /place id/);
});

test('applySeederEdits writes the file once and the reader reads back the new values', () => {
    const dir = tempFixtures();
    const file = join(dir, 'RestaurantsTableSeeder.php');
    const edits = [
        { target: { name: 'Go Go Curry' }, fields: { businessStatus: 'CLOSED_PERMANENTLY', googlePlaceId: 'ChIJgogo' } },
        { target: { name: 'Hakata Furyu' }, fields: { googlePlaceId: 'ChIJhakata' } },
    ];
    assert.equal(applySeederEdits(file, 'restaurants', edits), true);
    const places = readSeeder(file, 'restaurants');
    assert.equal(places.length, 13);
    assert.equal(places.find((p) => p.name === 'Go Go Curry').businessStatus, 'CLOSED_PERMANENTLY');
    assert.equal(places.find((p) => p.name === 'Hakata Furyu').googlePlaceId, 'ChIJhakata');
    // A second run changes nothing.
    const before = readFileSync(file, 'utf8');
    assert.equal(applySeederEdits(file, 'restaurants', edits), false);
    assert.equal(readFileSync(file, 'utf8'), before);
});

test('applySeederEdits keeps Windows line endings if the file has them', () => {
    const dir = tempFixtures();
    const file = join(dir, 'RestaurantsTableSeeder.php');
    writeFileSync(file, restoText.replaceAll('\n', '\r\n'));
    applySeederEdits(file, 'restaurants', [{ target: { name: 'Go Go Curry' }, fields: { googlePlaceId: 'ChIJgogo' } }]);
    const out = readFileSync(file, 'utf8');
    assert.ok(out.includes("'name' => 'Go Go Curry',\r\n            'google_place_id' => 'ChIJgogo',\r\n"));
    assert.ok(!/[^\r]\n/.test(out));
});

test('planSeederEdits verifies in memory and writes nothing', () => {
    const dir = tempFixtures();
    const file = join(dir, 'RestaurantsTableSeeder.php');
    const plan = planSeederEdits(file, 'restaurants', [{ target: { name: 'Go Go Curry' }, fields: { businessStatus: 'REPLACED' } }]);
    assert.equal(plan.changed, true);
    assert.ok(plan.after.includes("'business_status' => 'REPLACED',"));
    assert.equal(readFileSync(file, 'utf8'), restoText);
    // A failing edit throws before anything is written.
    assert.throws(() => planSeederEdits(file, 'restaurants', [{ target: { name: 'Nowhere' }, fields: { businessStatus: 'REPLACED' } }]), /No place/);
    assert.equal(readFileSync(file, 'utf8'), restoText);
});

test('phpString writes a single-quoted PHP string, Japanese and quotes included', () => {
    assert.equal(phpString('博多風龍'), "'博多風龍'");
    assert.equal(phpString("ジョニーズ・バー 'Johnny's'"), "'ジョニーズ・バー \\'Johnny\\'s\\''");
    assert.equal(phpString('a\\b'), "'a\\\\b'");
});

test('the reader reads name_ja, and rejects an empty one', () => {
    const named = restoText.replace("'name' => 'Go Go Curry',", "'name' => 'Go Go Curry',\n            'name_ja' => 'ゴーゴーカレー',");
    assert.equal(parseSeeder(named, 'restaurants').find((p) => p.name === 'Go Go Curry').nameJa, 'ゴーゴーカレー');
    const empty = restoText.replace("'name' => 'Go Go Curry',", "'name' => 'Go Go Curry',\n            'name_ja' => '',");
    assert.throws(() => parseSeeder(empty, 'restaurants'), /name_ja must be a non-empty string for the place 'Go Go Curry'/);
});

test('the editor inserts name_ja right after the name line, before the status and the id', () => {
    const out = editSeederText(restoText, 'restaurants', { name: 'Kindenmaru' }, { nameJa: "金田丸 '本店'" });
    assert.ok(out.includes(
        "            'name' => 'Kindenmaru',\n" +
        "            'name_ja' => '金田丸 \\'本店\\'',\n" +
        "            'business_status' => 'CLOSED_PERMANENTLY',\n" +
        "            'google_place_id' => 'ChIJclosedKindenmaru',\n",
    ));
    assert.equal(out.split('\n').length, restoText.split('\n').length + 1);
    // Read back, unescaped; a second run changes nothing; a new value replaces the line.
    assert.equal(parseSeeder(out, 'restaurants').find((p) => p.name === 'Kindenmaru').nameJa, "金田丸 '本店'");
    assert.equal(editSeederText(out, 'restaurants', { name: 'Kindenmaru' }, { nameJa: "金田丸 '本店'" }), out);
    const renamed = editSeederText(out, 'restaurants', { name: 'Kindenmaru' }, { nameJa: '金田丸' });
    assert.equal(renamed.split('\n').length, out.split('\n').length);
    assert.ok(renamed.includes("'name' => 'Kindenmaru',\n            'name_ja' => '金田丸',\n"));
});

test('a status and an id added later go after name_ja', () => {
    const named = editSeederText(restoText, 'restaurants', { name: 'Go Go Curry' }, { nameJa: 'ゴーゴーカレー' });
    const out = editSeederText(named, 'restaurants', { name: 'Go Go Curry' }, { businessStatus: 'CLOSED_PERMANENTLY', googlePlaceId: 'ChIJgogo' });
    assert.ok(out.includes(
        "            'name' => 'Go Go Curry',\n" +
        "            'name_ja' => 'ゴーゴーカレー',\n" +
        "            'business_status' => 'CLOSED_PERMANENTLY',\n" +
        "            'google_place_id' => 'ChIJgogo',\n",
    ));
});

test('the editor refuses an empty or multi-line Japanese name', () => {
    assert.throws(() => editSeederText(restoText, 'restaurants', { name: 'Usagi', lat: 35.662, lon: 139.7005 }, { nameJa: ' ' }), /Japanese name/);
    assert.throws(() => editSeederText(restoText, 'restaurants', { name: 'Usagi', lat: 35.662, lon: 139.7005 }, { nameJa: 'うさぎ\n' }), /Japanese name/);
});

test('applySeederEdits writes name_ja in both copies of a duplicated place and reads it back', () => {
    const dir = tempFixtures();
    const file = join(dir, 'RestaurantsTableSeeder.php');
    const edits = [{ target: { name: 'Usagi', lat: 35.662, lon: 139.7005 }, fields: { nameJa: 'うさぎ' } }];
    assert.equal(applySeederEdits(file, 'restaurants', edits), true);
    assert.deepEqual(readSeeder(file, 'restaurants').filter((p) => p.name === 'Usagi').map((p) => p.nameJa), ['うさぎ', 'うさぎ']);
    assert.equal(applySeederEdits(file, 'restaurants', edits), false);
});

test('two places of the same name get two different Japanese names, told apart by their coordinates', () => {
    const dir = tempFixtures();
    const file = join(dir, 'RestaurantsTableSeeder.php');
    assert.equal(applySeederEdits(file, 'restaurants', [
        { target: { name: "Mc Donald's", lat: 35.659, lon: 139.7 }, fields: { nameJa: "マクドナルド 'センター街'" } },
        { target: { name: "Mc Donald's", lat: 35.661, lon: 139.698 }, fields: { nameJa: 'マクドナルド 道玄坂店' } },
    ]), true);
    const text = readFileSync(file, 'utf8');
    assert.ok(text.includes("'name' => 'Mc Donald\\'s',\n            'name_ja' => 'マクドナルド \\'センター街\\'',\n"));
    const mcs = readSeeder(file, 'restaurants').filter((p) => p.name === "Mc Donald's");
    assert.deepEqual(mcs.map((p) => [p.lat, p.nameJa]), [[35.659, "マクドナルド 'センター街'"], [35.661, 'マクドナルド 道玄坂店']]);
});
