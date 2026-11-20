const { test, expect } = require('@playwright/test');
const { showResults, showFilter, waitForResults, isWide } = require('./helpers');

/** The English names of the listed places, top to bottom (a Japanese row shows its name_ja first, then the English name in a lang="en" span) */
async function names(page) {
  return page.locator('#results .rowName').evaluateAll(rows =>
    rows.map(row => (row.matches('[lang="en"]') ? row : row.querySelector('[lang="en"]')).textContent.trim()));
}

test.describe('Languages', () => {
  test('ラーメン on /ja/restaurants gives the same places as ramen on /restaurants', async ({ page }) => {
    await page.goto('/restaurants');
    const total = await showResults(page);
    await showFilter(page);
    await page.locator('#tags').getByRole('button', { name: 'ramen', exact: true }).click();
    await showResults(page);
    await expect(page.locator('#results .resultsCount')).not.toHaveText(`${total} results`); // filtered, not the full list
    const english = await names(page);
    expect(english.length).toBeGreaterThan(0);

    await page.goto('/ja/restaurants');
    await expect(page.locator('html')).toHaveAttribute('lang', 'ja');
    // The button shows the Japanese word and carries the English label the search uses
    const ramen = page.locator('#tags').getByRole('button', { name: 'ラーメン', exact: true });
    await expect(ramen).toHaveAttribute('data-tag', 'ramen');
    await ramen.click();
    await showResults(page);
    await expect.poll(() => names(page)).toEqual(english);
    // The count is in Japanese: "12件"
    await expect(page.locator('#results .resultsCount')).toHaveText(`${english.length}件`);
  });

  test('switching language keeps the open place and the tags', async ({ page }) => {
    await page.goto('/ja/restaurants');
    await page.locator('#tags').getByRole('button', { name: 'ラーメン', exact: true }).click();
    await showResults(page);
    await expect(page.locator('#results .resultsCount')).toHaveText(/件$/);
    const first = page.locator('#results a.resultCell').first();
    const name = (await first.locator('.rowName').textContent()).trim();
    await first.click();
    await expect(page.locator('#detailName')).toHaveText(name);
    // The address bar carries the state
    await expect(page).toHaveURL(/\/ja\/restaurants\?tags=ramen&place=\d+$/);

    // The switch link of the header (English), with the same state
    await page.locator('a.langSwitch[hreflang="en"]').click();
    await expect(page).toHaveURL(/\/restaurants\?tags=ramen&place=\d+$/);
    await expect(page.locator('html')).toHaveAttribute('lang', 'en');
    await expect(page.locator('#detailName')).toHaveText(name);
    // (By data-tag, not by role: on a phone the filter is hidden behind the details, and roles skip hidden elements)
    await expect(page.locator('#selectedTags .tag[data-tag="ramen"]')).toHaveAttribute('aria-pressed', 'true');
    // On a phone the place is on screen; BACK shows the filtered list, and the place leaves the address bar
    if (!(await isWide(page))) {
      await expect(page.locator('#mainDetails')).toBeVisible();
      await page.locator('#mainDetails .navButton').click();
      await expect(page).toHaveURL(/\/restaurants\?tags=ramen$/);
    }
    await expect(page.locator('#results .resultsCount')).toHaveText(/^\d+ results?$/);
  });

  test('a filtered address, copied from the address bar, opens in the same state', async ({ page, browser }) => {
    await page.goto('/restaurants');
    const total = await showResults(page);
    await showFilter(page);
    await page.locator('#tags').getByRole('button', { name: 'ramen', exact: true }).click();
    await page.locator('#tags').getByRole('button', { name: 'noodles', exact: true }).click();
    await page.locator('.opButton[data-op="OR"]').click();
    await showResults(page);
    await page.locator('.sortButton[data-order-by="cost"]').click();
    await expect(page.locator('#results .resultsCount')).not.toHaveText(`${total} results`);
    const count = await waitForResults(page);
    const first = page.locator('#results a.resultCell').first();
    const name = (await first.locator('.rowName').textContent()).trim();
    await first.click();
    await expect(page.locator('#detailName')).toHaveText(name);
    const shared = page.url();
    expect(shared).toMatch(/\?tags=ramen,noodles&op=OR&orderBy=cost&place=\d+$/);

    // A fresh browser (nothing remembered) opens the copied address
    const context = await browser.newContext({ viewport: page.viewportSize(), isMobile: await page.evaluate(() => navigator.maxTouchPoints > 0) });
    const other = await context.newPage();
    await other.goto(shared);
    await expect(other.locator('#detailName')).toHaveText(name);
    for (const tag of ['ramen', 'noodles']) {
      await expect(other.locator(`#selectedTags .tag[data-tag="${tag}"]`)).toHaveAttribute('aria-pressed', 'true');
    }
    await expect(other.locator('.opButton[data-op="OR"]')).toHaveAttribute('aria-pressed', 'true');
    await expect(other.locator('.sortButton[data-order-by="cost"]')).toHaveAttribute('aria-pressed', 'true');
    await expect(other.locator('#results .resultsCount')).toHaveText(`${count} results`);
    // The switch link carries the same state into Japanese
    await expect(other.locator('a.langSwitch[hreflang="ja"]')).toHaveAttribute('href', /\/ja\/restaurants\?tags=ramen,noodles&op=OR&orderBy=cost&place=\d+$/);
    await context.close();
  });

  test('a remembered "any" without tags survives a reload of a stateful address and the language switch', async ({ page }) => {
    await page.goto('/restaurants');
    await page.locator('.opButton[data-op="OR"]').click();
    // A sort order that is not the default puts state in the address (?orderBy=cost), without tags
    await showResults(page);
    await page.locator('.sortButton[data-order-by="cost"]').click();
    await expect(page).toHaveURL(/\/restaurants\?orderBy=cost$/);
    await page.reload();
    await expect(page.locator('.opButton[data-op="OR"]')).toHaveAttribute('aria-pressed', 'true');
    // Same in the other language (same browser, so the same remembered choice)
    await page.locator('a.langSwitch[hreflang="ja"]').click();
    await expect(page).toHaveURL(/\/ja\/restaurants\?orderBy=cost$/);
    await expect(page.locator('.opButton[data-op="OR"]')).toHaveAttribute('aria-pressed', 'true');
  });

  test('the state of the address survives a browser without localStorage', async ({ page }) => {
    // Storage disabled (private mode, settings): every access throws
    await page.addInitScript(() => {
      Object.defineProperty(window, 'localStorage', { get() { throw new Error('storage disabled'); } });
    });
    // The search must carry the operator and the order of the address, not the defaults
    const search = page.waitForRequest(request => /\/restaurants\/results\?/.test(request.url()) && /tags=/.test(request.url()));
    await page.goto('/restaurants?tags=ramen,noodles&op=OR&orderBy=cost');
    const url = new URL((await search).url());
    expect(url.searchParams.get('op')).toBe('OR');
    expect(url.searchParams.get('orderBy')).toBe('cost');
    await waitForResults(page); // (shown by the page itself on a phone, as the address has tags)
    await expect(page).toHaveURL(/\/restaurants\?tags=ramen,noodles&op=OR&orderBy=cost$/);
    await expect(page.locator('.opButton[data-op="OR"]')).toHaveAttribute('aria-pressed', 'true');
    await expect(page.locator('.sortButton[data-order-by="cost"]')).toHaveAttribute('aria-pressed', 'true');
    // A choice made on the page is kept too
    await page.locator('.sortButton[data-order-by="ratings"]').click();
    await expect(page).toHaveURL(/\/restaurants\?tags=ramen,noodles&op=OR&orderBy=ratings$/);
  });

  test('the anchor and the other parameters of the address stay when the state changes', async ({ page }) => {
    await page.goto('/restaurants?utm_source=newsletter#mainFilter');
    // The script wrote the address (the switch link carries the twin page) without touching it
    await expect(page.locator('a.langSwitch[hreflang="ja"]')).toHaveAttribute('href', /\/ja\/restaurants$/);
    await expect(page).toHaveURL(/\/restaurants\?utm_source=newsletter#mainFilter$/);
    await page.locator('#tags').getByRole('button', { name: 'ramen', exact: true }).click();
    await expect(page).toHaveURL(/\/restaurants\?utm_source=newsletter&tags=ramen#mainFilter$/);
    // The switch link carries the state only
    await expect(page.locator('a.langSwitch[hreflang="ja"]')).toHaveAttribute('href', /\/ja\/restaurants\?tags=ramen$/);
    await page.locator('#selectedTags').getByRole('button', { name: 'ramen', exact: true }).click();
    await expect(page).toHaveURL(/\/restaurants\?utm_source=newsletter#mainFilter$/);
  });

  test('an address with an unknown place drops it and shows the list', async ({ page }) => {
    await page.goto('/restaurants?tags=ramen&place=999999');
    await expect(page).toHaveURL(/\/restaurants\?tags=ramen$/);
    // On a phone the list is back instead of an error panel
    if (!(await isWide(page))) {
      await expect(page.locator('#mainDetails')).toBeHidden();
      await expect(page.locator('#results')).toBeVisible();
    }
    await expect(page.locator('#selectedTags .tag[data-tag="ramen"]')).toHaveAttribute('aria-pressed', 'true');
  });
});

test.describe('Details requests answering out of order', () => {
  // The service worker would answer the details itself, out of reach of page.route(): without it for this test
  test.use({ serviceWorkers: 'block' });

  test('a late answer for a place opened earlier neither replaces nor closes the current place', async ({ page }) => {
    await page.goto('/restaurants');
    await showResults(page);
    const rows = page.locator('#results a.resultCell');
    const ids = await Promise.all([0, 1].map(async i => (await rows.nth(i).getAttribute('href')).match(/\/(\d+)$/)[1]));
    const names = await Promise.all([0, 1].map(async i => (await rows.nth(i).locator('.rowName').textContent()).trim()));
    // The first place answers only when the test says so: with a 404 the first time (as a place removed
    // meanwhile) and with its page the second time; the second place answers at once
    const firstUrl = new RegExp(`/restaurants/details/${ids[0]}$`);
    let firstAnswers = 0;
    let release;
    await page.route(firstUrl, async route => {
      firstAnswers++;
      await new Promise(resolve => { release = resolve; });
      return firstAnswers === 1 ? route.fulfill({ status: 404, body: 'gone' }) : route.continue();
    });
    for (let round = 0; round < 2; round++) {
      // Open the first place, then the second before the first answers (a dispatched click, as on a phone the
      // list is behind the details panel at that moment and a real click would wait for it)
      await rows.nth(0).dispatchEvent('click');
      await rows.nth(1).dispatchEvent('click');
      await expect(page.locator('#detailName')).toHaveText(names[1]);
      // Now the first place answers (a 404, then its page): once the answer is in, nothing has changed
      await expect.poll(() => firstAnswers).toBe(round + 1);
      const answered = page.waitForResponse(response => firstUrl.test(response.url()));
      release();
      await answered;
      await expect(page.locator('#detailName')).toHaveText(names[1]);
      await expect(page).toHaveURL(new RegExp(`\\?place=${ids[1]}$`));
      await expect(page.locator('a.langSwitch[hreflang="ja"]')).toHaveAttribute('href', new RegExp(`/ja/restaurants\\?place=${ids[1]}$`));
      if (!(await isWide(page))) {
        await expect(page.locator('#mainDetails')).toBeVisible();
        await page.locator('#mainDetails .navButton').click();
      }
    }
    expect(firstAnswers).toBe(2);
  });
});
