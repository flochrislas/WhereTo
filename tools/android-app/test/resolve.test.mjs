// The path rule of the app (lib/resolve.mjs), on a small export made here
import assert from 'node:assert/strict';
import { mkdirSync, mkdtempSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { after, before, test } from 'node:test';
import { mimeType, resolve } from '../lib/resolve.mjs';

let root;

before(() => {
  root = mkdtempSync(join(tmpdir(), 'whereto-resolve-'));
  for (const [file, content] of Object.entries({
    'index.html': 'home',
    'ja/index.html': 'home ja',
    'ja/restaurants/index.html': 'restaurants ja',
    'restaurants/details/12/index.html': 'details 12',
    'css/style.css': 'body{}',
    'icons/tags.svg': '<svg/>',
    'fonts/open-sans.woff2': 'font',
    'app-data/meta.json': '{}',
    'favicon.ico': 'ico',
  })) {
    mkdirSync(join(root, file, '..'), { recursive: true });
    writeFileSync(join(root, file), content);
  }
});

after(() => rmSync(root, { recursive: true, force: true }));

test('a folder is served by its index.html, with or without the leading slash', () => {
  assert.deepEqual(resolve(root, '/'), { status: 200, file: join(root, 'index.html'), type: 'text/html; charset=utf-8' });
  assert.equal(resolve(root, '').file, join(root, 'index.html'));
  assert.equal(resolve(root, '/ja').file, join(root, 'ja', 'index.html'));
  assert.equal(resolve(root, '/ja/').file, join(root, 'ja', 'index.html'));
  assert.equal(resolve(root, 'ja/restaurants').file, join(root, 'ja', 'restaurants', 'index.html'));
  assert.equal(resolve(root, '/restaurants/details/12').file, join(root, 'restaurants', 'details', '12', 'index.html'));
});

test('a file is served as itself, the query string dropped', () => {
  assert.deepEqual(resolve(root, '/css/style.css?v=1'), { status: 200, file: join(root, 'css', 'style.css'), type: 'text/css; charset=utf-8' });
  assert.equal(resolve(root, '/icons/tags.svg?v=2#ramen').type, 'image/svg+xml');
  assert.equal(resolve(root, '/restaurants/details/12?place=3').status, 200);
  assert.equal(resolve(root, '/app-data/meta.json').type, 'application/json');
});

test('anything else is a 404, never a fall-through', () => {
  assert.deepEqual(resolve(root, '/restaurants/details/999'), { status: 404 });
  assert.deepEqual(resolve(root, '/restaurants/results?orderBy=ratings'), { status: 404 });
  assert.deepEqual(resolve(root, '/sw.js'), { status: 404 });
  assert.deepEqual(resolve(root, '/css'), { status: 404 }, 'a folder without index.html');
});

test('the way out of the export is refused', () => {
  for (const path of ['/../package.json', '/css/../../export.mjs', '/%2e%2e/package.json', '/css/..%2F..%2Fexport.mjs', '..', '/a\\..\\b', '/%E0%A4%A']) {
    assert.deepEqual(resolve(root, path), { status: 404 }, path);
  }
  // A ".." that would stay inside is refused too: the rule is simple, the pages never need it
  assert.deepEqual(resolve(root, '/ja/../index.html'), { status: 404 });
});

test('the content types of the files of the export', () => {
  assert.equal(mimeType('a.woff2'), 'font/woff2');
  assert.equal(mimeType('a.ico'), 'image/x-icon');
  assert.equal(mimeType('a.png'), 'image/png');
  assert.equal(mimeType('a.js'), 'text/javascript; charset=utf-8');
  assert.equal(mimeType('A.SVG'), 'image/svg+xml');
  assert.equal(mimeType('a.unknown'), 'application/octet-stream');
});
