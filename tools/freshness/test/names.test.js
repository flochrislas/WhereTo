import { test } from 'node:test';
import assert from 'node:assert/strict';
import { matchPlace, resolveClaims, japaneseName, hasJapanese, normalizeJapaneseName, japaneseNameVerdict, NAME_ENDINGS } from '../lib/names.js';

const ours = { kind: 'restaurants', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468 };

// An Overpass node `north` metres north of our place (1 m of latitude ~ 1/111,195 degree).
function node(id, tags, north = 0) {
    return { type: 'node', id, lat: ours.lat + north / 111195, lon: ours.lon, tags: { amenity: 'restaurant', ...tags } };
}
// A way (a building outline), which Overpass gives with a centre instead of lat/lon.
function way(id, tags, north = 0) {
    return { type: 'way', id, center: { lat: ours.lat + north / 111195, lon: ours.lon }, tags: { amenity: 'restaurant', ...tags } };
}

test('the Japanese name is name:ja, else name when it has kana or kanji', () => {
    assert.equal(japaneseName({ name: 'Hakata Furyu', 'name:ja': '博多風龍' }), '博多風龍');
    assert.equal(japaneseName({ name: '博多風龍 渋谷店' }), '博多風龍 渋谷店'); // kept as written
    assert.equal(japaneseName({ name: 'Hakata Furyu' }), null);
    // A name:ja without kana or kanji is no Japanese name: the name rule applies, else nothing.
    assert.equal(japaneseName({ name: 'ハブ 渋谷店', 'name:ja': 'HUB' }), 'ハブ 渋谷店');
    assert.equal(japaneseName({ name: 'HUB', 'name:ja': 'HUB' }), null);
    assert.ok(hasJapanese('カラオケ館') && hasJapanese('一蘭') && hasJapanese('すし') && !hasJapanese('JOYSOUND'));
});

test('matched: an exact English name, via name:en', () => {
    const r = matchPlace(ours, [node(1, { name: '博多風龍', 'name:en': 'Hakata Furyu' }, 20)]);
    assert.deepEqual(r, { match: 'matched', nameJa: '博多風龍', element: 'node/1', elementName: '博多風龍', distance: 20, reason: 'exact English name' });
});

test('matched: a partial English name, via a mixed name', () => {
    const r = matchPlace(ours, [node(2, { name: 'Hakata Furyu 渋谷センター街店' }, 10)]);
    assert.equal(r.match, 'matched');
    assert.equal(r.reason, 'partial English name');
    assert.equal(r.nameJa, 'Hakata Furyu 渋谷センター街店');
});

test('matched: exact is preferred to partial, then the nearest', () => {
    const elements = [
        node(1, { name: '博多風龍 センター街', 'name:en': 'Hakata Furyu Center Gai' }, 5), // partial, nearer
        node(2, { name: '博多風龍 遠い', 'name:en': 'Hakata Furyu' }, 40), // exact, farther
        node(3, { name: '博多風龍', 'name:en': 'Hakata Furyu' }, 30), // exact, nearer
    ];
    assert.equal(matchPlace(ours, elements).element, 'node/3');
});

test('nothing beyond 50 m', () => {
    const r = matchPlace(ours, [node(1, { name: '博多風龍', 'name:en': 'Hakata Furyu' }, 60)]);
    assert.deepEqual([r.match, r.nameJa, r.element, r.distance], ['none', null, null, null]);
    assert.match(r.reason, /nothing named within 50 m/);
});

test('nearest: a single named element within 15 m, with a Japanese name', () => {
    const r = matchPlace(ours, [node(1, { name: 'らーめん 風' }, 8), node(2, { name: '別の店' }, 30)]);
    assert.deepEqual([r.match, r.nameJa, r.element, r.distance], ['nearest', 'らーめん 風', 'node/1', 8]);
});

test('none: two candidates on the spot', () => {
    const r = matchPlace(ours, [node(1, { name: 'らーめん 風' }, 8), node(2, { name: '寿司 海' }, 12)]);
    assert.equal(r.match, 'none');
    assert.match(r.reason, /2 named elements within 15 m/);
});

