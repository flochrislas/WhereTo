const { test, expect } = require('@playwright/test');
const { showResults, waitForCachedResults } = require('./helpers');

test.describe('Service worker', () => {
  test('after one visit, the restaurants page and its results open again offline', async ({ page, context, browserName }) => {
    test.skip(browserName !== 'chromium', 'service workers are only driven in Chromium here');
    // First visit: the worker installs (pre-cache of the pages, the versioned stylesheet and script) and takes control
    await page.goto('/restaurants');
    await page.evaluate(() => navigator.serviceWorker.ready);
    await page.waitForFunction(() => navigator.serviceWorker.controller !== null);
    // A search made under the worker's control, so that its results fragment is in the cache
    await page.locator('#tags').getByRole('button', { name: 'ramen', exact: true }).click();
    await showResults(page);
    await waitForCachedResults(page);

    // Offline: the page, its stylesheet and its script come from the pre-cache, the results from the fragment cache
    await context.setOffline(true);
    await page.reload();
    await expect(page.locator('#tags .tag').first()).toBeVisible();
    // Styled (the stylesheet loaded: the page background is the theme's, not the browser's white)
    const background = await page.evaluate(() => getComputedStyle(document.body).backgroundColor);
    expect(['rgb(0, 0, 0)', 'rgb(217, 217, 227)']).toContain(background);
    // Alive (the script loaded: the theme button works)
    const theme = await page.locator('html').getAttribute('data-theme');
    await page.locator('#themeButton').click();
    await expect(page.locator('html')).not.toHaveAttribute('data-theme', theme);
    // The same search again: its fragment comes from the worker's cache
    await page.locator('#tags').getByRole('button', { name: 'ramen', exact: true }).click();
    await showResults(page);
    await context.setOffline(false);
  });
});
