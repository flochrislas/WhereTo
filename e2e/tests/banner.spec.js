const { test, expect } = require('@playwright/test');

// A browser set to Japanese: navigator.languages starts with "ja-JP"
test.use({ locale: 'ja-JP' });

test.describe('Language banner', () => {
  test.beforeEach(async ({ isMobile }) => {
    test.skip(!isMobile, 'the scenario runs on the phone project');
  });

  test('a Japanese browser is offered the Japanese page once, and the tap lands there', async ({ page }) => {
    await page.goto('/restaurants');
    const banner = page.locator('#languageBanner');
    await expect(banner).toBeVisible();
    await expect(banner.getByRole('link')).toHaveText('日本語で見る');
    await banner.getByRole('link').click();
    await expect(page).toHaveURL(/\/ja\/restaurants$/);
    // The Japanese page is the browser's language: nothing to offer
    await expect(page.locator('html')).toHaveAttribute('lang', 'ja');
    await expect(page.locator('#languageBanner')).toBeHidden();
    // Next visit to an English page: answered once, never asked again
    await page.goto('/');
    await expect(page.locator('#languageBanner')).toBeHidden();
  });

  test('closing the banner also means no banner on the next visit', async ({ page }) => {
    await page.goto('/');
    const banner = page.locator('#languageBanner');
    await expect(banner).toBeVisible();
    await banner.getByRole('button', { name: '閉じる' }).click();
    await expect(banner).toBeHidden();
    await expect(page).toHaveURL(/\/$/); // still on the English page
    await page.goto('/restaurants');
    await expect(page.locator('#languageBanner')).toBeHidden();
  });
});

test.describe('Language banner for an English browser', () => {
  test.use({ locale: 'en-US' });

  test('an English browser is offered nothing on English pages, and English on Japanese pages', async ({ page, isMobile }) => {
    test.skip(!isMobile, 'the scenario runs on the phone project');
    await page.goto('/');
    // The script has run (the theme button got its label) and decided: no banner
    await expect(page.locator('#themeButton')).toHaveAttribute('aria-label', /Switch to the/);
    await expect(page.locator('#languageBanner')).toBeHidden();
    await page.goto('/ja/restaurants');
    await expect(page.locator('#languageBanner').getByRole('link')).toHaveText('Read this page in English');
  });
});
