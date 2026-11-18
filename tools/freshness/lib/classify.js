// Decides what became of a place, from what Google says about it. Pure functions, no I/O.

// A Google result within this distance of our coordinates is "on our spot".
export const SAME_SPOT_METRES = 15;
// Beyond this distance, a Google result is not our place.
export const SEARCH_RADIUS_METRES = 50;

// The verdicts that change the status of a place, and the business_status they write.
// OPERATIONAL makes the seeder editor remove the business_status line (it is the default).
export const VERDICT_STATUS = {
    reopened: 'OPERATIONAL',
    closed_permanently: 'CLOSED_PERMANENTLY',
    closed_temporarily: 'CLOSED_TEMPORARILY',
    replaced: 'REPLACED',
};

// The words of a name: lower case, without accents, full-width letters made ordinary, split on
// anything that is not a letter or a digit ("Go! Go! Curry" -> ["go", "go", "curry"]).
function words(name) {
    return name
        .normalize('NFKC') // full-width letters (ＪＯＹ) become ordinary ones (JOY)
        .toLowerCase()
        .normalize('NFD') // splits "é" into "e" + an accent mark...
        .replace(/\p{M}/gu, '') // ...and this drops the accent marks
        .split(/[^\p{L}\p{N}]+/u) // cuts on spaces, punctuation, dashes...
        .filter(Boolean);
}

// The words that start the branch part of a name. Whole words only: "Shibuya109" is a name.
const BRANCH_WORDS = ['shibuya', '渋谷', '渋谷店'];

// Drops a "Shibuya" (or 渋谷...) word and everything after it, unless it is the first word:
// Google adds the branch after the name ("HUB Shibuya Center-gai" -> "HUB").
function withoutBranch(list) {
    const at = list.findIndex((w, i) => i > 0 && BRANCH_WORDS.includes(w));
    return at === -1 ? list : list.slice(0, at);
}

// Reduces a name to its letters and digits, without the branch: "Mc Donald's Shibuya" -> "mcdonalds".
// When the branch would leave less than 3 characters ("Le Shibuya" -> "le"), the name is kept
// whole: so short a rest would match far too many places.
export function normalizeName(name) {
    const all = words(name);
    const stripped = withoutBranch(all).join('');
    return stripped.length >= 3 ? stripped : all.join('');
}

// True when the text has Latin letters (a to z).
const hasLatin = (text) => /[a-z]/i.test(text.normalize('NFKC'));

// How well Google's name matches ours:
// - 'exact': equal once normalised ("Go Go Curry" / "GOGO CURRY Shibuya");
// - 'partial': our name (3 characters or more) is one or several whole words of Google's name
//   ("Hakata Furyu" / "Hakata Furyu Center Gai"), so "Bar" does not match "Barbacoa";
// - null: no match.
export function nameMatch(ours, google) {
    const x = normalizeName(ours);
    if (x === normalizeName(google)) return 'exact';
    if (x.length < 3) return null;
    const theirs = words(google);
    // Try every run of consecutive words of Google's name, glued together, against our name.
    for (let start = 0; start < theirs.length; start++) {
        let glued = '';
        for (let end = start; end < theirs.length && glued.length < x.length; end++) {
            glued += theirs[end];
            if (glued === x) return 'partial';
        }
    }
    return null;
}

// Boolean version of nameMatch.
export function namesMatch(ours, google) {
    return nameMatch(ours, google) !== null;
}

// Distance in metres between two points on Earth (haversine formula).
export function distanceMetres(lat1, lon1, lat2, lon2) {
    const R = 6371000; // mean Earth radius, in metres
    const rad = (deg) => (deg * Math.PI) / 180;
    const dLat = rad(lat2 - lat1);
    const dLon = rad(lon2 - lon1);
    const a = Math.sin(dLat / 2) ** 2 + Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(dLon / 2) ** 2;
    return 2 * R * Math.asin(Math.sqrt(a));
}

// Distance from our place to a Google place.
function distanceTo(place, result) {
    return distanceMetres(place.lat, place.lon, result.location.latitude, result.location.longitude);
}

