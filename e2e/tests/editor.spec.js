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

  test('a place moved after the report was loaded loses the report buttons, a renamed one keeps them', async ({ page }) => {
    mkdirSync(join(dir, 'reports'));
    const result = { kind: 'restaurants', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468, verdict: 'closed_permanently', reason: 'Google says permanently closed', google: { id: 'ChIJx', name: 'Furyu', address: 'Tokyo', status: 'CLOSED_PERMANENTLY' }, distance: 2 };
    writeFileSync(join(dir, 'reports', '2026-11-18T0930-all.json'), JSON.stringify({ date: '2026-11-18', results: [result] }));
    await page.goto(base + '/');
    await page.locator('#report').selectOption('2026-11-18T0930-all.json');
    const row = page.locator('tr.row[data-id="restaurants/0"]');
    await row.locator('td').first().click();
    const form = page.locator('tr.form');
    // A new name: the same spot, the verdict still stands
    await form.locator('[data-key="name"]').fill('Hakata Fūryū');
    await form.locator('[data-key="name"]').blur();
    await expect(form.locator('[data-key="name"] + .state')).toHaveText('saved');
    await expect(row.getByRole('button', { name: 'Keep id' })).toBeVisible();
    // New coordinates: the verdict was for the old ones
    await form.locator('[data-coords]').fill('35.66, 139.70');
    await form.locator('[data-coords]').blur();
    await expect(form.locator('[data-coords] + .state')).toHaveText('saved');
    await expect(row.locator('td.report')).toHaveText(/moved since the report/);
    await expect(row.locator('td.report button')).toHaveCount(0);
    await expect(form).toBeVisible(); // the open form was not rebuilt
  });

  test('a report button clicked while the move of the place is still being saved writes nothing', async ({ page }) => {
    mkdirSync(join(dir, 'reports'));
    const result = { kind: 'restaurants', name: 'Hakata Furyu', lat: 35.658545, lon: 139.698468, verdict: 'closed_permanently', reason: 'Google says permanently closed', google: { id: 'ChIJx', name: 'Furyu', address: 'Tokyo', status: 'CLOSED_PERMANENTLY' }, distance: 2 };
    writeFileSync(join(dir, 'reports', '2026-11-18T0930-all.json'), JSON.stringify({ date: '2026-11-18', results: [result] }));
    await page.goto(base + '/');
    await page.locator('#report').selectOption('2026-11-18T0930-all.json');
    const row = page.locator('tr.row[data-id="restaurants/0"]');
    await row.locator('td').first().click();
    // The move is held on its way to the server, so the click on Closed finds it pending.
    let patches = 0;
    await page.route('**/api/places/**', async (route) => {
      patches += 1;
      await new Promise((resolve) => setTimeout(resolve, 400));
      await route.continue();
    });
    await page.locator('tr.form [data-coords]').fill('35.66, 139.70');
    await row.getByRole('button', { name: 'Closed', exact: true }).click(); // leaves the input: the move is sent, the closure queued
    await expect(row.locator('td.report')).toHaveText(/moved since the report/);
    await page.waitForTimeout(600); // the time a second PATCH would take
    expect(patches).toBe(1);
    expect(seeder()).toContain("fillCoordinatesFromString('35.66, 139.7')");
    expect(seeder()).toContain("'business_status' => 'CLOSED_PERMANENTLY',\n            'google_place_id' => 'ChIJclosedKindenmaru'"); // Kindenmaru's, the only one
    expect(seeder().match(/CLOSED_PERMANENTLY/g)).toHaveLength(1);
  });

  test('a long text that starts with a newline keeps it when the text is edited', async ({ page }) => {
    writeFileSync(seederPath(), seeder().replace("'points' => 'A line with", "'points' => '\nA line with"));
    await page.goto(base + '/');
    await page.locator('tr.row[data-id="restaurants/0"] td').first().click();
    const points = page.locator('tr.form [data-key="points"]');
    await expect(points).toHaveValue(/^\nA line with/);
    await points.press('Control+End');
    await points.pressSequentially(' More.');
    await points.blur();
    await expect(page.locator('tr.form [data-key="points"] + .state')).toHaveText('saved');
    expect(seeder()).toContain("'points' => '\nA line with \\'quotes\\', commas, and => arrows inside. More.',");
  });

  test('a new place is created from the form, written at the end of its seeder, and opened', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('#kind').selectOption('restaurants');
    await page.getByRole('button', { name: 'Add a place' }).click();
    const form = page.locator('#newPlace');
    await expect(form.locator('[data-new-kind]')).toHaveValue('restaurants'); // the kind of the filter
    await expect(form.locator('[data-key="score_food"]')).toHaveValue('0');
    await form.locator('[data-key="name"]').fill("Menya d'Or");
    await form.locator('[data-key="location"]').fill('Shibuya');
    await form.locator('[data-key="type"]').fill('Ramen');
    await form.locator('[data-key="points"]').fill('Rich broth.\nLong queue.');
    await form.locator('[data-key="visited"]').check();
    await form.locator('[data-key="score_lunch"]').fill('3');
    await form.locator('[data-tags]').fill('ramen, japanese');
    await form.locator('[data-coords]').fill('35.6601, 139.7002, 17z');
    const before = seeder();
    await form.getByRole('button', { name: 'Create' }).click();

    await expect(page.locator('#done')).toHaveText(/Added "Menya d'Or" to the restaurants, line \d+\./);
    await expect(form).toBeHidden();
    await expect(page.locator('#count')).toHaveText('14 of 23');
    // The new row is open, ready for the keys the form did not have
    await expect(page.locator('tr.form[data-id="restaurants/13"] [data-key="name"]')).toHaveValue("Menya d'Or");
    const text = seeder();
    expect(text.startsWith(before.slice(0, before.indexOf('        /*\n        ///AAA///')))).toBe(true); // everything above is untouched
    expect(text).toContain([
      '        $resto = Restaurant::create([',
      "            'name' => 'Menya d\\'Or',",
      "            'location' => 'Shibuya',",
      "            'type' => 'Ramen',",
      "            'lunch_price' => null,",
      "            'points' => 'Rich broth.",
      "Long queue.',",
      "            'visited' => true,",
    ].join('\n'));
    expect(text).toContain("            'score_lunch' => 3,\n            'score_food' => 0,\n        ]);\n        $resto->attachTags(['ramen', 'japanese']);\n        $resto->fillCoordinatesFromString('35.6601, 139.7002');");
    expect(text).not.toContain("'name_ja' => null"); // an empty optional key gets no line

    // The new place is edited like any other
    const nameJa = page.locator('tr.form [data-add]');
    await nameJa.selectOption('name_ja');
    await page.locator('tr.form [data-key="name_ja"]').fill('麺屋ドール');
    await page.locator('tr.form [data-key="name_ja"]').press('Enter');
    await expect(page.locator('tr.form [data-key="name_ja"] + .state')).toHaveText('saved');
    expect(seeder()).toContain("'name' => 'Menya d\\'Or',\n            'name_ja' => '麺屋ドール',");
  });

  test('a place created while a block is inserted above opens the new place, not the one at its old position', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('#kind').selectOption('restaurants');
    await page.getByRole('button', { name: 'Add a place' }).click();
    const form = page.locator('#newPlace');
    await form.locator('[data-key="name"]').fill('Late Ramen');
    await form.locator('[data-coords]').fill('35.6601, 139.7002');
    // Between the write and the reload of the page, someone inserts a block at the top of the file
    await page.route('**/api/places/restaurants', async (route) => {
      const response = await route.fetch();
      const text = seeder();
      const start = text.indexOf('        $resto = Restaurant::create([');
      const marker = "139.698468');";
      const block = text.slice(start, text.indexOf(marker) + marker.length);
      writeFileSync(seederPath(), text.replace(block, block.replace("'Hakata Furyu'", "'Inserted'") + '\n\n' + block));
      await route.fulfill({ response });
    });
    await form.getByRole('button', { name: 'Create' }).click();

    await expect(page.locator('#done')).toHaveText(/Added "Late Ramen" to the restaurants, line \d+\./);
    await expect(page.locator('#count')).toHaveText('15 of 24');
    // The answer said restaurants/13, which is now the place that was last before; the new one is 14
    await expect(page.locator('tr.form')).toHaveCount(1);
    await expect(page.locator('tr.form[data-id="restaurants/14"] [data-key="name"]')).toHaveValue('Late Ramen');
  });

  test('a new place with a wrong field is not sent; the form keeps what was typed when the kind changes', async ({ page }) => {
    let posts = 0;
    await page.route('**/api/places/*', (route) => { if (route.request().method() === 'POST') posts++; return route.continue(); });
    await page.goto(base + '/');
    await page.getByRole('button', { name: 'Add a place' }).click();
    const form = page.locator('#newPlace');
    const before = seeder();
    await form.locator('[data-key="score_food"]').fill('');
    await form.getByRole('button', { name: 'Create' }).click();
    await expect(form.locator('[data-key="name"] + .state')).toHaveText('a place needs a name');
    await expect(form.locator('[data-key="score_food"] + .state')).toHaveText('must be a number');
    await expect(form.locator('[data-coords] + .state')).toHaveText('two numbers: lat, lon');
    await expect(form.locator('.actions .state')).toHaveText('not created: see the fields in red');
    expect(posts).toBe(0);

    // Another kind: its own keys, the typed values kept
    await form.locator('[data-key="name"]').fill('Big Echo');
    await form.locator('[data-coords]').fill('35.66, 139.70');
    await form.locator('[data-new-kind]').selectOption('karaokes');
    await expect(form.locator('[data-key="name"]')).toHaveValue('Big Echo');
    await expect(form.locator('[data-coords]')).toHaveValue('35.66, 139.70');
    await expect(form.locator('[data-key="lunch_price"]')).toHaveCount(0);

    // A server refusal (the same name at the same spot) is shown, the form stays
    await form.locator('[data-key="name"]').fill('Karaoke-kan');
    const kan = JSON.parse(await (await page.request.get(base + '/api/places')).text()).places.find((p) => p.kind === 'karaokes' && p.name === 'Karaoke-kan');
    await form.locator('[data-coords]').fill(`${kan.lat}, ${kan.lon}`);
    for (const score of await form.locator('input[type=number]').all()) if (!(await score.inputValue())) await score.fill('1');
    await form.getByRole('button', { name: 'Create' }).click();
    await expect(form.locator('.actions .state')).toHaveText(/already in karaokes/);
    await expect(form).toBeVisible();
    expect(seeder()).toBe(before);

    await form.getByRole('button', { name: 'Cancel' }).click();
    await expect(form).toBeHidden();
  });

  test('deleting a place asks first, naming it; refused, nothing changes; accepted, its block goes', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('tr.row', { hasText: 'Go Go Curry' }).click();
    const before = seeder();

    let warning = '';
    page.once('dialog', (dialog) => { warning = dialog.message(); return dialog.dismiss(); });
    await page.getByRole('button', { name: 'Delete this place…' }).click();
    expect(warning).toContain('Delete "Go Go Curry" from the restaurants?');
    expect(warning).toContain('line 64');
    expect(warning).toContain('business_status');
    expect(seeder()).toBe(before);
    await expect(page.locator('#count')).toHaveText('22 of 22');

    page.once('dialog', (dialog) => dialog.accept());
    await page.getByRole('button', { name: 'Delete this place…' }).click();
    await expect(page.locator('#done')).toHaveText('Deleted "Go Go Curry" from the restaurants (it was at line 64).');
    await expect(page.locator('#count')).toHaveText('21 of 21');
    await expect(page.locator('tr.row', { hasText: 'Go Go Curry' })).toHaveCount(0);
    await expect(page.locator('tr.form')).toHaveCount(0);
    const text = seeder();
    expect(text).not.toContain('Go Go Curry');
    expect(text).not.toContain('35.657333');
    expect(text.split('\n')).toHaveLength(before.split('\n').length - 16);

    // The place that took its position is the one the page now edits there
    await page.locator('tr.row[data-id="restaurants/2"] td').first().click();
    await expect(page.locator('tr.form [data-key="name"]')).toHaveValue("Mc Donald's");
    await page.locator('tr.form [data-key="location"]').fill('Shibuya, Center Gai');
    await page.locator('tr.form [data-key="location"]').press('Enter');
    await expect(page.locator('tr.form [data-key="location"] + .state')).toHaveText('saved');
    expect(seeder().match(/Shibuya, Center Gai/g)).toHaveLength(1);
    expect(seeder()).toContain("'name' => 'Mc Donald\\'s',\n            'location' => 'Shibuya, Center Gai',");
  });

  test('a delete on a seeder that changed outside removes nothing and reloads', async ({ page }) => {
    await page.goto(base + '/');
    await page.locator('tr.row', { hasText: 'Go Go Curry' }).click();
    writeFileSync(seederPath(), seeder().replace("'name' => 'Hakata Furyu'", "'name' => 'Hakata Furyu 2'"));
    const changed = seeder();
    page.once('dialog', (dialog) => dialog.accept());
    await page.getByRole('button', { name: 'Delete this place…' }).click();
    await expect(page.locator('#notice')).toHaveText(/the seeder changed on disk.*Nothing was changed/);
    expect(seeder()).toBe(changed);
    await expect(page.locator('tr.row', { hasText: 'Hakata Furyu 2' })).toHaveCount(1);
    await expect(page.locator('tr.row', { hasText: 'Go Go Curry' })).toHaveCount(1);
  });
});
