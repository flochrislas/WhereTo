# How it works: request flows

The public site is a mostly single-page experience per place type. The server renders a page shell with all the tags, and the browser then fetches HTML fragments (results, details, suggestion) with `fetch()` and injects them into the page. There is no JSON API for the UI; every AJAX endpoint returns rendered Blade HTML.

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
GET /restaurants ──▶ RestaurantTagController@restaurantsFilter
                      Cache 'all_restaurants_tags' ──▶ RestaurantTag ordered by type, weight, created_at
                      view restaurants.main
                        ├─ restaurants.filter   (#tags cloud, #selectedTags, "Show All" button)
                        ├─ restaurants.results  (hidden: sort buttons, headers, empty #results)
                        └─ inline JS: main, filter, results, details
```

Stores, bars and karaoke do the same through `StoreTagController@viewAllTags` and so on.

Client behaviour (`public/js/whereto.js`, tag filter and results sections):

1. Clicking a tag (a `<button>`) moves it between `#tags` and `#selectedTags` (`moveTag`), toggling its `aria-pressed` state.
2. Any change calls `refreshResults()`, which reads the labels of the selected tags, picks the sort order (parameter, else `localStorage.orderBy`, else `distance`), reads the tag operator from `localStorage.op` (`AND` by default, `OR` when "any" is chosen with the buttons under the selected tags), and calls `ajaxResults()`.
3. `ajaxResults()` requests `GET /restaurants/results?op=AND&tags=a,b&orderBy=distance&position=lat,lon` (parameters URL-encoded), stamps the request time in the `#results` `time` attribute so that stale responses are ignored, and injects the returned HTML into `#results`. The "Show All" button label becomes "Show N results" (N counted from `.resultsCounter` placeholders).
4. "Show All" with no tag selected (`getResults()`) loads every place once, sorted by distance (`loadAllResults()`), then switches to the results panel.

## 4. Results fragment

The results and details routes run through `cache.headers:etag;no_cache` (`routes/web.php`): each response carries an ETag, and a repeated request with `If-None-Match` gets an empty `304 Not Modified` when the HTML did not change.

```
GET /restaurants/results?op=&tags=&orderBy=&position=
  ──▶ RestaurantController@results
        TagOperator::fromRequest(op), OrderBy::fromRequest(orderBy)   enums, defaults AND and distance
        formatTags()         "a,b" ──▶ ['a','b'] or null
        RestaurantSearch->find()   key from (op, tags, orderBy, limit)
          └─ miss ──▶ readFromDB()
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

1. On install, pre-caches the app shell: `/`, the four place pages, the stylesheet, the font, the icon and the manifest.
2. For static files (`/css`, `/js`, `/fonts`, `/icons`, the manifest, the favicon): answers from the cache at once and refreshes the copy in the background ("stale-while-revalidate"). The script tag's `?v=` makes a new `whereto.js` a new URL, so it is fetched from the network.
3. For pages, results and details fragments: network first, and the last copy seen when offline. An unknown page requested offline falls back to the cached home page.
4. Never touches `/_debugbar`, non-GET requests or other origins.

`CACHE_VERSION` in `sw.js` names the cache; bumping it drops the previous cache on activation. The manifest (`public/manifest.json`) gives the name, colours and icons used by "Add to home screen"; the layout links it with `rel="manifest"` and an `apple-touch-icon` for iOS.
