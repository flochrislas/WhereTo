// The Tabelog pass: the status read from a Tabelog page, and tabelog.js end to end on a copy of
// the fixture seeders, a hand-made check report and a fake fetch.
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdtempSync, cpSync, mkdirSync, readdirSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, basename } from 'node:path';
import { tabelogStatus, tabelogVerdict, fetchTabelogStatus, tabelogJapaneseLink, tabelogPageInfo, withoutBranch, fetchTabelogNames } from '../lib/tabelog.js';
import { runTabelog } from '../tabelog.js';
import { readSeeder } from '../lib/seeders.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;
const CLOSED = readFileSync(join(FIXTURES, 'tabelog-closed.html'), 'utf8');
const OPEN = readFileSync(join(FIXTURES, 'tabelog-open.html'), 'utf8');
const TODAY = new Date(2026, 10, 20, 10, 15); // 20 November 2026

// A page with another status alert and title bracket (Tabelog's wording for a temporary closure).
const page = (bracket, alert) => CLOSED
    .replace('【閉店】', bracket)
    .replace('このお店は現在閉店しております。', alert);

test('tabelogStatus reads the closed, temporarily closed, moved and open pages', () => {
    assert.equal(tabelogStatus(CLOSED), 'closed');
    assert.equal(tabelogStatus(OPEN), 'open'); // the "report a closure" button on every page is not a status
    assert.equal(tabelogStatus(page('【休業中】', 'このお店は現在休業中です。')), 'temporarily_closed');
    assert.equal(tabelogStatus(page('【移転】', 'このお店は移転しました。')), 'moved');
    // The title alone is enough (the alert block may change), and the alert alone too.
    assert.equal(tabelogStatus(page('【閉店】', '')), 'closed');
    assert.equal(tabelogStatus(page('', 'このお店は現在閉店しております。')), 'closed');
    // The English pages (tabelog.com/en/...) say it in English.
    assert.equal(tabelogStatus(page('', 'This restaurant is permanently closed.')), 'closed');
    assert.equal(tabelogStatus(page('', 'This restaurant is temporarily closed.')), 'temporarily_closed');
});

test('tabelogVerdict changes the verdict for a closure only', () => {
    assert.deepEqual(tabelogVerdict('closed'), { verdict: 'closed_permanently', reason: 'Tabelog says permanently closed' });
    assert.deepEqual(tabelogVerdict('temporarily_closed'), { verdict: 'closed_temporarily', reason: 'Tabelog says temporarily closed' });
    assert.equal(tabelogVerdict('open'), null);
    assert.equal(tabelogVerdict('moved'), null);
    assert.equal(tabelogVerdict('error'), null);
});

test('fetchTabelogStatus sends the tool user agent and fails on a non-200 answer', async () => {
    const calls = [];
    const fetch = async (url, options) => {
        calls.push({ url, options });
        return new Response(CLOSED, { status: 200 });
    };
    assert.equal(await fetchTabelogStatus('https://tabelog.com/tokyo/A1303/A130301/13014309/', { fetch }), 'closed');
    assert.equal(calls[0].url, 'https://tabelog.com/tokyo/A1303/A130301/13014309/');
    assert.match(calls[0].options.headers['User-Agent'], /WhereTo-freshness/);
    const failing = async () => new Response('gone', { status: 404 });
    await assert.rejects(fetchTabelogStatus('https://tabelog.com/x/', { fetch: failing }), /HTTP 404/);
    const down = async () => { throw new Error('fetch failed', { cause: new Error('getaddrinfo ENOTFOUND tabelog.com') }); };
    await assert.rejects(fetchTabelogStatus('https://tabelog.com/x/', { fetch: down }), /Tabelog: getaddrinfo ENOTFOUND tabelog.com/);
});

// A workspace: a copy of the fixture seeders where the first three restaurants (Hakata Furyu,
// Kindenmaru, Go Go Curry) get a Tabelog link, and a reports directory with one check report.
function workspace() {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-tabelog-'));
    const seedersDir = join(dir, 'seeders');
    const reportsDir = join(dir, 'reports');
    cpSync(FIXTURES, seedersDir, { recursive: true, filter: (src) => !src.endsWith('.html') });
    const file = join(seedersDir, 'RestaurantsTableSeeder.php');
    let n = 0;
    writeFileSync(file, readFileSync(file, 'utf8').replace(/'tabelog_link' => null/g, (m) => (n < 3 ? `'tabelog_link' => 'https://tabelog.com/tokyo/A1303/A130301/${++n}/'` : m)));
    mkdirSync(reportsDir);
    return { seedersDir, reportsDir };
}

