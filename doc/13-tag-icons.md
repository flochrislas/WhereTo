# Tag icons: a small picture next to each tag

Design note, November 2026. Not implemented. The idea: a small, cute icon for each tag of the tag cloud (ramen, pasta, beer...), so that the cloud can be scanned at a glance, and so that the Japanese page (`12-multilingual-ui.md`) shows the same picture with only the word changing. The question asked was which AI to use to draw them; the answer is "mostly none": an existing icon set covers most of the drawable tags, AI fills the gaps, and the text stays.

## 1. The tags

139 tags, from the four tag seeders (`source/database/seeders/*TagsTableSeeder.php`):

| Kind | Tags | Drawable | Abstract |
|---|---|---|---|
| restaurants | 78 | about half: dishes, cuisines, drinks | visa, authentic, filling, small servings, menu with calories, menu with allergies, English menu, customisable dishes, all you can eat, smoking allowed, no smoking, smoking area, open air space... |
| stores | 35 | some: bakery, clothes, books... | most are qualities or services |
| bars | 13 | beer, wine, cocktail, pub... | all you can drink, standing, mingling... |
| karaokes | 13 | microphone | JOYSOUND, DAM, private rooms, big rooms... |

About a third of the tags are things one can draw. The rest are qualities and services: no icon says "customisable dishes" on its own, and a bowl icon does not tell ramen from noodles from soba.

**So the icon cannot replace the text.** It decorates the label and makes the cloud scannable; the label stays visible on desktop and on the phone, for the meaning (ramen or soba is read, not guessed), for the Japanese page where the icon is the same and only the word is translated (a real gain: an icon needs no translation), and for screen readers: the icon carries `aria-hidden="true"`, so each button is announced once, by its label.

## 2. Where the pictures come from

Three sources, in the order to use them.

### A. An existing emoji set (the base, free)

The emoji sets already cover most of the drawable tags, in one consistent, cute style, drawn by designers and licensed for this use:

