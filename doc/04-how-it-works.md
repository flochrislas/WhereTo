# How it works: request flows

The public site is a mostly single-page experience per place type. The server renders a page shell with all the tags, and the browser then fetches HTML fragments (results, details, suggestion) with `fetch()` and injects them into the page. There is no JSON API for the UI; every AJAX endpoint returns rendered Blade HTML.

## 0. Languages: the locale is in the URL

Every page exists in English at its usual URL and in Japanese under `/ja` (`/ja/restaurants`, `/ja/restaurant/details/12`, and the fragments `/ja/restaurants/results`...); the design is in `12-multilingual-ui.md`. The route group of a language sets it (`SetLocale` middleware), so `__()` reads `lang/ja.json`, `tag_label()` reads `lang/ja/tags.php`, and every URL a view prints goes through `LocaleUrl::route()` and stays in the same language, including the ones the script fetches. The tags are submitted with their English label (`data-tag`), so the search, its cache and the URLs do not depend on the language; the place names and notes stay English (`lang="en"`). The header's switch link goes to the same page in the other language (`LocaleUrl::twin()`). Nothing depends on the visitor: no cookie, no `Accept-Language` handling, no redirection. A 404 under `/ja` (an unknown place id, `/ja/restaurant/details/999`) renders in English: the route model binding fails before the `SetLocale` middleware of the group runs, so the error page sees the default language.

On the first visit, when the browser prefers another language of the site than the page's (`navigator.languages`), `whereto.js` shows a one-line banner ("日本語で見る") built from the switch link (its `data-banner` and `data-close` texts are in the offered language). Following it or closing it stores `languageBannerDismissed` in `localStorage`: the banner never shows again in that browser.

## 1. Home page

```
GET /  ──▶ Route::view('/', 'home')
            ├─ layouts.app  (header, #position-report, footer, loads public/js/whereto.js)
            └─ suggestions.suggestion
                 @inject SuggestionController
                 if isLunchTime() (11:30–14:00 Asia/Tokyo) ──▶ show "Get a suggestion" button
```

Clicking "Lunch in Shibuya" calls `startGeolocation()` first, so the browser asks for the GPS permission while navigating to `/restaurants`; the first fix is stored in `sessionStorage` under `lastCoordinates` and reused by the next page. The other three buttons go to `/stores`, `/bars`, `/karaokes`. The home page itself does not ask for the position until a button is clicked.

On a tablet or a PC (900px and more) the filter, the results and the details are shown side by side and the results load at page start; the panel swaps described below only happen on narrower screens (`isWideLayout()` in `whereto.js`, "wide screens" section of `style.css`).

The header's sun/moon button switches between the dark and the light theme on every page (`toggleTheme()` in `whereto.js`). The choice is stored in `localStorage` under `theme`; without one the OS preference applies, and the page follows the OS when it changes. The stylesheet is one file: the dark palette on `:root`, the light one on `:root[data-theme="light"]`, and the layout sets that attribute before the first paint so there is no flash.

## 2. Geolocation (client side, `public/js/whereto.js`, geolocation section)

On the place pages (the ones with a `data-results-url`), `startGeolocation()` runs as soon as the script loads and starts a single `navigator.geolocation.watchPosition()` for the page. Every fix is written to `sessionStorage` under `lastCoordinates`, and `getLocation()` simply returns that value (or `null` before the first fix) when a request is built. If results were already requested before the first fix arrived (they then carry `position=null` and the server measures from Shibuya Station), the first fix triggers one more request of the same results, with the position this time, so the distances and the "Near first" order are right. Later fixes update the stored value but do not refresh anything; the next click uses them.

`#position-report` tells the visitor which distances they see: "Locating you... distances are from the station until then", then "Distances are from your position", or the failure reason.

Failure messages are written into the `#position-report` div, including a hint to use HTTPS because browsers refuse geolocation on plain HTTP outside `localhost`.

