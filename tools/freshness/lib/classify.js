// Decides what became of a place, from what Google says about it. Pure functions, no I/O.

// A Google result within this distance of our coordinates is "on our spot".
export const SAME_SPOT_METRES = 15;
// Within this distance, a Google result with our name is our place.
export const SEARCH_RADIUS_METRES = 50;
// Within this distance, a Google result with our name still says "open" (coordinates noted a bit off,
// or another branch of a chain); beyond it, nothing found nearby.
export const NEARBY_METRES = 200;

// The verdicts that change the status of a place, and the business_status they write.
// OPERATIONAL makes the seeder editor remove the business_status line (it is the default).
export const VERDICT_STATUS = {
    reopened: 'OPERATIONAL',
    closed_permanently: 'CLOSED_PERMANENTLY',
    closed_temporarily: 'CLOSED_TEMPORARILY',
    replaced: 'REPLACED',
};
// not_found_nearby (no result with our name within 200 m) has no automatic status: apply offers it
// unchecked, and a ticked one is written CLOSED_PERMANENTLY.

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
export const hasLatin = (text) => /[a-z]/i.test(text.normalize('NFKC'));

// The Levenshtein distance between two strings: the number of letters to add, remove or change.
export function editDistance(a, b) {
    let previous = Array.from({ length: b.length + 1 }, (_, k) => k);
    for (let i = 1; i <= a.length; i++) {
        const row = [i];
        for (let k = 1; k <= b.length; k++) {
            row[k] = Math.min(previous[k] + 1, row[k - 1] + 1, previous[k - 1] + (a[i - 1] === b[k - 1] ? 0 : 1));
        }
        previous = row;
    }
    return previous[b.length];
}

// A near miss: a spelling variant of the same name ("tatsuichi" / "tasuichi", "yasube" / "yasubee").
// One letter apart for names of 6 characters or more, two for names of 10 or more (the shorter decides).
export function nearMiss(a, b) {
    const shorter = Math.min(a.length, b.length);
    const d = Math.abs(a.length - b.length) > 2 ? 3 : editDistance(a, b);
    return (shorter >= 6 && d <= 1) || (shorter >= 10 && d <= 2);
}

// True when `target` (normalised) is one or several consecutive whole words of `list`, glued
// together, or (unless `exactly`) a near miss of them.
function inWords(target, list, exactly = false) {
    for (let start = 0; start < list.length; start++) {
        let glued = '';
        for (let end = start; end < list.length && glued.length <= target.length + 2; end++) {
            glued += list[end];
            if (glued === target || (!exactly && nearMiss(glued, target))) return true;
        }
    }
    return false;
}

