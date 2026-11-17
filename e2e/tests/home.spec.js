const { test, expect } = require('@playwright/test');

test.describe('Home page', () => {
  test('shows the four kinds of place and leads to the restaurants', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/Shibuya/);
    // One big button per kind of place (plus the social links)
    for (const path of ['/restaurants', '/stores', '/bars', '/karaokes']) {
      await expect(page.locator(`.giantButton[href$="${path}"]`)).toBeVisible();
    }
    // "Lunch in Shibuya" goes to the restaurants page (and asks for the position on the way)
    await page.locator('.giantButton[href$="/restaurants"]').click();
    await expect(page).toHaveURL(/\/restaurants$/);
    await expect(page.locator('#tags .tag').first()).toBeVisible();
  });

  test('the theme button switches between dark and light and the choice survives a reload', async ({ page }) => {
    await page.goto('/');
    const html = page.locator('html');
    const before = await html.getAttribute('data-theme');
    const other = before === 'dark' ? 'light' : 'dark';
    await page.locator('#themeButton').click();
    await expect(html).toHaveAttribute('data-theme', other);
    // The browser bar colour follows
    const meta = page.locator('meta[name="theme-color"]');
    await expect(meta).toHaveAttribute('content', other === 'light' ? '#f4f4f8' : '#141422');
    // Remembered in localStorage: still there after a reload
    await page.reload();
    await expect(html).toHaveAttribute('data-theme', other);
    // The page itself is coloured accordingly (the stylesheet variables)
    const background = await page.evaluate(() => getComputedStyle(document.body).backgroundColor);
    expect(background).toBe(other === 'light' ? 'rgb(217, 217, 227)' : 'rgb(0, 0, 0)');
  });
});
