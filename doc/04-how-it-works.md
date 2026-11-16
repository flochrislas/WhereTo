# How it works: request flows

The public site is a mostly single-page experience per place type. The server renders a page shell with all the tags, and the browser then fetches HTML fragments (results, details, suggestion) with `fetch()` and injects them into the page. There is no JSON API for the UI; every AJAX endpoint returns rendered Blade HTML.

## 1. Home page

```
GET /  ──▶ Route::view('/', 'home')
            ├─ layouts.app  (header, #position-report, footer, geolocation JS)
            └─ suggestions.suggestion
                 @inject SuggestionController
                 if isLunchTime() (11:30–14:00 Asia/Tokyo) ──▶ show "Get a suggestion" button
```

Clicking "Lunch in Shibuya" calls `getLocation()` first, which asks the browser for the GPS position and stores it in `sessionStorage` under `lastCoordinates`, then navigates to `/restaurants`. The other three buttons go to `/stores`, `/bars`, `/karaokes`.

## 2. Geolocation (client side, `inline-javascript/geolocalisation.blade.php`)

`getLocation()` is called at almost every interaction (tag click, sort, "Show All", suggestion). It is asynchronous: it triggers `navigator.geolocation.getCurrentPosition()` and immediately returns whatever is already in `sessionStorage`. The first request of a session therefore usually carries `position=null`, and the server falls back to Shibuya Station.

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

Client behaviour (`inline-javascript/filter.blade.php`):

1. Clicking a tag moves its `<div>` between `#tags` and `#selectedTags` (`moveTag`).
2. Any change calls `refreshResults()`, which reads the labels of the selected tags, picks the sort order (parameter, else `localStorage.orderBy`, else `distance`), hard-codes `op=AND`, and calls `ajaxResults()`.
3. `ajaxResults()` requests `GET /restaurants/results?op=AND&tags=a,b&orderBy=distance&position=lat,lon` (parameters URL-encoded), stamps the request time in the `#results` `time` attribute so that stale responses are ignored, and injects the returned HTML into `#results`. The "Show All" button label becomes "Show N results" (N counted from `.resultsCounter` placeholders).
4. "Show All" with no tag selected (`getResults()` in `main.blade.php`) loads every place once, sorted by distance, then switches to the results panel.

## 4. Results fragment

```
GET /restaurants/results?op=&tags=&orderBy=&position=
  ──▶ RestaurantController@results
        formatTags()         "a,b" ──▶ ['a','b'] or null
        useCache()           key from (op, tags, orderBy, limit)
          └─ miss ──▶ readFromDB()
                        whereTags()   whereHas('tags', whereIn label) having count = N  (AND)
                        orderBy()     restaurants only:
                                        ratings/distance ──▶ score_lunch, score_food, score_place desc, id
                                        cost             ──▶ lunch_price asc, then scores, id
        handleDistances()
          positionSpoofer()  null, invalid, or > 2.5 km from Shibuya Station ──▶ station coordinates
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
          RestaurantController->useCache(null, 'AND', 'ratings', 100)   key 'l100-All-Restaurant'
          handleDistances(..., 'distance')   nearest first
          take the first 35, pick one at random (empty string if there is no restaurant)
          render suggestions.suggestion-text: "How about a nice <type> restaurant, <a href=detailsSingle>name</a>?"
```

The browser (`inline-javascript/suggestion.blade.php`) replaces the suggestion box with the text and a "Try again" button that repeats the call.

## 7. Caching strategy

Because the dataset is tiny and only changes through seeders, every query result is cached forever in the `cache` table and keyed by the search parameters (see 03-data-model.md). Distances are computed per request on the cached collection. The admin controllers flush the whole cache after every create, update or delete; after a change made outside the admin (SQL, tinker), run `php artisan cache:clear`.

## 8. Authentication and admin

- `GET /login` shows a form; `POST /login` (throttled to 5 attempts per minute per IP) calls `Auth::attempt()` and redirects to the intended URL or the dashboard; `POST /logout` ends the session. There is no registration and no password reset: admin users are created by the seeder or with `php artisan tinker`.
- Everything under `/admin` requires a logged-in user (`auth` middleware, redirects guests to `/login`). The dashboard links to restaurant, tag and type management. Creating and editing restaurants works again (`$fillable` was restored); the form has a "lat, lon" field so a new place gets coordinates and shows up in distance-sorted results. Tag and type edit/delete use proper `PUT`/`DELETE` forms.
- `POST /admin/debug?mode=on|off` toggles the Debugbar for the current process only, when the dev package is installed.

## 9. Environment flags that change behaviour

| Setting | Effect |
|---|---|
| `APP_ENV` | Nothing app-specific depends on it; only framework defaults change. |
| `APP_DEBUG=true` | Detailed error pages; the Debugbar is enabled by default when installed. |
| `DEBUGBAR_ENABLED` | Forces the Debugbar on or off. Note that `php artisan serve` only forwards a whitelist of environment variables to the server process, so put it in `.env`. |
| `CACHE_STORE` | Defaults to `database`; `file` or `redis` work as well. |
| `LOG_LEVEL` | Keep `info` or higher in production; the controllers log at `debug` level on every request. |