// How well Google's name matches ours:
// - 'exact': equal once normalised ("Go Go Curry" / "GOGO CURRY Shibuya");
// - 'partial', any of:
//   - our name (3 characters or more) is one or several whole words of Google's name
//     ("Hakata Furyu" / "Hakata Furyu Center Gai"), so "Bar" does not match "Barbacoa";
//   - Google's name (3 characters or more) is whole words of ours ("Les deux magots" / "Deux Magots");
//   - a near miss, on the whole names or on those words ("Tatsu Ichi" / "TASUICHI",
//     "Yasube" / "Tsukemen Yasubee Shibuya");
// - null: no match.
// A partial match confirms an open place; it never changes our status.
// `strict`: only exact, or our name as whole words of theirs, spelled the same (the OpenStreetMap
// matcher of names writes what it matches, so it does not take the looser rules).
export function nameMatch(ours, google, { strict = false } = {}) {
    const x = normalizeName(ours);
    const y = normalizeName(google);
    if (x === y) return 'exact';
    if (strict) return x.length >= 3 && inWords(x, words(google), true) ? 'partial' : null;
    if (nearMiss(x, y)) return 'partial';
    if (x.length >= 3 && inWords(x, words(google))) return 'partial';
    if (y.length >= 3 && inWords(y, words(ours))) return 'partial';
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

// How well a Google result's name matches our place: nameMatch on the English names, and when the
// place has a name_ja and Google's name has kana or kanji, a confirmed Japanese name counts as exact.
export function placeMatch(place, result) {
    const theirs = googleName(result);
    if (place.nameJa && hasJapanese(theirs) && japaneseNameVerdict(place.nameJa, theirs) === 'confirmed') return 'exact';
    return nameMatch(place.name, theirs);
}

// The result nearest to our place in a list (undefined if the list is empty).
function nearest(place, list) {
    return list.reduce((best, r) => (best === undefined || distanceTo(place, r) < distanceTo(place, best) ? r : best), undefined);
}

// The nearest result whose name matches ours within `metres`: exact preferred to partial (undefined if none).
function nearestMatch(place, results, metres) {
    const close = results.filter((r) => distanceTo(place, r) <= metres);
    const withMatch = (kind) => close.filter((r) => placeMatch(place, r) === kind);
    return nearest(place, withMatch('exact')) ?? nearest(place, withMatch('partial'));
}

// True when a result within 200 m has our name: if not, check searches again with the name_ja.
export function hasNearbyMatch(place, results) {
    return nearestMatch(place, results, NEARBY_METRES) !== undefined;
}

// Among the Text Search results, picks the one to judge, whatever Google's order:
// our name within 50 m (exact, then partial, the nearest), else our name within 200 m, else the
// nearest result of all. null if none.
export function pickCandidate(place, results) {
    if (!results.length) return null;
    return nearestMatch(place, results, SEARCH_RADIUS_METRES) ?? nearestMatch(place, results, NEARBY_METRES) ?? nearest(place, results);
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

// place: { name, nameJa, lat, lon, businessStatus } from the seeder.
// result: the Google place to judge ({ id, displayName, location, businessStatus }) or null; from a
//   search, the one pickCandidate chose.
// options.byId: true when the result comes from Place Details on the id we stored, so it is our
//   place for sure and only its status matters.
// options.results: the whole Text Search list, to know whether our name is anywhere in it.
// Returns { verdict, reason, distance, idTrusted }: distance in metres (null when nothing was
// found); idTrusted tells whether Google's id is surely ours, so that apply may store it.
export function classify(place, result, { byId = false, results = result ? [result] : [] } = {}) {
    const ours = place.businessStatus ?? null;
    if (!result) return { verdict: 'not_found_nearby', reason: `no match within ${NEARBY_METRES} m, nothing found`, distance: null, idTrusted: false };
    const distance = distanceTo(place, result);
    if (byId) return { ...fromStatus(result.businessStatus, ours), distance, idTrusted: true };

    const unsure = (reason) => ({ verdict: 'unsure', reason, distance, idTrusted: false });
    const match = placeMatch(place, result);

    // Our name within 50 m: our place.
    if (match && distance <= SEARCH_RADIUS_METRES) {
        if (match === 'exact') return { ...fromStatus(result.businessStatus, ours), distance, idTrusted: true };
        // A partial name may be a sister business ("The HUB British Pub" for "Hub"): good enough to
        // say "still open", not to change our status, nor to store the id.
        const judged = fromStatus(result.businessStatus, ours);
        if (judged.verdict === 'operational' || judged.verdict === 'unsure') return { ...judged, distance, idTrusted: false };
        return unsure(`only a partial name match, Google says ${result.businessStatus}`);
    }

    // Our name 50 to 200 m away: still open (coordinates a bit off, or another branch of a chain, so
    // the id is not stored). Only "open" is believed that far: anything else is for a manual look.
    if (match && distance <= NEARBY_METRES) {
        const near = `same name ${Math.round(distance)} m away`;
        if (fromStatus(result.businessStatus, ours).verdict === 'operational') return { verdict: 'operational', reason: near, distance, idTrusted: false };
        return unsure(`${near}, Google says ${result.businessStatus ?? 'no status'}`);
    }

    // No result with our name within 200 m (pickCandidate would have picked it).
    // "博多風龍" against "Hakata Furyu" within 50 m: the names cannot be compared, it may well be our place.
    if (distance <= SEARCH_RADIUS_METRES && hasLatin(place.name) !== hasLatin(googleName(result))) {
        return unsure('names in different scripts, cannot compare');
    }
    // "Replaced" writes a status that hides the place, so every condition must hold: another open
    // business on our spot, and our name nowhere in the results, even far away.
    const farMatch = nearest(place, results.filter((r) => placeMatch(place, r)));
    if (distance <= SAME_SPOT_METRES && result.businessStatus === 'OPERATIONAL' && !farMatch) {
        return { verdict: 'replaced', reason: 'another open business on our spot, our name nowhere', distance, idTrusted: false };
    }
    const why = farMatch
        ? `nearest same name ${Math.round(distanceTo(place, farMatch))} m away`
        : distance <= SAME_SPOT_METRES ? 'other businesses on the spot' : `nearest result ${Math.round(distance)} m away`;
    return { verdict: 'not_found_nearby', reason: `no match within ${NEARBY_METRES} m, ${why}`, distance, idTrusted: false };
}

// Japanese names (used by the status pass with the seeder's name_ja, and by check --names).

// True when the text has kana or kanji (hiragana, katakana, CJK ideographs).
export const hasJapanese = (text) => /[\p{Script=Hiragana}\p{Script=Katakana}\p{Script=Han}]/u.test(text ?? '');

// Words ending in 店 that are part of a name, not a branch: 山田商店, 中華飯店...
export const NAME_ENDINGS = ['商店', '飯店', '酒店', '書店', '売店'];

// Drops a trailing branch from the words of a Japanese name: a last word ending in 店 (but not in
// one of NAME_ENDINGS) or starting with 渋谷 ("ラーメン二郎 渋谷店" -> "ラーメン二郎"); a name without
// spaces loses a trailing 渋谷…店 ("ラーメン二郎渋谷店" -> "ラーメン二郎"). The first word always
// stays: it is the name.
function withoutJapaneseBranch(words) {
    const last = words.at(-1);
    const branchShop = last.endsWith('店') && !NAME_ENDINGS.some((ending) => last.endsWith(ending));
    if (words.length > 1 && (branchShop || last.startsWith('渋谷'))) return words.slice(0, -1);
    if (words.length === 1) {
        const at = last.lastIndexOf('渋谷');
        if (at > 0 && last.endsWith('店')) return [last.slice(0, at)];
    }
    return words;
}

// Reduces a Japanese name for the comparison: width folded (ＡＢＣ -> ABC, ｶﾗｵｹ -> カラオケ), the
// branch dropped, then spaces (full-width ones too), punctuation, symbols and middle dots (・) removed,
// Latin letters in lower case. Brackets separate words: "ラーメン二郎（渋谷店）" is "ラーメン二郎 渋谷店".
// "ＪＯＹＳＯＵＮＤ・渋谷店" -> "joysound".
export function normalizeJapaneseName(name) {
    // NFKC turns the full-width space and brackets into ordinary ones.
    const words = name.normalize('NFKC').replace(/[()]/g, ' ').split(/\s+/u).filter(Boolean);
    if (!words.length) return '';
    return withoutJapaneseBranch(words).join('').replace(/[\p{P}\p{S}]/gu, '').toLowerCase();
}

// Our name_ja against Google's Japanese name:
// - 'confirmed': equal once normalised;
// - 'disputed': different;
// - 'unchecked': no Google name (the place was not found).
// When Google's name has no kana or kanji (a Latin brand: "JOYSOUND"), the two are compared like the
// status pass compares names (normalizeName: case, branch after "Shibuya" or 渋谷店), so ours
// "JOYSOUND 渋谷店" is confirmed while "ゴーゴーカレー" against "Go! Go! Curry" is disputed.
export function japaneseNameVerdict(ours, google) {
    if (!google) return 'unchecked';
    if (!hasJapanese(google)) return normalizeName(ours) === normalizeName(google) ? 'confirmed' : 'disputed';
    return normalizeJapaneseName(ours) === normalizeJapaneseName(google) ? 'confirmed' : 'disputed';
}