// A result row like check.js writes it, with the fields the pass reads.
function row(place, verdict, reason, extra = {}) {
    return { kind: 'restaurants', name: place.name, lat: place.lat, lon: place.lon, line: place.line, businessStatus: null, knownPlaceId: null, lookup: 'search', retiredPlaceId: null, google: null, distance: null, verdict, reason, idTrusted: false, ...extra };
}

test('runTabelog checks the flagged places that have a link and writes a new report', async () => {
    const { seedersDir, reportsDir } = workspace();
    const places = readSeeder(join(seedersDir, 'RestaurantsTableSeeder.php'), 'restaurants');
    const by = (name) => places.find((p) => p.name === name);
    const results = [
        row(by('Hakata Furyu'), 'not_found_nearby', 'no match within 200 m, nothing found', { googleMapsLink: 'https://maps.example/1' }),
        row(by('Kindenmaru'), 'unsure', 'Google gives no business status'),
        row(by('Go Go Curry'), 'unsure', 'error: fetch failed'),
        row(by("Mc Donald's"), 'unsure', 'a name in another script'), // no Tabelog link in the seeder
        row(by('Il Ritrovo'), 'operational', 'open'), // not flagged: not checked, even with a link
    ];
    const inputPath = join(reportsDir, '2026-11-19T2001-all.json');
    const input = { date: '2026-11-19', generatedAt: '2026-11-19T11:01:00.000Z', kinds: ['restaurants'], results };
    writeFileSync(inputPath, JSON.stringify(input, null, 2) + '\n');

    const calls = [];
    const fetch = async (url) => {
        calls.push(url);
        if (url.endsWith('/1/')) return new Response(CLOSED, { status: 200 });
        if (url.endsWith('/2/')) return new Response(OPEN, { status: 200 });
        return new Response('busy', { status: 503 });
    };
    const lines = [];
    const { report, reportPath } = await runTabelog({ reportPath: inputPath, reportsDir, seedersDir, fetch, minIntervalMs: 0, today: TODAY, log: (l) => lines.push(l) });

    // One page per flagged place with a link, in the order of the report.
    assert.deepEqual(calls, [
        'https://tabelog.com/tokyo/A1303/A130301/1/',
        'https://tabelog.com/tokyo/A1303/A130301/2/',
        'https://tabelog.com/tokyo/A1303/A130301/3/',
    ]);
    // A new report, stamped now, next to the input; the input is untouched.
    assert.equal(basename(reportPath), '2026-11-20T1015-all+tabelog.json');
    assert.deepEqual(readdirSync(reportsDir).sort(), ['2026-11-19T2001-all.json', '2026-11-20T1015-all+tabelog.json']);
    assert.deepEqual(JSON.parse(readFileSync(inputPath, 'utf8')), input);
    const written = JSON.parse(readFileSync(reportPath, 'utf8'));
    assert.deepEqual(written, report);
    assert.equal(report.date, '2026-11-19'); // the date of the Google check stays: apply prints it
    assert.deepEqual(report.tabelog, { from: '2026-11-19T2001-all.json', checkedAt: TODAY.toISOString(), checked: 3 });
    assert.equal(report.source, undefined); // not a names report
    // The rows: same order, same fields, plus `tabelog`; the verdict changes for a closure only.
    assert.equal(report.results.length, 5);
    const [hakata, kinden, gogo, mc, ritrovo] = report.results;
    assert.equal(hakata.verdict, 'closed_permanently');
    assert.equal(hakata.reason, 'Tabelog says permanently closed; Google: no match within 200 m, nothing found');
    assert.deepEqual(hakata.tabelog, { link: 'https://tabelog.com/tokyo/A1303/A130301/1/', status: 'closed' });
    assert.equal(hakata.googleMapsLink, 'https://maps.example/1'); // the other fields are kept
    assert.equal(kinden.verdict, 'unsure');
    assert.equal(kinden.reason, 'Google gives no business status');
    assert.deepEqual(kinden.tabelog, { link: 'https://tabelog.com/tokyo/A1303/A130301/2/', status: 'open' });
    assert.equal(gogo.verdict, 'unsure');
    assert.deepEqual(gogo.tabelog, { link: 'https://tabelog.com/tokyo/A1303/A130301/3/', status: 'error', error: 'Tabelog: HTTP 503' });
    assert.equal(mc.tabelog, null);
    assert.equal(ritrovo.tabelog, null);
    assert.equal(ritrovo.verdict, 'operational');
    // The summary: one line per checked place, and the counts.
    const text = lines.join('\n');
    assert.match(text, /Hakata Furyu.*closed.*closed_permanently/);
    assert.match(text, /3 checked on Tabelog: 1 closed, 0 temporarily closed, 0 moved, 1 open, 1 error/);
    assert.match(text, /1 of 4 flagged places had no Tabelog link/);
    assert.match(text, /npm run apply/);
});

