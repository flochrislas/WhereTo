import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { join } from 'node:path';
import { parseSeederFull, planPlaceAdd, planPlaceRemoval, checkNewPlace } from '../lib/seeders.js';
import { templateKeys, validateNewPlace, knownKeys } from '../lib/editor.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;
const file = join(FIXTURES, 'RestaurantsTableSeeder.php');
const restoText = readFileSync(file, 'utf8');
const karaokeText = readFileSync(join(FIXTURES, 'KaraokesTableSeeder.php'), 'utf8');

const newPlace = (over = {}) => ({
    fields: { name: 'Menya Musashi', location: 'Shibuya', type: 'Ramen', lunch_price: null, points: "It's good.\nSecond line.", visited: false, score_lunch: 2 },
    tags: ['ramen', ' japanese '],
    lat: 35.6601,
    lon: 139.7002,
    ...over,
});

test('a new place is written after the last one, like it, and reads back with its values', () => {
    const plan = planPlaceAdd(file, 'restaurants', newPlace(), restoText);
    assert.equal(plan.changed, true);
    assert.equal(plan.index, 13);
    assert.equal(readFileSync(file, 'utf8'), restoText); // nothing written
    const places = parseSeederFull(plan.after, 'restaurants');
    assert.equal(places.length, 14);
    assert.deepEqual(places.slice(0, 13).map((p) => p.name), parseSeederFull(restoText, 'restaurants').map((p) => p.name));
    const added = places[13];
    assert.equal(added.id, 'restaurants/13');
    assert.deepEqual(added.tags, ['ramen', 'japanese']);
    assert.deepEqual([added.lat, added.lon], [35.6601, 139.7002]);
    assert.equal(added.fields.find((f) => f.key === 'points').value, "It's good.\nSecond line.");
    assert.equal(added.fields.find((f) => f.key === 'lunch_price').value, null);
    // The block as text: the variable, the model and the indentation of the last place, before the template comment.
    assert.ok(plan.after.includes([
        "        $resto->fillCoordinatesFromString('35.662000, 139.700500');",
        '',
        '        $resto = Restaurant::create([',
        "            'name' => 'Menya Musashi',",
        "            'location' => 'Shibuya',",
    ].join('\n')));
    assert.ok(plan.after.includes([
        "            'score_lunch' => 2,",
        '        ]);',
        "        $resto->attachTags(['ramen', 'japanese']);",
        "        $resto->fillCoordinatesFromString('35.6601, 139.7002');",
        '',
        '        /*',
        '        ///AAA///',
    ].join('\n')));
});

test('the name lines come first, and an empty optional key gets no line', () => {
    const fields = { location: 'Shibuya', google_place_id: 'ChIJabc', name_ja: null, business_status: 'OPERATIONAL', name: 'Ordered' };
    const { after } = planPlaceAdd(file, 'restaurants', newPlace({ fields, tags: [] }), restoText);
    const added = parseSeederFull(after, 'restaurants').at(-1);
    assert.deepEqual(added.fields.map((f) => f.key), ['name', 'business_status', 'google_place_id', 'location']);
    assert.equal(added.tags, null); // no tags: no attachTags line
    assert.ok(!after.includes('attachTags([]);'));
});

test('a new place is refused without a name or coordinates, twice at the same spot, or with a bad value', () => {
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace({ fields: { location: 'Shibuya' } }), restoText), /name/);
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace({ fields: { name: '  ' } }), restoText), /name/);
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace({ lat: undefined, lon: undefined }), restoText), /coordinates/);
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace({ tags: ["it's"] }), restoText), /tags/);
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace({ fields: { name: 'Hakata Furyu' }, lat: 35.658545, lon: 139.698468 }), restoText), /already in restaurants/);
    assert.throws(() => checkNewPlace({ fields: ['name'], lat: 1, lon: 1 }), /fields must be an object/);
    // The same name elsewhere is another place (the two Joysound karaokes).
    assert.equal(planPlaceAdd(file, 'restaurants', newPlace({ fields: { name: 'Hakata Furyu' } }), restoText).index, 13);
    // A seeder without a place gives nothing to write the new one like.
    const empty = restoText.slice(0, restoText.indexOf('        $resto = Restaurant::create([')) + '    }\n}\n';
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace(), empty.replace(/\/\*[\s\S]*?\*\//g, '')), /add the first one by hand/);
});

