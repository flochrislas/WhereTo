// check.js and apply.js end to end, on a temporary copy of the fixture seeders, with a fake fetch.
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdtempSync, cpSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { runCheck } from '../check.js';
import { runApply, latestReport, describe as describeChange } from '../apply.js';
import { readSeeder } from '../lib/seeders.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;
const TODAY = new Date(2026, 10, 18, 9, 30); // 18 November 2026 (months count from 0)

// A Google place `north` metres north of the given coordinates.
function gplace(id, name, status, lat, lon, north = 0) {
    return {
        id,
        displayName: { text: name, languageCode: 'en' },
        formattedAddress: 'Shibuya City, Tokyo',
        location: { latitude: lat + north / 111195, longitude: lon },
        businessStatus: status,
    };
}

// What the fake Google answers to a Text Search, per name.
const SEARCH = {
    'Hakata Furyu': [gplace('ChIJhakata', 'Hakata Furyu Shibuya', 'OPERATIONAL', 35.658545, 139.698468, 8)],
    'Go Go Curry': [gplace('ChIJgogo', 'Go! Go! Curry', 'CLOSED_PERMANENTLY', 35.657333, 139.703654, 3)],
    // Both branches come back for both searches; each must pick the one on its own spot.
    "Mc Donald's": [
        gplace('ChIJmc1', "McDonald's Shibuya", 'OPERATIONAL', 35.659, 139.7),
        gplace('ChIJmc2', "McDonald's Shibuya Dogenzaka", 'OPERATIONAL', 35.661, 139.698),
    ],
    'Bistro Shibuya': [gplace('ChIJramen', 'Ichiran', 'OPERATIONAL', 35.656, 139.699, 5)],
    'Sushi Zanmai': [],
    Pancho: [gplace('ChIJpancho', 'Spaghetti Pancho', 'OPERATIONAL', 35.6605, 139.6985, 12)],
    'Tengu Sakaba': [gplace('ChIJtengu', 'Tengu Sakaba', 'OPERATIONAL', 35.6588, 139.6978, 3)],
    'Krung Siam': [gplace('ChIJkrung', 'Krung Siam', 'OPERATIONAL', 35.6578, 139.7012, 2)],
    Usagi: [gplace('ChIJusagi', 'Usagi', 'OPERATIONAL', 35.662, 139.7005, 1)],
    'Karaoke-kan': [gplace('ChIJkan', 'Karaoke-kan', 'OPERATIONAL', 35.660434, 139.698699)],
};
const DETAILS = {
    ChIJknownRitrovo: gplace('ChIJknownRitrovo', 'Il Ritrovo', 'CLOSED_TEMPORARILY', 35.6601, 139.6971, 4),
};
// What the fake Google answers to a Place Details in Japanese (languageCode=ja), per id.
const JAPANESE = {
    ChIJhakata: '博多風龍 渋谷店',
    ChIJgogo: 'Go! Go! Curry', // Google only knows a Latin name
    ChIJknownRitrovo: 'イル・リトロヴォ',
    ChIJkrung: 'ソムタムダー',
};

// A fake fetch that answers like Google from the tables above and records every call.
function fakeGoogle() {
    const calls = [];
    const fetch = async (url, options = {}) => {
        calls.push(url);
        let body;
        if (url.endsWith('places:searchText')) {
            const query = JSON.parse(options.body).textQuery;
            body = { places: SEARCH[query] ?? [] };
        } else if (url.endsWith('?languageCode=ja')) {
            const id = decodeURIComponent(url.split('/places/')[1].split('?')[0]);
            if (!JAPANESE[id]) return new Response('{}', { status: 404 });
            body = { displayName: { text: JAPANESE[id], languageCode: 'ja' } };
        } else {
            const id = decodeURIComponent(url.split('/places/')[1].split('?')[0]);
            if (!DETAILS[id]) return new Response('{}', { status: 404 });
            body = DETAILS[id];
        }
        return new Response(JSON.stringify(body), { status: 200 });
    };
    return { fetch, calls };
}

