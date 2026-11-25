// The pure part of the seeder editor (edit.js): the type of each key, the validation of an edit,
// and the mapping of a report's results to the places of the page. No I/O.
// See doc/11-data-freshness.md, section 9.

import { checkPlaceEdit } from './seeders.js';

// The keys of the four seeders, with the input the page offers and whether null is a value.
// The kinds share their key names (a store has no lunch_price, but the name means the same).
export const KEY_TYPES = {
    name: { type: 'text', nullable: false },
    name_ja: { type: 'text', nullable: true },
    business_status: { type: 'status', nullable: true },
    google_place_id: { type: 'id', nullable: true },
    location: { type: 'text', nullable: false },
    area: { type: 'text', nullable: false },
    type: { type: 'text', nullable: false },
    points: { type: 'longtext', nullable: false },
    experience: { type: 'longtext', nullable: false },
    description: { type: 'longtext', nullable: false },
    visited: { type: 'boolean', nullable: false },
    visit_date: { type: 'datetime', nullable: true },
    google_maps_link: { type: 'url', nullable: true },
    tabelog_link: { type: 'url', nullable: true },
    official_website: { type: 'url', nullable: true },
    lunch_price: { type: 'number', nullable: true },
    score: { type: 'number', nullable: false },
    score_lunch: { type: 'number', nullable: false },
    score_place: { type: 'number', nullable: false },
    score_food: { type: 'number', nullable: false },
    score_price: { type: 'number', nullable: false },
    score_date: { type: 'number', nullable: false },
    score_mingling: { type: 'number', nullable: false },
};

// The keys a block may lack: offered to every row (section 8: absent is their null).
export const OPTIONAL_KEYS = ['name_ja', 'business_status', 'google_place_id'];

// The type of a key; an unknown key takes the type of its value (text when null), and may be null.
export function keyType(key, value) {
    if (Object.hasOwn(KEY_TYPES, key)) return KEY_TYPES[key];
    if (typeof value === 'number') return { type: 'number', nullable: true };
    if (typeof value === 'boolean') return { type: 'boolean', nullable: true };
    return { type: 'text', nullable: true };
}

// The keys to offer for a kind: name, the optional keys, then every key its places use, in order of
// first appearance.
export function knownKeys(kind, places) {
    const keys = ['name', ...OPTIONAL_KEYS];
    for (const p of places) {
        if (p.kind !== kind) continue;
        for (const { key } of p.fields) if (!keys.includes(key)) keys.push(key);
    }
    return keys;
}

const DATETIME_RE = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;

// Checks the values of an edit against the type of their keys, then the low-level rules of the seeders.
export function validateEdit(edit) {
    for (const [key, value] of Object.entries(edit?.fields ?? {})) {
        const { type, nullable } = keyType(key, value);
        if (value === null) {
            if (!nullable) throw new Error(`'${key}' cannot be null`);
            continue;
        }
        if (type === 'number' && typeof value !== 'number') throw new Error(`'${key}' must be a number`);
        if (type === 'boolean' && typeof value !== 'boolean') throw new Error(`'${key}' must be true or false`);
        if (['text', 'longtext', 'datetime', 'url', 'status', 'id'].includes(type) && typeof value !== 'string') throw new Error(`'${key}' must be text`);
        // Kept as the text it is: no time zone, no Date object, the seconds included.
        if (type === 'datetime' && !DATETIME_RE.test(value)) throw new Error(`'${key}' must be YYYY-MM-DD HH:MM:SS`);
        if (type === 'url' && !/^https?:\/\/\S+$/.test(value)) throw new Error(`'${key}' must start with http:// or https://`);
    }
    checkPlaceEdit(edit);
}

// A names report carries its source (OpenStreetMap or Tabelog); a check report does not.
export const reportKind = (report) => (report.source ? 'names' : 'check');

// The text of a report in the page's selector, from its file name and header: the time of the
// run, the kinds, the type, the Tabelog pass and the check it was run on, partial.
// "2026-11-20 19:18  all  check + Tabelog (on the 2026-11-19 20:01 check)".
export function reportLabel(file, report) {
    const stamp = (name) => {
        const m = name.match(/^(\d{4}-\d{2}-\d{2})T(\d{2})(\d{2})-([a-z]+)/);
        return m ? { when: `${m[1]} ${m[2]}:${m[3]}`, kinds: m[4] } : null;
    };
    const own = stamp(file);
    const kind = reportKind(report);
    const parts = own ? [own.when, ...(kind === 'check' ? [own.kinds] : [])] : [file];
    let type = kind === 'names' && /^Tabelog/.test(report.source) ? 'names from Tabelog' : kind;
    if (report.tabelog) {
        const from = typeof report.tabelog.from === 'string' ? stamp(report.tabelog.from) : null;
        type += ` + Tabelog${from ? ` (on the ${from.when} check)` : ''}`;
    }
    if (report.partial) type += ', partial';
    return [...parts, type].join('  ');
}

// The key the reports identify a place by (the same as check, apply and names), to 6 decimals.
const placeKey = (p) => `${p.kind}|${p.name}|${Number(p.lat).toFixed(6)}|${Number(p.lon).toFixed(6)}`;

// Attaches each result of a report to the id of its place, once, by kind, name and coordinates
// (all three, always: a same-name place at another spot is another place, or this one moved since
// the report, and the report's buttons must not act on it). Two blocks with the same name and
// coordinates (a place seeded twice) both get the result. A result with no place goes to `unmatched`.
export function mapReport(report, places) {
    const byKey = new Map();
    for (const p of places) {
        const key = placeKey(p);
        if (!byKey.has(key)) byKey.set(key, []);
        byKey.get(key).push(p);
    }
    const byId = {};
    const unmatched = [];
    for (const r of report.results ?? []) {
        const found = byKey.get(placeKey(r)) ?? [];
        if (!found.length) unmatched.push(r);
        for (const p of found) byId[p.id] = r;
    }
    return { kind: reportKind(report), byId, unmatched };
}