test('the form of a new place has the name, the optional keys and the usual keys of the kind; other keys are refused', () => {
    const places = [...parseSeederFull(restoText, 'restaurants'), ...parseSeederFull(karaokeText, 'karaokes')];
    const keys = templateKeys('restaurants', places);
    assert.deepEqual(keys.slice(0, 4), ['name', 'name_ja', 'business_status', 'google_place_id']);
    assert.ok(keys.includes('lunch_price') && keys.includes('score_food'));
    assert.ok(!templateKeys('karaokes', places).includes('lunch_price'));
    const known = knownKeys('restaurants', places);
    validateNewPlace(newPlace(), known);
    assert.throws(() => validateNewPlace(newPlace({ fields: { name: 'X', colour: 'red' } }), known), /unexpected key 'colour'/);
    assert.throws(() => validateNewPlace(newPlace({ fields: { name: 'X', score_lunch: 2.5 } }), known), /whole number/);
    assert.throws(() => validateNewPlace(newPlace({ fields: { name: 'X', location: null } }), known), /cannot be null/);
    assert.throws(() => validateNewPlace({ fields: { name: 'X' } }, known), /coordinates/);
});

test('a removed place takes its block, its tags and its coordinates; the others stay as they were', () => {
    const before = parseSeederFull(restoText, 'restaurants');
    const plan = planPlaceRemoval(file, 'restaurants', 2, restoText); // Go Go Curry
    const after = parseSeederFull(plan.after, 'restaurants');
    assert.deepEqual(after.map((p) => p.name), before.filter((_, i) => i !== 2).map((p) => p.name));
    assert.ok(!plan.after.includes('Go Go Curry'));
    assert.ok(!plan.after.includes('35.657333'));
    // One blank line between the neighbours, and the commented-out block below is untouched.
    assert.ok(plan.after.includes("        $resto->fillCoordinatesFromString('35.655166, 139.700365');\n\n        /*\n        // This place has been replaced by the FIX Lounge"));
    assert.equal(plan.after.split('\n').length, restoText.split('\n').length - 16);
    assert.equal(readFileSync(file, 'utf8'), restoText);
});

test('the first, the last and one of two same-name places can be removed; a comment above stays', () => {
    const names = (text) => parseSeederFull(text, 'restaurants').map((p) => p.name);
    assert.equal(names(planPlaceRemoval(file, 'restaurants', 0, restoText).after)[0], 'Kindenmaru');
    const last = planPlaceRemoval(file, 'restaurants', 12, restoText).after;
    assert.equal(names(last).length, 12);
    assert.ok(last.includes('///AAA///'));
    // Bistro Shibuya carries "// TODO: complete this" above it: a note, kept.
    const bistro = names(restoText).indexOf('Bistro Shibuya');
    const noBistro = planPlaceRemoval(file, 'restaurants', bistro, restoText).after;
    assert.ok(noBistro.includes('// TODO: complete this') && !noBistro.includes('Bistro Shibuya'));
    // The second Mc Donald's goes, the first stays, with its own coordinates.
    const second = names(restoText).lastIndexOf("Mc Donald's");
    const one = parseSeederFull(planPlaceRemoval(file, 'restaurants', second, restoText).after, 'restaurants').filter((p) => p.name === "Mc Donald's");
    assert.deepEqual(one.map((p) => p.lat), [35.659]);
    assert.throws(() => planPlaceRemoval(file, 'restaurants', 99, restoText), /no block 99/);
});

test('a place with calls the editor cannot follow is not removed', () => {
    // Tags in a second call after a blank line: they would stay behind, on the variable of the place above.
    const odd = restoText.replace("        $resto->fillCoordinatesFromString('35.657333, 139.703654');", "        $resto->fillCoordinatesFromString('35.657333, 139.703654');\n\n        $resto->attachTags(['curry']);");
    assert.throws(() => planPlaceRemoval(file, 'restaurants', 2, odd), /cannot follow/);
    // The same call right under the others is part of the place.
    const fine = odd.replace("');\n\n        $resto->attachTags(['curry']);", "');\n        $resto->attachTags(['curry']);");
    assert.ok(!planPlaceRemoval(file, 'restaurants', 2, fine).after.includes("attachTags(['curry'])"));
});

test('a line on the variable inside an if after the place stops its removal', () => {
    // Indented deeper than the block, the call still acts on the place: left behind, it would act on the place above.
    const coords = "        $resto->fillCoordinatesFromString('35.657333, 139.703654');";
    const guarded = restoText.replace(coords, `${coords}\n        if (app()->isLocal()) {\n            $resto->attachTags(['curry']);\n        }`);
    assert.notEqual(guarded, restoText);
    assert.throws(() => planPlaceRemoval(file, 'restaurants', 2, guarded), /cannot follow after its block \(line \d+\)/);
    // The other places are removed as before.
    assert.equal(parseSeederFull(planPlaceRemoval(file, 'restaurants', 1, guarded).after, 'restaurants').length, 12);
    // The variable passed to something, without a call on it, is a use of the place too.
    const passed = restoText.replace(coords, `${coords}\n\n        $this->remember($resto);`);
    assert.throws(() => planPlaceRemoval(file, 'restaurants', 2, passed), /cannot follow/);
});

