// Reads and edits the place seeders of the app (source/database/seeders/*TableSeeder.php).
//
// The seeders are PHP, but regular enough to be read line by line: each place is a block
//
//     $resto = Restaurant::create([
//         'name' => 'Hakata Furyu',
//         'key' => value,              (one key per line: a quoted string, a number, true/false/null)
//     ]);
//     $resto->attachTags([...]);
//     $resto->fillCoordinatesFromString('35.658545, 139.698468');
//
// Anything else inside a block stops the reader with the line number, rather than guessing.
// Code inside /* ... */ comments (old, closed places kept for the record) is skipped.

import { readFileSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { createHash } from 'node:crypto';
import assert from 'node:assert/strict';
import { phpStructure } from './php-structure.js';

// The seeder file of each kind of place.
export const KINDS = {
    restaurants: 'RestaurantsTableSeeder.php',
    stores: 'StoresTableSeeder.php',
    bars: 'BarsTableSeeder.php',
    karaokes: 'KaraokesTableSeeder.php',
};

// The app's seeders, relative to this file (tools/freshness/lib/ -> source/database/seeders/).
export const DEFAULT_SEEDERS_DIR = fileURLToPath(new URL('../../../source/database/seeders/', import.meta.url));

// The values the app accepts in the business_status column.
export const BUSINESS_STATUSES = ['OPERATIONAL', 'CLOSED_TEMPORARILY', 'CLOSED_PERMANENTLY', 'REPLACED', 'RELOCATED'];

// `$resto = Restaurant::create([` -> indentation, variable name, model.
const CREATE_RE = /^(\s*)\$(\w+) = (\w+)::create\(\[$/;
// `'key' => value,` -> indentation, key, the rest of the line (the value and its comma).
const KEY_RE = /^(\s*)'(\w+)' => (.*)$/;
// `$resto->fillCoordinatesFromString('35.65, 139.69');` -> variable name, latitude, longitude.
// Like the app (GeoUtils::toPositionArray), anything after a second comma is ignored: a few seeders
// carry a zoom level pasted from a Maps URL ('35.6604551,139.6999866,16').
const COORDS_RE = /^\s*\$(\w+)->fillCoordinatesFromString\('\s*(-?[\d.]+)\s*,\s*(-?[\d.]+)\s*(?:,[^']*)?'\);$/;
// The end of a block.
const END_RE = /^\s*\]\);$/;
// `$resto->attachTags(['ramen', 'japanese']);` -> variable name, the list between the brackets.
const TAGS_RE = /^\s*\$(\w+)->attachTags\(\[(.*)\]\);$/;
// Any attachTags call, on one line or the start of several.
const TAGS_CALL_RE = /^\s*\$(\w+)->attachTags\(/;
// The single-quoted strings of a PHP list: 'ramen', 'it\'s' -> their texts.
const quotedStrings = (list) => [...list.matchAll(/'((?:[^'\\]|\\.)*)'/g)].map((m) => m[1].replace(/\\(['\\])/g, '$1'));

// The revision of a seeder: the SHA-1 of its text. The editor sends it back with each edit, so
// that a page built from an older text cannot edit the wrong block.
export const revisionOf = (text) => createHash('sha1').update(text).digest('hex');

// Returns the index of the quote that closes a PHP single-quoted string opened at text[0],
// or -1 when the string goes on to the next line (a few descriptions span several lines).
function closingQuote(text) {
    for (let i = 1; i < text.length; i++) {
        if (text[i] === '\\') i++; // a backslash escapes the next character: skip it
        else if (text[i] === "'") return i;
    }
    return -1;
}

