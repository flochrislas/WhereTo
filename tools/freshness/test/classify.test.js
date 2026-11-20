import { test } from 'node:test';
import assert from 'node:assert/strict';
import { classify, normalizeName, nameMatch, namesMatch, nearMiss, editDistance, placeMatch, hasNearbyMatch, distanceMetres, pickCandidate, VERDICT_STATUS } from '../lib/classify.js';

const ours = { kind: 'restaurants', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468 };

// A Google place as the API returns it, `north` metres north of our place (1 m of latitude ~ 1/111,195 degree).
function google({ name = 'Hakata Furyu', status = 'OPERATIONAL', north = 0, id = 'ChIJx' } = {}) {
    const place = {
        id,
        displayName: { text: name, languageCode: 'en' },
        formattedAddress: '1-2-3 Dogenzaka, Shibuya City, Tokyo',
        location: { latitude: ours.lat + north / 111195, longitude: ours.lon },
    };
    if (status) place.businessStatus = status;
    return place;
}

test('names are compared without case, spaces, punctuation or the branch after "Shibuya"', () => {
    assert.equal(normalizeName('  Mc Donald\'s  SHIBUYA '), 'mcdonalds');
    assert.equal(normalizeName('Café  Kitsuné'), 'cafekitsune');
    assert.equal(normalizeName('ＪＯＹＳＯＵＮＤ 渋谷店'), 'joysound');
    assert.equal(normalizeName('HUB Shibuya Center-gai'), 'hub');
    // "Shibuya" as the first word is the name itself.
    assert.equal(normalizeName('Shibuya Sushi'), 'shibuyasushi');
    // Only the whole word is a branch: "Shibuya109" and "Shibuyabashi" are names.
    assert.equal(normalizeName('Magnet by Shibuya109'), 'magnetbyshibuya109');
    assert.equal(normalizeName('Cafe Shibuyabashi'), 'cafeshibuyabashi');
    // Less than 3 characters left after the branch: the name is kept whole.
    assert.equal(normalizeName('Le Shibuya'), 'leshibuya');
    assert.equal(nameMatch('Le Shibuya', 'Le'), null);
    assert.equal(nameMatch('Le', 'LE'), 'exact');
    assert.equal(nameMatch('Go Go Curry', 'GOGO CURRY Shibuya'), 'exact');
    assert.equal(nameMatch('Hub', 'HUB Shibuya Center-gai'), 'exact');
    // Our name as whole words of Google's name: a partial match (Google often adds the branch).
    assert.equal(nameMatch('Hakata Furyu', 'Hakata Furyu Center Gai'), 'partial');
    assert.equal(nameMatch('Hub', 'The HUB British Pub'), 'partial');
    // Not a whole word, or too short.
    assert.equal(nameMatch('Bar', 'Barbacoa'), null);
    assert.equal(nameMatch('Ba', 'Ba Ban'), null);
    assert.ok(!namesMatch('Hakata Furyu', 'Ichiran'));
});

test('a partial match also counts Google\'s name as whole words of ours', () => {
    assert.equal(nameMatch('Les deux magots', 'Deux Magots'), 'partial');
    assert.equal(nameMatch('Standing pizza bar Cona', 'CONA'), 'partial');
    // Whole words only, and 3 characters or more.
    assert.equal(nameMatch('Barbacoa Grill', 'Bar'), null);
    assert.equal(nameMatch('Le Petit', 'Le'), null);
});

test('a near miss counts as partial: one letter for 6 characters or more, two for 10 or more', () => {
    assert.equal(editDistance('tatsuichi', 'tasuichi'), 1);
    assert.equal(nameMatch('Tatsu Ichi', 'TASUICHI'), 'partial');
    assert.equal(nameMatch('Disc Union Shibuya', 'Disk Union Shibuya Jazz Rare Groove CD Record Store'), 'partial');
    assert.equal(nameMatch('Yasube', 'Tsukemen Yasubee Shibuya'), 'partial');
    assert.equal(nameMatch('Atomasashiya', 'Art Masashi-Ya'), 'partial'); // 12 characters, 2 apart
    assert.equal(nameMatch('Guykatsu Motomura', 'Gyukatsu Motomura Shibuya Sub-branch'), 'partial');
    assert.equal(nameMatch('Belle Mare Cafe', 'Bel Mare Cafe Shibuya'), 'partial');
    // Too short for a near miss: Denzo is not Denso, Ippudo not Ichiran.
    assert.equal(nameMatch('Denzo', 'Denso'), null);
    assert.equal(nearMiss('yasube', 'yasubee'), true);
    assert.equal(nearMiss('ramens', 'ramenz'), true);
    assert.equal(nearMiss('kitchen', 'kitchens2'), false); // 2 apart, but under 10 characters
    assert.equal(nameMatch('Ippudo', 'ICHIRAN Harajuku'), null);
});

