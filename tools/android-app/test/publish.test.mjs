// The publication of a new export (lib/publish.mjs): the previous site and its reference answers must survive
// any failure and any interruption, together.
import assert from 'node:assert/strict';
import { cpSync, existsSync, mkdirSync, mkdtempSync, readdirSync, readFileSync, renameSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { test } from 'node:test';
import { publish, recover } from '../lib/publish.mjs';

/** A folder with a published pair `name` (or none), and a staging folder with the new pair "new" */
function setUp(t, published = 'old') {
  const root = mkdtempSync(join(tmpdir(), 'whereto-publish-'));
  t.after(() => rmSync(root, { recursive: true, force: true }));
  const folder = join(root, 'tool');
  const staging = join(root, 'staging');
  mkdirSync(folder);
  mkdirSync(join(staging, 'site'), { recursive: true });
  writeFileSync(join(staging, 'site', 'index.html'), 'new');
  writeFileSync(join(staging, 'reference.json'), 'new');
  if (published) {
    mkdirSync(join(folder, 'export'));
    writeFileSync(join(folder, 'export', 'index.html'), published);
    writeFileSync(join(folder, 'export-reference.json'), published);
  }
  return { folder, site: join(staging, 'site'), reference: join(staging, 'reference.json') };
}

/** What is published in the folder: [site, reference], and the folder must hold nothing else; null for an empty folder */
function published(folder) {
  if (readdirSync(folder).length === 0) return null;
  assert.deepEqual(readdirSync(folder).sort(), ['export', 'export-reference.json']);
  return [readFileSync(join(folder, 'export', 'index.html'), 'utf8'), readFileSync(join(folder, 'export-reference.json'), 'utf8')];
}

/** File operations where the n-th call of `name` throws */
function failing(name, nth) {
  let calls = 0;
  const wrap = (own, real) => (...args) => {
    if (own === name && ++calls === nth) throw new Error(`${name} ${nth} failed`);
    return real(...args);
  };
  return { copy: wrap('copy', cpSync), rename: wrap('rename', renameSync) };
}

/** Run publish() until its n-th rename, then stop as a killed process would: no rollback */
function killedAtRename(folder, site, reference, nth) {
  const kill = new Error('killed');
  let calls = 0;
  const rename = (...args) => {
    if (++calls === nth) throw kill;
    return renameSync(...args);
  };
  // publish() rolls back with the real operations on a failure, which a kill would not: redo the steps by hand
  const at = suffix => ({ site: join(folder, `export${suffix}`), reference: join(folder, `export-reference.json${suffix}`) });
  cpSync(site, at('.new').site, { recursive: true });
  cpSync(reference, at('.new').reference);
  try {
    for (const item of ['site', 'reference']) {
      if (existsSync(at('')[item])) rename(at('')[item], at('.old')[item]);
      else writeFileSync(at('.none')[item], '');
    }
    rename(at('.new').site, at('').site);
    rename(at('.new').reference, at('').reference);
  } catch (error) {
    if (error !== kill) throw error;
  }
}

test('a new pair replaces the previous one', t => {
  const { folder, site, reference } = setUp(t);
  publish(folder, site, reference);
  assert.deepEqual(published(folder), ['new', 'new']);
});

test('a first export, with nothing published before', t => {
  const { folder, site, reference } = setUp(t, null);
  publish(folder, site, reference);
  assert.deepEqual(published(folder), ['new', 'new']);
});

for (const [name, nth] of [['copy', 1], ['copy', 2], ['rename', 1], ['rename', 2], ['rename', 3], ['rename', 4]]) {
  test(`a failure at ${name} ${nth} leaves the previous pair in place`, t => {
    const { folder, site, reference } = setUp(t);
    assert.throws(() => publish(folder, site, reference, failing(name, nth)), new RegExp(`${name} ${nth} failed`));
    assert.deepEqual(published(folder), ['old', 'old']);
  });
}

for (const nth of [1, 2, 3, 4]) {
  test(`killed before rename ${nth}, then a retry whose copy fails: the previous pair is still there`, t => {
    const { folder, site, reference } = setUp(t);
    killedAtRename(folder, site, reference, nth);
    assert.throws(() => publish(folder, site, reference, failing('copy', 1)), /copy 1 failed/);
    assert.deepEqual(published(folder), ['old', 'old']);
  });

  test(`killed before rename ${nth}, then a retry that works`, t => {
    const { folder, site, reference } = setUp(t);
    killedAtRename(folder, site, reference, nth);
    publish(folder, site, reference);
    assert.deepEqual(published(folder), ['new', 'new']);
  });
}

test('killed after the new pair was installed, before the previous one was deleted: the new pair stays', t => {
  const { folder, site, reference } = setUp(t);
  killedAtRename(folder, site, reference, 5);
  assert.ok(existsSync(join(folder, 'export.old')));
  assert.equal(recover(folder), 'completed');
  assert.deepEqual(published(folder), ['new', 'new']);
});

test('recover() says what it did', t => {
  const { folder, site, reference } = setUp(t);
  assert.equal(recover(folder), null);
  killedAtRename(folder, site, reference, 3);
  assert.equal(recover(folder), 'restored');
  assert.deepEqual(published(folder), ['old', 'old']);
});

// A first export has two renames only: the new site, then the new reference
for (const [name, nth] of [['copy', 1], ['copy', 2], ['rename', 1], ['rename', 2]]) {
  test(`a first export that fails at ${name} ${nth} leaves nothing published`, t => {
    const { folder, site, reference } = setUp(t, null);
    assert.throws(() => publish(folder, site, reference, failing(name, nth)), new RegExp(`${name} ${nth} failed`));
    assert.equal(published(folder), null);
  });
}

for (const nth of [1, 2]) {
  test(`a first export killed before rename ${nth}: recovery leaves nothing published, and a retry works`, t => {
    const { folder, site, reference } = setUp(t, null);
    killedAtRename(folder, site, reference, nth);
    assert.equal(recover(folder), 'restored');
    assert.equal(published(folder), null);
    publish(folder, site, reference);
    assert.deepEqual(published(folder), ['new', 'new']);
  });
}

test('a first export killed after both were installed: the new pair stays', t => {
  const { folder, site, reference } = setUp(t, null);
  killedAtRename(folder, site, reference, 3);
  assert.equal(recover(folder), 'completed');
  assert.deepEqual(published(folder), ['new', 'new']);
});
