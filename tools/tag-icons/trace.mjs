// node trace.mjs [id...]
//
// Traces the generated icons (generated/<id>.png, full size, transparent background) into small
// flat SVGs (traced/<id>.svg), which build.mjs puts in the sprite: sharp at any size, and lighter
// over the wire than an embedded PNG. Without arguments, traces every PNG of generated/.
// Needs ImageMagick (magick) and Inkscape 1.4 or later (inkscape) on the PATH.
//
// Inkscape's colour trace gives one path per colour, stacked from the brightest (at the bottom,
// covering the whole picture) to the darkest. So the transparent background is made the brightest
// colour: the drawing is darkened to 85% and laid on white, the white bottom layer is dropped
// after the trace, and the colours of the other layers are brought back to what they were.

import { execFileSync } from 'node:child_process';
import { mkdtempSync, mkdirSync, readFileSync, readdirSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { basename, dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));

const SIZE = 256; // the grid of the traced drawing: whole units of it are fine enough for a chip
const MARGIN = 4;
const COLOURS = 10; // fewer and the small parts of a dish (rice, peas) merge into their neighbours
const DARKEN = 0.85;

// The traced SVG of Inkscape, reduced to its paths: whole coordinates, the key layer dropped.
export function simplify(id, svg) {
    const layers = [...svg.matchAll(/<path[^>]*?style="fill:(#[0-9a-f]{6})[^"]*"[^>]*?d="([^"]+)"/gs)]
        .map(([, fill, d]) => ({ rgb: [1, 3, 5].map((i) => parseInt(fill.slice(i, i + 2), 16)), d }));
    if (layers.length < 2) throw new Error(`${id}: the trace has no layers`);
    if (Math.min(...layers[0].rgb) < 250) throw new Error(`${id}: the bottom layer is not the background, a white of the drawing was merged with it`);
    const paths = layers.slice(1).map(({ rgb, d }) => {
        const fill = rgb.map((v) => Math.min(255, Math.round(v / DARKEN)).toString(16).padStart(2, '0')).join('');
        const whole = d.replace(/-?\d*\.\d+/g, (n) => String(Math.round(Number(n)))).replace(/\s+/g, ' ');
        return `<path fill="#${fill}" d="${whole}"/>`;
    });
    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${SIZE} ${SIZE}">${paths.join('')}</svg>\n`;
}

function trace(id, work) {
    const flat = join(work, `${id}.png`);
    const raw = join(work, `${id}.svg`);
    const inner = SIZE - 2 * MARGIN;
    execFileSync('magick', [
        join(here, 'generated', `${id}.png`), '-trim', '+repage', '-resize', `${inner}x${inner}`,
        '-background', 'none', '-gravity', 'center', '-extent', `${SIZE}x${SIZE}`,
        '-channel', 'A', '-threshold', '50%', '+channel',
        '-channel', 'RGB', '+level', `0,${DARKEN * 100}%`, '+channel',
        '-background', 'white', '-alpha', 'remove', '-alpha', 'off', flat,
    ]);
    // object-trace: scans, smooth, stack, remove background, speckles, smooth corners, optimize.
    const actions = `select-all;object-trace:${COLOURS + 1},false,true,false,8,1.2,1.0;select-clear;select-by-element:image;delete;export-filename:${raw};export-plain-svg;export-do`;
    execFileSync('inkscape', [flat, `--actions=${actions}`], { stdio: 'ignore' });
    return simplify(id, readFileSync(raw, 'utf8'));
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    const asked = process.argv.slice(2);
    const ids = asked.length ? asked : readdirSync(join(here, 'generated')).filter((f) => f.endsWith('.png')).map((f) => basename(f, '.png')).sort();
    const work = mkdtempSync(join(tmpdir(), 'tag-icons-'));
    mkdirSync(join(here, 'traced'), { recursive: true });
    try {
        for (const id of ids) {
            const svg = trace(id, work);
            writeFileSync(join(here, 'traced', `${id}.svg`), svg);
            console.log(`${id}: ${(svg.length / 1024).toFixed(1)} KB`);
        }
    } finally {
        rmSync(work, { recursive: true, force: true });
    }
}
