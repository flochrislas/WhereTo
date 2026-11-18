import { test } from 'node:test';
import assert from 'node:assert/strict';
import { classify, normalizeName, nameMatch, namesMatch, distanceMetres, pickCandidate, VERDICT_STATUS } from '../lib/classify.js';

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

test('not replaced when the business on our spot is not open', () => {
    assert.equal(classify(ours, google({ name: 'Ichiran', status: 'CLOSED_TEMPORARILY', north: 5 })).verdict, 'unsure');
    assert.equal(classify(ours, google({ name: 'Ichiran', status: 'CLOSED_PERMANENTLY', north: 5 })).verdict, 'unsure');
    assert.equal(classify(ours, google({ name: 'Ichiran', status: null, north: 5 })).verdict, 'unsure');
});

test('a building with several tenants: our name elsewhere in the list keeps us operational', () => {
    const neighbour = google({ name: 'Ichiran', north: 3, id: 'neighbour' });
    const us = google({ name: 'Hakata Furyu', north: 25, id: 'us' });
    const results = [neighbour, us];
    const candidate = pickCandidate(ours, results);
    assert.equal(candidate.id, 'us');
    assert.equal(classify(ours, candidate, { results }).verdict, 'operational');
    // Our name in the list but too far to be us: not replaced either, unsure.
    const farUs = google({ name: 'Hakata Furyu', north: 400, id: 'far' });
    const r = classify(ours, pickCandidate(ours, [neighbour, farUs]), { results: [neighbour, farUs] });
    assert.equal(r.verdict, 'unsure');
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

test('unsure: nothing found, too far, another name a bit farther, or no status', () => {
    assert.equal(classify(ours, null).verdict, 'unsure');
    assert.equal(classify(ours, google({ north: 80 })).verdict, 'unsure');
    assert.equal(classify(ours, google({ status: 'CLOSED_PERMANENTLY', north: 80 })).verdict, 'unsure');
    assert.equal(classify(ours, google({ name: 'Ichiran', north: 30 })).verdict, 'unsure');
    assert.equal(classify(ours, google({ status: null })).verdict, 'unsure');
    assert.match(classify(ours, null).reason, /not found/);
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

test('pickCandidate prefers a matching name within 50 m, then the nearest result', () => {
    const far = google({ name: 'Hakata Furyu', north: 400, id: 'far' });
    const near = google({ name: 'Ichiran', north: 3, id: 'near' });
    const match = google({ name: 'Hakata Furyu', north: 20, id: 'match' });
    assert.equal(pickCandidate(ours, [far, near, match]).id, 'match');
    assert.equal(pickCandidate(ours, [far, near]).id, 'near');
    assert.equal(pickCandidate(ours, []), null);
});
