// The request paths of the parity tests: the query strings of test/parity-requests.json for every kind and every
// language of an export, with the tags taken from the export's own data (never from memory: the seeders change).
// export.mjs asks the site for them (php artisan app:export-reference); the browser spec compares the answers.
import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));

/** The kinds of place, as in the URLs (/restaurants/results) and the files of app-data/ */
export const KINDS = ['restaurants', 'stores', 'bars', 'karaokes'];

/** A tag no place has */
export const MISSING_TAG = 'no-such-tag';

/** The language prefixes of the export: "" for the default language, then "/ja"... (data-locale-prefixes of a page) */
export function localePrefixes(exportDir) {
  const html = readFileSync(join(exportDir, 'index.html'), 'utf8');
  const prefixes = /data-locale-prefixes="([^"]*)"/.exec(html)?.[1] ?? '';
  return ['', ...prefixes.split(',').filter(Boolean).map(prefix => '/' + prefix)];
}

/**
 * Two tags of a kind: the most common one among the listed places, and the one most often found with it (so that
 * AND gives some places and is not the same as OR). Ties: alphabetical, so the choice is stable. null when none.
 */
export function pickTags(data) {
  const count = new Map();
  const places = Object.values(data.places);
  for (const place of places) {
    for (const tag of place.tags) {
      count.set(tag, (count.get(tag) ?? 0) + 1);
    }
  }
  const best = counts => [...counts.entries()].sort((a, b) => b[1] - a[1] || (a[0] < b[0] ? -1 : 1))[0]?.[0] ?? null;
  const tag1 = best(count);
  const withTag1 = new Map();
  for (const place of places.filter(place => place.tags.includes(tag1))) {
    for (const tag of place.tags.filter(tag => tag !== tag1)) {
      withTag1.set(tag, (withTag1.get(tag) ?? 0) + 1);
    }
  }
  // No tag found with the first: the second most common one
  const tag2 = best(withTag1) ?? best(new Map([...count].filter(([tag]) => tag !== tag1)));
  return { tag1, tag2 };
}

/** The table of test/parity-requests.json */
export function parityTable() {
  return JSON.parse(readFileSync(join(here, '..', 'test', 'parity-requests.json'), 'utf8'));
}

/** Every request path of the parity tests for this export, e.g. /ja/restaurants/results?op=OR&tags=ramen,noodles */
export function parityRequests(exportDir, table = parityTable()) {
  const paths = [];
  for (const kind of KINDS) {
    const data = JSON.parse(readFileSync(join(exportDir, 'app-data', kind + '.json'), 'utf8'));
    const { tag1, tag2 } = pickTags(data);
    const values = { ...table.positions, tag1: tag1 ?? MISSING_TAG, tag2: tag2 ?? MISSING_TAG, missing: MISSING_TAG };
    for (const prefix of localePrefixes(exportDir)) {
      for (const query of table.queries) {
        // Each placeholder URL-encoded (a tag may hold a space); the commas and the "&" of the template stay
        const filled = query.replace(/\{(\w+)\}/g, (match, name) => encodeURIComponent(values[name] ?? match))
          // The space of "tags=a, b" is the server's ", " rule: send it encoded, as a browser would
          .replace(/ /g, '%20');
        paths.push(`${prefix}/${kind}/results?${filled}`);
      }
    }
  }
  return paths;
}