| Set | Licence | Style | Notes |
|---|---|---|---|
| **Fluent Emoji** (Microsoft, GitHub `microsoft/fluentui-emoji`) | MIT | flat, colour, 3D, high contrast | the cutest; SVG and PNG; no credit required |
| OpenMoji | CC BY-SA 4.0 | flat, outlined | credit line on the info page (next to OpenStreetMap's) |
| Twemoji | CC BY 4.0 | flat | credit line |
| Noto Emoji (Google) | OFL / Apache | flat | credit not required |

Ramen (🍜), spaghetti (🍝), curry (🍛), sushi (🍣), tempura (🍤), pizza, hamburger, taco, bread, coffee, beer, wine, cocktail, sake, cake, shopping bags, microphone (karaoke), no smoking, credit card: all exist. A mapping table (tag label to file name) and a script that copies the chosen files into `source/public/icons/` are an afternoon of work and need no generation at all.

Recommendation: **Fluent Emoji, flat style**: cute, consistent, MIT, and it has a dark-friendly palette. Pick one style and never mix two.

### B. A generated image, for the gaps only

Some dishes have no emoji: omurice, gapao, okonomiyaki, teishoku, hamburg steak, soba as distinct from ramen, monjayaki... Ten or so. For these, an image model.

**The hard part is not one cute bowl, it is the same style over all the icons.** Image models drift: the fifth icon has another line weight and palette. The way to hold the style is a reference: make a sheet of six icons from the chosen emoji set, then ask for each missing icon "in the style of this sheet", one at a time, with a transparent background, and reject the drifters.

The tools at hand:

| Tool | Generates images | Reference image | Transparent background | Cost |
|---|---|---|---|---|
| ChatGPT (the Codex plan is a ChatGPT subscription): GPT Image | yes | yes, follows style well | yes | included |
| Gemini, image model in AI Studio | yes | yes, good at consistency | yes | free tier |
| Microsoft Copilot (consumer, not GitHub Copilot) | yes (OpenAI model) | limited | no | free, daily cap |
| Claude, Claude Code | **no** raster images | | | |
| GitHub Copilot | yes on the eligible paid plans (recent) | | | included |
| Stable Diffusion / Flux, local | yes | with a LoRA or IP-adapter | with work | free, needs a GPU and tuning: not worth it for ten icons |

Decision: ChatGPT's GPT Image with the reference sheet (chosen over Copilot, same model, a preferred workplace), Gemini as the free second choice. The format of the generated icons is left open until the trial: a generated image is a PNG at a fixed resolution, and tracing it to SVG (`potrace`, Inkscape) can lose the detail that makes a dish recognisable. The trial compares the omurice as a transparent PNG and as a traced SVG at 20 px on the real chip, and that result decides the pipeline: either the sprite holds the generated icons as embedded PNG `<image>` elements at 2x, or as traced paths. The stock Fluent SVGs go straight into the sprite either way. Check the terms of the service on reuse before the site sells anything; the emoji sets are the safest of all.

### C. SVG written by a coding model (the abstract tags)

Claude Code or Codex can write a 24 px flat SVG for "a crossed-out cigarette", "a credit card", "a speech bubble with EN", "a calorie counter". The result is clean, tiny, recolourable for the dark theme, and consistent because it is code on a shared grid and palette. It is simple, not cute: right for the abstract third of the tags, where a glyph is what one wants, in the same palette as the emoji set.

## 3. The licence: what MIT asks of the app

Fluent Emoji is under the MIT licence. It puts one obligation on the app: keep Microsoft's copyright line and the licence text together with the copied files. Nothing else: the app's own code keeps its licence, the icons may be resized, recoloured and edited, no credit is owed in the UI, no share-alike, nothing to publish. In practice: a `LICENSE-fluent-emoji.txt` next to the icons in `source/public/icons/`, and a line on the info page as a courtesy. The CC BY-SA of OpenMoji would be heavier (a visible credit, share-alike on derived icons); the images generated by a service are governed by its terms (OpenAI's and Google's give the user the output; check again before the site sells anything).

## 4. Performance: one sprite, no script

Performance matters more than the rest here. The costly way is one `<img>` per tag: fifty requests on a page with the cloud. The cheap way is one sprite:

- **An SVG sprite** (recommended): one file of `<symbol>` elements, `public/icons/tags.svg`, used as `<svg aria-hidden="true"><use href="/icons/tags.svg?v=<version>#ramen"/></svg>` before the label. `/icons/` is the folder the service worker already treats as static (`sw.js`: served from the cache, refreshed when the `?v=` changes), so the sprite is pre-cached at the same versioned URL without the fragment, and an artwork-only change bumps the version so the cached copy is refreshed. One cacheable request (the stock symbols are vector and recolourable; an embedded PNG keeps its resolution and colours). The flat Fluent icons are 1 to 4 KB each: fifty icons make about 100 KB raw, about 30 KB over the wire.
- A WebP sprite sheet at 2x (40 px cells for a 20 px display) is smaller still, about 20 KB, but blurry at other sizes and not recolourable.

Only the flat style: the 3D and Color styles are gradients and shading, bigger files and heavier to paint. No JavaScript is involved; the sprite is built once by a script in `tools/` and committed, so it costs nothing per visitor and nothing at deploy.

## 5. The plan

On a branch of its own (`tag-icons`), after the Japanese names and the data editing are merged: it touches the tag partials and the service worker, not the data.

1. **A trial first, before any mapping** (Codex's point, agreed): four real chips, ramen, pasta, soba and omurice, placed in the existing phone filter among the ordinary text-only tags, in English and in Japanese, stock and generated versions side by side. A crowded list tells what an isolated art sheet cannot: whether the pictures help to find lunch. Ramen and pasta exist in the set, to compare stock against generated; soba tests whether two noodle dishes stay distinct at 20 px; omurice tests whether a generated dish is recognisable (a yellow omelette and a stripe of ketchup). The bet: the stock icons win on consistency, and the generator earns its place only on the dishes the set lacks. If the custom art gives the site a personality worth the work, draw more.
2. The mapping: a `config/tag_icons.php` keyed by the **English tag label**, which is already the stable search value of both languages (`12-multilingual-ui.md`): no database change, one mapping serves the English and the Japanese page. A tag without an entry shows text only; the abstract tags stay that way on purpose.
3. A script in `tools/` copies the chosen Fluent files into the sprite, records the source file names (so the set can be updated), and carries the licence file.
4. Generate the missing dishes with the reference-sheet method, transparent background, approve by hand at the real size, add to the sprite.
5. The abstract tags that deserve a glyph (no smoking, credit card, English menu): SVG written by hand or by a coding model, same palette, same sprite.
6. The label stays next to the icon on desktop and phone (`12-multilingual-ui.md` gives the words; a phone has no hover to explain a symbol). Tests: every mapped tag resolves to a symbol in the sprite; the Japanese page shows the same icons; the cloud stays usable with the keyboard; the sprite is in the service worker's pre-cache.
