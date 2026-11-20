// A small client for the Overpass API, which answers queries over the OpenStreetMap data.
// https://wiki.openstreetmap.org/wiki/Overpass_API
// The data is © OpenStreetMap contributors, under the Open Database License (ODbL): what is
// taken from it must be credited where it is shown (doc/11-data-freshness.md, section 8).

import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { isoDate } from './cli.js';

// The main public instance (no key, no account). A mirror can be set with OVERPASS_URL in .env.
export const DEFAULT_OVERPASS_URL = 'https://overpass-api.de/api/interpreter';
// Who is asking, as the Overpass usage policy wants: the tool and where to find it.
export const USER_AGENT = 'WhereTo-freshness/1.0 (+https://github.com/flochrislas/WhereTo)';
// The margin added around our places, so that a place on the edge still sees its neighbours.
export const MARGIN_METRES = 100;
// How long the server may work on the query, and how long we wait for its answer.
const SERVER_TIMEOUT_S = 90;
const CLIENT_TIMEOUT_MS = 120_000;

// The bounding box of the places, plus `margin` metres on every side: { south, west, north, east }.
export function boundingBox(places, margin = MARGIN_METRES) {
    const lats = places.map((p) => p.lat);
    const lons = places.map((p) => p.lon);
    const dLat = margin / 111195; // metres to degrees of latitude
    const dLon = margin / (111195 * Math.cos((Math.max(...lats.map(Math.abs)) * Math.PI) / 180)); // a degree of longitude shrinks away from the equator
    const round = (x) => Math.round(x * 1e6) / 1e6;
    return {
        south: round(Math.min(...lats) - dLat),
        west: round(Math.min(...lons) - dLon),
        north: round(Math.max(...lats) + dLat),
        east: round(Math.max(...lons) + dLon),
    };
}

// The query: every node and way ("nw") with one of the tags that mark a business, in the box.
// `out center tags` gives the tags, and a centre for the ways. No `["name"]` filter and no relations
// on purpose: "name" is one of the biggest tag indexes of the server and relations need their
// geometry, and the full form took minutes or timed out on every server (November 2026) where this
// one answers in under a minute. The matcher only looks at the named elements anyway.
export function buildQuery(box) {
    return [
        `[out:json][timeout:${SERVER_TIMEOUT_S}][bbox:${box.south},${box.west},${box.north},${box.east}];`,
        '(',
        '  nw["amenity"];',
        '  nw["shop"];',
        '  nw["leisure"];',
        '  nw["craft"];',
        ');',
        'out center tags;',
    ].join('\n');
}

// True when box `outer` contains box `inner`.
const contains = (outer, inner) => outer.south <= inner.south && outer.west <= inner.west && outer.north >= inner.north && outer.east >= inner.east;

// Reads the cache file, or null when there is none (or it cannot be read: it is fetched again).
function readCache(cachePath) {
    try {
        return JSON.parse(readFileSync(cachePath, 'utf8'));
    } catch {
        return null;
    }
}

// The OpenStreetMap elements around the places, with one request at most per day: the answer is
// kept in <cacheDir>/overpass-<YYYY-MM-DD>.json and reused that day when its box covers the places.
// `refresh` ignores the cache. `fetch` and `today` can be replaced in tests (no network there).
// Returns { elements, fromCache, cachePath }.
export async function fetchOsmElements({
    places,
    cacheDir,
    url = DEFAULT_OVERPASS_URL,
    fetch = globalThis.fetch,
    today = new Date(),
    refresh = false,
    log = () => {},
}) {
    const box = boundingBox(places);
    const cachePath = join(cacheDir, `overpass-${isoDate(today)}.json`);
    const cached = refresh ? null : readCache(cachePath);
    if (cached && cached.box && contains(cached.box, box) && Array.isArray(cached.elements)) {
        log(`OpenStreetMap: ${cached.elements.length} elements from the cache ${cachePath} (--refresh to ask again)`);
        return { elements: cached.elements, fromCache: true, cachePath };
    }

    log(`OpenStreetMap: asking ${url} for the named businesses around ${places.length} places...`);
    const query = buildQuery(box);
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'User-Agent': USER_AGENT },
        body: new URLSearchParams({ data: query }).toString(),
        signal: AbortSignal.timeout(CLIENT_TIMEOUT_MS),
    });
    if (!response.ok) {
        // 429: too many requests from us; 504: the server is busy. Both mean "try again later".
        throw new Error(`Overpass API: HTTP ${response.status} ${(await response.text().catch(() => '')).slice(0, 200)}`.trim());
    }
    const body = await response.json();
    // A query that ran out of time or memory still answers 200, with a "remark" and partial data.
    if (body.remark && /error/i.test(body.remark)) throw new Error(`Overpass API: ${body.remark}`);
    const elements = body.elements ?? [];

    mkdirSync(cacheDir, { recursive: true });
    writeFileSync(cachePath, JSON.stringify({
        fetchedAt: today.toISOString(),
        url,
        box,
        query,
        copyright: body.osm3s?.copyright ?? 'The data included in this document is from www.openstreetmap.org. The data is made available under ODbL.',
        elements,
    }) + '\n');
    log(`OpenStreetMap: ${elements.length} elements, saved to ${cachePath}`);
    return { elements, fromCache: false, cachePath };
}