## 3. Filter page

```
GET /restaurants ──▶ RestaurantController@index
                      RestaurantTag::allCached()   cache 'all_restaurants_tags', ordered by type, weight, created_at
                      RestaurantSearch->find(null, And, Distance) + withDistances(null)   every place, from the station
                      view restaurants.main
                        ├─ restaurants.filter   (#tags cloud, #selectedTags, "Show All" button)
                        └─ restaurants.results  (hidden on a phone: sort buttons, headers, and #results already
                                                 filled with results-data: the default list, time="1")
```

Stores, bars and karaoke do the same through `StoreController@index` and so on. The default list is in the HTML for the crawlers (see `10-seo.md`): each place name is a real link to its standalone page, and the JavaScript turns a click into the in-page details instead. Once the position is known, the script requests the list again with it (and at once when the remembered sort order is not "distance").

Client behaviour (`public/js/whereto.js`, tag filter and results sections):

1. Clicking a tag (a `<button>`) moves it between `#tags` and `#selectedTags` (`moveTag`), toggling its `aria-pressed` state.
2. Any change calls `refreshResults()`, which reads the labels of the selected tags, picks the sort order (parameter, else the current one: `distance` by default), reads the tag operator (`AND` by default, `OR` when "any" is chosen with the buttons under the selected tags), and calls `ajaxResults()`. Both live in the page (`state` in `whereto.js`); `localStorage` only remembers them for the next visit, so a browser without storage still honours the choice made on the page or in the address.
3. `ajaxResults()` requests `GET /restaurants/results?op=AND&tags=a,b&orderBy=distance&position=lat,lon` (parameters URL-encoded), stamps the request time in the `#results` `time` attribute so that stale responses are ignored, and injects the returned HTML into `#results`. The "Show All" button label becomes "Show N results" (N counted from `.resultsCounter` placeholders).
4. "Show All" with no tag selected (`getResults()`) loads every place once, sorted by distance (`loadAllResults()`), then switches to the results panel.

### The state in the query string

The selected tags, the operator, the sort order and the place open in the details panel are also written in the address bar: `/restaurants?tags=ramen,noodles&op=OR&orderBy=cost&place=12` (the defaults, `AND` and `distance`, are left out). `stateQuery()` serialises the state; `syncState()` runs on every change (tag, operator, sort, place opened, BACK on a phone), calls `history.replaceState()` with it (no new history entry) and sets the `href` of each switch link to its `data-twin-base` plus the same query, so switching language keeps the place and the filters. A page opened with such a query starts in that state (`restoreState()`, at page start): the tags are selected, the operator and sort order are applied and remembered, the search runs once (on a phone the results panel shows), and the place opens in the details panel through `data-details-url`. A filtered list can thus be shared or bookmarked. Without any of these parameters the page uses the remembered sort order and operator as before.

## 4. Results fragment

The results and details routes run through `cache.headers:etag;no_cache` (`routes/web.php`): each response carries an ETag, and a repeated request with `If-None-Match` gets an empty `304 Not Modified` when the HTML did not change.

```
GET /restaurants/results?op=&tags=&orderBy=&position=
  ──▶ RestaurantController@results
        TagOperator::fromRequest(op), OrderBy::fromRequest(orderBy)   enums, defaults AND and distance
        formatTags()         "a,b" ──▶ ['a','b'] or null
        RestaurantSearch->find()   key from (op, tags, orderBy, limit)
          └─ miss ──▶ readFromDB()
                        scopes('open')  business_status = OPERATIONAL (closed places are never listed)
                        whereTags()   AND: whereHas('tags', whereIn label) having count = N; OR: any
                        orderBy()     per kind (ratingColumns / costColumn):
                                        restaurants: ratings/distance ──▶ score_lunch, score_food, score_place desc, id
                                                     cost             ──▶ lunch_price asc, then scores, id
                                        bars: score, score_place, score_mingling; stores, karaoke: score
        withDistances()
          resolvePosition()  null, invalid, or > 2.5 km from Shibuya Station ──▶ station Position
          orderBy == distance ──▶ sortByDistance()  (collection sortBy currentDistance, no coordinates ──▶ dropped)
          otherwise          ──▶ generateCurrentDistances()  (fills $place->currentDistance)
        ellipsis(points, 50)
        view restaurants.results-data (one .resultRow per place)

Before the upgrade the bar controller ignored the visitor and used a hard-coded "office" position (a debug leftover); bars now behave like the other place types.
```