test('runTabelog takes the newest check report by default and refuses a names report', async () => {
    const { seedersDir, reportsDir } = workspace();
    const empty = { date: '2026-11-19', generatedAt: '2026-11-19T11:01:00.000Z', kinds: ['restaurants'], results: [] };
    writeFileSync(join(reportsDir, '2026-11-18T0900-restaurants.json'), JSON.stringify(empty));
    writeFileSync(join(reportsDir, '2026-11-19T2000-names.json'), JSON.stringify({ ...empty, source: 'OpenStreetMap' }));
    const { report, reportPath } = await runTabelog({ reportsDir, seedersDir, fetch: async () => { throw new Error('no fetch expected'); }, today: TODAY, log: () => {} });
    assert.equal(basename(reportPath), '2026-11-20T1015-restaurants+tabelog.json');
    assert.equal(report.tabelog.from, '2026-11-18T0900-restaurants.json');
    assert.equal(report.tabelog.checked, 0);
    await assert.rejects(
        runTabelog({ reportPath: join(reportsDir, '2026-11-19T2000-names.json'), reportsDir, seedersDir, fetch: async () => {}, today: TODAY, log: () => {} }),
        /names report/,
    );
});

test('a place of the report that is not in the seeders any more is left as it is', async () => {
    const { seedersDir, reportsDir } = workspace();
    const gone = { kind: 'restaurants', name: 'Gone', lat: 35.1, lon: 139.1, line: 1, verdict: 'unsure', reason: 'x' };
    const inputPath = join(reportsDir, '2026-11-19T2001-all.json');
    writeFileSync(inputPath, JSON.stringify({ date: '2026-11-19', generatedAt: 'x', kinds: ['restaurants'], results: [gone] }));
    const lines = [];
    const { report } = await runTabelog({ reportPath: inputPath, reportsDir, seedersDir, fetch: async () => {}, today: TODAY, log: (l) => lines.push(l) });
    assert.deepEqual(report.results[0], { ...gone, tabelog: null });
    assert.match(lines.join('\n'), /1 place of the report is not in the seeders/);
});

// The Japanese names: npm run names -- --tabelog.

// A Tabelog page with its structured data, as the real pages carry it.
const namePage = (name, { street = '道玄坂2-8-8', area = '渋谷', crumbs = ['渋谷・恵比寿・代官山 ラーメン', '渋谷 ラーメン', '渋谷駅 ラーメン'] } = {}) => `<html><head>
<title>${name} - ${area}/ラーメン | 食べログ</title>
<script type="application/ld+json">${JSON.stringify({ '@type': 'Restaurant', name, address: { streetAddress: street, addressLocality: '渋谷区', addressRegion: '東京都' } })}</script>
<script type="application/ld+json">${JSON.stringify({ '@type': 'BreadcrumbList', itemListElement: [{ name: '食べログ', item: 'https://tabelog.com/' }, ...crumbs.map((c) => ({ name: c, item: 'https://tabelog.com/x/' })), { name }] })}</script>
</head><body><h2 class="display-name"><span>${name}</span></h2></body></html>`;

