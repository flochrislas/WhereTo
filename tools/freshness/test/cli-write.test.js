import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdtempSync, readdirSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { writePlanSafely, writePlans, SeederChangedError } from '../lib/cli.js';

function plan(dir, before = 'before\n', after = 'after\n') {
    const filePath = join(dir, 'Seeder.php');
    writeFileSync(filePath, before);
    return { filePath, before, after, changed: true };
}
const ok = () => ({ status: 0, stdout: 'No syntax errors', stderr: '' });
const noPhp = () => null;

test('writes the file when php -l passes on the prepared copy, and leaves no temporary file', () => {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-'));
    const p = plan(dir);
    const linted = [];
    const run = (command, args) => { linted.push(args[1]); return ok(); };
    assert.equal(writePlanSafely(p, run), true);
    assert.equal(readFileSync(p.filePath, 'utf8'), 'after\n');
    assert.notEqual(linted[0], p.filePath); // the copy was linted, not the seeder
    assert.deepEqual(readdirSync(dir), ['Seeder.php']);
});

test('a php -l failure leaves the original byte for byte and no temporary file', () => {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-'));
    const p = plan(dir);
    const failing = () => ({ status: 255, stdout: 'PHP Parse error: syntax error', stderr: '' });
    assert.throws(() => writePlanSafely(p, failing), /php -l failed[\s\S]*Parse error[\s\S]*nothing was written/i);
    assert.equal(readFileSync(p.filePath, 'utf8'), 'before\n');
    assert.deepEqual(readdirSync(dir), ['Seeder.php']);
});

test('without php installed the file is written without a lint', () => {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-'));
    const p = plan(dir);
    assert.equal(writePlanSafely(p, noPhp), true);
    assert.equal(readFileSync(p.filePath, 'utf8'), 'after\n');
});

test('a file changed on disk since the plan was made is not replaced, before and after the lint', () => {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-'));
    const p = plan(dir);
    writeFileSync(p.filePath, 'someone else\n');
    assert.throws(() => writePlanSafely(p, ok), SeederChangedError);
    assert.equal(readFileSync(p.filePath, 'utf8'), 'someone else\n');

    const p2 = plan(dir);
    const editDuringLint = () => { writeFileSync(p2.filePath, 'saved during the lint\n'); return ok(); };
    assert.throws(() => writePlanSafely(p2, editDuringLint), SeederChangedError);
    assert.equal(readFileSync(p2.filePath, 'utf8'), 'saved during the lint\n');
    assert.deepEqual(readdirSync(dir), ['Seeder.php']);
});

test('writePlans writes the changed plans and logs them, skipping the unchanged', () => {
    const dir = mkdtempSync(join(tmpdir(), 'freshness-'));
    const p = plan(dir);
    const lines = [];
    const files = writePlans([p, { ...p, filePath: join(dir, 'Other.php'), changed: false }], { run: ok, log: (l) => lines.push(l) });
    assert.deepEqual(files, [p.filePath]);
    assert.match(lines.join('\n'), /Written: .*Seeder\.php/);
});
