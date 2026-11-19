const { test, expect } = require('@playwright/test');
const { showResults, waitForResults, waitForCachedResults } = require('./helpers');

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

    // Offline: the page, its stylesheet and its script come from the pre-cache, the results from the fragment cache.
    // The address is /restaurants?tags=ramen now (the state of the page): the worker serves /restaurants for it
    await context.setOffline(true);
    await expect(page).toHaveURL(/\/restaurants\?tags=ramen$/);
    await page.reload();
    // Attached, not visible: on a phone the restored search shows the results panel, the tags are behind it
    await expect(page.locator('#tags .tag').first()).toBeAttached();
    // Styled (the stylesheet loaded: the page background is the theme's, not the browser's white)
    const background = await page.evaluate(() => getComputedStyle(document.body).backgroundColor);
    expect(['rgb(0, 0, 0)', 'rgb(217, 217, 227)']).toContain(background);
    // Alive (the script loaded: the theme button works)
    const theme = await page.locator('html').getAttribute('data-theme');
    await page.locator('#themeButton').click();
    await expect(page.locator('html')).not.toHaveAttribute('data-theme', theme);
    // The same search again, restored from the address: its fragment comes from the worker's cache
    await expect(page.locator('#selectedTags .tag[data-tag="ramen"]')).toHaveAttribute('aria-pressed', 'true');
    // The restored search shows its results by itself, on a phone too
    await waitForResults(page);
    await expect(page.locator('#results')).not.toContainText('error');
    await context.setOffline(false);
  });

  test('the Japanese pages open offline too, and an unknown Japanese page falls back to the Japanese home', async ({ page, context, browserName }) => {
    test.skip(browserName !== 'chromium', 'service workers are only driven in Chromium here');
    // A visit to an English page installs the worker, which pre-caches the shell pages of every language
    await page.goto('/');
    await page.evaluate(() => navigator.serviceWorker.ready);
    await page.waitForFunction(() => navigator.serviceWorker.controller !== null);

    await context.setOffline(true);
    // Never visited, but pre-cached
    await page.goto('/ja/restaurants');
    await expect(page.locator('html')).toHaveAttribute('lang', 'ja');
    await expect(page.locator('#tags').getByRole('button', { name: 'ラーメン', exact: true })).toBeAttached();
    // A Japanese place page never seen: the Japanese home page instead of the browser's error page
    await page.goto('/ja/restaurant/details/999999');
    await expect(page.locator('html')).toHaveAttribute('lang', 'ja');
    await expect(page.locator('.giantButton').first()).toBeVisible();
    await context.setOffline(false);
  });
});
