// export.mjs recovers an interrupted publication before anything else: if this run then fails (the containers, the
// seeding...), the last complete export must already be back at its public paths. Runs a copy of export.mjs in a
// temporary repository, with a stand-in "podman" that fails where the test says.
import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { chmodSync, cpSync, mkdirSync, mkdtempSync, readdirSync, readFileSync, renameSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { dirname, join } from 'node:path';
import { test } from 'node:test';
import { fileURLToPath } from 'node:url';

const tool = join(dirname(fileURLToPath(import.meta.url)), '..');

/** A repository with the tool, a published pair "old", and a podman that fails at the step named by FAIL_AT */
function setUp(t) {
  const root = mkdtempSync(join(tmpdir(), 'whereto-export-'));
  t.after(() => rmSync(root, { recursive: true, force: true }));
  const folder = join(root, 'tools', 'android-app');
  mkdirSync(folder, { recursive: true });
  mkdirSync(join(root, 'source', 'storage', 'app'), { recursive: true });
  cpSync(join(tool, 'export.mjs'), join(folder, 'export.mjs'));
  cpSync(join(tool, 'lib'), join(folder, 'lib'), { recursive: true });
  mkdirSync(join(folder, 'export'));
  writeFileSync(join(folder, 'export', 'index.html'), 'old');
  writeFileSync(join(folder, 'export-reference.json'), 'old');
  const bin = join(root, 'bin');
  mkdirSync(bin);
  writeFileSync(join(bin, 'podman'), `#!/bin/sh
case "$*" in
  *" ps "*) [ "$FAIL_AT" = containers ] && exit 1; printf 'app\\ndb\\n' ;;
  *psql*) echo 1 ;;
  *app:export-static*) echo "seeding failed" >&2; exit 1 ;;
  *) exit 1 ;;
esac
`);
  chmodSync(join(bin, 'podman'), 0o755);
  const run = failAt => spawnSync(process.execPath, [join(folder, 'export.mjs')], {
    encoding: 'utf8',
    env: { ...process.env, PATH: `${bin}:${process.env.PATH}`, FAIL_AT: failAt },
  });
  return { folder, run };
}

/** An export killed after it moved the previous pair aside, before the new one was in place */
function interrupt(folder) {
  mkdirSync(join(folder, 'export.new'));
  writeFileSync(join(folder, 'export.new', 'index.html'), 'new');
  writeFileSync(join(folder, 'export-reference.json.new'), 'new');
  renameSync(join(folder, 'export'), join(folder, 'export.old'));
  renameSync(join(folder, 'export-reference.json'), join(folder, 'export-reference.json.old'));
}

function assertPreviousPairInPlace(folder) {
  assert.deepEqual(readdirSync(folder).sort(), ['export', 'export-reference.json', 'export.mjs', 'lib']);
  assert.equal(readFileSync(join(folder, 'export', 'index.html'), 'utf8'), 'old');
  assert.equal(readFileSync(join(folder, 'export-reference.json'), 'utf8'), 'old');
}

for (const step of ['containers', 'seeding']) {
  test(`an interrupted publication, then a retry that fails at the ${step}: the previous pair is back in place`, t => {
    const { folder, run } = setUp(t);
    interrupt(folder);
    const result = run(step);
    assert.equal(result.status, 1, result.stderr);
    if (step === 'seeding') assert.match(result.stderr, /The export failed/);
    assert.match(result.stdout, /the previous one is back in place/);
    assertPreviousPairInPlace(folder);
  });
}

test('a run that fails leaves no lock, and a lock left by another run refuses the export without touching anything', t => {
  const { folder, run } = setUp(t);
  assert.equal(run('seeding').status, 1);
  assertPreviousPairInPlace(folder);
  interrupt(folder);
  mkdirSync(join(folder, '.export.lock'));
  const result = run('seeding');
  assert.equal(result.status, 1);
  assert.match(result.stderr, /Another export is running/);
  // Not recovered: the run that holds the lock may be the one publishing
  assert.ok(readdirSync(folder).includes('export.old'));
});
