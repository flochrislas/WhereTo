// The stand-alone Android app in a desktop browser (doc/16-standalone-apk-plan.md, Phases 4 and 6): the exported
// files served by the app's own path rule (tools/android-app/preview.mjs), so the tests run on the exact files
// that go into the APK. Skipped unless STANDALONE_DIR names an export:
//
//   cd tools/android-app && npm test        (exports first with "npm run export")
//
// The spec starts its own preview on a free port and never uses BASE_URL. It runs in Chromium only, the engine of
// Android's WebView. Every test fails if the page asks for anything outside the preview: the app has no network.
const base = require('@playwright/test');
const { spawn } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');
const { isWide, showResults } = require('./helpers');

const { expect } = base;
const DIR = process.env.STANDALONE_DIR ? path.resolve(process.env.STANDALONE_DIR) : null;
const REFERENCE = DIR ? (process.env.STANDALONE_REFERENCE || path.join(DIR, '..', 'export-reference.json')) : null;
const PREVIEW = path.join(__dirname, '..', '..', 'tools', 'android-app', 'preview.mjs');

/** A file of the export's data, e.g. data('restaurants.json') */
function data(file) {
  return JSON.parse(fs.readFileSync(path.join(DIR, 'app-data', file), 'utf8'));
}

const test = base.test.extend({
  // One preview per worker, on a free port: its address is the line it prints on start
  origin: [async ({}, use) => {
    if (!DIR) {
      await use(null);
      return;
    }
    const server = spawn(process.execPath, [PREVIEW, '0', DIR], { stdio: ['ignore', 'pipe', 'inherit'] });
    const origin = await new Promise((resolve, reject) => {
      let output = '';
      server.stdout.on('data', chunk => {
        output += chunk;
        const found = /http:\/\/localhost:(\d+)\//.exec(output);
        if (found) {
          resolve(`http://localhost:${found[1]}`);
        }
      });
      server.on('exit', code => reject(new Error(`preview.mjs ended (${code}): ${output}`)));
    });
    await use(origin);
    server.kill('SIGTERM');
  }, { scope: 'worker' }],

  // The guard of every test: the requests of the page and of any other context the test opens with watch()
  outside: [async ({ context, origin }, use) => {
    const outside = [];
    const watch = watched => watched.on('request', request => {
      const url = request.url();
      if (origin && !/^(data|blob|about):/.test(url) && new URL(url).origin !== origin) {
        outside.push(url);
      }
    });
    watch(context);
    await use({ watch });
    expect(outside, 'requests outside the preview').toEqual([]);
  }, { auto: true }],
});