test('false "replaced" cases from the first real run are now operational', () => {
    for (const [name, theirs] of [['Tatsu Ichi', 'TASUICHI'], ['Disc Union Shibuya', 'Disk Union Shibuya Jazz Rare Groove CD Record Store'], ['Atomasashiya', 'Art Masashi-Ya']]) {
        const place = { ...ours, name };
        const r = classify(place, google({ name: theirs, north: 2 }));
        assert.deepEqual([name, r.verdict, r.idTrusted], [name, 'operational', false]);
    }
    // A near miss never drives a status change.
    assert.equal(classify({ ...ours, name: 'Tatsu Ichi' }, google({ name: 'TASUICHI', status: 'CLOSED_PERMANENTLY' })).verdict, 'unsure');
});

test('a confirmed name_ja counts as an exact name', () => {
    const tengu = { ...ours, name: 'Tengu Sakaba', nameJa: '天狗酒場' };
    const japanese = google({ name: '天狗酒場 渋谷西口店', status: 'CLOSED_PERMANENTLY', north: 9 });
    assert.equal(placeMatch(tengu, japanese), 'exact');
    const r = classify(tengu, japanese, { results: [japanese] });
    assert.deepEqual([r.verdict, r.idTrusted], ['closed_permanently', true]);
    // Without a name_ja, or with another one: names in different scripts.
    assert.equal(classify({ ...tengu, nameJa: null }, japanese).verdict, 'unsure');
    assert.equal(placeMatch({ ...tengu, nameJa: '金田丸' }, japanese), null);
    assert.equal(hasNearbyMatch(tengu, [japanese]), true);
    assert.equal(hasNearbyMatch({ ...tengu, nameJa: null }, [japanese]), false);
});

test('the distance is in metres (haversine)', () => {
    assert.equal(distanceMetres(35.0, 139.0, 35.0, 139.0), 0);
    const d = distanceMetres(35.658545, 139.698468, 35.658545 + 100 / 111195, 139.698468);
    assert.ok(Math.abs(d - 100) < 0.5, `got ${d}`);
});

test('operational: same name, open, close by', () => {
    const r = classify(ours, google({ name: 'Hakata Furyu Shibuya', north: 20 }));
    assert.equal(r.verdict, 'operational');
    assert.equal(Math.round(r.distance), 20);
    assert.equal(r.idTrusted, true);
    // A partial name match is operational too, but its id is not stored.
    const partial = classify(ours, google({ name: 'Hakata Furyu Center Gai' }));
    assert.deepEqual([partial.verdict, partial.idTrusted], ['operational', false]);
});

test('a short chain name with the branch added is operational, not replaced', () => {
    const hub = { kind: 'bars', name: 'Hub', lat: ours.lat, lon: ours.lon };
    const r = classify(hub, google({ name: 'HUB Shibuya Center-gai', north: 6 }));
    assert.equal(r.verdict, 'operational');
});

test('closed permanently and closed temporarily', () => {
    assert.equal(classify(ours, google({ status: 'CLOSED_PERMANENTLY' })).verdict, 'closed_permanently');
    assert.equal(classify(ours, google({ status: 'CLOSED_TEMPORARILY', north: 30 })).verdict, 'closed_temporarily');
});

