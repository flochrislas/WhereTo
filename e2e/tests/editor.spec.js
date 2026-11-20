// The seeder editor (tools/freshness/edit.js): the page's behaviour that the server tests cannot see.
// The server is started here, on a copy of the tool's fixtures, so the app does not need to run.
const { test, expect } = require('@playwright/test');
const { mkdtempSync, cpSync, mkdirSync, readFileSync, writeFileSync, copyFileSync } = require('node:fs');
const { tmpdir } = require('node:os');
const { join } = require('node:path');
const { pathToFileURL } = require('node:url');

const TOOL = join(__dirname, '..', '..', 'tools', 'freshness');
const FIXTURES = join(TOOL, 'test', 'fixtures');

let server;
let base;
let dir;

test.beforeEach(async () => {
  // A fresh copy of the seeders per test (the karaoke fixture stands in for the stores and the bars), and a server on a free port
  dir = mkdtempSync(join(tmpdir(), 'editor-'));
  const seeders = join(dir, 'seeders');
  cpSync(FIXTURES, seeders, { recursive: true });
  for (const kind of ['Stores', 'Bars']) copyFileSync(join(seeders, 'KaraokesTableSeeder.php'), join(seeders, `${kind}TableSeeder.php`));
  const { createEditorServer } = await import(pathToFileURL(join(TOOL, 'edit.js')).href); // an ES module from this CommonJS spec
  server = createEditorServer({ seedersDir: seeders, reportsDir: join(dir, 'reports'), repoRoot: dir, run: () => null });
  await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
  base = `http://127.0.0.1:${server.address().port}`;
});
test.afterEach(() => new Promise(resolve => server.close(resolve)));

const seederPath = () => join(dir, 'seeders', 'RestaurantsTableSeeder.php');
const seeder = () => readFileSync(seederPath(), 'utf8');

