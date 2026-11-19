# Multilingual UI: English by default, Japanese always, more languages later

Design note, November 2026. Steps 1 to 7 of section 4 were implemented in November 2026; step 8 (the Japanese place names, then more languages) is pending.

## 1. The goal

The app is for tourists first, so English stays the default. Japanese must always be available, so the locals can use it. French, Spanish and others come later, and adding one must be cheap. The app must stay what it is: small, fast, server-rendered, friendly to search engines.

Three reviews shaped this note: a first design (header-based language switch), the comments on it, and a review of the first draft of this note. The decisions below keep what all agreed on and settle the points of disagreement.

## 2. Decisions

| Question | Decision | Why |
|---|---|---|
| Where does a language live? | In the URL: English at the existing URLs, every other language under a prefix (`/ja/restaurants`, `/ja/restaurant/details/12`, later `/fr/...`). | One URL = one page, in one language. Search engines index each version, caches (browser, service worker, CDN) never mix them, a shared link opens in the language it was read in. Existing English links keep working. |
| Does the browser language switch the site? | No redirect. A small banner offers the version the browser prefers ("日本語で見る"), when it exists, on the first visit. It is built by the JavaScript from the browser's languages and a dismissal stored in the browser; the HTML is the same for every visitor. | Google advises against automatic redirection between language versions; a suggestion is what it recommends. Building it client-side keeps every page identical whatever the visitor's browser or cookies, so the ETag, the service worker and any cache in front stay simple. One tap on the first visit, nothing after. |
| How are strings translated? | Laravel JSON language files keyed by the English sentence: `__('Show all')` in the views, `lang/ja.json` holds `"Show all": "すべて表示"`. | No invented keys, the views stay readable, a missing translation falls back to the English text instead of breaking. Adding a language is one file. |
| What about the tags? | The tag label shown is translated, the value submitted stays the English label (`data-tag="ramen"`, text "ラーメン"). | The seeders, the search, the cache keys and the filter URLs do not change. |
| What about the place content? | Names, locations and notes stay English in the first release, marked `lang="en"` inside a Japanese page. A hand-curated `name_ja` (and later `address_ja`) key in the seeders comes second. | Japanese names are the one piece of content a local needs; notes can wait. They are typed by us, not copied from Google: the Places API terms forbid storing names and addresses beyond a short cache, and the freshness tool keeps checking in English anyway so it can compare names. |
| Are the Japanese place pages indexable at launch? | No. Until a place has `name_ja`, its Japanese page is `noindex`, has no `hreflang` pair and is not in the sitemap. The Japanese home and filter pages are indexable from day one. | A place page whose only Japanese text is the navigation is a thin duplicate of the English one; three hundred of them would hurt more than help. The rule flips per place as the Japanese content arrives. |