// A fresh temporary seeders folder and reports folder.
function workspace() {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-cmd-'));
    const seedersDir = join(dir, 'seeders');
    cpSync(FIXTURES, seedersDir, { recursive: true });
    return { seedersDir, reportsDir: join(dir, 'reports') };
}

// Runs check on the restaurants of a workspace, silently.
async function check(ws, extra = {}) {
    const google = fakeGoogle();
    const out = await runCheck({
        apiKey: 'KEY', fetch: google.fetch, minIntervalMs: 0, today: TODAY, log: () => {},
        kinds: ['restaurants'], ...ws, ...extra,
    });
    return { ...out, calls: google.calls };
}

// Fake commands: php and git are "not installed", so apply skips them.
const noCommands = () => null;

test('check queries every open place once and writes the report', async () => {
    const ws = workspace();
    const { report, reportPath, calls } = await check(ws);
    assert.equal(reportPath, join(ws.reportsDir, '2026-11-18T0930-restaurants.json'));
    assert.deepEqual(JSON.parse(readFileSync(reportPath, 'utf8')), report);
    assert.equal(report.partial, undefined);
    // 13 blocks, minus the permanently closed one, minus the second copy of Usagi.
    // The temporarily closed one (Tengu Sakaba) is checked again.
    assert.equal(calls.length, 11);
    assert.equal(calls.filter((u) => u.includes('/places/ChIJknownRitrovo')).length, 1);

    const verdict = Object.fromEntries(report.results.map((r) => [`${r.name}@${r.lat}`, r.verdict]));
    assert.deepEqual(verdict, {
        'Hakata Furyu@35.658545': 'operational',
        'Go Go Curry@35.657333': 'closed_permanently',
        "Mc Donald's@35.659": 'operational',
        "Mc Donald's@35.661": 'operational',
        'Il Ritrovo@35.6601': 'closed_temporarily',
        'Bistro Shibuya@35.656': 'replaced',
        'Sushi Zanmai@35.6595': 'not_found_nearby',
        'Pancho@35.6605': 'operational',
        'Tengu Sakaba@35.6588': 'reopened',
        'Krung Siam@35.6578': 'operational',
        'Usagi@35.662': 'operational',
    });
    const mc2 = report.results.find((r) => r.name === "Mc Donald's" && r.lat === 35.661);
    assert.equal(mc2.google.id, 'ChIJmc2');
    assert.equal(report.results.find((r) => r.name === 'Il Ritrovo').lookup, 'details');
    // Only an exact name lets apply store the id: "Pancho" is a partial match of "Spaghetti Pancho".
    assert.equal(report.results.find((r) => r.name === 'Pancho').idTrusted, false);
    assert.equal(report.results.find((r) => r.name === 'Hakata Furyu').idTrusted, true);
});

test('check prints the table without the operational places unless --all', async () => {
    const lines = [];
    await check(workspace(), { log: (l) => lines.push(l) });
    const text = lines.join('\n');
    assert.match(text, /Go Go Curry\s+Go! Go! Curry\s+CLOSED_PERMANENTLY\s+3 m\s+closed_permanently/);
    assert.match(text, /6 operational \(not listed/);
    assert.doesNotMatch(text, /Krung Siam/);

    const all = [];
    await check(workspace(), { all: true, log: (l) => all.push(l) });
    assert.match(all.join('\n'), /Krung Siam/);
});

test('check honours --kind and --limit', async () => {
    const { calls, report } = await check(workspace(), { kinds: ['karaokes'], limit: 1 });
    assert.equal(calls.length, 1);
    assert.deepEqual(report.results.map((r) => [r.name, r.verdict]), [['Karaoke-kan', 'operational']]);
});

test('check names the report after all the kinds when none is given', async () => {
    const ws = workspace();
    // The fixtures have no stores or bars seeder: give them empty ones.
    const empty = readFileSync(join(FIXTURES, 'KaraokesTableSeeder.php'), 'utf8').replace(/\n {8}\$karaoke = [\s\S]*(?=\n {4}\})/, '');
    writeFileSync(join(ws.seedersDir, 'StoresTableSeeder.php'), empty);
    writeFileSync(join(ws.seedersDir, 'BarsTableSeeder.php'), empty);
    const { reportPath } = await check(ws, { kinds: undefined, limit: 0 });
    assert.equal(reportPath, join(ws.reportsDir, '2026-11-18T0930-all.json'));
});