test('a Latin-only name is skipped as a source, but still counts as a neighbour on the spot', () => {
    // Our English name matches, but the element has no Japanese name: nothing to fill name_ja with.
    assert.equal(matchPlace(ours, [node(1, { name: 'Hakata Furyu' }, 5)]).match, 'none');
    assert.match(matchPlace(ours, [node(1, { name: 'Hakata Furyu' }, 5)]).reason, /no Japanese name/);
    // A Japanese-named element on the spot next to a Latin-only one: two candidates, no guess.
    assert.equal(matchPlace(ours, [node(1, { name: 'Some Bar' }, 5), node(2, { name: 'らーめん 風' }, 6)]).match, 'none');
    // ...but a Japanese-named element whose English name matches still wins.
    const r = matchPlace(ours, [node(1, { name: 'Hakata Furyu' }, 5), node(2, { name: '博多風龍', 'name:en': 'Hakata Furyu' }, 25)]);
    assert.deepEqual([r.match, r.element], ['matched', 'node/2']);
});

test('name:ja is preferred to name', () => {
    const r = matchPlace(ours, [node(1, { name: 'はかたふうりゅう', 'name:ja': '博多風龍', 'name:en': 'Hakata Furyu' }, 3)]);
    assert.equal(r.nameJa, '博多風龍');
    assert.equal(r.elementName, 'はかたふうりゅう');
});

test('a way is placed at its centre', () => {
    const r = matchPlace(ours, [way(77, { name: '博多風龍', 'name:en': 'Hakata Furyu' }, 12)]);
    assert.deepEqual([r.match, r.element, r.distance], ['matched', 'way/77', 12]);
    // An element without a position (not asked with "out center") is ignored.
    assert.equal(matchPlace(ours, [{ type: 'relation', id: 1, tags: { name: '博多風龍' } }]).match, 'none');
});

test('the Japanese comparison folds the width and drops spaces, punctuation and middle dots', () => {
    assert.equal(japaneseNameVerdict('博多風龍', '博多風龍'), 'confirmed');
    assert.equal(japaneseNameVerdict('ｶﾗｵｹ館', 'カラオケ館'), 'confirmed'); // half-width katakana
    assert.equal(japaneseNameVerdict('ＪＯＹＳＯＵＮＤ カラオケ', 'JOYSOUND カラオケ'), 'confirmed'); // full-width letters
    assert.equal(japaneseNameVerdict('博多 風龍', '博多　風龍'), 'confirmed'); // full-width space
    assert.equal(japaneseNameVerdict('スターバックス・コーヒー', 'スターバックス コーヒー！'), 'confirmed');
    assert.equal(normalizeJapaneseName('ＡＢＣ・キッチン'), 'abcキッチン');
});

test('the Japanese comparison drops a trailing branch from both sides', () => {
    assert.equal(japaneseNameVerdict('ラーメン二郎', 'ラーメン二郎 渋谷店'), 'confirmed');
    assert.equal(japaneseNameVerdict('ラーメン二郎 渋谷店', 'ラーメン二郎'), 'confirmed');
    assert.equal(japaneseNameVerdict('ラーメン二郎', 'ラーメン二郎渋谷店'), 'confirmed'); // no space: a 渋谷…店 suffix
    assert.equal(japaneseNameVerdict('一蘭', '一蘭 渋谷スペイン坂店'), 'confirmed');
    assert.equal(japaneseNameVerdict('博多風龍', '博多風龍 渋谷センター街'), 'confirmed'); // a last word starting with 渋谷
    assert.equal(japaneseNameVerdict('一蘭', '一蘭 道玄坂店'), 'confirmed'); // a last word ending in 店
    // The first word is the name, even when it looks like a branch.
    assert.equal(normalizeJapaneseName('渋谷店'), '渋谷店');
    assert.equal(normalizeJapaneseName('渋谷 ラーメン'), '渋谷ラーメン');
});

test('the Japanese comparison: different names, Latin only, no name', () => {
    assert.equal(japaneseNameVerdict('一蘭', '一風堂'), 'disputed');
    assert.equal(japaneseNameVerdict('一蘭', 'Ichiran Shibuya'), 'disputed'); // Google only knows a Latin name
    assert.equal(japaneseNameVerdict('一蘭', null), 'unchecked');
    assert.equal(japaneseNameVerdict('一蘭', ''), 'unchecked');
});