What was set aside: a subdomain per language (DNS and certificate work for nothing at this size), a query parameter (search engines treat it as the same page), a redirect from the browser language (Google's guidance, and the cache problems above).

## 3. How it works

### Routes

`config/app.php` gets `'locales' => ['en', 'ja']` (`'locale' => 'en'` is already the default). `routes/web.php` wraps the public routes in a function and registers them once per locale: the default one with no prefix and the route names as today, the others under `Route::prefix($locale)->name($locale.'.')`. `/ja/restaurants` is then the route `ja.restaurants.main`. `robots.txt`, `sitemap.xml`, `sw.js` and `manifest.json` are registered once, outside the loop.

A `SetLocale` middleware on the prefixed groups reads the prefix and calls `app()->setLocale()`. The default group needs nothing: the locale is already `en`.

Route caching keeps working; the loop runs at cache time.

### URLs in the pages

A helper `App\Support\LocaleUrl` builds URLs for the current locale: `LocaleUrl::route('restaurants.main')` gives `/restaurants` in English and `/ja/restaurants` in Japanese, and `LocaleUrl::twin('ja')` gives the same page in another language. Every link in the views, every `data-results-url` and `data-suggestion-url` the JavaScript reads, and the language switch go through it. The JavaScript never builds a path itself: it already takes its URLs from `data-` attributes.

### The language switch keeps the place and the filters

On a filter page the visitor's state lives in the page, not in the URL: the selected tags, the operator, the sort order, and the place open in the details panel (opened by `showDetails()`, which prevents the navigation). A switch link rendered by the server would therefore send the visitor back to an empty list. Two changes fix it, and both are useful on their own:

1. **The filter page accepts its state in the query string** and restores it on load: `/ja/restaurants?tags=ramen,noodles&op=AND&orderBy=cost&place=12` selects the tags, sorts, and opens place 12 in the panel (on the phone layout, the panel view; on the wide layout, next to the list). The JavaScript already has every piece (the tag buttons, `ajaxResults()`, `showDetails()`); it only gains an init step that reads `location.search`. This also makes a filtered list shareable, which it is not today.
2. **The JavaScript keeps the address bar and the switch links up to date.** One function serialises the state into a query string. On every state change (tag, operator, sort, place opened or closed) the script calls `history.replaceState()` with it, so the address bar shows the real state and copying it shares the filtered list; and it rewrites the `href` of each switch link, printed by the header with `data-twin-base="/ja/restaurants"`, as the base plus the same query. The server-rendered `href` is the plain twin, so a visitor without JavaScript still gets the right page. The acceptance test selects tags, opens a place, copies the actual address-bar URL and opens it in a fresh browser context: a test starting from a hand-built query would miss a serialiser that never reaches the address bar.

On a standalone place page the switch is the server-rendered twin (`/ja/restaurant/details/12`), nothing to keep up to date.

### Strings

- Views: `__('...')` around every visible English sentence; the dates and prices are already numbers. Translations are whole sentences with placeholders (`"Lunch for :price"`), so Japanese can use its own word order.
- JavaScript: the half-dozen messages (loading, error, "copied", "no position", the banner text) move to a small `<script>` in the layout that prints `window.whereToStrings = @json([...])` from the same `__()` calls, and `whereto.js` reads them from there. The service worker has no visible text.
- Tags: the main views print `<button class="tag" data-tag="{{ $tag->label }}">{{ tag_label($tag->label) }}</button>`, where `tag_label()` (an `App\Support\Tags` helper) returns the entry of `lang/ja/tags.php` (`'ramen' => 'ラーメン'`) when there is one and the English label otherwise. Never `__('tags.ramen')` directly: Laravel returns the key itself, "tags.ramen", when the entry is missing. `whereto.js` reads `dataset.tag` instead of `textContent` to build the `tags=` parameter and to find a tag's twin from the details panel. The `tags/autocomplete` route is used by nothing since the admin was removed; it is deleted rather than translated.
- Accessibility labels (`aria-label`, `title`, the visually hidden headings) are strings like the others and go through `__()`.
- `<html lang>` already prints the app locale.

### The banner

The layout prints the available locales and the twin URLs as data (the switch links). On load, `whereto.js` compares `navigator.languages` with the available locales and the page's locale; when the browser prefers another available language and `localStorage` holds no dismissal, it shows a one-line banner with the twin link and a close button. The link, the close button and a click on a language in the header all store the dismissal, so the banner shows once per browser, whichever way the visitor answers (a visitor who picked a language in the header has answered too). No cookie, no server-side matching, no page variant.

### Search engines

- The home and filter pages carry `<link rel="alternate" hreflang="...">` for every language, plus `hreflang="x-default"` pointing at the English page. Reciprocal: the Japanese page lists the English one and itself.
- A place page is indexable in a language when the place is not gone (the existing `business-status` rule: permanently closed and replaced places are `noindex` in every language) and it has content in that language: always true for English, `name_ja` present for Japanese, the matching key for a later language. A page lists in `hreflang` the versions that are indexable, and only those; a version that is not is `noindex` and absent from every `hreflang` list (pointing `hreflang` at a `noindex` page is ignored by Google and wastes crawl). Test cases: an open place with `name_ja` and no French content gets the English and Japanese alternates; a permanently closed place gets neither a sitemap entry nor alternates.
- The canonical of each page is itself, in its own language (the existing `PublicUrl::to(request()->path())` already does that, the prefix is part of the path).
- `og:locale` follows the page locale (`ja_JP`, `en_US`), with `og:locale:alternate` for the others, instead of the fixed `en_US` of today.
- The sitemap lists the home and filter pages in every language, and the place pages in English plus the Japanese ones that have `name_ja`. It is cached forever today under the key `sitemap`; the key becomes `sitemap-<locales>` so that adding a language cannot serve a stale sitemap, and the deploy's `cache:clear` (already in the update steps) covers the rest.
- Titles and descriptions of the filter pages are translated with everything else. The place pages keep the English name in the title; the Japanese name is added in front of it when `name_ja` exists.

### Offline (service worker)

`sw.js` pre-caches the five shell pages (`/`, `/restaurants`...). It adds the same five under each prefix so the Japanese shell works offline too, and its offline fallback serves the shell of the language of the failed URL (its prefix) instead of always `/`. The list of prefixes is printed into the `sw.js` URL query by the layout, next to the CSS and JS versions, so the worker knows the locales without a build step. Because the banner is client-side, the cached pages are the same for everyone and can be replayed offline without showing a stale suggestion.

### Caching and ETag

The forever cache in `PlaceSearch` holds models, not HTML, so it is locale-free and unchanged. The ETag of the fragments is computed on the rendered response, and the fragment URLs differ per locale, so nothing is shared by mistake. The language adds nothing that depends on the visitor (no cookie, no `Accept-Language` variant). The pages are still not identical across visitors: the layout prints a session CSRF token in a meta tag, so full-page HTML must not be shared across visitors by a CDN on the strength of this design.

### Tests that catch a missing translation

"Every key of `en` has a `ja` entry" is not checkable, because there is no `en.json`: the English sentences live in the views. The tests therefore work from the source:

- A test collects every literal passed to `__()` in `resources/views` and `app` (a regex over the files; the architecture tests already scan views this way) and asserts each one is a key of every non-default `lang/<locale>.json`. It fails with the list of the missing sentences.
- A test collects the tag labels of the four tag seeders and asserts each has an entry in every `lang/<locale>/tags.php`.
- Feature tests render the filter pages and a place page in every locale and assert on a few translated strings and `aria-label`s, and that the place name and notes are still English inside `lang="en"` blocks.

### Adding a language

1. Add `'fr'` to `config('app.locales')`.
2. Add `lang/fr.json` and `lang/fr/tags.php`.
3. Run the tests: the two coverage tests above fail on any missing sentence or tag.

Nothing else: the routes, the switch, the `hreflang` links, the banner, the service worker and the sitemap (whose cache key includes the locales) read the list.

## 4. Order of work

The restaurants go first, end to end, because they carry the hardest behaviour (the one-page panel, the filters, the switch); the three other kinds share the views' structure and follow once the pattern is settled.

| # | Step | Size |
|---|---|---|
| 1 | **Done (November 2026).** Routes per locale, `SetLocale` middleware, `LocaleUrl`, the switch links in the header, `og:locale`. Tests: `/ja/restaurants` is 200 with `lang="ja"`, the English URLs are unchanged, the switch of a standalone page is its twin, route caching passes. | M |
| 2 | **Done (November 2026).** **Restaurants only.** Strings of the layout, the home page, the restaurants filter page, results rows, details views and the suggestion through `__()`; `window.whereToStrings`; `lang/ja.json`. Tags: `data-tag`, `tag_label()`, `lang/ja/tags.php`, `whereto.js` reading `dataset.tag`, the dead autocomplete route removed. The filter page restores its state from the query string and the script keeps the switch links in sync. e2e (phone and desktop): on `/ja/restaurants` tap ラーメン and get the same results as `ramen` on `/restaurants`; open a place, switch to English and land on the same place with the same tags; share a filtered URL and see it restored. | L |
| 3 | **Done (November 2026).** The translation coverage tests (the `__()` scan and the tag scan). | S |
| 4 | **Done (November 2026).** Stores, bars, karaokes: the same pattern, mostly strings and the `data-tag` change in their views. | M |
| 5 | **Done (November 2026).** Search engines and offline: `hreflang` and `x-default` on the indexable pages, `noindex` on the Japanese place pages without `name_ja`, the sitemap rule and its cache key, the service worker prefixes and fallback. e2e: the Japanese page reopens offline. | S |
| 6 | **Done (November 2026).** The banner. e2e on the phone project with a Japanese browser: the banner shows once, the tap lands on `/ja/...`, the next visit shows no banner; closing it also shows no banner on the next visit. | S |
| 7 | **Done (November 2026).** Docs: `02-architecture.md` (lang files, middleware, helper), `04-how-it-works.md` (locale in the URL, the state in the query string, the banner), `10-seo.md` item 7 (hreflang done). | S |
| 8 | Later: `name_ja` in the seeders (typed by hand), shown first on Japanese pages and in the title; those place pages become indexable, get their `hreflang` pair and enter the sitemap. Then `lang/fr.json`. | content |
