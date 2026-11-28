// The pure functions of whereto-local.js (whereToLocalCore): the server's rules, reproduced in the phone.
// The script is plain browser JavaScript: it is loaded here in a vm context, where there is no document, so only
// whereToLocalCore is defined. The expected values come from the PHP code (PlaceSearch, Position, GeoUtils,
// SuggestionController) and its tests.
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { test } from 'node:test';
import vm from 'node:vm';

const here = dirname(fileURLToPath(import.meta.url));
const source = readFileSync(join(here, '..', '..', '..', 'source', 'public', 'js', 'whereto-local.js'), 'utf8');
const context = vm.createContext({});
vm.runInContext(source, context);
const core = context.whereToLocalCore;

const STATION = { lat: 35.658789, lon: 139.701262 };
const NEAR = '35.656113,139.699425'; // 300 m from the station
const params = query => new URLSearchParams(query);
// vm objects come from another realm: compare them as plain JSON values
const plain = value => JSON.parse(JSON.stringify(value));

/** Tokyo is UTC+9 all year (no summer time) */
const tokyo = (hours, minutes, seconds, milliseconds = 0) => new Date(Date.UTC(2026, 10, 27, hours - 9, minutes, seconds, milliseconds));

test('lunch time is 11:30:00 to 14:00:00 in Tokyo, both included', () => {
  assert.equal(core.isLunchTime(tokyo(11, 29, 59)), false);
  assert.equal(core.isLunchTime(tokyo(11, 29, 59, 999)), false);
  assert.equal(core.isLunchTime(tokyo(11, 30, 0)), true);
  assert.equal(core.isLunchTime(tokyo(12, 0, 0)), true);
  assert.equal(core.isLunchTime(tokyo(14, 0, 0)), true);
  // Carbon::isBetween compares to the microsecond: 14:00:00.5 is after 14:00:00
  assert.equal(core.isLunchTime(tokyo(14, 0, 0, 500)), false);
  assert.equal(core.isLunchTime(tokyo(14, 0, 1)), false);
  assert.equal(core.isLunchTime(tokyo(9, 0, 0)), false);
  assert.equal(core.isLunchTime(tokyo(0, 0, 0)), false);
});

test('lunch time does not depend on the time zone of the phone', () => {
  // The same instants, read with the process in other zones: only Tokyo time counts
  for (const zone of ['Europe/Paris', 'America/Los_Angeles', 'Pacific/Kiritimati']) {
    const zoned = vm.createContext({});
    const previous = process.env.TZ;
    process.env.TZ = zone;
    try {
      vm.runInContext(source, zoned);
      assert.equal(zoned.whereToLocalCore.isLunchTime(tokyo(12, 0, 0)), true, zone);
      assert.equal(zoned.whereToLocalCore.isLunchTime(tokyo(14, 0, 1)), false, zone);
      assert.notEqual(tokyo(12, 0, 0).getHours(), 12, `the process really is in ${zone}`);
    } finally {
      process.env.TZ = previous;
    }
  }
});

test('the tags are read as the server reads them', () => {
  assert.equal(core.parseTags(null), null);
  assert.equal(core.parseTags(''), null);
  assert.equal(core.parseTags('0'), null, 'PHP empty("0")');
  assert.deepEqual(plain(core.parseTags(',')), ['', ''], 'two empty tags');
  assert.deepEqual(plain(core.parseTags('a, b')), ['a', 'b']);
  assert.deepEqual(plain(core.parseTags('a,b')), ['a', 'b']);
  assert.deepEqual(plain(core.parseTags('a ,b')), ['a ', 'b'], 'only ", " loses its space');
  assert.deepEqual(plain(core.parseTags('a,  b')), ['a', ' b'], 'str_replace is not repeated');
  assert.deepEqual(plain(core.parseTags('private rooms')), ['private rooms']);
});

test('the operators and orders fall back to the defaults of the server, case included', () => {
  assert.equal(core.operator('OR'), 'OR');
  assert.equal(core.operator('or'), 'AND');
  assert.equal(core.operator(null), 'AND');
  assert.equal(core.operator('XYZ'), 'AND');
  assert.equal(core.orderBy('cost'), 'cost');
  assert.equal(core.orderBy('Cost'), 'distance');
  assert.equal(core.orderBy(null), 'distance');
});

