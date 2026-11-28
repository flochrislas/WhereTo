// Puts a new export in place: the site (export/) and the reference answers of the parity tests
// (export-reference.json), which only make sense together. Used by export.mjs; apart so that it can be tested.
//
// The steps, in `folder`:
//   0. recover() from an earlier run that was interrupted (see below);
//   1. copy the new pair next to its destination, as export.new and export-reference.json.new (the staging folder
//      may be on another filesystem, where a move is a copy that can fail half-way);
//   2. move the previous pair aside, as export.old and export-reference.json.old. An item that does not exist yet
//      (a first export) gets an empty marker instead, export.none or export-reference.json.none: "nothing was here";
//   3. rename export.new, then export-reference.json.new, into place;
//   4. delete the .old pair and the markers.
// A failure at any step puts the previous pair back. A process killed at any step leaves a state that recover()
// reads without a journal:
//   - no .old and no marker: nothing was moved aside, what was published is in place;
//   - one exists and export-reference.json.new still exists: step 3 did not finish. What was there before is put
//     back (the .old), or nothing (the marker), and the half-installed new pair is removed;
//   - one exists and export-reference.json.new is gone: step 3 finished, the new pair is in place. Only the
//     .old pair and the markers are left to delete.
// (export-reference.json.new is made in step 1, before anything is moved aside, and only step 3 or a rollback
// removes it.) So the .old files are never deleted while they may be the only complete pair, and a site is never
// left published without its reference answers.
import { cpSync, existsSync, renameSync, rmSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';

/** The paths of the pair in `folder`, with a suffix ('', '.new', '.old', '.none') */
function pair(folder, suffix = '') {
  return { site: join(folder, `export${suffix}`), reference: join(folder, `export-reference.json${suffix}`) };
}

const remove = path => rmSync(path, { recursive: true, force: true });

/**
 * Finish or undo a publication that was interrupted, then clear what it left. Returns 'restored' (the previous pair
 * was put back), 'completed' (the new pair was already in place) or null (nothing to recover).
 */
export function recover(folder, { rename = renameSync } = {}) {
  const target = pair(folder);
  const staged = pair(folder, '.new');
  const previous = pair(folder, '.old');
  const none = pair(folder, '.none');
  const items = ['site', 'reference'];
  let outcome = null;
  if (items.some(item => existsSync(previous[item]) || existsSync(none[item]))) {
    if (existsSync(staged.reference)) {
      // Step 3 did not finish: whatever is in place of an item that was set aside is new, and goes
      for (const item of items) {
        if (existsSync(previous[item])) {
          remove(target[item]);
          rename(previous[item], target[item]);
        } else if (existsSync(none[item])) {
          remove(target[item]);
        }
      }
      outcome = 'restored';
    } else {
      items.forEach(item => remove(previous[item]));
      outcome = 'completed';
    }
    items.forEach(item => remove(none[item]));
  }
  // Only now: nothing here is the last copy of anything
  remove(staged.site);
  remove(staged.reference);
  return outcome;
}

/**
 * Publish the export in the folder `site` and the reference answers in the file `reference` into `folder`.
 * Throws when it fails, the previous pair back in place. `ops` replaces the file operations in the tests.
 */
export function publish(folder, site, reference, ops = {}) {
  const { copy = cpSync, rename = renameSync } = ops;
  recover(folder, { rename });
  const target = pair(folder);
  const staged = pair(folder, '.new');
  const previous = pair(folder, '.old');
  const none = pair(folder, '.none');
  try {
    copy(site, staged.site, { recursive: true });
    copy(reference, staged.reference);
    for (const item of ['site', 'reference']) {
      if (existsSync(target[item])) {
        rename(target[item], previous[item]);
      } else {
        writeFileSync(none[item], '');
      }
    }
    rename(staged.site, target.site);
    rename(staged.reference, target.reference);
  } catch (error) {
    // The same reading of the state as after a kill: the previous pair back, the new one removed
    recover(folder);
    throw error;
  }
  // The same reading again: the staged reference is gone, so this deletes the .old pair and the markers
  recover(folder);
}
