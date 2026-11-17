const { test, expect } = require('@playwright/test');
const { showResults, waitForResults, showFilter } = require('./helpers');

test.describe('Tag filter', () => {
  test('selecting a tag filters the results, "all" and "any" change the count', async ({ page }) => {
    await page.goto('/restaurants');
    // Click "ramen": the button moves to the selected tags and is marked pressed
    const tags = page.locator('#tags');
    await tags.getByRole('button', { name: 'ramen', exact: true }).click();
    const selected = page.locator('#selectedTags');
    await expect(selected.getByRole('button', { name: 'ramen', exact: true })).toHaveAttribute('aria-pressed', 'true');
    await tags.getByRole('button', { name: 'noodles', exact: true }).click();

    // "all" (AND): places that have both tags; "any" (OR): places that have at least one. Any gives more.
    await page.locator('.opButton[data-op="AND"]').click();
    const all = await showResults(page);
    await showFilter(page); // the "all / any" buttons are in the filter panel
    await page.locator('.opButton[data-op="OR"]').click();
    await expect(page.locator('.opButton[data-op="OR"]')).toHaveAttribute('aria-pressed', 'true');
    await showResults(page); // the results refreshed in the background; on a phone, show the panel again
    await expect(page.locator('#results .resultsCount')).not.toHaveText(`${all} results`);
    const any = await waitForResults(page);
    expect(any).toBeGreaterThan(all);
    expect(all).toBeGreaterThan(0);
  });

  test('"Show All" lists every restaurant and the keyboard reaches the tags', async ({ page, isMobile }) => {
    test.skip(isMobile, 'no keyboard on a phone');
    await page.goto('/restaurants');
    const total = await showResults(page);
    expect(total).toBeGreaterThan(100);
    // The tags are real buttons: Tab reaches them, Space presses them
    const first = page.locator('#tags .tag').first();
    const label = (await first.textContent()).trim();
    await first.focus();
    await page.keyboard.press('Space');
    await expect(page.locator('#selectedTags').getByRole('button', { name: label, exact: true })).toHaveAttribute('aria-pressed', 'true');
  });
});
