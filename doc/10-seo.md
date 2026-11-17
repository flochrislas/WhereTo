# SEO: how the app looks to search engines, and what to do about it

Assessment made in November 2026, after the `improvements-2026` branch. Short version: the pages are honest server-rendered HTML with good titles, but Google can barely find the pages worth ranking (the standalone place pages) and gets no help to understand them.

## 1. Where the app stands

### What is fine

- Every page has its own `<title>` and `<meta name="description">`: the home page, the four filter pages (`/restaurants`, `/stores`, `/bars`, `/karaokes`) and the standalone place pages (`/restaurant/details/{id}` and the three others). See the `@section('metatags')` of each view.
- The home page, the filter pages and the standalone place pages are rendered by the server. There is no JavaScript app shell that a crawler would have to execute to see the content.
- `<html lang="en">`, `public/robots.txt` allows everything, the site is tiny and fast, and it is mobile friendly (Google indexes the mobile version first).

### What hurts

1. **The place pages are unreachable by a crawler.** The standalone page of a place is the one worth ranking, but nothing links to it. The results list is loaded by JavaScript from a fragment URL (`/restaurants/results`), and a place opens through an `onclick`, not an `<a href>`. The only link in a results row goes to Google Maps. Google runs JavaScript but does not click, so the three hundred place pages are invisible unless someone shares a URL.
2. **No sitemap.** Together with the point above, Google has no way to learn the place URLs.
3. **The fragments are indexable.** `/restaurants/results?...` and `/restaurants/details/{id}` answer plain GET with no `noindex`, so Google may index partial HTML blobs instead of the real pages, and treat them as thin duplicates of the standalone pages.
4. **No canonical URL.** The same place exists at the fragment URL and at the standalone URL, with nothing saying which one is the real page.
5. **No headings.** No `<h1>` anywhere: the page structure gives Google nothing to weigh.
6. **No Open Graph or Twitter card tags.** A link shared in Slack, LINE or Twitter shows no title and no picture. Those shares are also how a small site gets its first backlinks.
7. **No structured data.** Google shows rich results for restaurants (type, address, price range, rating) from JSON-LD markup. The data for it is already in the database (name, location, coordinates, lunch price, scores).
8. **The "2026 review" in every title.** The year in the titles and descriptions comes from `updated_at`, which the seeder sets at every deploy, so every place claims a review from the deploy year. Misleading for a reader and for Google. `restaurants.visit_date` is the real review date.
9. **Thin content per place.** A few lines of notes per place. Fine for a visitor, little for a search engine competing against Tabelog and Google Maps. Not fixable by code; only by writing more.

## 2. What to do, in order

| # | Change | Size | Where |
|---|---|---|---|
| 1 | **Done (November 2026).** **Crawlable links to the place pages.** The place name in a results row becomes `<a href="/restaurant/details/{id}">`; the JavaScript keeps the one-page behaviour (`showDetails()` on click, `preventDefault`). And the default results list is rendered by the server in the filter page, so the links exist in the HTML without JavaScript; the script keeps refreshing the list on tags and sorts. | S/M | `*/results-data.blade.php`, `*/main.blade.php`, the four controllers' filter actions, `whereto.js` |
| 2 | **Done (November 2026).** **`sitemap.xml`.** A route that lists the home page, the four filter pages and every place page, with `lastmod` from `visit_date`. Referenced from `robots.txt` (`Sitemap: https://.../sitemap.xml`). Cached like the rest. | S | `routes/web.php`, a small controller or closure, `robots.txt` |
| 3 | **Done (November 2026).** **`noindex` on the fragments, canonical on the pages.** `X-Robots-Tag: noindex` header on the results, details-fragment and autocomplete routes (a middleware alias on the existing group), and `<link rel="canonical">` on every page from the layout (`url()->current()`), plus `APP_URL` set right in production. | S | `routes/web.php`, `layouts/app.blade.php` |
| 4 | **One `<h1>` per page, and Open Graph / Twitter tags.** The place name as `<h1>` on the standalone page, the page purpose on the others. `og:title`, `og:description`, `og:type`, `og:url`, `og:image` (the app icon until places have pictures), `twitter:card=summary`, filled by the `metatags` section of each view. | S | the views' `metatags` sections, `layouts/app.blade.php` |
| 5 | **JSON-LD on the standalone pages.** `Restaurant`, `Store`, `BarOrPub` and `EntertainmentBusiness` (karaoke) with `name`, `address` (the `location` text), `geo` (lat/lon), `priceRange` (the lunch price), `url`, `sameAs` (Google Maps, Tabelog, website), and an `AggregateRating` built from the scores. Validate with Google's Rich Results Test. | M | the four `details-single` views (a Blade component or a small helper) |
| 6 | **Real review dates.** Use `visit_date` (year) in the titles and descriptions instead of `updated_at`; drop the year when the place was never visited. | S | the four `details-single` views |
| 7 | Later: a per-place picture (also used by `og:image`), Japanese titles and descriptions (hreflang) once there is a Japanese UI, and more text per place. | L | content, not code |

Items 1 to 4 are one small batch; 5 is a bit more; 6 is a one-liner per view.

## 3. What to expect

The site will not rank for "lunch Shibuya": those results belong to Tabelog, Google Maps and the travel sites. It can rank for a place name plus "review" or "lunch", and for long-tail queries the big sites do not answer ("okonomiyaki lunch Shibuya cheap"). That is exactly where crawlable links, a sitemap and structured data make the difference, because today Google does not even know the place pages exist.

Measure with Google Search Console (add the property once the site is deployed, submit the sitemap, watch "Pages" for the indexed count) and the Rich Results Test for the JSON-LD.