Distances use `GeoUtils::distance()`, an equirectangular approximation in metres, and the list shows `GeoUtils::walkingTime()` in minutes at 3 km/h. Places with no coordinates are dropped when sorting by distance and shown with `N/A` otherwise.

Tag operators `OR`, `ANDNOT` and `ORNOT` are implemented server-side but the UI always sends `AND`.

## 5. Details

Clicking a result name calls `showDetails(url, id)`: if the id differs from the one currently loaded it fetches `GET /restaurants/details/{id}` and puts the fragment into `#mainDetails`, then hides `#content`. "BACK" simply toggles the two divs. The fragment shows links (Google Maps, Tabelog, website), the scores with tooltips, `points`, `experience`, and the place's tags from `tagsCached()`.

"Copy URL" copies the standalone route `GET /restaurant/details/{id}` (`detailsSingle`), which wraps the same fragment in the full layout with SEO meta tags. That is the shareable, crawlable page.

### Closed places

A place that is no longer in business (`business_status` other than `OPERATIONAL`, see `03-data-model.md` and `11-data-freshness.md`) stays in the database but is never listed. The `open()` local scope (`#[Scope]` method of the `Location` trait, `business_status = 'OPERATIONAL'`) is applied once, in `PlaceSearch::readFromDB()`, before the result is cached; every list goes through that search, so the results fragment, the default list of the filter page, the lunch suggestion and `sitemap.xml` all leave the closed places out. It is a local scope, not a global one, so the route model binding of the details actions (`detailsSingle(Restaurant $place)`) still loads a closed place. Its standalone page renders as usual, with the `business-status` Blade component under the heading: a banner with the status ("Permanently closed", "Temporarily closed", "Replaced by another business"), the status appended to the name in the `<title>` and the descriptions (`nameWithStatus()`, e.g. "Kindenmaru, permanently closed"), and, for the permanently closed and replaced ones only, a `<meta name="robots" content="noindex">` pushed to the layout's `head` stack and no JSON-LD (the `structured-data` component skips it: the markup would describe a live business with our review). The Google Maps link stays. A temporarily closed place keeps its indexing and its JSON-LD but, like every non-operational place, leaves `sitemap.xml`, which lists open places only: a mixed signal accepted on purpose (the page is still worth finding, it is just not offered to the crawlers). An unknown stored status (a typo such as `CLOSED`) is shown as open on the page and hidden from the lists, since `open()` compares with `OPERATIONAL` exactly. The fragment is only reached from the list, so it never shows a closed place.

## 6. Lunch suggestion

```
GET /suggestions?position=lat,lon ──▶ SuggestionController@suggest
  isLunchTime()?  no ──▶ ""  (empty body)
  yes ──▶ suggestLunch()
          RestaurantSearch->find(null, And, Ratings, 100)   key 'l100-All-Restaurant'
          withDistances(..., Distance)   nearest first
          take the first 35, pick one at random (empty string if there is no restaurant)
          render suggestions.suggestion-text: "How about a nice <type> restaurant, <a href=detailsSingle>name</a>?"
```

The browser (`ajaxSuggestion()` in `public/js/whereto.js`, URL from the `data-suggestion-url` attribute of `#suggestion`) replaces the suggestion box with the text and a "Try again" button that repeats the call.

