const { test, expect } = require('@playwright/test');
const { showResults } = require('./helpers');

/** The numbers of a column of the results list, top to bottom */
async function column(page, selector) {
  const texts = await page.locator(`#results .resultRow ${selector}`).allTextContents();
  return texts.map(t => parseFloat(t));
}

test.describe('Sort buttons', () => {
  test('near, best and cost first', async ({ page }) => {
    await page.goto('/restaurants');
    await showResults(page);

    // "Near first": the walking times never decrease down the list (the position is the faked GPS one)
    await page.locator('.sortButton[data-order-by="distance"]').click();
    await expect(page.locator('.sortButton[data-order-by="distance"]')).toHaveAttribute('aria-pressed', 'true');
    const minutes = await column(page, '.distance .oneDigit');
    expect(minutes.length).toBeGreaterThan(10);
    expect(minutes).toEqual([...minutes].sort((a, b) => a - b));

    // "Best first": the lunch scores never increase down the list
    await page.locator('.sortButton[data-order-by="ratings"]').click();
    await expect(page.locator('.sortButton[data-order-by="ratings"]')).toHaveAttribute('aria-pressed', 'true');
    // The lunch score is the 4th cell of a row (name, distance, cost, lunch, food, place)
    await expect.poll(async () => {
      const lunch = await column(page, '.resultCell:nth-child(4) .oneDigit');
      return lunch.length > 10 && lunch.every((s, i) => i === 0 || s <= lunch[i - 1]);
    }).toBe(true);

    // "Cost first": the prices never decrease down the list
    await page.locator('.sortButton[data-order-by="cost"]').click();
    await expect(page.locator('.sortButton[data-order-by="cost"]')).toHaveAttribute('aria-pressed', 'true');
    await expect.poll(async () => {
      const prices = (await column(page, '.fourDigits')).filter(p => !Number.isNaN(p));
      return prices.length > 10 && prices.every((p, i) => i === 0 || p >= prices[i - 1]);
    }).toBe(true);
  });
});
