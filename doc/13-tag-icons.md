# Tag icons: a small picture next to each tag

Implemented in November 2026 (branch `tag-icons`). Each tag of the tag cloud and of the place details shows a small, cute picture before its label (ramen, pasta, beer...), so that the cloud can be scanned at a glance, and so that the Japanese page (`12-multilingual-ui.md`) shows the same picture with only the word changing. 118 tag labels have one; the other tags show their text only.

## 1. The icon decorates the label, it does not replace it

About a third of the tags are things one can draw; the rest are qualities and services (customisable dishes, small servings, menu with calories). And a bowl does not tell ramen from noodles from soba at 20 px. So the label always stays, on desktop and on the phone (a phone has no hover to explain a symbol), and the icon carries `aria-hidden="true"`: a screen reader announces each tag once, by its label.

A tag without a picture is not a fault: `western`, the karaoke chains and most qualities have none, on purpose. Three tags share the ramen bowl (`ramen`, `tonkotsu`, `miso`: the last two are tastes of ramen).

## 2. Where the pictures come from

One sprite, three sources. All are vector, so they are sharp at any size and pixel density.

| Source | Icons | Folder in `tools/tag-icons/` | What it is for |
|---|---|---|---|
| **Fluent Emoji, flat style** (Microsoft, GitHub `microsoft/fluentui-emoji`, MIT) | 67 | `fluent/` | everything the set has: pizza, sushi, beer, shopping bags, guitar... |
| **Generated, then traced** | 19 | `traced/` (from `generated/`) | the dishes the set lacks: omurice, soba, gapao, okonomiyaki, gyoza... |
| **Written by hand as SVG** | 23 | `svg/` | the flags of the cuisines, the Africa silhouette, three scenes (`yakiniku`, `shabushabu`, `terrace`), six things no emoji shows (a record, a shisha pipe, a standing table, a pub sign, an English menu, a crossed-out yen coin for no table charge) and two store tags whose emoji said the wrong thing: `music` (a CD with a note, not a radio: the tag is for shops selling CDs) and `drinks` (a bottle of wine and a bottle of whisky, not a soft drink: the tag is for liquor shops) |

Only the flat style of Fluent Emoji is used: the 3D and Color styles are gradients, which are heavier and need `id`s, and a sprite cannot hold ids safely (they would be shared by every symbol). `build.mjs` refuses a source with an `id`.

### Generated dishes

An image model (GPT Image, through Codex or ChatGPT) draws one dish per image, with `tools/tag-icons/reference-sheet.png` (six Fluent icons) attached as the style to match. The hard part is not one cute bowl but the same style over all of them: image models drift over a series, so each request repeats the whole style and the reference. The prompt that gave the 19 dishes:

```
A single emoji-style icon in the exact style of the attached reference sheet
(Microsoft Fluent Emoji, flat style): flat solid colours, no outlines, no gradients,
no texture, simple rounded shapes, a few flat highlight and shadow shapes, bright and
slightly saturated palette, a cute and friendly look.
One object only, centred, filling about 85% of a square canvas, seen from a slight
three-quarter top view like the bowls of the reference sheet.
Fully transparent background. No text, no letters, no border, no drop shadow on the
ground, no table, no extra props beyond the ones named.
It must stay recognisable when shrunk to 20 pixels: few, large shapes, strong contrast
between the food and its dish, nothing thin.
Subject: <the dish, with its dish, colours and the two or three things that identify it>
```