test('check stops at once on a bad key, and keeps the results so far in a partial report', async () => {
    const google = fakeGoogle();
    let calls = 0;
    // Google answers the first two requests, then the quota runs out.
    const fetch = async (url, options) => (++calls > 2
        ? new Response(JSON.stringify({ error: { message: 'Quota exceeded' } }), { status: 429 })
        : google.fetch(url, options));
    const ws = workspace();
    await assert.rejects(
        runCheck({ apiKey: 'KEY', fetch, minIntervalMs: 0, today: TODAY, log: () => {}, kinds: ['restaurants'], ...ws }),
        /429/,
    );
    const report = JSON.parse(readFileSync(join(ws.reportsDir, '2026-11-18T0930-restaurants.json'), 'utf8'));
    assert.equal(report.partial, true);
    assert.match(report.error, /Quota exceeded/);
    assert.deepEqual(report.results.map((r) => r.name), ['Hakata Furyu', 'Go Go Curry']);

    // apply refuses it, unless --partial.
    await assert.rejects(runApply({ ...ws, yes: true, run: noCommands, log: () => {} }), /partial report.*--partial/);
    await runApply({ ...ws, yes: true, partial: true, run: noCommands, log: () => {} });
    const places = readSeeder(join(ws.seedersDir, 'RestaurantsTableSeeder.php'), 'restaurants');
    assert.equal(places.find((p) => p.name === 'Go Go Curry').businessStatus, 'CLOSED_PERMANENTLY');
});

test('check refuses to run without a key', async () => {
    await assert.rejects(runCheck({ apiKey: undefined, log: () => {}, ...workspace() }), /GOOGLE_PLACES_API_KEY/);
});

test('apply --yes writes the statuses and the ids into the seeders, but not "replaced"', async () => {
    const ws = workspace();
    await check(ws);
    const { changedFiles } = await runApply({ ...ws, yes: true, run: noCommands, log: () => {} });
    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    assert.deepEqual(changedFiles, [file]);

    const places = readSeeder(file, 'restaurants');
    const get = (name, lat) => places.filter((p) => p.name === name && (lat === undefined || p.lat === lat));
    const pick = (p) => [p.businessStatus, p.googlePlaceId];
    assert.deepEqual(pick(get('Go Go Curry')[0]), ['CLOSED_PERMANENTLY', 'ChIJgogo']);
    assert.deepEqual(pick(get('Il Ritrovo')[0]), ['CLOSED_TEMPORARILY', 'ChIJknownRitrovo']);
    // Replaced is opt-in: --yes leaves it out.
    assert.deepEqual(pick(get('Bistro Shibuya')[0]), [null, null]);
    // Reopened: the status says OPERATIONAL again.
    assert.deepEqual(pick(get('Tengu Sakaba')[0]), ['OPERATIONAL', 'ChIJtengu']);
    // Operational places: the status is written (a blank status means "not checked"), and the id.
    assert.deepEqual(pick(get('Hakata Furyu')[0]), ['OPERATIONAL', 'ChIJhakata']);
    assert.deepEqual(pick(get("Mc Donald's", 35.661)[0]), ['OPERATIONAL', 'ChIJmc2']);
    assert.deepEqual(pick(get("Mc Donald's", 35.659)[0]), ['OPERATIONAL', 'ChIJmc1']);
    assert.deepEqual(get('Usagi').map(pick), [['OPERATIONAL', 'ChIJusagi'], ['OPERATIONAL', 'ChIJusagi']]);
    // A partial name match: operational, but the id is not sure enough to store.
    assert.deepEqual(pick(get('Pancho')[0]), ['OPERATIONAL', null]);
    // Unsure: untouched.
    assert.deepEqual(pick(get('Sushi Zanmai')[0]), [null, null]);
    // Already closed before: untouched.
    assert.deepEqual(pick(get('Kindenmaru')[0]), ['CLOSED_PERMANENTLY', 'ChIJclosedKindenmaru']);

    // A second apply of the same report changes nothing.
    const before = readFileSync(file, 'utf8');
    const again = await runApply({ ...ws, yes: true, run: noCommands, log: () => {} });
    assert.deepEqual(again.changedFiles, []);
    assert.equal(readFileSync(file, 'utf8'), before);
});