test('the four operators', () => {
  const tags = ['a', 'b'];
  // AND: all the tags; OR: any; ANDNOT: none; ORNOT: a tag outside the list
  const cases = [
    [['a', 'b'], { AND: true, OR: true, ANDNOT: false, ORNOT: false }],
    [['a', 'b', 'c'], { AND: true, OR: true, ANDNOT: false, ORNOT: true }],
    [['a'], { AND: false, OR: true, ANDNOT: false, ORNOT: false }],
    [['c'], { AND: false, OR: false, ANDNOT: true, ORNOT: true }],
    [[], { AND: false, OR: false, ANDNOT: true, ORNOT: false }],
  ];
  for (const [placeTags, expected] of cases) {
    for (const [op, result] of Object.entries(expected)) {
      assert.equal(core.matches(placeTags, tags, op), result, `${op} on [${placeTags}]`);
    }
  }
  // No tags: every place
  assert.equal(core.matches([], null, 'AND'), true);
  // AND compares the count of matching tags with the count asked: a tag asked twice never matches
  assert.equal(core.matches(['a'], ['a', 'a'], 'AND'), false);
  // "," asks two empty tags: AND and OR find nothing, ANDNOT everything
  assert.equal(core.matches(['a'], ['', ''], 'AND'), false);
  assert.equal(core.matches(['a'], ['', ''], 'OR'), false);
  assert.equal(core.matches(['a'], ['', ''], 'ANDNOT'), true);
});

test('positions are accepted and refused as Position::tryFromString does', () => {
  // Expected values printed by PHP 8.5 with the code of GeoUtils::toPositionArray
  const cases = [
    ['35.658789, 139.701262', [35.658789, 139.701262]],
    ['35.6586,139.7016', [35.6586, 139.7016]],
    ['35.6610036,139.6899592,15', [35.6610036, 139.6899592]],
    ['35.6586,139.7016,abc', [35.6586, 139.7016]],
    ['+35.6586,+139.7016', [35.6586, 139.7016]],
    ['-35.6,-139.7', [-35.6, -139.7]],
    ['.5,.5', [0.5, 0.5]],
    ['5.,5.', [5, 5]],
    ['3.5656113e1,1.39699425e2', [35.656113, 139.699425]],
    ['+.5,-.5e-3', [0.5, -0.0005]],
    [' 35.6 , 139.7 ', [35.6, 139.7]],
    ['35.6\t,139.7', [35.6, 139.7]],
    ['\t35.6,\n139.7', [35.6, 139.7]],
    ['35,6,139,7', [35, 6]],
    ['1e400,1', [Infinity, 1]],
    ['null', null],
    ['', null],
    [null, null],
    ['garbage', null],
    ['35.6586', null],
    ['35.6586,', null],
    ['35.6586,abc', null],
    [',139.7', null],
    ['1e,2', null],
    ['0x10,0x20', null],
    ['Infinity,1', null],
    ['NaN,1', null],
    ['1_0,2', null],
    ['٣٥,١٣٩', null],
    [' , ', null],
    ['1.,.e1', null],
  ];
  for (const [text, expected] of cases) {
    const position = core.parsePosition(text);
    assert.deepEqual(position === null ? null : [position.lat, position.lon], expected, JSON.stringify(text));
  }
});

test('the position falls back to the station when missing, invalid or further than 2,500 m', () => {
  assert.deepEqual(plain(core.resolvePosition(null)), STATION);
  assert.deepEqual(plain(core.resolvePosition('null')), STATION);
  assert.deepEqual(plain(core.resolvePosition('abc')), STATION);
  assert.deepEqual(plain(core.resolvePosition('35.748789,139.701262')), STATION, '10 km north');
  assert.deepEqual(plain(core.resolvePosition(NEAR)), { lat: 35.656113, lon: 139.699425 });
  // About 2.2 km north: still accepted; about 2.8 km: the station
  assert.deepEqual(plain(core.resolvePosition('35.6788,139.701262')), { lat: 35.6788, lon: 139.701262 });
  assert.deepEqual(plain(core.resolvePosition('35.6838,139.701262')), STATION);
});

test('distances are the equirectangular approximation of GeoUtils, in metres', () => {
  // From GeoUtilsTest / PositionTest: the station to Hachiko is about 60 m
  const hachiko = core.distance(STATION.lat, STATION.lon, 35.659050, 139.700640);
  assert.ok(hachiko > 50 && hachiko < 70, String(hachiko));
  // Values printed by PHP: GeoUtils::distance(35.656113, 139.699425, 35.658789, 139.701262)
  assert.equal(core.distance(35.656113, 139.699425, 35.658789, 139.701262), 340.7142132235823);
  assert.equal(core.walkingTime(340.7142132235823), 7);
  assert.equal(core.walkingTime(0), 0);
  assert.equal(core.walkingTime(null), 0, 'a place without coordinates shows 0');
  assert.equal(core.walkingTime(50), 1);
});

/** Three places: 1 the best, near the station; 2 without coordinates; 3 cheap, a little further */
const DATA = {
  byRatings: [1, 2, 3],
  byCost: [3, 2, 1],
  places: {
    1: { tags: ['ramen', 'noodles'], lat: 35.6586, lon: 139.7016 },
    2: { tags: ['ramen'], lat: null, lon: null },
    3: { tags: ['curry'], lat: 35.6560, lon: 139.6990 },
  },
};
const ids = rows => plain(rows).map(row => row.id);

