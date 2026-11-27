// node build.mjs
//
// Builds the sprite of the tag icons, source/public/icons/tags.svg: one <symbol> per entry of
// icons.json (symbol id => source file, an SVG or a PNG of this folder). The sprite is committed:
// run this after changing icons.json or a source file. See doc/13-tag-icons.md.
//
// An SVG source becomes the content of its symbol (vector, a few KB). A PNG source (a generated
// icon) is embedded as an <image> in its symbol, at the resolution of the file.
//
// Many emoji sit at the bottom of their box, as on a ground (the credit card fills its lower half):
// next to a label they look too low. So the view of each SVG symbol is moved to put the drawing in
// its middle. The drawing is measured with ImageMagick (magick), which the build needs on the PATH.

import { execFileSync } from 'node:child_process';
import { readFileSync, writeFileSync, copyFileSync } from 'node:fs';
import { dirname, join, extname } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const ICONS_DIR = join(here, '..', '..', 'source', 'public', 'icons');

// The symbol of an SVG file: its viewBox and what is inside its <svg> element. `centre` is where
// the middle of the drawing is, as fractions of the box ([0.5, 0.5] when it is centred already).
export function svgSymbol(id, svg, centre = [0.5, 0.5]) {
    const open = svg.match(/<svg\b[^>]*>/);
    const box = open?.[0].match(/viewBox="([^"]+)"/)?.[1].trim().split(/[\s,]+/).map(Number);
    if (box?.length !== 4 || box.some(Number.isNaN)) throw new Error(`${id}: the SVG has no viewBox`);
    // The view follows the drawing, in steps of a hundredth of the box.
    const moved = (min, size, at) => Number((min + Math.round((at - 0.5) * 100) / 100 * size).toFixed(2));
    const viewBox = `${moved(box[0], box[2], centre[0])} ${moved(box[1], box[3], centre[1])} ${box[2]} ${box[3]}`;
    const inner = svg.slice(open.index + open[0].length, svg.lastIndexOf('</svg>')).trim();
    // An id inside a symbol would be shared by the whole sprite: the flat Fluent icons have none.
    if (/\sid="/.test(inner)) throw new Error(`${id}: the SVG has ids (gradients, clip paths), which a sprite cannot hold safely`);
    // Two decimals are a hundredth of a unit of a 32-unit drawing: invisible at 20 px, and a third of the file.
    const rounded = inner.replace(/\d*\.\d{3,}/g, (n) => String(Number(Number(n).toFixed(2))).replace(/^0\./, '.'));
    return `<symbol id="${id}" viewBox="${viewBox}">${rounded.replace(/\s*\n\s*/g, '')}</symbol>`;
}

// The symbol of a PNG file: the picture itself, in a square of its size (read from the PNG header).
export function pngSymbol(id, png) {
    const width = png.readUInt32BE(16);
    const height = png.readUInt32BE(20);
    return `<symbol id="${id}" viewBox="0 0 ${width} ${height}"><image width="${width}" height="${height}" href="data:image/png;base64,${png.toString('base64')}"/></symbol>`;
}

// Where the middle of the drawing of an SVG file is, as fractions of its box: the file is painted
// and the box of what is not transparent is read ("%@" gives it as WxH+X+Y).
function centreOf(path) {
    const SIDE = 320;
    const out = execFileSync('magick', ['-background', 'none', '-density', '288', path, '-resize', `${SIDE}x${SIDE}!`, '-format', '%@', 'info:'], { stdio: ['ignore', 'pipe', 'ignore'] }).toString();
    const [w, h, x, y] = out.match(/\d+/g).map(Number);
    return [(x + w / 2) / SIDE, (y + h / 2) / SIDE];
}

export function buildSprite(icons, read, centre = () => [0.5, 0.5]) {
    const symbols = Object.entries(icons).map(([id, file]) => {
        if (!/^[a-z0-9-]+$/.test(id)) throw new Error(`${id}: a symbol id is lower case letters, digits and dashes`);
        const ext = extname(file);
        if (ext === '.svg') return svgSymbol(id, read(file).toString('utf8'), centre(file));
        if (ext === '.png') return pngSymbol(id, read(file));
        throw new Error(`${id}: ${file} is neither an SVG nor a PNG`);
    });
    return `<svg xmlns="http://www.w3.org/2000/svg">\n${symbols.join('\n')}\n</svg>\n`;
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    const icons = JSON.parse(readFileSync(join(here, 'icons.json'), 'utf8'));
    const sprite = buildSprite(icons, (file) => readFileSync(join(here, file)), (file) => centreOf(join(here, file)));
    writeFileSync(join(ICONS_DIR, 'tags.svg'), sprite);
    // The MIT licence of Fluent Emoji asks for its text to stay with the copies.
    copyFileSync(join(here, 'fluent', 'LICENSE-fluent-emoji.txt'), join(ICONS_DIR, 'LICENSE-fluent-emoji.txt'));
    console.log(`${Object.keys(icons).length} icons, ${(sprite.length / 1024).toFixed(1)} KB: ${join(ICONS_DIR, 'tags.svg')}`);
}