test('apply shows the changes grouped by verdict, "replaced" unchecked, and keeps what is ticked', async () => {
    const ws = workspace();
    await check(ws);
    let shown;
    // The fake prompt ticks the replaced place and unchecks the permanently closed one.
    const prompt = async (groups) => {
        shown = groups.map((g) => [g.verdict, g.checked, g.results.map((r) => r.name)]);
        return groups.flatMap((g) => g.results).filter((r) => r.verdict !== 'closed_permanently');
    };
    await runApply({ ...ws, prompt, run: noCommands, log: () => {} });
    assert.deepEqual(shown, [
        ['operational', true, ['Hakata Furyu', "Mc Donald's", "Mc Donald's", 'Pancho', 'Krung Siam', 'Usagi']],
        ['closed_permanently', true, ['Go Go Curry']],
        ['closed_temporarily', true, ['Il Ritrovo']],
        ['reopened', true, ['Tengu Sakaba']],
        ['replaced', false, ['Bistro Shibuya']],
        ['not_found_nearby', false, ['Sushi Zanmai']],
    ]);
    const places = readSeeder(join(ws.seedersDir, 'RestaurantsTableSeeder.php'), 'restaurants');
    // Replaced, and no id: the one found belongs to the new business.
    assert.deepEqual([places.find((p) => p.name === 'Bistro Shibuya').businessStatus, places.find((p) => p.name === 'Bistro Shibuya').googlePlaceId], ['REPLACED', null]);
    assert.equal(places.find((p) => p.name === 'Go Go Curry').businessStatus, null);
    // Confirmed open: the status is written, once (a second apply rewrites the same line).
    assert.equal(places.find((p) => p.name === 'Hakata Furyu').businessStatus, 'OPERATIONAL');
    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    const before = readFileSync(file, 'utf8');
    await runApply({ ...ws, prompt, run: noCommands, log: () => {} }); // the same choices again
    assert.equal(readFileSync(file, 'utf8'), before);
});

test('apply does not offer a temporarily closed place that is still temporarily closed', async () => {
    const ws = workspace();
    const { reportPath, report } = await check(ws);
    const tengu = report.results.find((r) => r.name === 'Tengu Sakaba');
    Object.assign(tengu, { verdict: 'closed_temporarily', google: { ...tengu.google, status: 'CLOSED_TEMPORARILY' } });
    writeFileSync(reportPath, JSON.stringify(report));
    let shown;
    await runApply({ ...ws, prompt: async (groups) => { shown = groups; return []; }, run: noCommands, log: () => {} });
    assert.deepEqual(shown.find((g) => g.verdict === 'closed_temporarily').results.map((r) => r.name), ['Il Ritrovo']);
});

test('apply verifies every file before writing any', async () => {
    const ws = workspace();
    const { reportPath, report } = await check(ws);
    // A karaoke that does not exist: the karaoke plan fails, so the restaurants file must stay as it was.
    report.results.push({ ...report.results[1], kind: 'karaokes', name: 'Nowhere' });
    writeFileSync(reportPath, JSON.stringify(report));
    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    const before = readFileSync(file, 'utf8');
    await assert.rejects(runApply({ ...ws, yes: true, run: noCommands, log: () => {} }), /No place named 'Nowhere'/);
    assert.equal(readFileSync(file, 'utf8'), before);
});

