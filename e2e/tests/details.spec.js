const { test, expect } = require('@playwright/test');
const { showResults, isWide } = require('./helpers');

test.describe('Details', () => {
  test('a result opens its details, BACK returns to the list, a details tag selects it in the filter', async ({ page }) => {
    await page.goto('/restaurants');
    await showResults(page);
    const firstName = (await page.locator('#results .rowName').first().textContent()).trim();
    await page.locator('#results .resultCell[role="button"]').first().click();

    // The details show the place; on a phone they replace the list, on a wide screen they sit next to it
    await expect(page.locator('#detailName')).toHaveText(firstName);
    const wide = await isWide(page);
    await expect(page.locator('#results')).toBeVisible({ visible: wide });

    // A tag in the details selects its twin in the filter
    const tag = page.locator('#mainDetails button.tag').first();
    const label = (await tag.textContent()).trim();
    await tag.click();
    await expect(page.locator('#selectedTags .tag', { hasText: label })).toHaveAttribute('aria-pressed', 'true');

    // BACK (phone only): the list again
    if (!wide) {
      await page.locator('#mainDetails .navButton').click();
      await expect(page.locator('#results')).toBeVisible();
      await expect(page.locator('#mainDetails')).toBeHidden();
    }
  });

  test('the standalone page of a place works without the one-page script', async ({ page }) => {
    await page.goto('/restaurants');
    await showResults(page);
    const onclick = await page.locator('#results .resultCell[role="button"]').first().getAttribute('onclick');
    const id = onclick.match(/details\/(\d+)/)[1];
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.goto(`/restaurant/details/${id}`);
    await expect(page.locator('#detailName')).toBeVisible();
    // There is no filter on this page: a click on a tag does nothing, and throws nothing
    const tag = page.locator('button.tag').first();
    if (await tag.count()) {
      await tag.click();
    }
    expect(errors).toEqual([]);
  });
});