The image is a PNG of about 1250 px. It is saved as `generated/<id>.png` and traced into `traced/<id>.svg` by `trace.mjs` (ImageMagick and Inkscape's colour trace, 10 colours on a 256-unit grid). The trace was compared with a 64 px PNG embedded in the sprite, the first pipeline tried: the PNG was slightly soft on a 3x phone and did not compress, the trace is sharp and travels at about a third of the weight. With 8 colours the small parts merge (the taco rice lost its rice); with 12 the files grow by a quarter for no visible gain.

`generated/` is not in git (17 MB): it is the source of the traces, kept on the machine that made them. A dish can be traced again only from there, or generated again.

### Hand-written SVG

The flags are a few rectangles: an image model gets star counts and proportions wrong, and the code is exact and tiny (5 KB for the twelve). They share one frame (a 28 × 20 rectangle with round corners in a 32 × 32 box), a softened palette close to Fluent's, and a thin grey outline when white touches the edge, so the flag does not vanish on a light chip. The details that disappear at 20 px are left out (no coat of arms on Spain, seven stripes and six dots on the United States).

Three scenes are drawn by hand because the emoji said the wrong thing: `yakiniku` was a meat on a bone and is now a mesh grill with slices of beef and pork, `shabushabu` was a stew pot and is now a pot of broth with chopsticks holding a slice of meat, `open air space` was a beach umbrella and is now a table with two chairs under a parasol. They are plainer than the generated dishes; generating them is the way to make them match.

## 3. How it is built

| File | Role |
|---|---|
| `tools/tag-icons/icons.json` | symbol id => source file (an SVG of `fluent/`, `traced/` or `svg/`) |
| `tools/tag-icons/build.mjs` | builds the sprite: `node tools/tag-icons/build.mjs` (needs ImageMagick) |
| `tools/tag-icons/trace.mjs` | traces `generated/<id>.png` into `traced/<id>.svg` (needs ImageMagick and Inkscape 1.4) |
| `source/public/icons/tags.svg` | the sprite: one `<symbol>` per icon. Built, committed, never edited by hand |
| `source/public/icons/LICENSE-fluent-emoji.txt` | the MIT licence, copied by the build |
| `source/config/tag_icons.php` | English tag label => symbol id |
| `App\Support\Tags::icon()`, `::sprite()` | the symbol of a label; the URL of the sprite with its `?v=` |
| `resources/views/components/tag.blade.php` | the tag chip, the only place that prints an icon |

The mapping is keyed by the **English tag label**, which is the stable search value of both languages: no database change, and one table serves the English and the Japanese page.

The chip prints `<svg class="tagIcon" aria-hidden="true" focusable="false"><use href="/icons/tags.svg?v=<mtime>#ramen"/></svg>` before the label. `.tagIcon` is sized in `em` (1.6), so it follows the smaller tags of the details, with negative margins so that it does not make its chip taller.

The build does two things to each source besides copying it: it rounds the coordinates to two decimals (a third of the file, invisible), and it **centres the drawing in its box**. Many emoji sit at the bottom of their box, as on a ground (the credit card fills its lower half) and looked too low next to a label; the build paints each source, reads the box of what is not transparent, and moves the `viewBox` of the symbol accordingly.

### Adding an icon

1. Put the source in place: a Fluent file in `fluent/`, or a generated PNG in `generated/` then `node trace.mjs <id>`, or an SVG written by hand in `svg/` (a `viewBox`, flat fills, no `id`).
2. Add `"<symbol id>": "<folder>/<file>.svg"` to `icons.json` and `'<tag label>' => '<symbol id>'` to `config/tag_icons.php`.
3. `node tools/tag-icons/build.mjs`, then `php artisan config:clear` in the dev container.
4. Look at the chip on the page, in the dark and the light theme.

`tests/Architecture/TagIconsTest.php` checks that every icon of the config is a symbol of the sprite, that every label of the config is a tag of a seeder, and that the licence is published.

## 4. The licence

Fluent Emoji is under the MIT licence. It puts one obligation on the app: keep Microsoft's copyright line and the licence text with the copied files, which is `LICENSE-fluent-emoji.txt` next to the sprite. Nothing else: the icons may be resized and edited, no credit is owed in the UI, no share-alike. The generated images are governed by the terms of the service that made them (OpenAI's give the user the output); check them again before the site sells anything.

## 5. Performance

One sprite and no script: one request for all the icons, instead of one per tag.

| | Raw | Gzipped |
|---|---|---|
| The sprite, 109 icons | 345 KB | 94 KB |
| of which the 19 traced dishes | about 200 KB | about 45 KB |

- **Compression matters**: the Nginx configuration of `06-deploy-production.md` compresses `image/svg+xml`. The dev server does not, and sends the 345 KB.
- **Cached and versioned**: the URL carries `?v=<modification time>`, so a new sprite is a new URL. The service worker pre-caches that exact URL (the page gives it the version in `data-icons-version`, see `04-how-it-works.md` section 10), so the icons are there on an offline visit.
- **Not done**: one sprite per kind of place. All four pages load the 109 icons, and the karaokes page uses two of them. A split would cut that download, at the price of four files: not worth it for an app and a set of this size.

## 6. Open points

- The browser tests (`e2e/tests/icons.spec.js`) check the icons in Chromium and in WebKit, the engine of Safari. Firefox is a run by hand (`npm run test:firefox`): every icon was drawn there in November 2026.
- Tags still without a picture that could have one: `menu with calories`, `menu with allergies`.
- The dark plate of `hamburg` and the pot of `sukiyaki` merge into a dark chip; the food stays visible.
