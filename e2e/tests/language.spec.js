const { test, expect } = require('@playwright/test');
const { showResults, showFilter, waitForResults, isWide } = require('./helpers');

/** The names of the listed places, top to bottom */
async function names(page) {
  return (await page.locator('#results .rowName').allTextContents()).map(name => name.trim());
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