test('an element matching our English name without a Japanese name stops the nearest guess', () => {
    const blue = { name: 'Blue Bottle Coffee', lat: ours.lat, lon: ours.lon };
    const r = matchPlace(blue, [node(1, { name: 'Blue Bottle Coffee', amenity: 'cafe' }, 20), node(2, { name: 'ローソン', shop: 'convenience' }, 10)]);
    assert.deepEqual([r.match, r.nameJa, r.reason], ['none', null, 'matched element has no Japanese name']);
});

test('a nearest guess on an element claimed by several places becomes none; a matched one stays', () => {
    const izakaya = node(1, { name: '居酒屋 一' }, 5);
    const a = { name: 'Bistro A', lat: ours.lat, lon: ours.lon };
    const b = { name: 'Bar B', lat: ours.lat + 10 / 111195, lon: ours.lon }; // 10 m north, same building
    const both = resolveClaims([a, b].map((p) => ({ name: p.name, ...matchPlace(p, [izakaya]) })));
    assert.deepEqual(both.map((r) => [r.match, r.nameJa, r.element]), [['none', null, null], ['none', null, null]]);
    assert.equal(both[0].reason, 'node/1 claimed by 2 places');
    // The element carries our English name for A: A keeps it, B's guess on it is dropped.
    const named = node(1, { name: '居酒屋 一', 'name:en': 'Bistro A' }, 5);
    const out = resolveClaims([a, b].map((p) => ({ name: p.name, ...matchPlace(p, [named]) })));
    assert.deepEqual(out.map((r) => [r.match, r.element]), [['matched', 'node/1'], ['none', null]]);
    // A single claim is left alone.
    assert.equal(resolveClaims([{ name: 'A', ...matchPlace(a, [izakaya]) }])[0].match, 'nearest');
});

test('the Japanese comparison: brackets separate words', () => {
    assert.equal(japaneseNameVerdict('ラーメン二郎（渋谷店）', 'ラーメン二郎 渋谷店'), 'confirmed');
    assert.equal(japaneseNameVerdict('ラーメン二郎', 'ラーメン二郎(渋谷店)'), 'confirmed');
});

test('the Japanese comparison keeps a last word ending in 商店, 飯店... (part of the name)', () => {
    assert.deepEqual(NAME_ENDINGS, ['商店', '飯店', '酒店', '書店', '売店']);
    assert.equal(normalizeJapaneseName('らーめん 山田商店'), 'らーめん山田商店');
    assert.equal(japaneseNameVerdict('らーめん 山田商店', 'らーめん 渋谷店'), 'disputed');
    assert.equal(japaneseNameVerdict('中華 東方飯店', '中華 東方飯店'), 'confirmed');
});

test('a Latin-only Google name is compared like the status pass compares names', () => {
    assert.equal(japaneseNameVerdict('JOYSOUND 渋谷店', 'JOYSOUND'), 'confirmed');
    assert.equal(japaneseNameVerdict('HUB', 'Hub Shibuya'), 'confirmed');
    assert.equal(japaneseNameVerdict('HUB', 'Starbucks'), 'disputed');
    assert.equal(japaneseNameVerdict('ゴーゴーカレー', 'Go! Go! Curry'), 'disputed');
});

test('the OpenStreetMap matcher keeps the strict name rules (no near miss, no reverse partial)', () => {
    const suzu = { name: 'Suzu Cafe', lat: ours.lat, lon: ours.lon };
    // "Cafe" is a whole word of ours, but it would name our place after any café.
    const r = matchPlace(suzu, [node(1, { name: 'カフェ', 'name:en': 'Cafe' }, 30)]);
    assert.equal(r.match, 'none');
    const tatsu = { name: 'Tatsu Ichi', lat: ours.lat, lon: ours.lon };
    assert.equal(matchPlace(tatsu, [node(2, { name: 'たすいち', 'name:en': 'Tasuichi' }, 30)]).match, 'none');
    // The usual partial still works.
    assert.equal(matchPlace(ours, [node(3, { name: '博多風龍', 'name:en': 'Hakata Furyu Center Gai' }, 30)]).match, 'matched');
});