test('the orders: ratings and cost keep the server order, distance sorts and drops the places without coordinates', () => {
  assert.deepEqual(ids(core.search(DATA, params('orderBy=ratings'))), [1, 2, 3]);
  assert.deepEqual(ids(core.search(DATA, params('orderBy=cost'))), [3, 2, 1]);
  assert.deepEqual(ids(core.search(DATA, params('orderBy=distance&position=null'))), [1, 3]);
  assert.deepEqual(ids(core.search(DATA, params(`orderBy=distance&position=${NEAR}`))), [3, 1]);
  // Unknown order: distance. A kind without byCost: cost is ratings
  assert.deepEqual(ids(core.search(DATA, params('orderBy=XYZ'))), [1, 3]);
  const noCost = { byRatings: DATA.byRatings, places: DATA.places };
  assert.deepEqual(ids(core.search(noCost, params('orderBy=cost'))), [1, 2, 3]);
  // The place without coordinates: null distance (never NaN), 0 minutes in the row
  const row = plain(core.search(DATA, params('orderBy=ratings'))).find(found => found.id === 2);
  assert.equal(row.distance, null);
});

test('equal distances keep the ratings order (stable sort, as PHP)', () => {
  const twins = { byRatings: [5, 4], places: { 4: { tags: [], lat: 35.66, lon: 139.70 }, 5: { tags: [], lat: 35.66, lon: 139.70 } } };
  assert.deepEqual(ids(core.search(twins, params('orderBy=distance'))), [5, 4]);
});

test('the tag filter of a search, with the query string as the browser sends it', () => {
  assert.deepEqual(ids(core.search(DATA, params('op=AND&tags=ramen%2Cnoodles&orderBy=ratings'))), [1]);
  assert.deepEqual(ids(core.search(DATA, params('op=OR&tags=noodles,curry&orderBy=ratings'))), [1, 3]);
  assert.deepEqual(ids(core.search(DATA, params('op=ANDNOT&tags=ramen&orderBy=ratings'))), [3]);
  assert.deepEqual(ids(core.search(DATA, params('op=ORNOT&tags=ramen&orderBy=ratings'))), [1, 3]);
  assert.deepEqual(ids(core.search(DATA, params('tags=ramen,%20noodles&orderBy=ratings'))), [1], '", " is one separator');
  assert.deepEqual(ids(core.search(DATA, params('tags=0&orderBy=ratings'))), [1, 2, 3]);
  assert.deepEqual(ids(core.search(DATA, params('tags=,&orderBy=ratings'))), []);
  // A repeated parameter: the last one, as PHP reads a query string
  assert.deepEqual(ids(core.search(DATA, params('tags=curry&tags=ramen&orderBy=ratings'))), [1, 2]);
  assert.deepEqual(ids(core.search(DATA, params('tags=nothing'))), []);
});

test('the suggestion: lunch time only, the 35 nearest of the 100 best with coordinates, one at random', () => {
  const noon = tokyo(12, 0, 0);
  assert.equal(core.suggestion(DATA, 'null', tokyo(9, 0, 0), () => 0), null, 'outside lunch time');
  // Place 2 has no coordinates: never suggested
  const picked = new Set();
  for (let i = 0; i < 20; i++) {
    picked.add(core.suggestion(DATA, 'null', noon, () => i / 20));
  }
  assert.deepEqual([...picked].sort(), [1, 3]);
  // The random source decides: 0 is the nearest, just under 1 the furthest
  assert.equal(core.suggestion(DATA, NEAR, noon, () => 0), 3);
  assert.equal(core.suggestion(DATA, NEAR, noon, () => 0.999), 1);
  // No candidate: null (the page then shows an empty suggestion, as the server's empty string)
  assert.equal(core.suggestion({ byRatings: [2], places: { 2: DATA.places[2] } }, 'null', noon, () => 0), null);
});

test('the suggestion picks among the 35 nearest of the first 100 only', () => {
  // 120 places on a line going north from the station, the best first: the 100 best are the nearest 100 here
  const places = {};
  const byRatings = [];
  for (let id = 1; id <= 120; id++) {
    places[id] = { tags: [], lat: STATION.lat + id * 0.0001, lon: STATION.lon };
    byRatings.push(id);
  }
  // In reverse rating order, the places 101 to 120 are never in the 100 best
  byRatings.reverse();
  const data = { byRatings, places };
  const seen = new Set();
  for (let i = 0; i < 100; i++) {
    seen.add(core.suggestion(data, 'null', tokyo(12, 0, 0), () => i / 100));
  }
  // The 100 best are 120..21; the 35 nearest of them are 21..55
  assert.deepEqual([...seen].sort((a, b) => a - b), Array.from({ length: 35 }, (_, i) => 21 + i));
});