test('a later line on the variable stops a removal across a place of another variable', () => {
    // Go Go Curry in $resto, then a place in $other, then a call on $resto: with Go Go Curry gone, it would act on the place above it.
    const coords = "        $resto->fillCoordinatesFromString('35.657333, 139.703654');";
    const other = ["        $other = Restaurant::create([", "            'name' => 'Other Variable',", '        ]);', "        $other->fillCoordinatesFromString('35.1, 139.1');"].join('\n');
    const text = restoText.replace(coords, `${coords}\n\n${other}\n        $resto->attachTags(['late']);`);
    assert.equal(parseSeederFull(text, 'restaurants')[3].name, 'Other Variable');
    assert.throws(() => planPlaceRemoval(file, 'restaurants', 2, text), /'Go Go Curry' has lines the editor cannot follow after its block \(line \d+\)/);
    // The place of the other variable has no such line, and goes.
    assert.ok(!planPlaceRemoval(file, 'restaurants', 3, text).after.includes('Other Variable'));
    // Without the late call, the neighbour in another variable does not matter.
    const quiet = restoText.replace(coords, `${coords}\n\n${other}`);
    assert.equal(parseSeederFull(planPlaceRemoval(file, 'restaurants', 2, quiet).after, 'restaurants').length, 13);
    // A place of the same variable inside an if gives it a new value only sometimes: the search does not end there.
    const sometimes = restoText.replace(coords, `${coords}\n        if (app()->isLocal()) {\n${other.replaceAll('$other', '$resto')}\n        }`);
    assert.throws(() => planPlaceRemoval(file, 'restaurants', 2, sometimes), /cannot follow after its block/);
    // The same without braces: the next place is the body of the if, at the brace depth of its neighbours.
    const next = '        $resto = Restaurant::create([';
    const after = restoText.indexOf(next, restoText.indexOf(coords));
    for (const open of ['if (app()->isLocal())', 'if (false) {} else', 'while (rand(0, 1))', '$ok = app()->isLocal() and']) {
        const braceless = restoText.slice(0, after) + `        ${open}\n` + restoText.slice(after);
        assert.throws(() => planPlaceRemoval(file, 'restaurants', 2, braceless), /'Go Go Curry' has lines the editor cannot follow after its block \(line \d+\)/, open);
    }
});

test('a place is not added when the last place has lines the editor cannot follow', () => {
    // The new block would go before these lines, and PHP would run them on the new place.
    const lastCoords = "        $resto->fillCoordinatesFromString('35.662000, 139.700500');\n\n        /*"; // of the last place: the one above the commented template
    const lastName = parseSeederFull(restoText, 'restaurants').at(-1).name;
    for (const extra of ["\n\n        $resto->attachTags(['late']);", "\n        if (app()->isLocal()) {\n            $resto->attachTags(['late']);\n        }", "\n        // a note\n        $resto->attachTags(['late']);"]) {
        const odd = restoText.replace(lastCoords, lastCoords.replace('\n\n        /*', extra + '\n\n        /*'));
        assert.notEqual(odd, restoText);
        assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace(), odd), new RegExp(`'${lastName}' has lines the editor cannot follow after its block \\(line \\d+\\): it is the last place of restaurants`));
    }
    // Right under the other calls, the line is part of the last place: the new block goes after it.
    const fine = restoText.replace(lastCoords, lastCoords.replace('\n\n        /*', "\n        $resto->attachTags(['late']);\n\n        /*"));
    const { after } = planPlaceAdd(file, 'restaurants', newPlace(), fine);
    assert.ok(after.includes("        $resto->attachTags(['late']);\n\n        $resto = Restaurant::create([\n            'name' => 'Menya Musashi',"));
    // Such lines on a place that is not the last do not stop an addition.
    const coords = "        $resto->fillCoordinatesFromString('35.657333, 139.703654');";
    assert.equal(planPlaceAdd(file, 'restaurants', newPlace(), restoText.replace(coords, `${coords}\n\n        $resto->attachTags(['curry']);`)).index, 13);
});