test('apply runs php -l on each edited file and git diff --stat, and stops if php -l fails', async () => {
    const ws = workspace();
    const { reportPath } = await check(ws);
    const ran = [];
    const run = (command, args) => {
        ran.push([command, args[0]]);
        return { status: 0, stdout: command === 'git' ? ' RestaurantsTableSeeder.php | 12 ++++\n' : 'No syntax errors', stderr: '' };
    };
    const lines = [];
    await runApply({ ...ws, reportPath, yes: true, run, log: (l) => lines.push(l) });
    assert.deepEqual(ran, [['php', '-l'], ['git', 'diff']]);
    assert.match(lines.join('\n'), /RestaurantsTableSeeder\.php \| 12/);
    assert.match(lines.join('\n'), /Written: .*RestaurantsTableSeeder\.php/);
    assert.match(lines.join('\n'), /Places checked on 2026-11-18: 2 closed, 0 replaced, 1 reopened/);

    const ws2 = workspace();
    await check(ws2);
    const file2 = join(ws2.seedersDir, 'RestaurantsTableSeeder.php');
    const before2 = readFileSync(file2, 'utf8');
    const failing = (command) => (command === 'php' ? { status: 255, stdout: 'Parse error', stderr: '' } : { status: 0, stdout: '' });
    await assert.rejects(runApply({ ...ws2, yes: true, run: failing, log: () => {} }), /php -l failed[\s\S]*Parse error[\s\S]*nothing was written/i);
    assert.equal(readFileSync(file2, 'utf8'), before2); // the lint runs on a copy: the seeder is untouched
});

test('apply without a report asks to run check first, and picks the latest report', async () => {
    const ws = workspace();
    await assert.rejects(runApply({ ...ws, yes: true, run: noCommands, log: () => {} }), /npm run check/);
    await check(ws);
    await check(ws, { today: new Date(2026, 10, 18, 14, 5), kinds: ['karaokes'] });
    await check(ws, { today: new Date(2026, 10, 18, 8, 0) });
    assert.equal(latestReport(ws.reportsDir), join(ws.reportsDir, '2026-11-18T1405-karaokes.json'));
});

test('check searches again when Google has retired a stored id', async () => {
    const ws = workspace();
    // Il Ritrovo's stored id is unknown to this fake Google (404), but a search finds it.
    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    writeFileSync(file, readFileSync(file, 'utf8').replace('ChIJknownRitrovo', 'ChIJretired'));
    SEARCH['Il Ritrovo'] = [gplace('ChIJnewRitrovo', 'Il Ritrovo', 'OPERATIONAL', 35.6601, 139.6971, 2)];
    try {
        const { report, calls } = await check(ws);
        const ritrovo = report.results.find((r) => r.name === 'Il Ritrovo');
        assert.ok(calls.some((u) => u.includes('/places/ChIJretired')));
        assert.deepEqual([ritrovo.lookup, ritrovo.retiredPlaceId, ritrovo.verdict], ['search', 'ChIJretired', 'operational']);
        assert.match(ritrovo.reason, /retired/);
        // apply replaces the retired id with the new one.
        await runApply({ ...ws, yes: true, run: noCommands, log: () => {} });
        assert.equal(readSeeder(file, 'restaurants').find((p) => p.name === 'Il Ritrovo').googlePlaceId, 'ChIJnewRitrovo');
    } finally {
        delete SEARCH['Il Ritrovo'];
    }
});

// Gives a few fixture places a name_ja, in the workspace copy.
function withJapaneseNames(ws) {
    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    let text = readFileSync(file, 'utf8');
    for (const [name, ja] of [['Hakata Furyu', '博多風龍'], ['Go Go Curry', 'ゴーゴーカレー'], ['Il Ritrovo', 'イル リトロヴォ'], ['Sushi Zanmai', 'すし三昧'], ['Krung Siam', 'クルンサイアム']]) {
        text = text.replace(`'name' => '${name}',`, `'name' => '${name}',\n            'name_ja' => '${ja}',`);
    }
    writeFileSync(file, text);
}

