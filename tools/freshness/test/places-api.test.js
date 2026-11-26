import { test } from 'node:test';
import assert from 'node:assert/strict';
import { createPlacesClient } from '../lib/places-api.js';

// A fake fetch that records the calls and answers with the given responses, in order.
function fakeFetch(responses) {
    const calls = [];
    const fn = async (url, options = {}) => {
        calls.push({ url: String(url), options });
        const { status = 200, body = {} } = responses.shift() ?? {};
        return new Response(JSON.stringify(body), { status, headers: { 'Content-Type': 'application/json' } });
    };
    fn.calls = calls;
    return fn;
}

test('searchText posts the name, a 50 m circle, the key and the field mask', async () => {
    const fetch = fakeFetch([{ body: { places: [{ id: 'ChIJa' }] } }]);
    const client = createPlacesClient({ apiKey: 'KEY', fetch, minIntervalMs: 0 });
    const places = await client.searchText({ name: 'Hakata Furyu', lat: 35.65, lon: 139.69 });
    assert.deepEqual(places, [{ id: 'ChIJa' }]);
    const [call] = fetch.calls;
    assert.equal(call.url, 'https://places.googleapis.com/v1/places:searchText');
    assert.equal(call.options.method, 'POST');
    assert.equal(call.options.headers['X-Goog-Api-Key'], 'KEY');
    assert.equal(call.options.headers['X-Goog-FieldMask'], 'places.id,places.displayName,places.formattedAddress,places.location,places.businessStatus');
    assert.deepEqual(JSON.parse(call.options.body), {
        textQuery: 'Hakata Furyu',
        languageCode: 'en',
        locationBias: { circle: { center: { latitude: 35.65, longitude: 139.69 }, radius: 50 } },
    });
});

test('searchText returns an empty list when Google finds nothing', async () => {
    const client = createPlacesClient({ apiKey: 'KEY', fetch: fakeFetch([{ body: {} }]), minIntervalMs: 0 });
    assert.deepEqual(await client.searchText({ name: 'x', lat: 0, lon: 0 }), []);
});

test('getDetails gets the place by id, and returns null when Google no longer knows it', async () => {
    const fetch = fakeFetch([{ body: { id: 'ChIJa', businessStatus: 'OPERATIONAL' } }, { status: 404, body: { error: { status: 'NOT_FOUND' } } }]);
    const client = createPlacesClient({ apiKey: 'KEY', fetch, minIntervalMs: 0 });
    assert.deepEqual(await client.getDetails('ChIJa'), { id: 'ChIJa', businessStatus: 'OPERATIONAL' });
    assert.equal(fetch.calls[0].url, 'https://places.googleapis.com/v1/places/ChIJa?languageCode=en');
    assert.equal(fetch.calls[0].options.headers['X-Goog-FieldMask'], 'id,displayName,formattedAddress,location,businessStatus');
    assert.equal(await client.getDetails('ChIJgone'), null);
});

test('an API error throws with the status and Google message', async () => {
    const fetch = fakeFetch([{ status: 403, body: { error: { message: 'API key not valid' } } }]);
    const client = createPlacesClient({ apiKey: 'BAD', fetch, minIntervalMs: 0 });
    await assert.rejects(client.searchText({ name: 'x', lat: 0, lon: 0 }), (error) => {
        assert.equal(error.status, 403);
        assert.match(error.message, /403.*API key not valid/);
        return true;
    });
});

test('the client refuses to start without a key', () => {
    assert.throws(() => createPlacesClient({ apiKey: '' }), /GOOGLE_PLACES_API_KEY/);
});

test('requests are spaced by the minimum interval', async () => {
    const waits = [];
    let now = 1000;
    const client = createPlacesClient({
        apiKey: 'KEY',
        fetch: fakeFetch([{}, {}, {}]),
        minIntervalMs: 200,
        now: () => now,
        // A fake sleep that records the wait and moves the fake clock forward.
        sleep: async (ms) => { waits.push(ms); now += ms; },
    });
    await client.getDetails('a');
    now += 50; // 50 ms pass between the first and the second call
    await client.getDetails('b');
    await client.getDetails('c');
    assert.deepEqual(waits, [150, 200]);
});

test('getJapaneseName asks Place Details in Japanese for the name only', async () => {
    const fetch = fakeFetch([{ body: { displayName: { text: '博多風龍', languageCode: 'ja' } } }, { status: 404 }, { body: {} }]);
    const client = createPlacesClient({ apiKey: 'KEY', fetch, minIntervalMs: 0 });
    assert.equal(await client.getJapaneseName('ChIJa'), '博多風龍');
    assert.equal(fetch.calls[0].url, 'https://places.googleapis.com/v1/places/ChIJa?languageCode=ja');
    assert.equal(fetch.calls[0].options.headers['X-Goog-FieldMask'], 'displayName');
    assert.equal(await client.getJapaneseName('ChIJgone'), null); // unknown id
    assert.equal(await client.getJapaneseName('ChIJnoname'), null); // no name
});