test('tabelogJapaneseLink drops the language folder and refuses what is not Tabelog', () => {
    assert.equal(tabelogJapaneseLink('https://tabelog.com/en/tokyo/A1303/A130301/13176753/'), 'https://tabelog.com/tokyo/A1303/A130301/13176753/');
    assert.equal(tabelogJapaneseLink('https://tabelog.com/tokyo/A1303/A130301/13160788/'), 'https://tabelog.com/tokyo/A1303/A130301/13160788/');
    assert.equal(tabelogJapaneseLink('http://s.tabelog.com/tw/tokyo/A1303/A130301/1/?x=1'), 'https://tabelog.com/tokyo/A1303/A130301/1/');
    assert.equal(tabelogJapaneseLink('https://tabelog.com/english/1/'), 'https://tabelog.com/english/1/'); // not a language folder
    assert.equal(tabelogJapaneseLink('https://example.com/en/1/'), null);
    assert.equal(tabelogJapaneseLink('sdfsdf'), null);
});

test('tabelogPageInfo reads the name and the places of the page', () => {
    assert.deepEqual(tabelogPageInfo(namePage('博多風龍 渋谷店')), { name: '博多風龍 渋谷店', places: ['東京都', '東京', '渋谷区', '渋谷', '道玄坂', '恵比寿', '代官山', '渋谷駅'] });
    assert.deepEqual(tabelogPageInfo(namePage('味源', { street: '宇田川町25-5 センタービル', area: '神泉' })).places.slice(4, 7), ['宇田川町', '宇田川', '神泉']);
    // Without structured data: the heading and the area of the title (the fixture of the status tests).
    assert.deepEqual(tabelogPageInfo(OPEN), { name: '博多風龍 渋谷店', places: ['渋谷'] });
    assert.equal(tabelogPageInfo('<h2 class="display-name"><span> Tom &amp; Jerry&#39;s </span></h2>').name, "Tom & Jerry's");
    assert.deepEqual(tabelogPageInfo('<html></html>'), { name: null, places: [] });
});

test('withoutBranch drops the branch from the end of the name, and only from the end', () => {
    const places = ['東京都', '東京', '渋谷区', '渋谷', '道玄坂', '宇田川町', '宇田川', '代官山', '渋谷駅'];
    const cases = {
        '博多風龍 渋谷店': '博多風龍',
        'バーガーキング 渋谷センター街店': 'バーガーキング',
        'CoCo壱番屋 渋谷区宇田川町店': 'CoCo壱番屋',
        '北陸×イタリアン バルッチャ道玄坂': '北陸×イタリアン バルッチャ',
        '一蘭渋谷スペイン坂店': '一蘭',
        'バンコク屋台 カオサン 渋谷宮益坂': 'バンコク屋台 カオサン', // a branch without 店
        '庄や 渋谷店はなれ': '庄や',
        '麻布茶房 西武渋谷店': '麻布茶房', // the whole last word, the building too
        'ドゥ マゴ フードショー店': 'ドゥ マゴ', // a branch named after no place of the page
        'PIZZA SALVATORE CUOMO 代官山': 'PIZZA SALVATORE CUOMO',
        '俺流塩らーめん 渋谷総本店': '俺流塩らーめん',
        '上等カレー 本店': '上等カレー',
        'HUB 渋谷2号店': 'HUB',
        'らーめん金伝丸　渋谷道玄坂店': 'らーめん金伝丸', // a full-width space
        'ラ ココリコ 渋谷': 'ラ ココリコ',
        // What stays.
        'オンザコーナー': 'オンザコーナー',
        '渋谷 ガパオ食堂': '渋谷 ガパオ食堂', // a place at the start is the name
        '渋谷トンテキ': '渋谷トンテキ',
        '渋谷': '渋谷',
        '道玄坂 渋谷店': '道玄坂', // the first word always stays
        '山田 珈琲店': '山田 珈琲店', // a kind of shop
        '中華 大宮飯店': '中華 大宮飯店',
        'ピッツァ 青山': 'ピッツァ 青山', // 青山 is not a place of this page
    };
    for (const [name, expected] of Object.entries(cases)) assert.equal(withoutBranch(name, places), expected, name);
    assert.equal(withoutBranch('博多風龍 渋谷店'), '博多風龍'); // no place known: the 店 word still goes
    assert.equal(withoutBranch('バルッチャ道玄坂'), 'バルッチャ道玄坂');
});