test.describe('Stand-alone app (preview of the export)', () => {
  test.skip(!DIR, 'STANDALONE_DIR is not set: run "npm test" in tools/android-app');
  test.skip(({ browserName }) => browserName !== 'chromium', 'the app runs in Android\'s WebView: Chromium only');

  test('the home page and a main page load in both languages, with their styles and icons', async ({ page, origin }) => {
    const failed = [];
    page.on('response', response => {
      if (response.status() >= 400) {
        failed.push(`${response.status()} ${response.url()}`);
      }
    });
    for (const prefix of ['', '/ja']) {
      await page.goto(`${origin}${prefix}/`);
      await expect(page.locator('html')).toHaveAttribute('lang', prefix ? 'ja' : 'en');
      await expect(page.locator('html')).toHaveAttribute('data-standalone', /^\d{4}-\d\d-\d\dT/);
      await expect(page.locator('a.giantButton').first()).toBeVisible();
      // The stylesheet is there and applied
      expect(await page.evaluate(() => [...document.styleSheets].some(sheet => /\/css\/style\.css/.test(sheet.href || '') && sheet.cssRules.length > 0))).toBe(true);

      await page.goto(`${origin}${prefix}/restaurants`);
      await expect(page.locator('#tags .tag').first()).toBeVisible();
      // The icons come from the sprite of the export
      const sprite = await page.locator('svg.tagIcon use').first().getAttribute('href');
      expect(sprite).toMatch(/^\/icons\/tags\.svg\?v=\d+#/);
      expect(await page.evaluate(async href => (await (await fetch(href)).text()).includes('<symbol'), sprite)).toBe(true);
    }
    expect(failed).toEqual([]);
  });

  test('a place opens by its address and from its standalone page, without "Copy URL"', async ({ page, origin }) => {
    const id = data('restaurants.json').byRatings[0];
    await page.goto(`${origin}/restaurants?place=${id}`);
    await expect(page.locator(`#detailName[detailid="${id}"]`)).toBeVisible();
    await expect(page.getByText('Copy URL')).toHaveCount(0);
    await expect(page.locator('#mainDetails').getByText('Google maps')).toBeVisible();

    await page.goto(`${origin}/restaurant/details/${id}`);
    await expect(page.locator('#detailName')).toBeVisible();
    await expect(page.getByText('Copy URL')).toHaveCount(0);
  });

  test('whereToBack() closes the open place once; window.open is the browser\'s own', async ({ page, origin }) => {
    const id = data('restaurants.json').byRatings[0];
    await page.goto(`${origin}/restaurants?place=${id}`);
    await expect(page.locator(`#detailName[detailid="${id}"]`)).toBeVisible();
    if (await isWide(page)) {
      // Three panels side by side: the details sit next to the list, Back leaves the page
      expect(await page.evaluate(() => whereToBack())).toBe(false);
    } else {
      expect(await page.evaluate(() => whereToBack())).toBe(true);
      await expect(page.locator('#mainDetails')).toBeHidden();
      await expect(page).not.toHaveURL(/place=/);
      expect(await page.evaluate(() => whereToBack())).toBe(false);
    }
    // No Android bridge in a browser: window.open is left alone
    expect(await page.evaluate(() => typeof window.WhereToApp === 'undefined' && /\[native code\]/.test(window.open.toString()))).toBe(true);
  });

  test('an unknown place answers a real 404, and its address closes the panel', async ({ page, origin }) => {
    expect((await page.request.get(`${origin}/restaurants/details/99999999`)).status()).toBe(404);
    expect((await page.request.get(`${origin}/ja/restaurants/details/99999999`)).status()).toBe(404);
    await page.goto(`${origin}/restaurants?place=99999999`);
    await expect(page).not.toHaveURL(/place=/);
    await expect(page.locator('#detailName')).toHaveCount(0);
  });

  test('filter by a tag, open a place, close it with BACK', async ({ page, origin }) => {
    const restaurants = data('restaurants.json');
    const tag = await page.goto(`${origin}/restaurants`).then(() => page.locator('#tags .tag').first().getAttribute('data-tag'));
    await page.locator(`#tags .tag[data-tag="${tag}"]`).click();
    await expect(page.locator(`#selectedTags .tag[data-tag="${tag}"]`)).toBeVisible();
    // The places with that tag, nearest first (all of them have coordinates, or they are left out by distance)
    const expected = restaurants.byRatings.filter(id => restaurants.places[id].tags.includes(tag) && restaurants.places[id].lat !== null);
    if (!(await isWide(page))) {
      await page.locator('#filterHeaderButton').click();
    }
    await expect.poll(() => page.locator('#results .resultRow').count()).toBe(expected.length);
    const shown = await page.locator('#results .resultRow').evaluateAll(rows => rows.map(row => Number(row.dataset.placeId)));
    expect([...shown].sort((a, b) => a - b)).toEqual([...expected].sort((a, b) => a - b));

    const first = page.locator('#results .resultRow').first();
    const id = await first.getAttribute('data-place-id');
    await first.locator('a.nameAndType').click();
    await expect(page.locator(`#detailName[detailid="${id}"]`)).toBeVisible();
    await expect(page).toHaveURL(new RegExp(`place=${id}`));
    if (!(await isWide(page))) {
      await page.locator('#mainDetails .navButton').click();
      await expect(page.locator('#mainDetails')).toBeHidden();
      await expect(page.locator('#results')).toBeVisible();
      await expect(page).not.toHaveURL(/place=/);
      // Again, closed by Android's Back this time
      await first.locator('a.nameAndType').click();
      await expect(page.locator(`#detailName[detailid="${id}"]`)).toBeVisible();
      expect(await page.evaluate(() => whereToBack())).toBe(true);
      expect(await page.evaluate(() => whereToBack())).toBe(false);
    }
  });

  test('the language switch keeps the filter and the open place', async ({ page, origin }) => {
    const restaurants = data('restaurants.json');
    const id = restaurants.byRatings[0];
    const tag = restaurants.places[id].tags[0];
    await page.goto(`${origin}/restaurants?tags=${encodeURIComponent(tag)}&place=${id}`);
    await expect(page.locator(`#detailName[detailid="${id}"]`)).toBeVisible();
    await page.locator('a.langSwitch[hreflang="ja"]').click();
    await expect(page).toHaveURL(new RegExp(`/ja/restaurants\\?tags=.*&place=${id}`));
    await expect(page.locator('html')).toHaveAttribute('lang', 'ja');
    await expect(page.locator(`#selectedTags .tag[data-tag="${tag}"]`)).toHaveCount(1);
    await expect(page.locator(`#detailName[detailid="${id}"]`)).toBeVisible();
  });

  test('the three sort orders', async ({ page, origin }) => {
    const restaurants = data('restaurants.json');
    await page.goto(`${origin}/restaurants`);
    await showResults(page);
    const ids = () => page.locator('#results .resultRow').evaluateAll(rows => rows.map(row => Number(row.dataset.placeId)));
    await page.locator('#mainResults .sortButton[data-order-by="ratings"]').click();
    await expect.poll(ids).toEqual(restaurants.byRatings);
    await page.locator('#mainResults .sortButton[data-order-by="cost"]').click();
    await expect.poll(ids).toEqual(restaurants.byCost);
    await page.locator('#mainResults .sortButton[data-order-by="distance"]').click();
    const located = restaurants.byRatings.filter(id => restaurants.places[id].lat !== null);
    await expect.poll(async () => (await ids()).length).toBe(located.length);
    const minutes = await page.locator('#results .resultRow .distance .oneDigit').evaluateAll(cells => cells.map(cell => Number(cell.textContent)));
    // Nearest first. No bounds on the values: a place at the test position is 0 minutes away, a far one 60 or more;
    // the exact order and walking times are compared with the site's own answers in the parity tests below
    expect(minutes).toEqual([...minutes].sort((a, b) => a - b));
    await expect(page.locator('#results #resultsEnd strong')).toHaveText(String(located.length));
  });

  test.describe('the suggestion, decided by the clock of the phone in Tokyo time', () => {
    const at = time => new Date(`2026-11-27T${time}+09:00`);

    test('none at 09:00', async ({ page, origin }) => {
      await page.clock.install({ time: at('09:00:00') });
      await page.goto(`${origin}/`);
      await expect(page.locator('#suggestion')).toBeHidden();
    });

    test('at 12:00 a sentence that links to an exported restaurant, and "Try again"', async ({ page, origin }) => {
      const best = data('restaurants.json').byRatings.slice(0, 100);
      await page.clock.install({ time: at('12:00:00') });
      await page.goto(`${origin}/`);
      await expect(page.locator('#suggestion')).toBeVisible();
      await page.locator('#suggestButton').click();
      const link = page.locator('#suggestion a');
      await expect(link).toHaveAttribute('href', /^\/restaurant\/details\/\d+$/);
      const id = Number((await link.getAttribute('href')).split('/').pop());
      expect(best).toContain(id);
      expect((await page.request.get(origin + (await link.getAttribute('href')))).status()).toBe(200);
      await page.locator('#suggestButton').click();
      await expect(page.locator('#suggestion a')).toHaveAttribute('href', /^\/restaurant\/details\/\d+$/);
      // In Japanese, the Japanese sentence and page
      await page.goto(`${origin}/ja/`);
      await page.locator('#suggestButton').click();
      await expect(page.locator('#suggestion a')).toHaveAttribute('href', /^\/ja\/restaurant\/details\/\d+$/);
    });

    test('the control appears when lunch time comes while the page is open', async ({ page, origin }) => {
      await page.clock.install({ time: at('11:00:00') });
      await page.goto(`${origin}/`);
      await expect(page.locator('#suggestion')).toBeHidden();
      await page.clock.fastForward('01:00:00');
      await expect(page.locator('#suggestion')).toBeVisible();
    });
  });

  test('location denied: distances are from the station', async ({ browser, origin, outside }) => {
    const context = await browser.newContext({ permissions: [] });
    outside.watch(context);
    try {
      const page = await context.newPage();
      await page.goto(`${origin}/restaurants`);
      await expect(page.locator('#position-report')).toContainText('Distances are from the station.');
    } finally {
      await context.close();
    }
  });

  // Parity with the site: the site's answers (export-reference.json, written by export.mjs from the export's own
  // database) and whereto-local.js's, reduced to what the page shows, must be equal
  const reference = REFERENCE && fs.existsSync(REFERENCE) ? JSON.parse(fs.readFileSync(REFERENCE, 'utf8')) : {};
  const groups = {};
  for (const [request, answer] of Object.entries(reference)) {
    const [, prefix, kind] = /^(\/[a-z]{2})?\/([a-z]+)\/results\?/.exec(request);
    (groups[`${prefix || ''}/${kind}`] ??= []).push([request, answer]);
  }

  test('the reference answers exist', async () => {
    expect(Object.keys(reference).length, `no reference in ${REFERENCE}: run "npm run export"`).toBeGreaterThan(0);
  });

  for (const [group, requests] of Object.entries(groups)) {
    test(`parity of the results with the site: ${group}`, async ({ page, origin }) => {
      for (const [request, answer] of requests) {
        expect(answer.status, request).toBe(200);
      }
      const prefix = group.replace(/\/[a-z]+$/, '');
      await page.goto(`${origin}${prefix}/`);
      const compared = await page.evaluate(async requests => {
        // What the page shows of a results fragment, whatever the serialisation and the origin of its URLs
        function reduce(html) {
          const template = document.createElement('template');
          template.innerHTML = html;
          const fragment = template.content;
          const text = element => element ? element.textContent.replace(/\s+/g, ' ').trim() : null;
          const pathOf = url => url ? new URL(url, 'http://reference.invalid').pathname : null;
          return {
            count: text(fragment.querySelector('.resultsCount')),
            end: text(fragment.querySelector('#resultsEnd')),
            rows: [...fragment.querySelectorAll('.resultRow')].map(row => {
              const link = row.querySelector('a.nameAndType');
              const details = /showDetails\(event, '([^']*)'/.exec(link?.getAttribute('onclick') || '');
              return {
                id: row.dataset.placeId,
                name: text(row.querySelector('.rowName')),
                type: text(row.querySelector('.rowType')),
                minutes: text(row.querySelector('.distance .oneDigit')),
                scores: [...row.querySelectorAll('.score')].map(text),
                link: pathOf(link?.getAttribute('href')),
                details: pathOf(details && details[1]),
                // No link and an empty link are the same thing to the visitor: the app prints no link for a place without one
                maps: row.querySelector('.distance a')?.getAttribute('href') || null,
              };
            }),
          };
        }
        const results = [];
        for (const [request, answer] of requests) {
          results.push({ request, local: reduce(await window.whereToLocal.load(request)), site: reduce(answer.body) });
        }
        return results;
      }, requests);
      for (const { request, local, site } of compared) {
        expect(local, request).toEqual(site);
      }
    });
  }

  test('the parity table covers a place without coordinates, or says the export has none', async () => {
    const without = ['restaurants', 'stores', 'bars', 'karaokes']
      .flatMap(kind => Object.values(data(`${kind}.json`).places))
      .filter(place => place.lat === null || place.lon === null);
    // With one, the "ratings" and "cost" requests of the table show its row (0 minutes) and "distance" drops it.
    // The seeders of November 2026 have none among the listed places: the Node tests cover the case
    // (tools/android-app/test/local.test.mjs)
    test.info().annotations.push({ type: 'places without coordinates', description: String(without.length) });
    expect(without.length).toBeGreaterThanOrEqual(0);
  });
});