## 7. Caching strategy

The results and details fragments carry `X-Robots-Tag: noindex` (`NoIndex` middleware, `noindex` alias in `bootstrap/app.php`), and every page carries `<link rel="canonical">` with its own URL built on `APP_URL` (`App\Support\PublicUrl`, also used for `og:url`, the sitemap and the JSON-LD) from the layout, so the search engines index the pages, never the fragments (`10-seo.md`).

Each standalone place page also carries a JSON-LD block (`components/structured-data.blade.php`): the place as a schema.org `Restaurant`, `Store`, `BarOrPub` or `EntertainmentBusiness`, with its name, a `PostalAddress` (Shibuya, Tokyo, JP: the `location` column is a neighbourhood label, not a street, so it stays out of the markup), coordinates, Google Maps / Tabelog / website links, the lunch price and our score as a single `Review`. Each model builds its array in `structuredData()` on top of `App\Support\StructuredData`; the JSON is encoded with `<`, `>` and `&` escaped so no text from the seeders can break out of the script tag.

Because the dataset is tiny and only changes through seeders, every query result is cached forever in the `cache` table and keyed by the search parameters (see 03-data-model.md). Distances are computed per request on the cached collection. The seeder flushes the whole cache at the end; after a change made another way (SQL, tinker), run `php artisan cache:clear`.

## 8. No authentication, no admin

The site is read-only: every route is a `GET`, there is no login and no account. The legacy admin (restaurant, tag and type CRUD behind a login) was removed in November 2026: the seeders are the source of truth and a redeploy would have overwritten any online edit anyway, so the admin was only an attack surface. Edit the data in `database/seeders/` and run `php artisan db:seed`.

## 9. Environment flags that change behaviour

| Setting | Effect |
|---|---|
| `APP_ENV` | Nothing app-specific depends on it; only framework defaults change. |
| `APP_DEBUG=true` | Detailed error pages; the Debugbar is enabled by default when installed. |
| `DEBUGBAR_ENABLED` | Forces the Debugbar on or off. Note that `php artisan serve` only forwards a whitelist of environment variables to the server process, so put it in `.env`. |
| `CACHE_STORE` | Defaults to `database`; `file` or `redis` work as well. |
| `LOG_LEVEL` | Keep `info` or higher in production; the controllers log at `debug` level on every request. |

## 10. Installable app and offline (service worker)

`whereto.js` registers `public/sw.js` on every public page. The worker:

1. On install, pre-caches the app shell: `/`, the four place pages, the same five under each language prefix (`/ja`, `/ja/restaurants`...; the page passes the prefixes as `&locales=ja` in the worker URL, from `data-locale-prefixes` on `<html>`), the stylesheet and the script with their `?v=` version (the page registers the worker as `/sw.js?css=<v>&js=<v>`, from data attributes of `<html>`, so a new file version is a new worker and a new cache), the font, the icon and the manifest.
2. For static files (`/css`, `/js`, `/fonts`, `/icons`, the manifest, the favicon): answers from the cache at once and refreshes the copy in the background ("stale-while-revalidate"). The script tag's `?v=` makes a new `whereto.js` a new URL, so it is fetched from the network.
3. For pages, results and details fragments: network first, and the last copy seen when offline. At most `MAX_FRAGMENTS` (20) such copies are kept, oldest out; the number is a constant at the top of `sw.js` with its reasoning. A page requested offline with a query string (the state of a filter page) falls back to the same page without it, where the script restores the state from the address; an unknown page falls back to the cached home page of its language (`/ja` for `/ja/...`).
4. Never touches `/_debugbar`, non-GET requests or other origins.

`CACHE_VERSION` in `sw.js` names the cache; bumping it drops the previous cache on activation. The manifest (`public/manifest.json`) gives the name, colours and icons used by "Add to home screen"; the layout links it with `rel="manifest"` and an `apple-touch-icon` for iOS.
