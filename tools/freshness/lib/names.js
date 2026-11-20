// The Japanese names of the places. Pure functions, no I/O:
// - matchPlace: finds the OpenStreetMap element of one of our places, for `npm run names`;
// - japaneseNameVerdict: compares our name_ja with Google's, for `npm run check -- --names`.
// See doc/11-data-freshness.md, section 8.

import { nameMatch, distanceMetres, hasLatin, hasJapanese, SAME_SPOT_METRES, SEARCH_RADIUS_METRES } from './classify.js';

// The Japanese comparison lives in classify.js (the status pass uses it too); exported here as well.
export { hasJapanese, NAME_ENDINGS, normalizeJapaneseName, japaneseNameVerdict } from './classify.js';


// The Japanese name of an OpenStreetMap element: `name:ja`, else `name`, the first that has kana or
// kanji (a name:ja of "HUB" is no Japanese name); null otherwise. Kept as written: a branch suffix
// (渋谷店) is part of the sign.
export function japaneseName(tags = {}) {
    if (hasJapanese(tags['name:ja'])) return tags['name:ja'].trim();
    return hasJapanese(tags.name) ? tags.name.trim() : null;
}

// The names of an element to compare with our English name: `name:en`, and `name` when it has
// Latin letters (a mixed name such as "JOYSOUND 渋谷店" too).
function englishNames(tags = {}) {
    return [tags['name:en'], hasLatin(tags.name ?? '') ? tags.name : null].filter(Boolean);
}

// How well an element's names match ours: 'exact', 'partial' or null (the best of its names).
function elementMatch(ours, tags) {
    const kinds = englishNames(tags).map((n) => nameMatch(ours, n, { strict: true }));
    return kinds.includes('exact') ? 'exact' : kinds.includes('partial') ? 'partial' : null;
}

// The position of an element: a node has lat/lon, a way or relation its `center` (out center).
function position(element) {
    if (element.lat !== undefined) return { lat: element.lat, lon: element.lon };
    return element.center ?? null;
}

// The id of an element as OpenStreetMap writes it in its URLs: "node/123", "way/456".
export const elementId = (element) => `${element.type}/${element.id}`;

// place: { name, lat, lon } from the seeder. elements: the Overpass answer's `elements`.
// Returns { match, nameJa, element, elementName, distance, reason }:
// - 'matched': the nearest element within 50 m whose English name matches ours (exact preferred to
//   partial) and that has a Japanese name;
// - 'nearest': no element matches our English name, but exactly one named element is within 15 m,
//   and it has a Japanese name;
// - 'none': anything else (nameJa, element and distance are then null), including an element that
//   matches our English name but has no Japanese name: our place is mapped, without the name we need.
// Two places may claim the same element: see resolveClaims.
export function matchPlace(place, elements) {
    // The named elements within 50 m, nearest first.
    const close = elements
        .filter((e) => e.tags?.name || e.tags?.['name:ja'])
        .map((e) => ({ e, pos: position(e) }))
        .filter(({ pos }) => pos)
        .map(({ e, pos }) => ({ e, distance: distanceMetres(place.lat, place.lon, pos.lat, pos.lon) }))
        .filter(({ distance }) => distance <= SEARCH_RADIUS_METRES)
        .sort((a, b) => a.distance - b.distance);

    const result = (match, { e, distance }, reason) => ({
        match,
        nameJa: japaneseName(e.tags),
        element: elementId(e),
        elementName: e.tags.name ?? null,
        distance: Math.round(distance),
        reason,
    });
    const none = (reason) => ({ match: 'none', nameJa: null, element: null, elementName: null, distance: null, reason });

    // An element without a Japanese name cannot fill name_ja: skipped.
    const withJapanese = close.filter(({ e }) => japaneseName(e.tags));
    for (const kind of ['exact', 'partial']) {
        const found = withJapanese.find(({ e }) => elementMatch(place.name, e.tags) === kind);
        if (found) return result('matched', found, `${kind} English name`);
    }
    // Our place is there, under its English name only: a neighbour on the spot would be a wrong guess.
    if (close.some(({ e }) => elementMatch(place.name, e.tags))) return none('matched element has no Japanese name');

    if (!close.length) return none(`nothing named within ${SEARCH_RADIUS_METRES} m`);
    const onSpot = close.filter(({ distance }) => distance <= SAME_SPOT_METRES);
    if (onSpot.length > 1) return none(`${onSpot.length} named elements within ${SAME_SPOT_METRES} m`);
    if (onSpot.length === 1 && japaneseName(onSpot[0].e.tags)) return result('nearest', onSpot[0], `the only named element within ${SAME_SPOT_METRES} m`);
    if (onSpot.length === 1) return none(`the only element within ${SAME_SPOT_METRES} m has no Japanese name`);
    return none(`no named element within ${SAME_SPOT_METRES} m`);
}

// Results of matchPlace for several places (each with its `element`): a "nearest" guess on an element
// that two or more places claim (two restaurants in one building, one of them mapped) becomes none.
// A "matched" result keeps its match: its English name says which place it is.
export function resolveClaims(results) {
    const claims = {};
    for (const r of results) if (r.element) claims[r.element] = (claims[r.element] ?? 0) + 1;
    return results.map((r) => (r.match === 'nearest' && claims[r.element] > 1
        ? { ...r, match: 'none', nameJa: null, element: null, elementName: null, distance: null, reason: `${r.element} claimed by ${claims[r.element]} places` }
        : r));
}
