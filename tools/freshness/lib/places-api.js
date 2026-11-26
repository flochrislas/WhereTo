// A small client for the Google Places API (New) calls the tool needs.
// https://developers.google.com/maps/documentation/places/web-service/text-search
// https://developers.google.com/maps/documentation/places/web-service/place-details

const BASE_URL = 'https://places.googleapis.com/v1';
// The fields to return. Google bills by the fields asked for: these all fall in the "Pro" SKU.
const SEARCH_FIELDS = 'places.id,places.displayName,places.formattedAddress,places.location,places.businessStatus';
const DETAILS_FIELDS = 'id,displayName,formattedAddress,location,businessStatus';
// Ask for names in English where Google has one, so they compare with ours.
const LANGUAGE = 'en';
// check --names: only the name, in Japanese (same Pro SKU as the other Details call).
const JAPANESE_FIELDS = 'displayName';

// Creates the client. `fetch`, `now` and `sleep` can be replaced in tests (no network there).
// minIntervalMs: the minimum time between two requests (200 ms = 5 requests per second).
export function createPlacesClient({
    apiKey,
    fetch = globalThis.fetch,
    minIntervalMs = 200,
    now = Date.now,
    sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms)),
}) {
    if (!apiKey) throw new Error('GOOGLE_PLACES_API_KEY is not set (see tools/freshness/README.md)');
    let lastRequest = -Infinity; // when the previous request started

    // Waits until minIntervalMs has passed since the previous request.
    async function throttle() {
        const wait = lastRequest + minIntervalMs - now();
        if (wait > 0) await sleep(wait);
        lastRequest = now();
    }

    // Sends a request; returns the JSON body, or null on 404. Throws on any other error.
    async function request(url, options) {
        await throttle();
        const response = await fetch(url, options);
        if (response.status === 404) return null;
        const body = await response.json().catch(() => ({})); // an error page may not be JSON
        if (!response.ok) {
            const error = new Error(`Google Places API: HTTP ${response.status} ${body.error?.message ?? ''}`.trim());
            error.status = response.status;
            throw error;
        }
        return body;
    }

    return {
        // Text Search: the places named `name` around our coordinates (a list, maybe empty).
        async searchText({ name, lat, lon }) {
            const body = await request(`${BASE_URL}/places:searchText`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Goog-Api-Key': apiKey, 'X-Goog-FieldMask': SEARCH_FIELDS },
                body: JSON.stringify({
                    textQuery: name,
                    languageCode: LANGUAGE,
                    // A bias, not a restriction: Google may still return places farther away.
                    locationBias: { circle: { center: { latitude: lat, longitude: lon }, radius: 50 } },
                }),
            });
            return body?.places ?? [];
        },

        // Place Details: the place with this id, or null when Google no longer knows it.
        async getDetails(id) {
            return request(`${BASE_URL}/places/${encodeURIComponent(id)}?languageCode=${LANGUAGE}`, {
                headers: { 'X-Goog-Api-Key': apiKey, 'X-Goog-FieldMask': DETAILS_FIELDS },
            });
        },

        // Place Details in Japanese: Google's Japanese name of the place, or null (unknown id, no name).
        // Google may answer with another language when it has no Japanese name. The caller only
        // compares it with ours: Google's terms forbid storing it (doc/11-data-freshness.md, section 8).
        async getJapaneseName(id) {
            const body = await request(`${BASE_URL}/places/${encodeURIComponent(id)}?languageCode=ja`, {
                headers: { 'X-Goog-Api-Key': apiKey, 'X-Goog-FieldMask': JAPANESE_FIELDS },
            });
            return body?.displayName?.text ?? null;
        },
    };
}
