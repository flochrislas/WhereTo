const { test, expect } = require('@playwright/test');
const { showResults, showFilter, waitForResults } = require('./helpers');

test.describe('Requests that fail or answer late', () => {
  test('after a failed "Show All", the next "Show All" tries again', async ({ page }) => {
    // The results requests fail (as with no network) until the test says otherwise
    let blocked = true;
    await page.route(/\/restaurants\/results/, route => blocked ? route.abort() : route.continue());
    await page.goto('/restaurants');
    await showResults(page).catch(() => {});
    await expect(page.locator('#results')).toContainText('error', { timeout: 10000 });
    // The network is back: "Show All" (the button exists in both layouts) must request again
    blocked = false;
    await showFilter(page);
    await page.locator('#filterHeaderButton').click();
    const count = await waitForResults(page);
    expect(count).toBeGreaterThan(100);
  });

  test('a tag answer arriving after the tag was removed does not replace the list', async ({ page }) => {
    // The tag search answers slowly, the unfiltered one at once
    await page.route(/\/restaurants\/results\?.*tags=ramen/, async route => {
      await new Promise(resolve => setTimeout(resolve, 2000));
      return route.continue();
    });
    await page.goto('/restaurants');
    const total = await showResults(page);
    await showFilter(page);
    // Select ramen, then remove it before its (slow) answer comes back
    await page.locator('#tags').getByRole('button', { name: 'ramen', exact: true }).click();
    await page.locator('#selectedTags').getByRole('button', { name: 'ramen', exact: true }).click();
    // The full list must be there and stay there once the late answer arrives
    await page.waitForTimeout(3000);
    const after = await showResults(page);
    expect(after).toBe(total);
    await expect(page.locator('#results')).not.toContainText('Updating results');
  });
});
