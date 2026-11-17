const { test, expect } = require('@playwright/test');
const { showResults, waitForCachedResults } = require('./helpers');

test.describe('Service worker', () => {
  test('the restaurants page and its results open again offline', async ({ page, context, browserName }) => {
    test.skip(browserName !== 'chromium', 'service workers are only driven in Chromium here');
    await page.goto('/restaurants');
    // The first visit registers the worker; it controls the page from the next load on (clients.claim() in sw.js)
    await page.evaluate(() => navigator.serviceWorker.ready);
    await page.waitForFunction(() => navigator.serviceWorker.controller !== null);
    await page.reload();
    await showResults(page);
    await waitForCachedResults(page);

    // Offline: the page comes from the worker's pre-cache, the results from its cache (network-first, cached fallback)
    await context.setOffline(true);
    await page.reload();
    await expect(page.locator('#tags .tag').first()).toBeVisible();
    await showResults(page);
    await context.setOffline(false);
  });
});