test('fetchTabelogNames reads each page once, keeps the answers of the day, and notes a page that fails', async () => {
    const cacheDir = mkdtempSync(join(tmpdir(), 'freshness-tabelog-names-'));
    const calls = [];
    const fetch = async (url) => {
        calls.push(url);
        return url.endsWith('/404/') ? new Response('', { status: 404 }) : new Response(namePage('博多風龍 渋谷店'), { status: 200 });
    };
    const run = (extra = {}) => fetchTabelogNames({ links: ['https://tabelog.com/1/', 'https://tabelog.com/404/', 'https://tabelog.com/1/'], cacheDir, fetch, today: TODAY, minIntervalMs: 0, ...extra });
    const first = await run();
    assert.equal(first.fetched, 2);
    assert.equal(first.pages.get('https://tabelog.com/1/').name, '博多風龍 渋谷店');
    assert.deepEqual(first.pages.get('https://tabelog.com/404/'), { error: 'Tabelog: HTTP 404' });
    assert.equal(first.cachePath, join(cacheDir, 'tabelog-2026-11-20.json'));
    assert.ok(!readFileSync(first.cachePath, 'utf8').includes('<html>')); // the name and the places, never the page

    const second = await run(); // the page that failed is asked again, the other comes from the cache
    assert.equal(second.fetched, 1);
    assert.equal(calls.length, 3);
    assert.equal(second.pages.get('https://tabelog.com/1/').name, '博多風龍 渋谷店');
    assert.equal((await run({ refresh: true })).fetched, 2);
});

test('a second pass on a +tabelog report keeps what the first pass read, and reads the Japanese page of an /en/ link', async () => {
    const { seedersDir, reportsDir } = workspace();
    const file = join(seedersDir, 'RestaurantsTableSeeder.php');
    const places = readSeeder(file, 'restaurants');
    const place = (name) => places.find((p) => p.name === name);
    const row = (p, verdict, extra = {}) => ({ kind: 'restaurants', name: p.name, lat: p.lat, lon: p.lon, line: p.line, verdict, reason: 'x', ...extra });
    // Give Go Go Curry an English link.
    const text = readFileSync(file, 'utf8');
    const at = text.indexOf("'name' => 'Go Go Curry',");
    const key = text.indexOf("'tabelog_link' =>", at);
    writeFileSync(file, text.slice(0, key) + "'tabelog_link' => 'https://tabelog.com/en/tokyo/A1303/A130301/9/'," + text.slice(text.indexOf('\n', key)));

    const evidence = { link: 'https://tabelog.com/tokyo/A1303/A130301/1/', status: 'closed' };
    const results = [
        row(place('Hakata Furyu'), 'closed_permanently', { tabelog: evidence }), // settled by the first pass
        row(place('Go Go Curry'), 'unsure', { tabelog: { link: 'x', status: 'error', error: 'HTTP 503' } }), // still flagged: read again
    ];
    const inputPath = join(reportsDir, '2026-11-20T0900-all+tabelog.json');
    writeFileSync(inputPath, JSON.stringify({ date: '2026-11-19', generatedAt: 'x', kinds: ['restaurants'], tabelog: { from: '2026-11-19T2001-all.json' }, results }));
    const calls = [];
    const fetch = async (url) => { calls.push(url); return new Response(CLOSED, { status: 200 }); };
    const { report } = await runTabelog({ reportsDir, seedersDir, fetch, minIntervalMs: 0, today: TODAY, log: () => {} });
    assert.deepEqual(calls, ['https://tabelog.com/tokyo/A1303/A130301/9/']);
    assert.deepEqual(report.results[0].tabelog, evidence);
    assert.equal(report.results[1].verdict, 'closed_permanently');
    assert.deepEqual(report.results[1].tabelog, { link: 'https://tabelog.com/en/tokyo/A1303/A130301/9/', status: 'closed' }); // the seeder's link
});

test('the English words of a Tabelog page are read whatever their case', () => {
    assert.equal(tabelogStatus('<title>【Permanently Closed】Furyu</title>'), 'closed');
    assert.equal(tabelogStatus('<div class="rst-status-alert">This restaurant is Temporarily Closed.</div>'), 'temporarily_closed');
});