test('a place written inside an if is neither removed nor used as the place to add after', () => {
    // The last place wrapped in a condition: a block added after it would be seeded only when the condition holds.
    const start = restoText.lastIndexOf('        $resto = Restaurant::create([', restoText.indexOf('        /*\n        ///AAA///'));
    const end = restoText.indexOf('\n\n        /*\n        ///AAA///');
    const body = restoText.slice(start, end);
    const wrap = (open) => restoText.slice(0, start) + `        ${open}\n` + body.replace(/^/gm, '    ') + "\n        }\n        $resto->attachTags(['late']);" + restoText.slice(end);
    for (const open of ['if (app()->isLocal()) {', 'foreach ([1, 2] as $n) {', 'DB::transaction(function () {']) {
        const nested = wrap(open);
        assert.equal(parseSeederFull(nested, 'restaurants').length, 13); // still read, and editable
        assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace(), nested), /'Usagi' is written inside other code \(line \d+\).*add the new one in the seeder/);
        assert.throws(() => planPlaceRemoval(file, 'restaurants', 12, nested), /'Usagi' is written inside other code \(line \d+\).*remove it in the seeder/);
        // The place above it is concerned too: when that code does not run, the late call acts on it.
        assert.throws(() => planPlaceRemoval(file, 'restaurants', 11, nested), /cannot follow after its block/);
        // The one before is not.
        assert.equal(parseSeederFull(planPlaceRemoval(file, 'restaurants', 10, nested).after, 'restaurants').length, 12);
    }
    // The same code around the place, its lines left where they were: PHP does not read the indentation.
    for (const [open, close] of [['if (app()->isLocal()) {', '}'], ['if (app()->isLocal())', ''], ['if (false) {} else', ''], ['DB::transaction(function () {', '});'], ['if (app()->isLocal()) { // }', '}']]) {
        const flat = restoText.slice(0, start) + `        ${open}\n` + body + (close ? `\n        ${close}` : '') + restoText.slice(end);
        assert.equal(parseSeederFull(flat, 'restaurants').length, 13);
        assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace(), flat), /'Usagi' is written inside other code \(line \d+\)/, open);
        assert.throws(() => planPlaceRemoval(file, 'restaurants', 12, flat), /'Usagi' is written inside other code \(line \d+\)/, open);
    }
    // A brace in a string or in a comment opens nothing.
    for (const quiet of ["$note = 'if (x) {';", '// if (x) {', '/* { */', '$note = "{";']) {
        const flat = restoText.slice(0, start) + `        ${quiet}\n` + body + restoText.slice(end);
        assert.equal(planPlaceAdd(file, 'restaurants', newPlace(), flat).index, 13, quiet);
        assert.equal(parseSeederFull(planPlaceRemoval(file, 'restaurants', 12, flat).after, 'restaurants').length, 12, quiet);
    }
    // The alternative syntax has no braces to follow: no change of structure in such a file.
    const alternative = restoText.slice(0, start) + '        if (app()->isLocal()):\n' + body + '\n        endif;' + restoText.slice(end);
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace(), alternative), /the alternative syntax \(endif\) \(line \d+\)/);
    assert.throws(() => planPlaceRemoval(file, 'restaurants', 0, alternative), /the alternative syntax/);
    // A return above the last place, even in an if: the places below are seeded only when the method gets there.
    const guard = restoText.slice(0, start) + '        if (app()->isProduction()) {\n            return;\n        }\n\n' + restoText.slice(start);
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace(), guard), /can stop before its end \(return, line \d+\)/);
    assert.equal(parseSeederFull(planPlaceRemoval(file, 'restaurants', 12, guard).after, 'restaurants').length, 12); // a removal is the same removal either way

    // A return inside a closure only leaves the closure; an exit there stops the seeding.
    const callback = (word) => restoText.slice(0, start) + `        $twice = array_map(function ($x) {\n            ${word} $x * 2;\n        }, [1, 2]);\n\n` + restoText.slice(start);
    assert.equal(planPlaceAdd(file, 'restaurants', newPlace(), callback('return')).index, 13);
    assert.throws(() => planPlaceAdd(file, 'restaurants', newPlace(), callback('exit')), /can stop before its end \(exit/);

    // The opening brace of the method on the line of its declaration is a method all the same.
    const sameLine = restoText.replace(/(\n\s*public function run\(\)[^\n]*)\n\s*\{/, '$1 {');
    assert.notEqual(sameLine, restoText);
    assert.equal(planPlaceAdd(file, 'restaurants', newPlace(), sameLine).index, 13);
});

test('the same variable name in another method is other code, not a line of the last place', () => {
    // The real restaurants seeder ends with a helper method whose loop uses $resto too.
    const helper = restoText.replace(/\n    }\n}\n$/, "\n    }\n\n    private function fill(): void\n    {\n        foreach (Restaurant::all() as $resto) {\n            $resto->fillCoordinatesFromGoogleLink();\n        }\n    }\n}\n");
    assert.notEqual(helper, restoText);
    const plan = planPlaceRemoval(file, 'restaurants', 12, helper);
    assert.ok(plan.after.includes('$resto->fillCoordinatesFromGoogleLink();'));
    assert.equal(parseSeederFull(plan.after, 'restaurants').length, 12);
});

test('adding then removing the new place gives the text back', () => {
    const added = planPlaceAdd(file, 'restaurants', newPlace(), restoText);
    assert.equal(planPlaceRemoval(file, 'restaurants', added.index, added.after).after, restoText);
});
