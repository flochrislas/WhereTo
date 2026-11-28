/**
 * WhereTo in the stand-alone Android app (doc/16-standalone-apk-plan.md).
 *
 * Loaded only by the pages written by "php artisan app:export-static" (data-standalone on <html>), before whereto.js.
 * There is no server in the app: every page and every details fragment is a file, and whereto.js fetches them as on
 * the website. What depends on the visitor (the results, with their distances and filters, and the lunch suggestion)
 * is computed here instead, from the data the export prepared under /app-data/:
 *   - <kind>.json                 the ids in the two orders the server returns them (byRatings, and byCost for a kind
 *                                 with a price), and the tags and coordinates of each listed place
 *   - <locale>/<kind>-results.html the results fragment with every row, rendered by the server
 *   - <locale>/suggestions.json   the sentence of each restaurant the suggestion may pick
 *   - <locale>/strings.json       the count sentences of the results fragment
 * whereto.js asks through loadFragment(): handles(url) says which URLs are answered here, load(url) answers them.
 * The rules are the server's (App\Services\PlaceSearch, SuggestionController), reproduced exactly: the parity tests
 * of tools/android-app compare the two on the same data.
 *
 * It also talks to the Android app, when the page runs in it (window.WhereToApp, MainActivity): window.open() goes
 * to the app, which opens the link in another app (Maps, the browser), and the app is told whether a place is open,
 * so that Android's Back closes it first (window.whereToBack()). In a desktop browser (the preview) neither happens.
 *
 * Plain browser JavaScript like whereto.js: no build step, no modules. The pure functions are in whereToLocalCore,
 * which the Node tests of tools/android-app load on their own (no document there, so the page part does not run).
 */

/* =========================================================================
 * The rules of the server, as pure functions (no DOM, the clock and the random source given)
 * ========================================================================= */