test('reopened: a temporarily closed place that Google sees open again', () => {
    const closed = { ...ours, businessStatus: 'CLOSED_TEMPORARILY' };
    assert.equal(classify(closed, google({ north: 10 })).verdict, 'reopened');
    assert.equal(classify(closed, google({ name: 'Other' }), { byId: true }).verdict, 'reopened');
    assert.equal(classify(closed, google({ status: 'CLOSED_TEMPORARILY' })).verdict, 'closed_temporarily');
    // Another name on the spot is not a reopening.
    assert.notEqual(classify(closed, google({ name: 'Ichiran', north: 30 })).verdict, 'reopened');
});

test('replaced: another open business on our spot, and our name nowhere in the results', () => {
    const other = google({ name: 'Ichiran', north: 10 });
    const r = classify(ours, other, { results: [other, google({ name: 'Ippudo', north: 300 })] });
    assert.deepEqual([r.verdict, r.idTrusted], ['replaced', false]);
});

test('not replaced when the business on our spot is not open: not found nearby', () => {
    for (const status of ['CLOSED_TEMPORARILY', 'CLOSED_PERMANENTLY', null]) {
        const r = classify(ours, google({ name: 'Ichiran', status, north: 5 }));
        assert.deepEqual([r.verdict, r.reason], ['not_found_nearby', 'no match within 200 m, other businesses on the spot']);
    }
});

test('a building with several tenants: our name elsewhere in the list keeps us operational', () => {
    const neighbour = google({ name: 'Ichiran', north: 3, id: 'neighbour' });
    const us = google({ name: 'Hakata Furyu', north: 25, id: 'us' });
    const results = [neighbour, us];
    const candidate = pickCandidate(ours, results);
    assert.equal(candidate.id, 'us');
    assert.equal(classify(ours, candidate, { results }).verdict, 'operational');
    // Our name in the list but too far to be us: not replaced either, not found nearby.
    const farUs = google({ name: 'Hakata Furyu', north: 400, id: 'far' });
    const r = classify(ours, pickCandidate(ours, [neighbour, farUs]), { results: [neighbour, farUs] });
    assert.deepEqual([r.verdict, r.reason], ['not_found_nearby', 'no match within 200 m, nearest same name 400 m away']);
    // Ours absent from the list: replaced.
    assert.equal(classify(ours, neighbour, { results: [neighbour] }).verdict, 'replaced');
});

test('a partial name match never changes our status', () => {
    const closed = google({ name: 'Hakata Furyu Annex', status: 'CLOSED_PERMANENTLY', north: 10 });
    const r = classify(ours, closed);
    assert.deepEqual([r.verdict, r.idTrusted], ['unsure', false]);
    assert.match(r.reason, /partial/);
    assert.equal(classify(ours, google({ name: 'Hakata Furyu Annex', status: 'CLOSED_TEMPORARILY' })).verdict, 'unsure');
});

test('reviewer case: an open "Hub" is not closed by a sister pub farther away', () => {
    const hub = { kind: 'bars', name: 'Hub', lat: ours.lat, lon: ours.lon };
    const results = [
        google({ name: 'The HUB British Pub', status: 'CLOSED_PERMANENTLY', north: 40, id: 'pub' }),
        google({ name: 'HUB Shibuya Center-gai', status: 'OPERATIONAL', north: 3, id: 'hub' }),
    ];
    const candidate = pickCandidate(hub, results);
    assert.equal(candidate.id, 'hub');
    assert.equal(classify(hub, candidate, { results }).verdict, 'operational');
    // Even alone, the closed sister pub only makes it unsure.
    assert.equal(classify(hub, results[0], { results: [results[0]] }).verdict, 'unsure');
});

test('reviewer case: a temporarily closed place does not reopen on a partial name', () => {
    const kindenmaru = { kind: 'restaurants', name: 'Kindenmaru', lat: ours.lat, lon: ours.lon, businessStatus: 'CLOSED_TEMPORARILY' };
    const annex = google({ name: 'Kindenmaru Ramen Annex', status: 'OPERATIONAL', north: 45 });
    const r = classify(kindenmaru, pickCandidate(kindenmaru, [annex]), { results: [annex] });
    assert.equal(r.verdict, 'unsure');
    assert.match(r.reason, /partial/);
});

