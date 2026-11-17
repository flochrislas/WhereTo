// Small helpers shared by the scenarios
const { expect } = require('@playwright/test');

/** True when the page shows the three panels side by side (900px and more, see style.css) */
async function isWide(page) {
  return page.evaluate(() => window.matchMedia('(min-width: 900px)').matches);
}

/** Wait for the results list to be filled (the "N results" line is in the fragment the server returns) */
async function waitForResults(page) {
  await expect(page.locator('#results .resultsCount')).toBeVisible();
  const text = await page.locator('#results .resultsCount').textContent();
  return parseInt(text, 10);
}

/** On a phone, press "Show All" to see the results panel; on a wide screen the results are already there */
async function showResults(page) {
  if (!(await isWide(page))) {
    await page.locator('#filterHeaderButton').click();
  }
  return waitForResults(page);
}

/** On a phone, press "Show Filters" to see the tags again; on a wide screen they are always there */
async function showFilter(page) {
  if (!(await isWide(page))) {
    await page.locator('#mainResults .navButton').click();
  }
}

/** Wait until the service worker has stored a results fragment (it stores it after answering) */
async function waitForCachedResults(page) {
  await expect.poll(() => page.evaluate(async () => {
    const cache = await caches.open((await caches.keys())[0]);
    return (await cache.keys()).some(request => request.url.includes('/results?'));
  })).toBe(true);
}

module.exports = { isWide, waitForResults, showResults, showFilter, waitForCachedResults };