test.describe('Seeder editor', () => {
  test('opens without a reports folder and saves a field on blur', async ({ page }) => {
    await page.goto(base + '/');
    await expect(page.locator('#count')).toHaveText('22 of 22');
    await page.locator('tr.row', { hasText: 'Hakata Furyu' }).click();
    const location = page.locator('tr.form [data-key="location"]');
    await location.fill('Shibuya, Dogenzaka');
    await location.press('Enter');
    await expect(page.locator('tr.form [data-key="location"] + .state')).toHaveText('saved');
    expect(seeder()).toContain("'location' => 'Shibuya, Dogenzaka',");
  });

  test('two queued edits and an outside change: the first gets 409, the second is never sent', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('tr.row', { hasText: 'Hakata Furyu' }).click();
    // Hold the first save, so the second one queues behind it
    let release;
    const held = new Promise(resolve => { release = resolve; });
    const sent = [];
    await page.route('**/api/places/restaurants/*', async route => {
      sent.push(JSON.parse(route.request().postData()));
      if (sent.length === 1) await held;
      await route.continue();
    });
    await page.locator('tr.form [data-key="location"]').fill('First');
    await page.locator('tr.form [data-key="location"]').press('Tab');
    await page.locator('tr.form [data-key="type"]').fill('Second');
    await page.locator('tr.form [data-key="type"]').press('Tab');
    await expect(page.locator('tr.form [data-key="type"] + .state')).toHaveText('saving…'); // queued, not sent
    // Meanwhile someone inserts a block at the top of the file in a text editor
    const text = seeder();
    const start = text.indexOf('        $resto = Restaurant::create([');
    const marker = "139.698468');";
    const end = text.indexOf(marker) + marker.length;
    const block = text.slice(start, end);
    writeFileSync(seederPath(), text.replace(block, block.replace("'Hakata Furyu'", "'Inserted'") + '\n\n' + block));
    release();
    await expect(page.locator('#notice')).toContainText('changed on disk');
    await expect(page.locator('#count')).toHaveText('23 of 23'); // reloaded: the inserted block is listed
    expect(sent.length).toBe(1); // the second edit was dropped, not sent with the new revision
    expect(seeder()).not.toContain("'type' => 'Second'");
    expect(seeder()).not.toContain("'location' => 'First'");
    // The form follows its place (now the second block), never the index, and says what was lost
    await expect(page.locator('tr.form [data-key="name"]')).toHaveValue('Hakata Furyu');
    await expect(page.locator('#notice')).toContainText('location = First');
    await expect(page.locator('#notice')).toContainText('type = Second');
    await expect(page.locator('tr.form [data-key="location"]')).toHaveValue('Shibuya'); // the value on disk, not the lost one
  });

  test('an emptied text key writes an empty string, an emptied score is refused', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('tr.row', { hasText: 'Hakata Furyu' }).click();
    await page.locator('tr.form [data-key="type"]').fill('');
    await page.locator('tr.form [data-key="type"]').press('Enter');
    await expect(page.locator('tr.form [data-key="type"] + .state')).toHaveText('saved');
    expect(seeder()).toContain("'type' => '',");
    const before = seeder();
    await page.locator('tr.form [data-key="score_food"]').fill('');
    await page.locator('tr.form [data-key="score_food"]').press('Enter');
    await expect(page.locator('tr.form [data-key="score_food"] + .state')).toHaveClass(/error/);
    await expect(page.locator('tr.form [data-key="score_food"] + .state')).toContainText('number');
    expect(seeder()).toBe(before);
    // lunch_price may be null: emptied, it is cleared
    await page.locator('tr.form [data-key="lunch_price"]').fill('');
    await page.locator('tr.form [data-key="lunch_price"]').press('Enter');
    await expect(page.locator('tr.form [data-key="lunch_price"] + .state')).toHaveText('saved');
    expect(seeder()).toContain("'lunch_price' => null,");
  });

  test('an edit made while the page reloads is refused, not sent', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('tr.row', { hasText: 'Hakata Furyu' }).click();
    // Someone inserts a block above, then the user presses Reload; the reports answer is held so the old form stays visible
    const text = seeder();
    const start = text.indexOf('        $resto = Restaurant::create([');
    const marker = "139.698468');";
    const block = text.slice(start, text.indexOf(marker) + marker.length);
    writeFileSync(seederPath(), text.replace(block, block.replace("'Hakata Furyu'", "'Inserted'") + '\n\n' + block));
    let release;
    const held = new Promise(resolve => { release = resolve; });
    await page.route('**/api/reports', async route => { await held; await route.continue(); });
    const sent = [];
    await page.route('**/api/places/restaurants/*', async route => { sent.push(route.request().postData()); await route.continue(); });
    await page.locator('#reload').click();
    await page.locator('tr.form [data-key="location"]').fill('During reload');
    await page.locator('tr.form [data-key="location"]').press('Enter');
    await expect(page.locator('tr.form [data-key="location"] + .state')).toContainText('reloading');
    release();
    await expect(page.locator('#count')).toHaveText('23 of 23');
    expect(sent.length).toBe(0);
    expect(seeder()).not.toContain('During reload');
  });

  test('an edit queued before a reload is dropped when the reload brings a changed seeder', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('tr.row', { hasText: 'Hakata Furyu' }).click();
    // A first save is held (it will be refused: not a URL); a location change queues behind it
    let release;
    const held = new Promise(resolve => { release = resolve; });
    const sent = [];
    await page.route('**/api/places/restaurants/*', async route => {
      sent.push(JSON.parse(route.request().postData()));
      if (sent.length === 1) await held;
      await route.continue();
    });
    await page.locator('tr.form [data-key="tabelog_link"]').fill('not a url');
    await page.locator('tr.form [data-key="tabelog_link"]').press('Tab');
    await page.locator('tr.form [data-key="location"]').fill('Queued before reload');
    await page.locator('tr.form [data-key="location"]').press('Tab');
    await expect(page.locator('tr.form [data-key="location"] + .state')).toHaveText('saving…');
    // Someone inserts a block above, and the user reloads before the first save comes back
    const text = seeder();
    const start = text.indexOf('        $resto = Restaurant::create([');
    const marker = "139.698468');";
    const block = text.slice(start, text.indexOf(marker) + marker.length);
    writeFileSync(seederPath(), text.replace(block, block.replace("'Hakata Furyu'", "'Inserted'") + '\n\n' + block));
    await page.locator('#reload').click();
    await expect(page.locator('#count')).toHaveText('23 of 23');
    release();
    await expect(page.locator('#notice')).toContainText('location = Queued before reload');
    expect(sent.length).toBe(1); // the queued edit was never sent with the new revision
    expect(seeder()).not.toContain('Queued before reload');
  });

  test('two reloads with a report selected are one reload, and saves stay refused until the rows are redrawn', async ({ page }) => {
    mkdirSync(join(dir, 'reports'));
    const result = { kind: 'restaurants', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468, verdict: 'operational', reason: 'open', google: { id: 'ChIJx', name: 'Furyu', address: 'Tokyo', status: 'OPERATIONAL' }, distance: 2 };
    writeFileSync(join(dir, 'reports', '2026-11-18T0930-all.json'), JSON.stringify({ date: '2026-11-18', results: [result] }));
    await page.goto(base + '/');
    await page.locator('#report').selectOption('2026-11-18T0930-all.json');
    await expect(page.locator('tr.row', { hasText: 'Hakata Furyu' })).toContainText('operational');
    await page.locator('tr.row', { hasText: 'Hakata Furyu' }).click();
    // The report answer is held: a reload installs the places and waits for it before redrawing
    let release;
    const held = new Promise(resolve => { release = resolve; });
    let reportRequests = 0;
    await page.route('**/api/reports/*', async route => { reportRequests++; await held; await route.continue(); });
    const sent = [];
    await page.route('**/api/places/restaurants/*', async route => { sent.push(route.request().postData()); await route.continue(); });
    await page.locator('#reload').click();
    await page.locator('#reload').click();
    await expect.poll(() => reportRequests).toBe(1); // one reload in flight, not two
    await page.locator('tr.form [data-key="location"]').fill('During two reloads');
    await page.locator('tr.form [data-key="location"]').press('Enter');
    await expect(page.locator('tr.form [data-key="location"] + .state')).toContainText('reloading');
    release();
    await expect(page.locator('tr.row', { hasText: 'Hakata Furyu' })).toContainText('operational');
    expect(reportRequests).toBe(1);
    expect(sent.length).toBe(0);
    expect(seeder()).not.toContain('During two reloads');
  });

  test('a reload whose report fetch fails still redraws the rows, and the form follows its place', async ({ page }) => {
    mkdirSync(join(dir, 'reports'));
    const result = { kind: 'restaurants', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468, verdict: 'operational', reason: 'open', google: { id: 'ChIJx', name: 'Furyu', address: 'Tokyo', status: 'OPERATIONAL' }, distance: 2 };
    writeFileSync(join(dir, 'reports', '2026-11-18T0930-all.json'), JSON.stringify({ date: '2026-11-18', results: [result] }));
    await page.goto(base + '/');
    await page.locator('#report').selectOption('2026-11-18T0930-all.json');
    await page.locator('tr.row', { hasText: 'Hakata Furyu' }).click();
    // A block is inserted above, and the report answer fails during the reload
    const text = seeder();
    const start = text.indexOf('        $resto = Restaurant::create([');
    const marker = "139.698468');";
    const block = text.slice(start, text.indexOf(marker) + marker.length);
    writeFileSync(seederPath(), text.replace(block, block.replace("'Hakata Furyu'", "'Inserted'") + '\n\n' + block));
    await page.route('**/api/reports/*', route => route.fulfill({ status: 500, contentType: 'application/json', body: '{"error":"report broken"}' }));
    await page.locator('#reload').click();
    await expect(page.locator('#count')).toHaveText('23 of 23');
    await expect(page.locator('#notice')).toContainText('report broken');
    await expect(page.locator('tr.form [data-key="name"]')).toHaveValue('Hakata Furyu');
    await page.locator('tr.form [data-key="location"]').fill('After a failed report');
    await page.locator('tr.form [data-key="location"]').press('Enter');
    await expect(page.locator('tr.form [data-key="location"] + .state')).toHaveText('saved');
    const after = seeder();
    const furyu = after.slice(after.indexOf("'name' => 'Hakata Furyu'"), after.indexOf("'name' => 'Hakata Furyu'") + 200);
    expect(furyu).toContain("'location' => 'After a failed report'");
    const inserted = after.slice(after.indexOf("'name' => 'Inserted'"), after.indexOf("'name' => 'Inserted'") + 200);
    expect(inserted).toContain("'location' => 'Shibuya'");
  });

  test('coordinates need two numbers; an explicit zero is one', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('tr.row', { hasText: 'Hakata Furyu' }).click();
    const before = seeder();
    const coords = page.locator('tr.form [data-coords]');
    await coords.fill('35.65,');
    await coords.press('Enter');
    await expect(page.locator('tr.form [data-coords] + .state')).toContainText('two numbers');
    expect(seeder()).toBe(before);
    await coords.fill('35.65, 0');
    await coords.press('Enter');
    await expect(page.locator('tr.form [data-coords] + .state')).toHaveText('saved');
    expect(seeder()).toContain("fillCoordinatesFromString('35.65, 0');");
  });

  test('a report button takes its value from the report, apostrophe included', async ({ page }) => {
    mkdirSync(join(dir, 'reports'));
    const result = { kind: 'restaurants', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468, match: 'matched', nameJa: "博多風龍 'テスト'", element: 'node/1', elementName: "博多風龍 'テスト'", distance: 3, reason: 'exact English name' };
    writeFileSync(join(dir, 'reports', '2026-11-19T2000-names.json'), JSON.stringify({ date: '2026-11-19', source: 'OpenStreetMap', results: [result] }));
    await page.goto(base + '/');
    await page.locator('#report').selectOption('2026-11-19T2000-names.json');
    const row = page.locator('tr.row', { hasText: 'Hakata Furyu' });
    await row.getByRole('button', { name: 'Use this name' }).click();
    await expect(row.locator('td').nth(2)).toHaveText("博多風龍 'テスト'");
    expect(seeder()).toContain("'name_ja' => '博多風龍 \\'テスト\\'',");
  });
});