test('an exact name at 40 m beats a partial name at 3 m', () => {
    const results = [
        google({ name: 'Hakata Furyu Center Gai', north: 3, id: 'partial' }),
        google({ name: 'Hakata Furyu', north: 40, id: 'exact' }),
    ];
    assert.equal(pickCandidate(ours, results).id, 'exact');
    // And among exact names, the nearest.
    assert.equal(pickCandidate(ours, [google({ north: 45, id: 'far' }), google({ north: 10, id: 'near' })]).id, 'near');
});

test('a Japanese-only name on our spot is unsure, never replaced', () => {
    const japanese = google({ name: '博多風龍 渋谷店', north: 4 });
    assert.equal(classify(ours, japanese, { results: [japanese] }).verdict, 'unsure');
    assert.equal(classify(ours, google({ name: '一蘭', north: 30 })).verdict, 'unsure');
});

test('unsure: our name 50 to 200 m away but not open, or no status', () => {
    const r = classify(ours, google({ status: 'CLOSED_PERMANENTLY', north: 80 }));
    assert.deepEqual([r.verdict, r.reason], ['unsure', 'same name 80 m away, Google says CLOSED_PERMANENTLY']);
    // A temporarily closed place does not reopen on a result that far.
    assert.equal(classify({ ...ours, businessStatus: 'CLOSED_TEMPORARILY' }, google({ north: 80 })).verdict, 'unsure');
    assert.equal(classify(ours, google({ status: null })).verdict, 'unsure');
});

test('operational: our name 50 to 200 m away and open, without storing the id', () => {
    const r = classify(ours, google({ north: 97 }));
    assert.deepEqual([r.verdict, r.reason, r.idTrusted], ['operational', 'same name 97 m away', false]);
    const partial = classify(ours, google({ name: 'Hakata Furyu Center Gai', north: 150 }));
    assert.deepEqual([partial.verdict, partial.idTrusted], ['operational', false]);
    // pickCandidate takes it over a stranger nearer.
    const stranger = google({ name: 'Ichiran', north: 5, id: 'stranger' });
    const us = google({ north: 120, id: 'us' });
    assert.equal(pickCandidate(ours, [stranger, us]).id, 'us');
});

test('not found nearby: nothing found, or no result with our name within 200 m', () => {
    assert.deepEqual([classify(ours, null).verdict, classify(ours, null).reason], ['not_found_nearby', 'no match within 200 m, nothing found']);
    const far = classify(ours, google({ north: 1745 }));
    assert.deepEqual([far.verdict, far.reason, far.idTrusted], ['not_found_nearby', 'no match within 200 m, nearest same name 1745 m away', false]);
    const other = classify(ours, google({ name: 'Ichiran', north: 30 }));
    assert.deepEqual([other.verdict, other.reason], ['not_found_nearby', 'no match within 200 m, nearest result 30 m away']);
});

test('a place looked up by its stored id trusts the status, whatever the name', () => {
    const renamed = google({ name: '博多風龍 渋谷', status: 'CLOSED_PERMANENTLY', north: 25 });
    const r = classify(ours, renamed, { byId: true });
    assert.deepEqual([r.verdict, r.idTrusted], ['closed_permanently', true]);
    assert.equal(classify(ours, google({ name: 'Other', north: 25 }), { byId: true }).verdict, 'operational');
});

test('the verdicts that change the status map to the app values', () => {
    assert.deepEqual(VERDICT_STATUS, {
        reopened: 'OPERATIONAL',
        closed_permanently: 'CLOSED_PERMANENTLY',
        closed_temporarily: 'CLOSED_TEMPORARILY',
        replaced: 'REPLACED',
    });
});

test('pickCandidate prefers a matching name within 50 m, then within 200 m, then the nearest result', () => {
    const far = google({ name: 'Hakata Furyu', north: 400, id: 'far' });
    const near = google({ name: 'Ichiran', north: 3, id: 'near' });
    const match = google({ name: 'Hakata Furyu', north: 20, id: 'match' });
    assert.equal(pickCandidate(ours, [far, near, match]).id, 'match');
    assert.equal(pickCandidate(ours, [far, near]).id, 'near');
    assert.equal(pickCandidate(ours, []), null);
});