var whereToLocalCore = (function () {
  "use strict";

  /** Shibuya Station, PlaceSearch::STATION_POSITION */
  var STATION = { lat: 35.658789, lon: 139.701262 };

  /** Beyond this distance (metres) from the station the visitor's position is ignored, as on the server */
  var MAX_DISTANCE_FROM_STATION = 2500;

  /**
   * A number as PHP's is_numeric() accepts it: decimal digits with an optional sign, point and exponent, and
   * whitespace around (PHP's " \t\n\r\v\f"). Not hexadecimal, not "Infinity", not empty: Number() would take those.
   */
  var NUMERIC = /^[ \t\n\r\v\f]*[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?[ \t\n\r\v\f]*$/;

  /** Lunch time in Tokyo, both ends included, in milliseconds of the day: SuggestionController::isLunchTime() */
  var LUNCH_START = (11 * 3600 + 30 * 60) * 1000;
  var LUNCH_END = 14 * 3600 * 1000;

  /** How many of the nearest best restaurants the suggestion picks from (SuggestionController::TOP_N) */
  var TOP_N = 35;

  /** The kinds of place and the operators the server knows; anything else is its default */
  var OPERATORS = ["AND", "OR", "ANDNOT", "ORNOT"];
  var ORDERS = ["distance", "ratings", "cost"];

  /**
   * The value of a query parameter as PHP reads it: the last one when the name repeats, null when missing.
   * A name with brackets (tags[]=a) is an array for PHP, which the server ignores: absent here too.
   */
  function param(params, name) {
    var values = params.getAll(name);
    return values.length === 0 ? null : values[values.length - 1];
  }

  /** TagOperator::fromRequest(): the exact value, else AND */
  function operator(value) {
    return OPERATORS.indexOf(value) >= 0 ? value : "AND";
  }

  /** OrderBy::fromRequest(): the exact value, else distance */
  function orderBy(value) {
    return ORDERS.indexOf(value) >= 0 ? value : "distance";
  }

  /**
   * PlaceController::formatTags(): null for no tags (PHP's empty(): missing, "" or "0"); otherwise ", " becomes ","
   * and the string is split on the commas, empty parts kept ("," is two empty tags)
   */
  function parseTags(value) {
    if (value === null || value === undefined || value === "" || value === "0") {
      return null;
    }
    return String(value).split(", ").join(",").split(",");
  }

  /** Does a place with these tags pass the filter: PlaceSearch::whereTags() */
  function matches(placeTags, tags, op) {
    if (tags === null) {
      return true;
    }
    // The number of the place's tags that are in the list (a SQL count over its tags)
    var matching = placeTags.filter(function (tag) { return tags.indexOf(tag) >= 0; }).length;
    switch (op) {
      case "OR":
        return matching > 0;
      case "ANDNOT":
        return matching === 0;
      case "ORNOT":
        return placeTags.some(function (tag) { return tags.indexOf(tag) < 0; });
      default:
        // AND: as many matching tags as tags asked (a tag asked twice is never matched twice)
        return matching === tags.length;
    }
  }

  /** PHP's deg2rad(): the division first, as PHP computes it (the other order differs in the last bit) */
  function deg2rad(degrees) {
    return degrees / 180 * Math.PI;
  }

  /** GeoUtils::distance(): the equirectangular approximation, in metres, from point 1 to point 2 */
  function distance(lat1, lon1, lat2, lon2) {
    var p1 = deg2rad(lon2 - lon1) * Math.cos(deg2rad(lat1 + lat2) / 2);
    var p2 = deg2rad(lat1 - lat2);
    return 6371 * Math.sqrt(p1 * p1 + p2 * p2) * 1000;
  }

  /**
   * Position::tryFromString(): {lat, lon}, or null for missing, "null", or not two numbers. The spaces are removed,
   * the string split on the commas, extra parts ignored ("lat,lon,15")
   */
  function parsePosition(text) {
    if (text === null || text === undefined || text === "" || text === "null") {
      return null;
    }
    var parts = String(text).split(" ").join("").split(",");
    if (parts.length < 2 || !NUMERIC.test(parts[0]) || !NUMERIC.test(parts[1])) {
      return null;
    }
    return { lat: Number(parts[0]), lon: Number(parts[1]) };
  }

  /** PlaceSearch::resolvePosition(): the visitor's position, or the station when it is missing, invalid or too far */
  function resolvePosition(text) {
    var position = parsePosition(text);
    if (position === null) {
      return STATION;
    }
    if (distance(position.lat, position.lon, STATION.lat, STATION.lon) > MAX_DISTANCE_FROM_STATION) {
      return STATION;
    }
    return position;
  }

  /** The distance from the position to a place, or null when the place has no coordinates (never NaN) */
  function placeDistance(position, place) {
    if (place.lat === null || place.lon === null || place.lat === undefined || place.lon === undefined) {
      return null;
    }
    return distance(position.lat, position.lon, place.lat, place.lon);
  }

  /** Sorted by distance, the places without one dropped: PlaceSearch::sortByDistance(). Array.sort is stable */
  function byDistance(rows) {
    return rows
      .filter(function (row) { return row.distance !== null; })
      .sort(function (a, b) { return a.distance - b.distance; });
  }

  /**
   * The results of a kind for a query string (URLSearchParams): [{id, distance}] in the order of the server.
   * data: the <kind>.json of the export.
   */
  function search(data, params) {
    var order = orderBy(param(params, "orderBy"));
    var op = operator(param(params, "op"));
    var tags = parseTags(param(params, "tags"));
    // The SQL order: by cost when asked and the kind has a price, else by ratings ("distance" starts from there too)
    var ids = order === "cost" && data.byCost ? data.byCost : data.byRatings;
    var position = resolvePosition(param(params, "position"));
    var rows = [];
    ids.forEach(function (id) {
      var place = data.places[id];
      if (place && matches(place.tags, tags, op)) {
        rows.push({ id: id, distance: placeDistance(position, place) });
      }
    });
    return order === "distance" ? byDistance(rows) : rows;
  }

  /** GeoUtils::walkingTime(): minutes at 3 km/h, rounded up; 0 for a place without a distance, as the site shows */
  function walkingTime(metres) {
    return metres === null ? 0 : Math.ceil(metres / 1000 / 3 * 60);
  }

  /** The time of day in Tokyo, in milliseconds since midnight there, whatever the phone's own time zone */
  var tokyoClock = null;
  function tokyoMilliseconds(date) {
    tokyoClock = tokyoClock || new Intl.DateTimeFormat("en-GB", {
      timeZone: "Asia/Tokyo", hourCycle: "h23", hour: "2-digit", minute: "2-digit", second: "2-digit"
    });
    var parts = {};
    tokyoClock.formatToParts(date).forEach(function (part) { parts[part.type] = Number(part.value); });
    // The milliseconds do not depend on the zone (Tokyo is a whole number of minutes from UTC)
    return ((parts.hour * 60 + parts.minute) * 60 + parts.second) * 1000 + date.getUTCMilliseconds();
  }

  /** From 11:30:00 to 14:00:00 in Tokyo, both included */
  function isLunchTime(date) {
    var time = tokyoMilliseconds(date);
    return time >= LUNCH_START && time <= LUNCH_END;
  }

  /**
   * SuggestionController::suggest(): outside lunch time an empty string; else the 100 best restaurants, without the
   * ones that have no coordinates, the 35 nearest, one at random. Returns its id, or null when there is no candidate.
   * random: a function giving a number in [0, 1), Math.random in the page.
   */
  function suggestion(data, positionText, date, random) {
    if (!isLunchTime(date)) {
      return null;
    }
    var position = resolvePosition(positionText);
    var rows = data.byRatings.slice(0, 100).map(function (id) {
      return { id: id, distance: placeDistance(position, data.places[id] || {}) };
    });
    var candidates = byDistance(rows).slice(0, TOP_N);
    if (candidates.length === 0) {
      return null;
    }
    return candidates[Math.floor(random() * candidates.length)].id;
  }

  return {
    STATION: STATION,
    parseTags: parseTags,
    operator: operator,
    orderBy: orderBy,
    matches: matches,
    distance: distance,
    parsePosition: parsePosition,
    resolvePosition: resolvePosition,
    search: search,
    walkingTime: walkingTime,
    isLunchTime: isLunchTime,
    suggestion: suggestion
  };
})();

/* =========================================================================
 * The page: answers to whereto.js, the suggestion control, the Android app
 * ========================================================================= */

(function () {
  "use strict";
  if (typeof document === "undefined") {
    return; // the Node tests load the file for whereToLocalCore only
  }
  var core = whereToLocalCore;

  /* ---- The exported data, fetched on first use and kept ---- */

  var files = {};

  /** A file of /app-data/ as text: a promise, rejected with the HTTP status as loadFragment() is */
  function dataFile(path) {
    if (!files[path]) {
      files[path] = fetch("/app-data/" + path).then(function (response) {
        return response.ok ? response.text() : Promise.reject(response.status);
      });
      // A failure is not kept: the next request tries again
      files[path].catch(function () { delete files[path]; });
    }
    return files[path];
  }

  function dataJson(path) {
    return dataFile(path).then(JSON.parse);
  }

  /** The language of the page, whose files are used */
  function language() {
    return document.documentElement.lang;
  }

  /**
   * The path of a URL without its language prefix (/ja/restaurants/results gives /restaurants/results), or null for
   * another origin
   */
  function sitePath(url) {
    var address = new URL(url, location.href);
    if (address.origin !== location.origin) {
      return null;
    }
    var path = address.pathname;
    var prefixes = (document.documentElement.dataset.localePrefixes || "").split(",").filter(Boolean);
    prefixes.forEach(function (prefix) {
      if (path === "/" + prefix || path.indexOf("/" + prefix + "/") === 0) {
        path = path.substring(prefix.length + 1) || "/";
      }
    });
    return path;
  }

  /** The kind of a results URL (/restaurants/results gives "restaurants"), "suggestions", or null */
  function route(url) {
    var path = sitePath(url);
    if (path === null) {
      return null;
    }
    if (path === "/suggestions") {
      return "suggestions";
    }
    var results = /^\/([a-z]+)\/results$/.exec(path);
    return results ? results[1] : null;
  }

  /* ---- The results fragment ---- */

  /**
   * The rows of a kind in a language: a map from place id to its pair .resultsCounter + .resultRow, and the template
   * of what surrounds them. The rows are taken out of the template once they are indexed: each search then clones
   * the surrounding markup and the rows it shows, not the whole list
   */
  var fragments = {};
  function fragment(kind) {
    var key = language() + "/" + kind;
    if (!fragments[key]) {
      fragments[key] = dataFile(language() + "/" + kind + "-results.html").then(function (html) {
        var template = document.createElement("template");
        template.innerHTML = html;
        var rows = {};
        template.content.querySelectorAll(".resultRow[data-place-id]").forEach(function (row) {
          rows[row.dataset.placeId] = [row.previousElementSibling, row];
        });
        var table = template.content.querySelector(".resultTable");
        if (table) {
          table.replaceChildren();
        }
        return { template: template, rows: rows };
      });
      fragments[key].catch(function () { delete fragments[key]; });
    }
    return fragments[key];
  }

  /** The results fragment for a URL, assembled from the rows the server rendered */
  function results(kind, url) {
    var params = new URL(url, location.href).searchParams;
    return Promise.all([dataJson(kind + ".json"), fragment(kind), dataJson(language() + "/strings.json")])
      .then(function (loaded) {
        var data = loaded[0], parts = loaded[1], strings = loaded[2];
        var found = core.search(data, params);
        var page = parts.template.content.cloneNode(true);
        var table = page.querySelector(".resultTable");
        found.forEach(function (result) {
          var pair = parts.rows[result.id];
          if (!pair) {
            return;
          }
          var row = pair[1].cloneNode(true);
          var minutes = row.querySelector(".distance .oneDigit");
          if (minutes) {
            minutes.textContent = String(core.walkingTime(result.distance));
          }
          if (pair[0]) {
            table.appendChild(pair[0].cloneNode(true));
          }
          table.appendChild(row);
        });
        var count = table.querySelectorAll(".resultRow").length;
        var counter = page.querySelector(".resultsCount");
        if (counter) {
          counter.textContent = count === 1 ? strings.one : strings.many.replace(":count", count);
        }
        // The number in bold inside the sentence, cut at :count as the view does
        var end = page.querySelector("#resultsEnd");
        if (end) {
          var cut = strings.end.split(":count");
          var number = document.createElement("strong");
          number.textContent = String(count);
          end.replaceChildren(document.createTextNode(cut[0]), number, document.createTextNode(cut.slice(1).join(":count")));
        }
        var holder = document.createElement("div");
        holder.appendChild(page);
        return holder.innerHTML;
      });
  }

  /* ---- The suggestion ---- */

  function suggestionText(url) {
    var params = new URL(url, location.href).searchParams;
    var now = new Date();
    if (!core.isLunchTime(now)) {
      return Promise.resolve("");
    }
    return Promise.all([dataJson("restaurants.json"), dataJson(language() + "/suggestions.json")]).then(function (loaded) {
      var id = core.suggestion(loaded[0], params.get("position"), now, Math.random);
      return id === null ? "" : (loaded[1][id] || "");
    });
  }

  /**
   * The suggestion control is in every exported home page, hidden: shown from 11:30 to 14:00 Tokyo time by the
   * phone's clock, checked on load, when the page is shown again, when the app comes back to the front, and once a
   * minute while the page is visible. A suggestion already shown stays until the control is hidden; hidden, it gets
   * its first content back for the next day.
   */
  var suggestionContent = null;
  function updateSuggestionControl() {
    var control = document.getElementById("suggestion");
    if (control === null) {
      return;
    }
    if (suggestionContent === null) {
      suggestionContent = control.innerHTML;
    }
    if (core.isLunchTime(new Date())) {
      control.hidden = false;
    } else if (!control.hidden) {
      control.hidden = true;
      control.innerHTML = suggestionContent;
    }
  }

  updateSuggestionControl();
  window.addEventListener("pageshow", updateSuggestionControl);
  document.addEventListener("visibilitychange", function () {
    if (document.visibilityState === "visible") {
      updateSuggestionControl();
    }
  });
  setInterval(function () {
    if (document.visibilityState === "visible") {
      updateSuggestionControl();
    }
  }, 60 * 1000);

  /* ---- The Android app ---- */

  /** The Android app's bridge (MainActivity, addJavascriptInterface), or null in a desktop browser */
  function app() {
    return window.WhereToApp || null;
  }

  /**
   * Is a place open, so that Back has something to close: on a phone the details panel replaces the list. On a
   * wide screen the details sit next to the list and the page's own BACK does nothing: Back leaves the page.
   * shownPlaceId, isWideLayout() and hideDetails() are globals of whereto.js, which runs after this file.
   */
  function placeOpen() {
    return typeof shownPlaceId !== "undefined" && shownPlaceId !== null && typeof isWideLayout === "function" && !isWideLayout();
  }

  /* ---- What whereto.js and the Android app call ---- */

  window.whereToLocal = {
    /** Is this URL answered here: the results of a kind and the suggestion. The rest (details, pages) are files */
    handles: function (url) {
      return route(url) !== null;
    },
    /** The HTML text for a URL that handles() accepted: a promise, rejected with a status as fetch would be */
    load: function (url) {
      var kind = route(url);
      if (kind === null) {
        return Promise.reject(404);
      }
      return kind === "suggestions" ? suggestionText(url) : results(kind, url);
    },
    /** Called by syncState() of whereto.js each time a place opens or closes */
    stateChanged: function () {
      if (app()) {
        app().setPlaceOpen(placeOpen());
      }
    }
  };

  /** Android's Back (MainActivity, evaluateJavascript): close the open place and say so, or false when none is open */
  window.whereToBack = function () {
    if (!placeOpen()) {
      return false;
    }
    hideDetails();
    return true;
  };

  // The phone turned from portrait to landscape (or back) may switch between one panel and three side by side
  try {
    window.matchMedia("(min-width: 900px)").addEventListener("change", function () {
      window.whereToLocal.stateChanged();
    });
  } catch (e) {
    // An old WebView without addEventListener on a media query: the next place opened or closed tells the app
  }

  // The buttons of a place call window.open(url, '_blank'), which a WebView cannot follow: the app opens the URL in
  // the app it belongs to (Maps, the browser). In a desktop browser window.open stays the browser's own
  if (app()) {
    window.open = function (url) {
      app().openExternal(String(url));
      return null;
    };
  }
})();
