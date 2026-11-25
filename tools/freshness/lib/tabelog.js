// Tabelog (tabelog.com): the status of a restaurant read from its page. Tabelog keeps the page
// of a closed restaurant, with a 【閉店】 in the title and a warning block under the name; Google
// drops the place after a while. So Tabelog can settle the places Google is unsure about or
// cannot find. It also shows the Japanese name of the place. Only the status, the name and the
// place names of the address are kept, never the page.

import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { USER_AGENT } from './overpass.js';
import { NAME_ENDINGS } from './classify.js';
import { isoDate } from './cli.js';

const TIMEOUT_MS = 30_000;

// The verdict of check.js a Tabelog status gives, and its reason. A move or an open page changes
// nothing: Google's verdict stands, for a look by hand.
const VERDICTS = {
    closed: { verdict: 'closed_permanently', reason: 'Tabelog says permanently closed' },
    temporarily_closed: { verdict: 'closed_temporarily', reason: 'Tabelog says temporarily closed' },
};

// The words of the title bracket and of the status alert, Japanese pages (the seeders' links)
// and English pages (tabelog.com/en/), in the order to test them.
const STATUS_WORDS = [
    ['closed', /閉店|permanently closed/i],
    ['temporarily_closed', /休業|temporarily closed/i],
    ['moved', /移転|moved/i],
];

// 'closed', 'temporarily_closed', 'moved' or 'open', from the HTML of a Tabelog restaurant page.
// Two places say it: the title, 【閉店】 before the name, and the alert block under the name
// (class rst-status-alert, "このお店は現在閉店しております。"). Either is enough. Nothing else on the
// page is read: the "report a closure" button is on every page and says the same words.
export function tabelogStatus(html) {
    const title = html.match(/<title>[^<]*?【([^】]*)】/)?.[1] ?? '';
    const alert = html.match(/rst-status-alert[^>]*>([\s\S]*?)<\/div>/)?.[1].replace(/<[^>]*>/g, '') ?? '';
    const text = `${title} ${alert}`;
    for (const [status, words] of STATUS_WORDS) if (words.test(text)) return status;
    return 'open';
}

// { verdict, reason } when the Tabelog status changes the verdict of a place, else null.
export const tabelogVerdict = (status) => VERDICTS[status] ?? null;

// Fetches one Tabelog page and returns its HTML. Throws on a non-200 answer (the message starts
// with "Tabelog: HTTP <status>").
export async function fetchTabelogPage(link, { fetch = globalThis.fetch } = {}) {
    let response;
    try {
        response = await fetch(link, {
            headers: { 'User-Agent': USER_AGENT, 'Accept-Language': 'ja,en' },
            signal: AbortSignal.timeout(TIMEOUT_MS),
        });
    } catch (e) {
        // Node's fetch says "fetch failed" and hides the reason (DNS, refused, timeout) in `cause`.
        throw new Error(`Tabelog: ${e.cause?.message ?? e.message}`);
    }
    if (!response.ok) throw new Error(`Tabelog: HTTP ${response.status}`);
    return response.text();
}

// Fetches one Tabelog page and returns its status.
export async function fetchTabelogStatus(link, { fetch = globalThis.fetch } = {}) {
    return tabelogStatus(await fetchTabelogPage(link, { fetch }));
}

// The Japanese names, for `npm run names -- --tabelog`.

// The Japanese page of a Tabelog link: the language folder of a translated page (tabelog.com/en/,
// /cn/, /tw/, /kr/) is dropped, the name there is romanised. null when the link is not a page of
// tabelog.com.
export function tabelogJapaneseLink(link) {
    let url;
    try {
        url = new URL(link);
    } catch {
        return null;
    }
    if (!/^(?:s\.)?tabelog\.com$/.test(url.hostname)) return null;
    return `https://tabelog.com${url.pathname.replace(/^\/(?:en|cn|tw|kr)(?=\/)/, '')}`;
}