// Turns the text of a PHP value (without its trailing comma) into a JavaScript value, or throws.
function parseValue(raw) {
    if (raw.startsWith("'")) {
        const end = closingQuote(raw);
        if (end !== raw.length - 1) throw new Error(`cannot read the value ${raw}`);
        // In single quotes, PHP only knows two escapes: \' and \\.
        return raw.slice(1, end).replace(/\\(['\\])/g, '$1');
    }
    if (/^-?\d+(\.\d+)?$/.test(raw)) return Number(raw);
    if (raw === 'true') return true;
    if (raw === 'false') return false;
    if (raw === 'null') return null;
    throw new Error(`cannot read the value ${raw}`);
}

// Writes a JavaScript string as a PHP single-quoted string. Only \\ and ' need escaping there;
// any other character, Japanese included, is written as it is (the seeders are UTF-8).
export function phpString(value) {
    return `'${value.replace(/[\\']/g, '\\$&')}'`;
}

// Splits the text into lines and remembers the line ending, so an edit can put it back as it was.
function splitLines(text) {
    const eol = text.includes('\r\n') ? '\r\n' : '\n';
    return { lines: text.split(eol), eol };
}

// The low-level reader: returns every block with the indexes (0-based) of its interesting lines.
// `label` (a file name or kind) only serves the error messages.
function parseBlocks(lines, label) {
    const blocks = [];
    let createCount = 0; // `::create(` outside comments, to check against the blocks found
    let inComment = false;
    let block = null; // the block being read, between `create([` and `]);`
    let last = null; // the last block closed, waiting for its coordinates

    const fail = (index, message) => {
        throw new Error(`${label}, line ${index + 1}: ${message}`);
    };

    let pending = null; // a string value that spans several lines: { key, text, start }

    // Stores a key and its value in the current block.
    const addKey = (key, rest, start, end) => {
        let value;
        try {
            value = parseValue(rest.replace(/,$/, '')); // the comma after the last key is optional
        } catch (error) {
            fail(start, error.message);
        }
        if (key in block.values) fail(start, `the key '${key}' appears twice`);
        block.values[key] = value;
        block.keyOrder.push(key);
        block.keyLines[key] = start;
        block.keyEnds[key] = end;
    };

    lines.forEach((line, index) => {
        // Inside a multi-line string, every line belongs to the string (even a "/*" or "//").
        if (pending) {
            pending.text += '\n' + line;
            if (closingQuote(pending.text) !== -1) {
                addKey(pending.key, pending.text, pending.start, index);
                pending = null;
            }
            return;
        }

        const trimmed = line.trim();

        // Skip /* ... */ comments, which may span many lines.
        if (inComment) {
            if (trimmed.includes('*/')) inComment = false;
            return;
        }
        if (trimmed.startsWith('/*')) {
            // A comment closed on its own line (`/* ... */`) does not open a region.
            inComment = !trimmed.slice(2).includes('*/');
            return;
        }
        if (trimmed.startsWith('//')) return;

        if (line.includes('::create(')) createCount++;

        if (block) {
            if (END_RE.test(line)) {
                // An unknown status would break the app's enum: stop here rather than carry it on.
                const status = block.values.business_status;
                if (status !== undefined && !BUSINESS_STATUSES.includes(status)) {
                    fail(block.keyLines.business_status, `unknown business_status '${status}' for the place '${block.values.name}' (expected one of ${BUSINESS_STATUSES.join(', ')})`);
                }
                // name_ja is a name, null or absent: an empty or non-string value is a typo.
                const nameJa = block.values.name_ja;
                if (nameJa !== undefined && nameJa !== null && (typeof nameJa !== 'string' || nameJa.trim() === '')) {
                    fail(block.keyLines.name_ja, `name_ja must be a non-empty string for the place '${block.values.name}'`);
                }
                block.end = index;
                blocks.push(block);
                last = block;
                block = null;
                return;
            }
            const key = line.match(KEY_RE);
            if (!key) fail(index, `unexpected line in a create([...]) block: ${trimmed}`);
            block.indent ??= key[1]; // the indentation of the keys, for the lines the editor adds
            if (key[3].startsWith("'") && closingQuote(key[3]) === -1) {
                pending = { key: key[2], text: key[3], start: index };
            } else {
                addKey(key[2], key[3], index, index);
            }
            return;
        }

        const create = line.match(CREATE_RE);
        if (create) {
            if (last && last.lat === undefined) fail(last.start, `the place '${last.values.name}' has no coordinates (fillCoordinatesFromString)`);
            // keyLines / keyEnds: the first and last line of each key (they differ for a multi-line string).
            // keyOrder: the keys as they appear; end: the `]);` line; lineIndent: the indentation of the block's own lines.
            block = { start: index, variable: create[2], model: create[3], lineIndent: create[1], values: {}, keyLines: {}, keyEnds: {}, keyOrder: [], indent: undefined, tags: undefined, tagsLine: undefined, tagsCalls: 0, coordsLine: undefined };
            return;
        }

        // Every attachTags call of the block is counted, whatever its form: only the first one, when
        // it fits on a line, is read, and an edit of the tags must not leave the others behind.
        const tagsCall = line.match(TAGS_CALL_RE);
        if (tagsCall && last && tagsCall[1] === last.variable) last.tagsCalls += 1;
        const tags = line.match(TAGS_RE);
        if (tags && last && tags[1] === last.variable && last.tags === undefined) {
            last.tags = quotedStrings(tags[2]);
            last.tagsLine = index;
        }

        const coords = line.match(COORDS_RE);
        if (coords && last && coords[1] === last.variable && last.lat === undefined) {
            last.lat = Number(coords[2]);
            last.lon = Number(coords[3]);
            last.coordsLine = index;
        }
    });

    if (pending) fail(pending.start, 'this string is never closed');
    if (block) fail(block.start, 'this block is never closed');
    if (createCount !== blocks.length) {
        throw new Error(`${label}: ${createCount} ::create( found but ${blocks.length} blocks read; a create is not in the expected shape`);
    }
    for (const b of blocks) {
        if (typeof b.values.name !== 'string') fail(b.start, 'this block has no name');
        if (b.lat === undefined) fail(b.start, `the place '${b.values.name}' has no coordinates (fillCoordinatesFromString)`);
    }
    return blocks;
}

// The public shape of a place.
function toPlace(block, kind) {
    return {
        kind,
        name: block.values.name,
        nameJa: block.values.name_ja ?? null,
        businessStatus: block.values.business_status ?? null,
        googlePlaceId: block.values.google_place_id ?? null,
        googleMapsLink: block.values.google_maps_link ?? null,
        tabelogLink: block.values.tabelog_link ?? null,
        lat: block.lat,
        lon: block.lon,
        line: block.start + 1,
    };
}

// The full shape of a place, for the editor: every key in the seeder's order, the tags, the
// coordinates, and an id: the kind and the position of the block in its seeder. The line number
// moves when a line is inserted; the position only moves when a block above is removed (a new block
// goes after the last one), and the page reads everything again after that.
function toFullPlace(block, kind, index) {
    return {
        id: `${kind}/${index}`,
        kind,
        index,
        line: block.start + 1,
        name: block.values.name,
        lat: block.lat,
        lon: block.lon,
        tags: block.tags ?? null,
        fields: block.keyOrder.map((key) => ({ key, value: block.values[key] })),
    };
}

// The full places of a seeder given as text, of a file, of several kinds.
export function parseSeederFull(text, kind) {
    return parseBlocks(splitLines(text).lines, kind).map((b, i) => toFullPlace(b, kind, i));
}

export function readSeederFull(filePath, kind) {
    return parseSeederFull(readFileSync(filePath, 'utf8'), kind);
}

export function readAllSeedersFull(seedersDir = DEFAULT_SEEDERS_DIR, kinds = Object.keys(KINDS)) {
    return kinds.flatMap((kind) => readSeederFull(join(seedersDir, KINDS[kind]), kind));
}

// Reads the places of a seeder given as text.
export function parseSeeder(text, kind) {
    return parseBlocks(splitLines(text).lines, kind).map((b) => toPlace(b, kind));
}

// Reads the places of a seeder file.
export function readSeeder(filePath, kind) {
    return parseBlocks(splitLines(readFileSync(filePath, 'utf8')).lines, filePath).map((b) => toPlace(b, kind));
}

// Reads the places of several kinds (all four by default), in one list.
export function readAllSeeders(seedersDir = DEFAULT_SEEDERS_DIR, kinds = Object.keys(KINDS)) {
    return kinds.flatMap((kind) => readSeeder(join(seedersDir, KINDS[kind]), kind));
}

// Finds the blocks of a place: by name, and by coordinates when several places share the name
// (there are two "Joysound" karaokes, for instance). `target` is { name, lat?, lon? }.
// Returns one block, or several when the same place is in the seeder twice (same name, same
// coordinates); they are one place for Google, so they get the same edit.
function findBlocks(blocks, target, label) {
    const named = blocks.filter((b) => b.values.name === target.name);
    if (named.length === 0) throw new Error(`${label}: No place named '${target.name}'`);
    if (named.length === 1) return named;
    // Compare to 6 decimals, the precision the seeders use, to avoid floating point surprises.
    const same = (a, b) => a !== undefined && Math.abs(a - b) < 1e-6;
    const here = named.filter((b) => same(target.lat, b.lat) && same(target.lon, b.lon));
    if (here.length === 0) {
        throw new Error(`${label}: ${named.length} places are named '${target.name}' and none is at the given coordinates`);
    }
    return here;
}

// Checks the fields before they are written into PHP code. null on any of the three means "no line"
// (the editor's way to remove a Japanese name, an id, or a status: a place not checked yet).
function checkFields(fields) {
    // A Japanese name is free text, but on one line (the reader reads one key per line) and not empty.
    if (fields.nameJa !== undefined && fields.nameJa !== null && (typeof fields.nameJa !== 'string' || !fields.nameJa.trim() || /[\p{Cc}]/u.test(fields.nameJa))) {
        throw new Error(`Unexpected Japanese name: ${JSON.stringify(fields.nameJa)}`);
    }
    if (fields.businessStatus !== undefined && fields.businessStatus !== null && !BUSINESS_STATUSES.includes(fields.businessStatus)) {
        throw new Error(`Unknown business status: ${fields.businessStatus}`);
    }
    // Google place ids are letters, digits, '-' and '_'; anything else would be suspicious in code.
    if (fields.googlePlaceId !== undefined && fields.googlePlaceId !== null && !/^[\w-]+$/.test(fields.googlePlaceId)) {
        throw new Error(`Unexpected Google place id: ${fields.googlePlaceId}`);
    }
}

// Edits the lines of one place, in place in the `lines` array.
function editLines(lines, kind, target, fields) {
    checkFields(fields);
    const count = findBlocks(parseBlocks(lines, kind), target, kind).length;
    // Read the file again before each block: an inserted line moves the ones below it.
    for (let i = 0; i < count; i++) editBlock(lines, kind, () => findBlocks(parseBlocks(lines, kind), target, kind)[i], fields);
}

// Edits one block; `find` returns the block as it is in `lines` right now.
function editBlock(lines, kind, find, fields) {
    const indent = find().indent;

    // name_ja: always right after the 'name' line (the order is name, name_ja, business_status, google_place_id).
    // null removes the line: absent is the "no Japanese name" of the seeders.
    if (fields.nameJa !== undefined) {
        const block = find();
        const existing = block.keyLines.name_ja;
        if (fields.nameJa === null) {
            if (existing !== undefined) lines.splice(existing, 1);
        } else {
            const wanted = `${indent}'name_ja' => ${phpString(fields.nameJa)},`;
            if (existing !== undefined) lines[existing] = wanted;
            else lines.splice(block.keyEnds.name + 1, 0, wanted);
        }
    }

    // business_status: OPERATIONAL is written like the other statuses (a confirmed open place);
    // null removes the line (no line: the place was not checked, the column defaults to OPERATIONAL).
    if (fields.businessStatus !== undefined) {
        const block = find(); // the name_ja line may have moved the others
        const existing = block.keyLines.business_status;
        const status = fields.businessStatus;
        const wanted = status === null ? null : `${indent}'business_status' => ${phpString(status)},`;
        if (status === null) {
            if (existing !== undefined) lines.splice(existing, 1);
        } else if (existing !== undefined) {
            lines[existing] = wanted;
        } else {
            lines.splice((block.keyEnds.name_ja ?? block.keyEnds.name) + 1, 0, wanted); // after the name lines
        }
    }

    // google_place_id: after the status line if there is one, after the name lines otherwise. null removes it.
    if (fields.googlePlaceId !== undefined) {
        const updated = find(); // the status line may have moved the others
        const existing = updated.keyLines.google_place_id;
        if (fields.googlePlaceId === null) {
            if (existing !== undefined) lines.splice(existing, 1);
            return;
        }
        const wanted = `${indent}'google_place_id' => ${phpString(fields.googlePlaceId)},`;
        if (existing !== undefined) {
            lines[existing] = wanted;
        } else {
            lines.splice((updated.keyEnds.business_status ?? updated.keyEnds.name_ja ?? updated.keyEnds.name) + 1, 0, wanted);
        }
    }
}

// Returns the text of a seeder with one place edited. `fields` is { nameJa?, businessStatus?, googlePlaceId? };
// a field left undefined is not touched. Only the lines of those keys change.
export function editSeederText(text, kind, target, fields) {
    const { lines, eol } = splitLines(text);
    editLines(lines, kind, target, fields);
    return lines.join(eol);
}

// ---- The editor of any key, tags and coordinates, by block position (edit.js). ----

// The keys the three-field editor above knows, with their camelCase names.
const KNOWN_KEYS = { name_ja: 'nameJa', business_status: 'businessStatus', google_place_id: 'googlePlaceId' };

// A JavaScript value as PHP writes it in a create([...]) block.
function phpValue(value) {
    if (typeof value === 'string') return phpString(value);
    if (typeof value === 'number') return String(value);
    if (typeof value === 'boolean') return value ? 'true' : 'false';
    return 'null';
}

// Two numbers as the seeders write them: '35.658545, 139.698468'.
export const formatCoordinates = (lat, lon) => `${lat}, ${lon}`;

// Checks an edit of the editor ({ fields?, tags?, lat?, lon? }) before any line is touched.
export function checkPlaceEdit(edit) {
    const { fields, tags, lat, lon } = edit ?? {};
    if (fields === undefined && tags === undefined && lat === undefined && lon === undefined) throw new Error('nothing to edit');
    if (fields !== undefined && (typeof fields !== 'object' || fields === null || Array.isArray(fields))) throw new Error('fields must be an object');
    for (const [key, value] of Object.entries(fields ?? {})) {
        // A word, and not a name every object already has (constructor, toString...), which `in` would find.
        if (!/^\w+$/.test(key) || key in {}) throw new Error(`unexpected key '${key}'`);
        if (!['string', 'number', 'boolean'].includes(typeof value) && value !== null) throw new Error(`unexpected value for '${key}'`);
        if (typeof value === 'number' && !Number.isFinite(value)) throw new Error(`'${key}' must be a finite number`);
        // Newlines are fine (a description spans lines); other control characters are not.
        if (typeof value === 'string' && /[^\P{Cc}\n]/u.test(value)) throw new Error(`'${key}' contains a control character`);
        if (key === 'name' && (typeof value !== 'string' || !value.trim())) throw new Error('the name must be a non-empty string');
        if (key === 'name_ja' && value !== null && (typeof value !== 'string' || !value.trim() || value.includes('\n'))) throw new Error('name_ja must be a non-empty one-line string, or null');
        if (key === 'business_status' && value !== null && !BUSINESS_STATUSES.includes(value)) throw new Error(`unknown business status '${value}'`);
        if (key === 'google_place_id' && value !== null && !/^[\w-]+$/.test(value)) throw new Error('a Google place id is letters, digits, - and _');
    }
    if (tags !== undefined && (!Array.isArray(tags) || tags.some((t) => typeof t !== 'string' || /[,\n'\\]/.test(t)))) {
        throw new Error('tags must be a list of strings without commas or quotes');
    }
    if ((lat === undefined) !== (lon === undefined)) throw new Error('latitude and longitude go together');
    if (lat !== undefined && (typeof lat !== 'number' || !(lat >= -90 && lat <= 90))) throw new Error('latitude must be a number between -90 and 90');
    if (lon !== undefined && (typeof lon !== 'number' || !(lon >= -180 && lon <= 180))) throw new Error('longitude must be a number between -180 and 180');
}

// Edits the block at `index` (0-based position among the blocks of the seeder), in place in `lines`.
function editLinesAt(lines, kind, index, edit) {
    checkPlaceEdit(edit);
    const find = () => {
        const block = parseBlocks(lines, kind)[index];
        if (!block) throw new Error(`${kind}: no block ${index}`);
        return block;
    };
    const { fields = {}, tags, lat, lon } = edit;

    // The three keys with a fixed place go through the editor above; the others are general.
    const known = {};
    for (const [key, camel] of Object.entries(KNOWN_KEYS)) if (key in fields) known[camel] = fields[key];
    if (Object.keys(known).length) editBlock(lines, kind, find, known);

    for (const [key, value] of Object.entries(fields)) {
        if (key in KNOWN_KEYS) continue;
        const block = find(); // every insert moves the lines below it: read again
        const wanted = `${block.indent}'${key}' => ${phpValue(value)},`.split('\n'); // a multi-line string makes several lines
        if (Object.hasOwn(block.keyLines, key)) {
            lines.splice(block.keyLines[key], block.keyEnds[key] - block.keyLines[key] + 1, ...wanted);
        } else {
            // Before the closing `]);`. The reader accepts a last key without a comma, PHP would not:
            // add it on the last key's line (a comment may sit between that line and the closing).
            const lastKey = block.keyEnds[block.keyOrder.at(-1)];
            if (!lines[lastKey].endsWith(',')) lines[lastKey] += ',';
            lines.splice(block.end, 0, ...wanted);
        }
    }

    if (tags !== undefined) {
        const block = find();
        if (block.tagsCalls > (block.tagsLine === undefined ? 0 : 1)) {
            throw new Error(`the tags of '${block.values.name}' are written in several attachTags calls, or over several lines: edit them in the seeder`);
        }
        const clean = tags.map((t) => t.trim()).filter(Boolean);
        const wanted = `${block.lineIndent}$${block.variable}->attachTags([${clean.map(phpString).join(', ')}]);`;
        if (block.tagsLine !== undefined) lines[block.tagsLine] = wanted;
        else lines.splice(block.end + 1, 0, wanted); // right after the block, before the coordinates
    }

    if (lat !== undefined) {
        const block = find();
        lines[block.coordsLine] = `${block.lineIndent}$${block.variable}->fillCoordinatesFromString(${phpString(formatCoordinates(lat, lon))});`;
    }
}

// Returns the text of a seeder with the block at `index` edited. Only the lines of the edit change.
export function editPlaceText(text, kind, index, edit) {
    const { lines, eol } = splitLines(text);
    editLinesAt(lines, kind, index, edit);
    return lines.join(eol);
}

// The full place with an edit applied in memory, as the file should read back after the edit.
function expectedAfter(place, edit) {
    const values = Object.fromEntries(place.fields.map((f) => [f.key, f.value]));
    for (const [key, value] of Object.entries(edit.fields ?? {})) {
        if (key in KNOWN_KEYS && value === null) delete values[key];
        else values[key] = value;
    }
    const tags = edit.tags === undefined ? place.tags : edit.tags.map((t) => t.trim()).filter(Boolean);
    return { name: values.name, lat: edit.lat ?? place.lat, lon: edit.lon ?? place.lon, tags, values };
}

// What a full place looks like for the comparison: no line numbers, keys as a map.
const comparable = (place) => ({ name: place.name, lat: place.lat, lon: place.lon, tags: place.tags, values: Object.fromEntries(place.fields.map((f) => [f.key, f.value])) });

// Prepares one edit of the editor, in memory: edits the text, then reads it back and checks that the
// edited block has the expected values and that every other block is untouched. Nothing is written.
// `before` is the text to edit (read from the file when not given): the server passes the text whose
// revision it checked, so the index cannot point at a block of a newer text.
// Returns { filePath, before, after, changed }: pass it to writePlanSafely (lib/cli.js).
export function planPlaceEdit(filePath, kind, index, edit, before = readFileSync(filePath, 'utf8')) {
    const placesBefore = parseSeederFull(before, kind);
    if (!placesBefore[index]) throw new Error(`${filePath}: no block ${index}`);
    const after = editPlaceText(before, kind, index, edit);
    if (after === before) return { filePath, before, after, changed: false };

    const placesAfter = parseSeederFull(after, kind);
    if (placesAfter.length !== placesBefore.length) throw new Error(`${filePath}: ${placesBefore.length} places before the edit, ${placesAfter.length} after`);
    placesAfter.forEach((p, i) => {
        const expected = i === index ? expectedAfter(placesBefore[i], edit) : comparable(placesBefore[i]);
        try {
            assert.deepStrictEqual(comparable(p), expected);
        } catch {
            throw new Error(`${filePath}: after the edit, '${p.name}' (block ${i}) would not read back as expected`);
        }
    });
    return { filePath, before, after, changed: true };
}

// ---- A new place, a place removed (edit.js). ----

// Where a place stands in the PHP of its seeder, for a change of structure: the braces are read by
// phpStructure, since the indentation proves nothing (PHP runs an `if` whatever its body looks like).
// Returns { last, first }: the last line of the place (its create([...]) block, then the calls on its
// variable that follow it without a gap, attachTags and fillCoordinatesFromString, each a whole
// statement on one line) and the first token of the place.
// Throws, with `advice` as what to do instead, when the editor cannot answer for the change:
// - the place is not a statement right in the body of a method: inside an `if`, a loop, a closure or
//   a braceless `if (...)`, that code decides whether the place is seeded, a block added after it would
//   depend on it too, and the lines of the place may go on after the closing brace;
// - the variable of the place is used again before the next place or the end of the method, past
//   the calls right under the block (after a blank line or a comment, inside an `if`, over several
//   lines, passed to a function): such a line acts on the place the variable holds when it runs, so
//   a new block written before it, or a removal that leaves it behind, would hand it to another place
//   without the reader of this file seeing a difference.
function placeBounds(lines, kind, blocks, position, advice) {
    const block = blocks[position];
    const name = block.values.name;
    const structure = phpStructure(lines.join('\n'));
    if (structure.unsupported) {
        throw new Error(`${kind}: the seeder has ${structure.unsupported.why} (line ${structure.unsupported.line + 1}), which the editor does not follow: ${advice}`);
    }
    const { tokens } = structure;
    const unreadable = (line) => new Error(`'${name}' has lines the editor cannot follow (line ${line + 1}): ${advice}`);
    const nested = (line) => new Error(`'${name}' is written inside other code (line ${line + 1}), which decides whether it is seeded: ${advice}`);

    const first = structure.first(block.start);
    const shape = first && tokens.slice(first.index, first.index + 5).map((t) => t.text).join(' ');
    if (first?.type !== 'var' || shape !== `${block.variable} = ${block.model} :: create`) throw unreadable(block.start);

    // Right in the body of a method: only a class (and a namespace) around the method, and the
    // statement starts where one can start, not after `if (...)`, `else` or inside an expression.
    const around = first.stack.slice(0, -1);
    const method = first.stack.at(-1);
    if (method?.kind !== 'method' || around.some((entry) => entry.kind !== 'class' && entry.kind !== 'namespace')) {
        const inner = first.stack.findLast((entry) => entry.kind === 'other' || entry.kind === 'closure');
        throw nested(inner ? tokens[inner.open].line : block.start);
    }
    // A statement of the method's body starts after a `;`, a `{` or a `}`, outside any parenthesis.
    // After `if (...)`, `else` or `do`, without braces, it is the body of that code.
    const startsStatement = (token) => ['{', '}', ';'].includes(tokens[token.index - 1].text) && token.depth === tokens[method.open].depth;
    if (!startsStatement(first)) throw nested(tokens[first.index - 1].line);

    // The block itself, then each call under it: whole statements, with no brace in them.
    const statementEnd = (line) => {
        const end = structure.last(line);
        return end?.text === ';' && end.depth === first.depth && end.stack === first.stack ? end : null;
    };
    let lastToken = statementEnd(block.end);
    if (!lastToken) throw unreadable(block.end);
    let last = block.end;
    for (;;) {
        const start = structure.first(last + 1);
        const end = statementEnd(last + 1);
        const call = start?.type === 'var' && start.text === block.variable && tokens[start.index + 1]?.text === '->' && tokens[start.index - 1] === lastToken;
        if (!call || !end || tokens.slice(start.index, end.index).some((t) => !t.inString && t.text === ';')) break;
        last += 1;
        lastToken = end;
    }

    // The variable used again before it is given another place, or the end of the method. Only a place
    // of the same variable that is itself a statement of the method's body ends the search: a place
    // of another variable leaves this one as it was, and one inside an `if`, with braces or not,
    // gives it a new value only sometimes (its own `$variable =` is then a use, and refuses the change).
    const next = blocks.slice(position + 1).map((b) => b.variable === block.variable && structure.first(b.start)).find((t) => t && t.stack === first.stack && startsStatement(t));
    const limit = next ? next.index : (method.close ?? tokens.length);
    const stray = tokens.find((t) => t.index > lastToken.index && t.index < limit && t.type === 'var' && t.text === block.variable);
    if (stray) throw new Error(`'${name}' has lines the editor cannot follow after its block (line ${stray.line + 1}): ${advice}`);

    return { last, first, method, tokens };
}

// What makes a method stop before its end. A `return` or a `goto` inside a closure only leaves the
// closure; an `exit` or a `throw` there still stops the seeding when the closure runs.
const EXITS = new Set(['return', 'exit', 'die', 'throw', 'goto']);
const LOCAL_EXITS = new Set(['return', 'goto']);
const stopsMethod = (token, method) => {
    if (token.type !== 'word' || token.method !== method) return false;
    const word = token.text.toLowerCase();
    if (!EXITS.has(word)) return false;
    const inClosure = token.stack.slice(token.stack.indexOf(method) + 1).some((entry) => entry.kind === 'closure');
    return !(inClosure && LOCAL_EXITS.has(word));
};

const sameSpot = (a, b) => Math.abs(a.lat - b.lat) < 1e-6 && Math.abs(a.lon - b.lon) < 1e-6;

// Checks a new place ({ fields, tags?, lat, lon }) before any line is written: the rules of an edit,
// and what a block cannot do without (a name, coordinates).
export function checkNewPlace(place) {
    const { fields, tags, lat, lon } = place ?? {};
    if (typeof fields !== 'object' || fields === null || Array.isArray(fields)) throw new Error('fields must be an object');
    checkPlaceEdit({ fields, tags, lat, lon });
    if (typeof fields.name !== 'string') throw new Error('the name must be a non-empty string');
    if (lat === undefined) throw new Error('a new place needs its coordinates');
}

// Adds a place after the last one of the seeder, written like it (variable, model, indentation):
// name first, then name_ja, business_status and google_place_id when they have a value (null: no
// line, as for an edit), then the other keys in the order given.
function addLines(lines, kind, place) {
    checkNewPlace(place);
    const blocks = parseBlocks(lines, kind);
    const model = blocks.at(-1);
    if (!model) throw new Error(`${kind}: the seeder has no place to write the new one like: add the first one by hand`);
    const { fields, tags = [], lat, lon } = place;
    if (blocks.some((b) => b.values.name === fields.name && sameSpot(b, { lat, lon }))) {
        throw new Error(`'${fields.name}' is already in ${kind} at these coordinates`);
    }
    const first = ['name', ...Object.keys(KNOWN_KEYS)];
    const keys = [...first.filter((k) => Object.hasOwn(fields, k) && fields[k] !== null), ...Object.keys(fields).filter((k) => !first.includes(k))];
    const clean = tags.map((t) => t.trim()).filter(Boolean);
    const own = model.lineIndent;
    const written = [
        '',
        `${own}$${model.variable} = ${model.model}::create([`,
        ...keys.flatMap((key) => `${model.indent}'${key}' => ${phpValue(fields[key])},`.split('\n')),
        `${own}]);`,
        ...(clean.length ? [`${own}$${model.variable}->attachTags([${clean.map(phpString).join(', ')}]);`] : []),
        `${own}$${model.variable}->fillCoordinatesFromString(${phpString(formatCoordinates(lat, lon))});`,
    ];
    const { last, first: start, method, tokens } = placeBounds(lines, kind, blocks, blocks.length - 1, `it is the last place of ${kind}, add the new one in the seeder`);
    // A `return` above, even inside an `if`, and the new place is seeded only when the method gets there.
    const exit = tokens.find((t) => t.index < start.index && stopsMethod(t, method));
    if (exit) throw new Error(`${kind}: the method of the places can stop before its end (${exit.text}, line ${exit.line + 1}), which decides whether a new place is seeded: add it in the seeder`);
    lines.splice(last + 1, 0, ...written);
}

// Removes the place at `index`: its block and the calls that follow it. The comments above it stay
// (they are notes, and nothing says which block they are about).
function removeLines(lines, kind, index) {
    const blocks = parseBlocks(lines, kind);
    const block = blocks[index];
    if (!block) throw new Error(`${kind}: no block ${index}`);
    const { last } = placeBounds(lines, kind, blocks, index, 'remove it in the seeder');
    lines.splice(block.start, last - block.start + 1);
    // One blank line between the neighbours, not two.
    if (lines[block.start]?.trim() === '' && lines[block.start - 1]?.trim() === '') lines.splice(block.start, 1);
}

// Throws unless `after` reads back as `expected` (a list of comparable places).
function assertReadsBack(filePath, kind, after, expected) {
    const places = parseSeederFull(after, kind);
    if (places.length !== expected.length) throw new Error(`${filePath}: ${expected.length} places expected after the change, ${places.length} read`);
    places.forEach((p, i) => {
        try {
            assert.deepStrictEqual(comparable(p), expected[i]);
        } catch {
            throw new Error(`${filePath}: after the change, '${p.name}' (block ${i}) would not read back as expected`);
        }
    });
}

// Prepares a new place, in memory: writes its block after the last one, reads the text back and checks
// that every place is as it was and that the new one has the values given. Nothing is written.
// Returns { filePath, before, after, changed, index }: index is the position of the new place.
export function planPlaceAdd(filePath, kind, place, before = readFileSync(filePath, 'utf8')) {
    const { lines, eol } = splitLines(before);
    addLines(lines, kind, place);
    const after = lines.join(eol);
    const placesBefore = parseSeederFull(before, kind);
    const values = Object.fromEntries(Object.entries(place.fields).filter(([key, value]) => !(key in KNOWN_KEYS && value === null)));
    const tags = (place.tags ?? []).map((t) => t.trim()).filter(Boolean);
    const added = { name: values.name, lat: place.lat, lon: place.lon, tags: tags.length ? tags : null, values };
    assertReadsBack(filePath, kind, after, [...placesBefore.map(comparable), added]);
    return { filePath, before, after, changed: true, index: placesBefore.length };
}

// Prepares the removal of the place at `index`, in memory: removes its lines, reads the text back and
// checks that the other places are all there, as they were, in the same order. Nothing is written.
export function planPlaceRemoval(filePath, kind, index, before = readFileSync(filePath, 'utf8')) {
    const { lines, eol } = splitLines(before);
    removeLines(lines, kind, index);
    const after = lines.join(eol);
    assertReadsBack(filePath, kind, after, parseSeederFull(before, kind).filter((_, i) => i !== index).map(comparable));
    return { filePath, before, after, changed: true };
}

// Prepares a list of edits ({ target, fields }) to a seeder file, in memory: edits the text, then
// reads the new text back with the reader to check that every place is still there with the
// expected values. Nothing is written; throws if anything is off.
// Returns { filePath, after, changed }: pass it to writeSeederEdits to write the file.
export function planSeederEdits(filePath, kind, edits) {
    const before = readFileSync(filePath, 'utf8');
    const { lines, eol } = splitLines(before);
    const placesBefore = parseBlocks(lines, filePath).map((b) => toPlace(b, kind));
    for (const { target, fields } of edits) editLines(lines, kind, target, fields);
    const after = lines.join(eol);
    if (after === before) return { filePath, before, after, changed: false };

    // Verify: same places, in the same order, with the new values where edited and the old ones elsewhere.
    const blocksAfter = parseBlocks(splitLines(after).lines, filePath);
    const placesAfter = blocksAfter.map((b) => toPlace(b, kind));
    if (placesAfter.length !== placesBefore.length) {
        throw new Error(`${filePath}: ${placesBefore.length} places before the edit, ${placesAfter.length} after`);
    }
    const expected = placesBefore.map((p) => ({ ...p }));
    for (const { target, fields } of edits) {
        for (const block of findBlocks(blocksAfter, target, filePath)) {
            const index = blocksAfter.indexOf(block);
            if (fields.businessStatus !== undefined) {
                expected[index].businessStatus = fields.businessStatus;
            }
            if (fields.googlePlaceId !== undefined) expected[index].googlePlaceId = fields.googlePlaceId;
            if (fields.nameJa !== undefined) expected[index].nameJa = fields.nameJa;
        }
    }
    placesAfter.forEach((p, i) => {
        const e = expected[i];
        // `line` moves with the inserted lines; everything else must match.
        if (p.name !== e.name || p.lat !== e.lat || p.lon !== e.lon || p.businessStatus !== e.businessStatus || p.googlePlaceId !== e.googlePlaceId || p.nameJa !== e.nameJa) {
            throw new Error(`${filePath}: after the edit, '${p.name}' (line ${p.line}) would not read back as expected`);
        }
    });
    return { filePath, before, after, changed: true };
}

// Writes a file prepared by planSeederEdits (if it changed).
export function writeSeederEdits(plan) {
    if (plan.changed) writeFileSync(plan.filePath, plan.after);
}

// Both at once, for a single file: prepares and verifies, then writes. Returns true when the file changed.
export function applySeederEdits(filePath, kind, edits) {
    const plan = planSeederEdits(filePath, kind, edits);
    writeSeederEdits(plan);
    return plan.changed;
}