test('check without --names makes no Japanese request and writes no nameJa field', async () => {
    const ws = workspace();
    withJapaneseNames(ws);
    const lines = [];
    const { report, reportPath, calls } = await check(ws, { all: true, log: (l) => lines.push(l) });
    // 11, plus the second search with Sushi Zanmai's name_ja (found nothing in English): a search,
    // not the Japanese Details request of --names.
    assert.equal(calls.length, 12);
    assert.ok(!calls.some((u) => u.includes('languageCode=ja')));
    assert.equal(report.results.find((r) => r.name === 'Sushi Zanmai').lookup, 'search+ja');
    assert.ok(report.results.every((r) => !('nameJa' in r)));
    // The Maps link is only there for the places to look at by hand (here: not found nearby).
    assert.deepEqual(report.results.filter((r) => 'googleMapsLink' in r).map((r) => r.verdict), ['not_found_nearby']);
    assert.doesNotMatch(readFileSync(reportPath, 'utf8'), /nameJa/);
    assert.doesNotMatch(lines.join('\n'), /Japanese name/);
});

test('check --names compares every name_ja with Google, and never shows Google\'s Japanese name', async () => {
    const ws = workspace();
    withJapaneseNames(ws);
    const lines = [];
    const { report, reportPath, calls } = await check(ws, { names: true, log: (l) => lines.push(l) });
    // One more Details call, in Japanese, per place with a name_ja that was found (Sushi Zanmai was not).
    const japanese = calls.filter((u) => u.endsWith('?languageCode=ja'));
    assert.deepEqual(japanese.map((u) => u.split('/places/')[1].split('?')[0]).sort(), ['ChIJgogo', 'ChIJhakata', 'ChIJknownRitrovo', 'ChIJkrung']);
    assert.equal(calls.length, 11 + 1 + 4); // + the name_ja search for Sushi Zanmai

    const verdict = Object.fromEntries(report.results.map((r) => [`${r.name}@${r.lat}`, r.nameJa]));
    assert.deepEqual(verdict, {
        'Hakata Furyu@35.658545': 'confirmed', // the branch 渋谷店 is dropped
        'Go Go Curry@35.657333': 'disputed', // Google's name is in Latin only
        "Mc Donald's@35.659": null, // no name_ja: nothing to check
        "Mc Donald's@35.661": null,
        'Il Ritrovo@35.6601': 'confirmed', // found by its id; the middle dot and the space do not count
        'Bistro Shibuya@35.656': null,
        'Sushi Zanmai@35.6595': 'unchecked', // not found
        'Pancho@35.6605': null,
        'Tengu Sakaba@35.6588': null,
        'Krung Siam@35.6578': 'disputed', // another name
        'Usagi@35.662': null,
    });
    // The status verdicts do not change.
    assert.equal(report.results.find((r) => r.name === 'Krung Siam').verdict, 'operational');
    assert.equal(report.results.find((r) => r.name === 'Krung Siam').googleMapsLink, 'https://www.google.com/maps/place/9');

    // Google's Japanese names are nowhere: not in the report, not on the screen.
    const text = lines.join('\n');
    for (const google of Object.values(JAPANESE).filter((n) => /[^\x00-\x7f]/.test(n))) {
        assert.ok(!readFileSync(reportPath, 'utf8').includes(google), `${google} is in the report`);
        assert.ok(!text.includes(google), `${google} is printed`);
    }
    // The table gets the column; the disputed places are listed at the end with their Maps link.
    assert.match(text, /verdict\s+reason\s+Japanese name/);
    assert.match(text, /Sushi Zanmai\s+-\s+-\s+-\s+not_found_nearby\s+no match within 200 m, nothing found\s+unchecked/);
    assert.match(text, /Japanese names: confirmed: 2, disputed: 2, unchecked: 1/);
    assert.match(text, /disputed by Google[^\n]*\n\s+restaurants\s+Go Go Curry: https:\/\/www\.google\.com\/maps\/place\/2\n\s+restaurants\s+Krung Siam: https:\/\/www\.google\.com\/maps\/place\/9/);
});

