// The pictures of the tags (doc/13-tag-icons.md): each one is a <use> pointing into the sprite, a separate
// SVG file. A wrong address or a missing symbol draws nothing and breaks nothing, so only a test sees it.
const { test, expect } = require('@playwright/test');

test.describe('Tag icons', () => {
  test('every icon of the tag cloud is a symbol of the sprite the page asks for', async ({ page }) => {
    await page.goto('/restaurants');
    const icons = page.locator('#tags .tag .tagIcon use');
    await expect(icons.first()).toBeAttached();

    const hrefs = await icons.evaluateAll((uses) => uses.map((use) => use.getAttribute('href')));
    expect(hrefs.length).toBeGreaterThan(50);
    const sprites = [...new Set(hrefs.map((href) => href.split('#')[0]))];
    expect(sprites, 'one sprite, at one versioned address').toHaveLength(1);
    expect(sprites[0]).toMatch(/\/icons\/tags\.svg\?v=\d+$/);

    const response = await page.request.get(sprites[0]);
    expect(response.ok()).toBe(true);
    expect(response.headers()['content-type']).toContain('image/svg+xml');
    const symbols = new Set([...(await response.text()).matchAll(/<symbol id="([^"]+)"/g)].map((match) => match[1]));
    const missing = hrefs.map((href) => href.split('#')[1]).filter((id) => !symbols.has(id));
    expect(missing).toEqual([]);
  });

  // The <svg> keeps its size when its <use> draws nothing (a sprite the browser refuses, a symbol that is
  // not there): only the box of the <use> itself tells a drawn icon from a blank one.
  test('every icon of the tag cloud is drawn', async ({ page }) => {
    await page.goto('/restaurants');
    const icons = page.locator('#tags .tag .tagIcon use');
    await expect(icons.first()).toBeAttached();
    const blank = () => icons.evaluateAll((uses) => uses.filter((use) => !(use.getBBox().width > 0)).map((use) => use.getAttribute('href').split('#')[1]));
    await expect.poll(blank, 'icons that draw nothing').toEqual([]);

    // The check does see a blank icon: a symbol the sprite does not have
    await icons.first().evaluate((use) => use.setAttribute('href', use.getAttribute('href').split('#')[0] + '#no-such-icon'));
    await expect.poll(blank).toEqual(['no-such-icon']);
  });

  test('the icon sits before the label, hidden from screen readers, and does not make its tag taller', async ({ page }) => {
    await page.goto('/restaurants');
    const withIcon = page.locator('#tags .tag[data-tag="ramen"]');
    const without = page.locator('#tags .tag[data-tag="western"]');
    await expect(withIcon.locator('svg.tagIcon')).toHaveAttribute('aria-hidden', 'true');
    await expect(withIcon).toHaveText('ramen');
    await expect(without.locator('svg')).toHaveCount(0);

    const icon = await withIcon.locator('svg.tagIcon').boundingBox();
    expect(icon.width).toBeGreaterThan(12);
    expect(Math.abs(icon.width - icon.height)).toBeLessThan(1);
    const [tall, plain] = [await withIcon.boundingBox(), await without.boundingBox()];
    expect(Math.abs(tall.height - plain.height)).toBeLessThan(1);
  });
});