// Google's name of a result ('' when absent).
const googleName = (result) => result.displayName?.text ?? '';

// The result nearest to our place in a list (undefined if the list is empty).
function nearest(place, list) {
    return list.reduce((best, r) => (best === undefined || distanceTo(place, r) < distanceTo(place, best) ? r : best), undefined);
}

// Among the Text Search results, picks the one to judge, whatever Google's order:
// the nearest exact name within 50 m, else the nearest partial name within 50 m, else the
// nearest result of all. null if none.
export function pickCandidate(place, results) {
    if (!results.length) return null;
    const close = results.filter((r) => distanceTo(place, r) <= SEARCH_RADIUS_METRES);
    const withMatch = (kind) => close.filter((r) => nameMatch(place.name, googleName(r)) === kind);
    return nearest(place, withMatch('exact')) ?? nearest(place, withMatch('partial')) ?? nearest(place, results);
}

// The verdict for a Google status, once we know the result is our place. `ours` is the
// business_status in our seeder (null when open): a temporarily closed place that Google sees
// open again has reopened.
function fromStatus(status, ours) {
    if (status === 'OPERATIONAL') {
        return ours === 'CLOSED_TEMPORARILY' ? { verdict: 'reopened', reason: 'Google says open again' } : { verdict: 'operational', reason: 'open' };
    }
    if (status === 'CLOSED_PERMANENTLY') return { verdict: 'closed_permanently', reason: 'Google says permanently closed' };
    if (status === 'CLOSED_TEMPORARILY') return { verdict: 'closed_temporarily', reason: 'Google says temporarily closed' };
    return { verdict: 'unsure', reason: 'Google gives no business status' };
}

// place: { name, lat, lon, businessStatus } from the seeder.
// result: the Google place to judge ({ id, displayName, location, businessStatus }) or null.
// options.byId: true when the result comes from Place Details on the id we stored, so it is our
//   place for sure and only its status matters.
// options.results: the whole Text Search list, to know whether our name is anywhere in it.
// Returns { verdict, reason, distance, idTrusted }: distance in metres (null when nothing was
// found); idTrusted tells whether Google's id is surely ours, so that apply may store it.
export function classify(place, result, { byId = false, results = result ? [result] : [] } = {}) {
    if (!result) return { verdict: 'unsure', reason: 'not found on Google', distance: null, idTrusted: false };
    const distance = distanceTo(place, result);
    const ours = place.businessStatus ?? null;
    if (byId) return { ...fromStatus(result.businessStatus, ours), distance, idTrusted: true };

    const unsure = (reason) => ({ verdict: 'unsure', reason, distance, idTrusted: false });
    if (distance > SEARCH_RADIUS_METRES) return unsure(`nearest result ${Math.round(distance)} m away`);

    const match = nameMatch(place.name, googleName(result));
    if (match === 'exact') return { ...fromStatus(result.businessStatus, ours), distance, idTrusted: true };
    if (match === 'partial') {
        // A partial name may be a sister business ("The HUB British Pub" for "Hub"): good enough to
        // say "still open", not to change our status, nor to store the id.
        const judged = fromStatus(result.businessStatus, ours);
        if (judged.verdict === 'operational' || judged.verdict === 'unsure') return { ...judged, distance, idTrusted: false };
        return unsure(`only a partial name match, Google says ${result.businessStatus}`);
    }

    // Another name. "Replaced" writes a status that hides the place, so every condition must hold.
    if (distance > SAME_SPOT_METRES) return unsure('another name, not on our spot');
    if (hasLatin(place.name) !== hasLatin(googleName(result))) {
        // "博多風龍" against "Hakata Furyu": the names cannot be compared, it may well be our place.
        return unsure('names in different scripts, cannot compare');
    }
    if (result.businessStatus !== 'OPERATIONAL') return unsure('another name on our spot, but not open');
    if (results.some((r) => namesMatch(place.name, googleName(r)))) {
        // A building with several tenants: our name is in the list, somewhere else.
        return unsure('another business on our spot, but our name is also in the results');
    }
    return { verdict: 'replaced', reason: 'another open business on our spot, our name nowhere', distance, idTrusted: false };
}