test('check --names stops on a fatal error of the Japanese request, keeping a partial report', async () => {
    const ws = workspace();
    withJapaneseNames(ws);
    const google = fakeGoogle();
    const fetch = async (url, options) => (url.endsWith('?languageCode=ja')
        ? new Response(JSON.stringify({ error: { message: 'Quota exceeded' } }), { status: 429 })
        : google.fetch(url, options));
    await assert.rejects(
        runCheck({ apiKey: 'KEY', fetch, minIntervalMs: 0, today: TODAY, log: () => {}, kinds: ['restaurants'], names: true, ...ws }),
        /429/,
    );
    const report = JSON.parse(readFileSync(join(ws.reportsDir, '2026-11-18T0930-restaurants.json'), 'utf8'));
    assert.equal(report.partial, true);
    assert.deepEqual(report.results.map((r) => r.name), []); // Hakata Furyu, the first place, has a name_ja
});

test('check searches again with the name_ja when the English search finds nothing nearby', async () => {
    const ws = workspace();
    withJapaneseNames(ws);
    // Google knows Sushi Zanmai under its Japanese name only.
    SEARCH['すし三昧'] = [gplace('ChIJsushi', 'すし三昧 渋谷店', 'CLOSED_PERMANENTLY', 35.6595, 139.6995, 6)];
    try {
        const { report, calls } = await check(ws);
        const searches = calls.filter((u) => u.endsWith('places:searchText'));
        assert.equal(searches.length, 10 + 1); // one per searched place, plus the name_ja one
        const sushi = report.results.find((r) => r.name === 'Sushi Zanmai');
        // The Japanese name confirmed counts as an exact name: Google's status is believed, the id is ours.
        assert.deepEqual([sushi.lookup, sushi.verdict, sushi.idTrusted, sushi.google.id], ['search+ja', 'closed_permanently', true, 'ChIJsushi']);
        // A place whose English search matched is not searched again.
        assert.equal(report.results.find((r) => r.name === 'Hakata Furyu').lookup, 'search');
    } finally {
        delete SEARCH['すし三昧'];
    }
});

test('check prints the reason after the verdict, ordered by verdict then reason', async () => {
    const lines = [];
    await check(workspace(), { log: (l) => lines.push(l) });
    const table = lines.find((l) => l.includes('verdict'));
    assert.match(table, /verdict\s+reason\n/);
    const verdicts = table.trim().split('\n').slice(2).map((row) => row.match(/(closed_permanently|closed_temporarily|reopened|replaced|not_found_nearby|unsure)/)[1]);
    assert.deepEqual(verdicts, ['closed_permanently', 'closed_temporarily', 'reopened', 'replaced', 'not_found_nearby']);
    assert.match(table, /Go Go Curry.*closed_permanently\s+Google says permanently closed/);
    assert.match(lines.join('\n'), /6 operational \(not listed/);
});

test('apply lists the places not found nearby unchecked, with what Google found and the Maps link', async () => {
    const ws = workspace();
    await check(ws);
    let shown;
    const prompt = async (groups) => {
        shown = groups.find((g) => g.verdict === 'not_found_nearby');
        return shown.results; // tick them
    };
    // --yes leaves them out.
    const yes = await runApply({ ...ws, yes: true, run: noCommands, log: () => {} });
    assert.ok(!yes.confirmed.some((r) => r.verdict === 'not_found_nearby'));
    const file = join(ws.seedersDir, 'RestaurantsTableSeeder.php');
    assert.equal(readSeeder(file, 'restaurants').find((p) => p.name === 'Sushi Zanmai').businessStatus, null);

    await runApply({ ...ws, prompt, run: noCommands, log: () => {} });
    assert.equal(shown.checked, false);
    assert.deepEqual(shown.results.map((r) => r.name), ['Sushi Zanmai']);
    assert.equal(describeChange(shown.results[0]), 'restaurants Sushi Zanmai -> Google found nothing; https://www.google.com/maps/place/7');
    const sushi = readSeeder(file, 'restaurants').find((p) => p.name === 'Sushi Zanmai');
    assert.deepEqual([sushi.businessStatus, sushi.googlePlaceId], ['CLOSED_PERMANENTLY', null]);

    const found = { kind: 'bars', name: 'Hobgoblin', verdict: 'not_found_nearby', distance: 2090, google: { name: 'GOBLIN' }, googleMapsLink: 'https://maps/x' };
    assert.equal(describeChange(found), 'bars        Hobgoblin -> Google found "GOBLIN" 2090 m away instead; https://maps/x');
});