const ENTITIES = { amp: '&', lt: '<', gt: '>', quot: '"', apos: "'", nbsp: ' ' };
const decodeEntities = (text) => text
    .replace(/&#x([0-9a-f]+);/gi, (_, hex) => String.fromCodePoint(parseInt(hex, 16)))
    .replace(/&#(\d+);/g, (_, n) => String.fromCodePoint(Number(n)))
    .replace(/&(\w+);/g, (all, name) => ENTITIES[name] ?? all);

// The block of the page's structured data (JSON-LD) of the given type, or null.
function structuredData(html, type) {
    for (const [, json] of html.matchAll(/<script type="application\/ld\+json">([\s\S]*?)<\/script>/g)) {
        try {
            const data = JSON.parse(json);
            if (data?.['@type'] === type) return data;
        } catch {
            // not JSON: the next block
        }
    }
    return null;
}

// The areas and the station of the breadcrumb: 渋谷, 恵比寿, 代官山 of "渋谷・恵比寿・代官山 イタリアン",
// 渋谷駅 of "渋谷駅 イタリアン". The first item is Tabelog, the last (no link) the restaurant.
function breadcrumbPlaces(html) {
    const items = structuredData(html, 'BreadcrumbList')?.itemListElement;
    if (!Array.isArray(items)) return [];
    return items.slice(1).filter((item) => item?.item && typeof item.name === 'string').flatMap((item) => item.name.split(' ')[0].split('・'));
}

// A place name and its shorter form without the ending that says what it is: 渋谷区 and 渋谷,
// 宇田川町 and 宇田川, 東京都 and 東京, 渋谷駅 and 渋谷.
function placeWords(word) {
    const w = word?.trim();
    if (!w) return [];
    const short = w.replace(/[都道府県市区町村駅]$/u, '');
    return short.length >= 2 && short !== w ? [w, short] : [w];
}

// { name, places } from the HTML of a Tabelog restaurant page. name: the name as Tabelog shows it
// (the structured data, else the heading under class display-name), branch included, or null.
// places: where the restaurant is, as the page says it (the region, ward and town of the address,
// the area of the title, the areas and the station of the breadcrumb), which is what its branch is
// usually named after.
export function tabelogPageInfo(html) {
    const data = structuredData(html, 'Restaurant');
    const heading = html.match(/class="display-name"[^>]*>\s*(?:<span[^>]*>)?([^<]*)/)?.[1];
    const name = decodeEntities(typeof data?.name === 'string' ? data.name : heading ?? '').replace(/\s+/gu, ' ').trim() || null;
    const address = data?.address ?? {};
    const area = html.match(/<title>[^<]*\s-\s([^/<|]+)\/[^<|]*\|/)?.[1];
    const town = address.streetAddress?.match(/^[^\d０-９\s-]+/u)?.[0]; // 道玄坂 of "道玄坂2-8-8"
    const places = [address.addressRegion, address.addressLocality, town, area, ...breadcrumbPlaces(html)].flatMap(placeWords);
    return { name, places: [...new Set(places)] };
}

// Last words ending in 店 that say what the place is, not which branch: 山田 珈琲店.
const SHOP_WORDS = [...NAME_ENDINGS, '珈琲店', 'コーヒー店', '喫茶店', '茶店', '専門店', '菓子店', '料理店', '洋食店', '精肉店', '鮮魚店', '百貨店', 'パン店'];
// A branch that names no place: the main shop, an annex, a number.
const BRANCH_RE = /(?:総本店|本店|支店|別館|新館|[0-9０-９一二三四五六七八九十]+号店)$/u;

// The name without its branch. Dropped from the end, again and again, as long as something is
// left before it:
// - a last word ending in 店 that is not a kind of shop: "博多風龍 渋谷店", "麻布茶房 西武渋谷店",
//   "ドゥ マゴ フードショー店", but "山田 珈琲店" stays;
// - a last word starting with a place of the page: "カオサン 渋谷宮益坂", "庄や 渋谷店はなれ";
// - a place of the page at the end of a word, with what follows it up to 店: "バルッチャ道玄坂",
//   "一蘭渋谷スペイン坂店";
// - 本店, 別館, 2号店...
// `places` are the place names of the page (tabelogPageInfo). The first word always stays, so a
// place at the start is part of the name: "渋谷 ガパオ食堂", "渋谷トンテキ".
export function withoutBranch(name, places = []) {
    const words = places.filter(Boolean).sort((a, b) => b.length - a.length);
    const escaped = words.map((w) => w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
    const placeRe = words.length ? new RegExp(`(?:${escaped.join('|')})(?:\\S{0,10}店)?$`, 'u') : null;
    let rest = name.replace(/\s+/gu, ' ').trim();
    for (;;) {
        const at = rest.lastIndexOf(' ');
        const last = rest.slice(at + 1);
        const cut = at > 0 && last.endsWith('店') && !SHOP_WORDS.some((w) => last.endsWith(w)) ? at
            : at > 0 && words.some((w) => last.startsWith(w)) ? at
            : placeRe?.exec(rest)?.index ?? BRANCH_RE.exec(rest)?.index ?? rest.length;
        const shorter = rest.slice(0, cut).replace(/[\s・]+$/u, '');
        if (!shorter || shorter === rest) return rest;
        rest = shorter;
    }
}

// Reads the cache file, or null when there is none (or it cannot be read).
function readCache(cachePath) {
    try {
        return JSON.parse(readFileSync(cachePath, 'utf8'));
    } catch {
        return null;
    }
}

// The name and places (tabelogPageInfo) of each Tabelog page of `links`, one page at a time,
// `minIntervalMs` apart. The answers of the day are kept in <cacheDir>/tabelog-<YYYY-MM-DD>.json
// (the name and the place names only, never the page) and reused; `refresh` ignores them. A page
// that fails gives { error } and is not kept. Returns { pages: Map(link -> info), cachePath, fetched }.
export async function fetchTabelogNames({
    links,
    cacheDir,
    fetch = globalThis.fetch,
    today = new Date(),
    refresh = false,
    minIntervalMs = 1000,
    log = () => {},
}) {
    const cachePath = join(cacheDir, `tabelog-${isoDate(today)}.json`);
    const cached = (refresh ? null : readCache(cachePath))?.pages ?? {};
    const unique = [...new Set(links)];
    const toFetch = unique.filter((link) => !cached[link]);
    if (toFetch.length) log(`Tabelog: reading ${toFetch.length} pages, one a second (${unique.length - toFetch.length} from the cache ${cachePath})...`);
    else log(`Tabelog: ${unique.length} pages from the cache ${cachePath} (--refresh to read them again)`);

    const pages = new Map(unique.filter((link) => cached[link]).map((link) => [link, cached[link]]));
    const save = () => {
        mkdirSync(cacheDir, { recursive: true });
        writeFileSync(cachePath, JSON.stringify({ fetchedAt: today.toISOString(), pages: cached }) + '\n');
    };
    let fetched = 0;
    for (const link of toFetch) {
        if (fetched > 0) await new Promise((resolve) => setTimeout(resolve, minIntervalMs));
        fetched += 1;
        try {
            const info = tabelogPageInfo(await fetchTabelogPage(link, { fetch }));
            pages.set(link, info);
            cached[link] = info;
        } catch (e) {
            pages.set(link, { error: e.message }); // one bad page: note it and go on
        }
        if (fetched % 10 === 0) {
            save(); // an interrupted run keeps what it read
            log(`  ${fetched} / ${toFetch.length}`);
        }
    }
    if (fetched) save();
    return { pages, cachePath, fetched };
}
