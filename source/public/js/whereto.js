/**
 * WhereTo front-end JavaScript (public pages).
 *
 * One plain script, no build step. It is loaded once by layouts/app.blade.php with "defer",
 * so it runs after the HTML is parsed and the browser can cache it between pages.
 * The functions are global on purpose: the Blade views call them from onclick="..." attributes.
 *
 * Values that come from the server are read from data-* attributes:
 *   - #content[data-results-url]      URL of the results fragment, e.g. /restaurants/results
 *   - #suggestion[data-suggestion-url] URL of the lunch suggestion
 *
 * Sections: geolocation, panels (filter/results/details), tag filter, results, details, suggestion.
 */

/* =========================================================================
 * Geolocation
 * ========================================================================= */

/** Options for watchPosition: GPS if possible, give up after 5.5 s, accept a fix up to 1.5 s old */
var geolocationOptions = {
  enableHighAccuracy: true,
  timeout: 5500,
  maximumAge: 1500
};

/** Id returned by watchPosition, so we start the watch only once per page */
var geolocationWatchId = null;

/** true once this page has received at least one position fix */
var geolocationHasFix = false;

/** Where the geolocation messages are displayed (div in the layout) */
function getPositionReportDiv() {
  return document.getElementById("position-report");
}

/**
 * Start following the visitor's position. Safe to call several times: only the first call starts the watch.
 * This needs HTTPS (or localhost) to work in Chrome. The browser asks the visitor for permission the first time.
 */
function startGeolocation() {
  if (geolocationWatchId !== null) {
    return; // already watching
  }
  if (!navigator.geolocation) {
    reportPosition("Geolocation is not supported by this browser. Distances are from the station.");
    console.warn("Geolocation is not supported by this browser.");
    return;
  }
  reportPosition("Locating you... distances are from the station until then.");
  // watchPosition calls onPositionSuccess every time the position changes, onPositionError when it fails
  // See https://developer.mozilla.org/en-US/docs/Web/API/Geolocation/watchPosition
  geolocationWatchId = navigator.geolocation.watchPosition(onPositionSuccess, onPositionError, geolocationOptions);
}

/**
 * The latest known coordinates as "lat,lon", or null when there is no fix yet.
 * sessionStorage keeps the last fix across the pages of the same tab, so the first search on a
 * place page can reuse the fix obtained on the home page.
 */
function getLocation() {
  return readSessionStore("lastCoordinates");
}

/** watchPosition success: remember the fix, and refresh the results once if they were computed without it */
function onPositionSuccess(position) {
  const coordinates = position.coords.latitude + "," + position.coords.longitude;
  sessionStore("lastCoordinates", coordinates);
  console.info("Geolocation: " + coordinates);
  reportPosition("Distances are from your position.");

  if (!geolocationHasFix) {
    geolocationHasFix = true;
    // The first results of the page may have been requested before the fix arrived (distances from the station):
    // request them again, now with the position. Later fixes do not refresh anything, the visitor does.
    refreshResultsAfterFix();
  }
}

/** watchPosition failure: explain, distances stay from the station */
function onPositionError(error) {
  let message;
  switch (error.code) {
    case error.PERMISSION_DENIED:
      message = "You denied the request for geolocation.";
      break;
    case error.POSITION_UNAVAILABLE:
      message = "Location information is unavailable.";
      break;
    case error.TIMEOUT:
      message = "The request to get your location timed out.";
      break;
    default:
      message = "An unknown geolocation error occurred (" + error.code + ": " + error.message + ").";
  }
  // Browsers refuse geolocation on plain HTTP (except localhost)
  if (location.protocol !== "https:" && location.hostname !== "localhost") {
    message = "Please use the HTTPS version of the service to enable geolocation.";
  }
  console.warn("Geolocation error " + error.code + ": " + error.message);
  reportPosition(message + " Distances are from the station.");
}

/** Write a message in the position report div (if the page has one) */
function reportPosition(message) {
  const div = getPositionReportDiv();
  if (div !== null) {
    div.textContent = message;
  }
}

/** sessionStorage helpers: remember a value in the browser for the lifetime of the tab */
function sessionStore(key, value) {
  try {
    sessionStorage.setItem(key, value);
  } catch (e) {
    console.warn("No session storage support from the browser. Application's behavior may be affected.");
  }
}
function readSessionStore(key) {
  try {
    return sessionStorage.getItem(key);
  } catch (e) {
    return null;
  }
}

/** localStorage helpers: remember a value in the browser, across pages and visits */
function store(key, value) {
  try {
    localStorage.setItem(key, value);
  } catch (e) {
    // Storage disabled (private mode, old browser)
    console.warn("No local storage support from the browser. Application's behavior may be affected.");
  }
}
function readStore(key) {
  try {
    return localStorage.getItem(key);
  } catch (e) {
    return null;
  }
}

/* =========================================================================
 * Panels: tag filter, results, details
 * ========================================================================= */

/** The page content: filter panel + results panel (null on pages without them, like the home page) */
function getContentDiv() {
  return document.getElementById("content");
}
function getMainFilterDiv() {
  return document.getElementById("mainFilter");
}
function getMainResultsDiv() {
  return document.getElementById("mainResults");
}
/** Where the details are displayed */
function getDetailsDiv() {
  return document.getElementById("mainDetails");
}

/** URL of the results fragment for this page (e.g. /restaurants/results), set by Blade on #content */
function getResultsUrl() {
  const content = getContentDiv();
  // ?. stops at null (no #content), ?? turns a missing attribute (undefined) into null
  return content?.dataset.resultsUrl ?? null;
}

/** Tablet or PC (900px and more): the panels are side by side (style.css, "wide screens"), nothing to swap */
function isWideLayout() {
  return window.matchMedia("(min-width: 900px)").matches;
}

/** Switch from the tags panel to the results panel */
function showResults() {
  if (isWideLayout()) {
    return;
  }
  getMainFilterDiv().style.display = "none";
  getMainResultsDiv().style.display = "flex";
}

/** Switch from the results panel back to the tags panel */
function showFilter() {
  if (isWideLayout()) {
    return;
  }
  getMainFilterDiv().style.display = "flex";
  getMainResultsDiv().style.display = "none";
}

/* =========================================================================
 * Results list
 * ========================================================================= */

/** Where the results are displayed */
function getResultsDiv() {
  return document.getElementById("results");
}
/** Loading state, kept in the "loading" attribute of the results div: none, all, tag */
function getResultsLoading() {
  return getResultsDiv().getAttribute("loading");
}
function setResultsLoading(state) {
  return getResultsDiv().setAttribute("loading", state);
}
/** Current time in milliseconds, used to stamp the requests */
function nowStamp() {
  return Date.now();
}
/** Time of the latest request, kept in the "time" attribute of the results div (0 = nothing loaded yet) */
function getResultsLoadingTime() {
  return getResultsDiv().getAttribute("time");
}
function setResultsLoadingTime(time) {
  return getResultsDiv().setAttribute("time", time);
}

/** "Show All" button: load every place once (sorted by distance) and switch to the results panel. */
function getResults() {
  showResults();
  // Get all the places only if nothing has been loaded yet (loading time 0 = initial state)
  if (getResultsLoading() == "none" && getResultsLoadingTime() == 0) {
    loadAllResults();
  }
}

/** Request every place of the page, sorted by distance (no tags parameter = all the places) */
function loadAllResults() {
  setResultsLoading("all");
  // Stamp the request now (not when it answers): a GPS fix arriving while it is in flight must see that
  // something was requested, so that refreshResultsAfterFix() asks again with the position. Two "all" requests
  // can then be in flight (station, then position): only the latest one may write the list or clear the state.
  const now = nowStamp();
  setResultsLoadingTime(now);
  getResultsDiv().innerHTML = "Loading ALL entries. Please wait or select a tag...";
  // The remembered sort order, so the list matches the highlighted sort button
  const orderBy = readStore("orderBy") || "distance";
  const url = getResultsUrl() + '?position=' + getLocation() + '&orderBy=' + encodeURIComponent(orderBy);
  console.info('GET: ' + url);
  fetch(url)
    .then(response => response.ok ? response.text() : Promise.reject(response.status))
    .then(html => {
      // Only write if no other request (a later "all" one, or a tag search) started in the meantime
      if (now == getResultsLoadingTime()) {
        getResultsDiv().innerHTML = html;
      }
    })
    .catch(() => {
      if (now == getResultsLoadingTime()) {
        getResultsDiv().innerHTML = "Sorry, an error occurred during the search. Check your Internet connection and try again.";
        // Nothing loaded: back to the initial state (no loading, no stamp) so that "Show All" tries again.
        // The loading state first: the finally below only clears it while the stamp is still ours.
        setResultsLoading("none");
        setResultsLoadingTime(0);
      }
    })
    .finally(() => {
      // Only the latest request clears the loading state
      if (now == getResultsLoadingTime()) {
        setResultsLoading("none");
      }
    });
}

/**
 * Called when the first position fix arrives. If results were already requested without the position,
 * request them again so the distances and the "Near first" order are right.
 */
function refreshResultsAfterFix() {
  if (getResultsUrl() === null || getResultsDiv() === null) {
    return; // not a place page
  }
  if (getResultsLoadingTime() == 0) {
    return; // nothing requested yet: the next request will carry the position anyway
  }
  if (getSelectedTagsDiv().children.length > 0) {
    refreshResults(); // same tags, same order, with the position this time
  } else {
    loadAllResults(); // the "Show All" list
  }
}

/** Refreshes the list of results. orderBy: distance | ratings | cost (remembered in localStorage). */
function refreshResults(orderBy) {
  // Read all the divs under selectedTags and make a comma separated list of their labels
  const tagsLabels = getTagsLabels(getSelectedTagsDiv().children);

  // Check for the orderBy choice
  if (orderBy != null) {
    // Priority to the parameter (a sort button was clicked): remember it
    store("orderBy", orderBy);
  } else {
    // No parameter: try the stored choice
    orderBy = readStore("orderBy");
    if (orderBy == null) {
      // Nothing in the parameter nor the store: use the default value
      orderBy = "distance";
      store("orderBy", orderBy);
    }
  }

  // Show which sort button is the current one
  markSortButton(orderBy);

  // Operator between the tags: AND (all of them) or OR (any of them), chosen with the "all / any" buttons.
  // ANDNOT and ORNOT exist server-side but are not exposed.
  const op = getTagsOperator();

  // AJAX request to update the results
  ajaxResults(op, tagsLabels, orderBy);
}

/** The operator between the selected tags, remembered in localStorage: "AND" (default) or "OR" */
function getTagsOperator() {
  return readStore("op") === "OR" ? "OR" : "AND";
}

/** "all" / "any" buttons: remember the operator, show it on the buttons, and refresh the results if tags are selected */
function setTagsOperator(op) {
  store("op", op);
  markOperatorButton(op);
  if (getSelectedTagsDiv().children.length > 0) {
    refreshResults();
  }
}

/** aria-pressed="true" on the button of the given operator, "false" on the other (buttons carry data-op) */
function markOperatorButton(op) {
  document.querySelectorAll('.opButton[data-op]').forEach(button => {
    button.setAttribute('aria-pressed', button.dataset.op === op ? 'true' : 'false');
  });
}

/** aria-pressed="true" on the sort button of the given order, "false" on the others (buttons carry data-order-by) */
function markSortButton(orderBy) {
  document.querySelectorAll('.sortButton[data-order-by]').forEach(button => {
    button.setAttribute('aria-pressed', button.dataset.orderBy === orderBy ? 'true' : 'false');
  });
}

/** Request the results fragment. Responses older than the latest request are ignored. */
function ajaxResults(op, tags, orderBy) {
  // Build the URL; the values are URL-encoded
  const url = getResultsUrl()
          + '?op=' + encodeURIComponent(op)
          + '&tags=' + encodeURIComponent(tags)
          + '&orderBy=' + encodeURIComponent(orderBy)
          + '&position=' + getLocation();
  console.info("GET: " + url);
  // Stamp this request: the results div remembers the time of the latest request
  const now = nowStamp();
  setResultsLoadingTime(now);
  setResultsLoading("tag");
  // fetch() returns a promise; the .then() steps run when the response arrives
  fetch(url)
    // HTTP error (4xx, 5xx): jump to .catch()
    .then(response => response.ok ? response.text() : Promise.reject(response.status))
    .then(html => {
      if (now < getResultsLoadingTime()) {
        return; // a more recent request was made (it may even have answered already): leave the list alone
      }
      // Write the results (HTML fragment from the server)
      getResultsDiv().innerHTML = html;
      // Update the "Show N results" button with the number of results
      refreshShowResultsButton();
    })
    .catch(() => {
      if (now >= getResultsLoadingTime()) {
        getResultsDiv().innerHTML = "Sorry, an error occurred during the search. Check your Internet connection and try again.";
      }
    })
    .finally(() => {
      // Only the latest request clears the loading state
      if (now == getResultsLoadingTime()) {
        setResultsLoading("none");
      }
    });
}

/* =========================================================================
 * Tag filter
 * ========================================================================= */

function getFilterHeaderButtonDiv() {
  return document.getElementById("filterHeaderButton");
}
function getSelectedTagsDiv() {
  return document.getElementById("selectedTags");
}

/** What happens when we click on a tag: it moves between the available and selected lists, then the results refresh */
function moveTag(event) {
  // currentTarget: the button the handler is on (target could be something inside it)
  const tag = event.currentTarget;
  // A tag shown in the details of a place (not in the filter): select its twin in the available tags, if any
  if (tag.parentNode.id !== "selectedTags" && tag.parentNode.id !== "tags") {
    const label = tag.textContent.trim();
    const twin = Array.from(document.querySelectorAll("#tags .tag")).find(t => t.textContent.trim() === label);
    if (twin) {
      twin.click();
    }
    return;
  }
  // Move the tag in the proper div
  if (tag.parentNode.id == "selectedTags") {
    // Was selected: back to the available tags
    document.getElementById("tags").appendChild(tag);
    tag.setAttribute("aria-pressed", "false");
    // Check if any selected tag is left
    if (getSelectedTagsDiv().children.length > 0) {
      // Run the search from the selected tags immediately in the background
      refreshResults();
    } else {
      // Reset the "Show All" button (on a phone the visitor presses it to see the unfiltered list again)
      resetFilterHeaderButton();
      // On a wide screen the list is visible: refresh it now, without filter
      if (isWideLayout()) {
        loadAllResults();
      }
    }
  } else {
    // Was available: add it to the selected tags
    getSelectedTagsDiv().appendChild(tag);
    tag.setAttribute("aria-pressed", "true");
    // Run the search from the selected tags immediately in the background
    refreshResults();
  }
}

/** Back to the initial state of the results panel and its button */
function resetFilterHeaderButton() {
  setResultsLoading("none");
  setResultsLoadingTime(0);
  getFilterHeaderButtonDiv().disabled = false;
  getFilterHeaderButtonDiv().innerHTML = "Show All";
}

/** From a NodeList of tag divs, the comma separated tag labels */
function getTagsLabels(tags) {
  // NodeList -> array -> labels (the text of each div) -> "a,b,c"
  return Array.from(tags).map(tag => tag.innerHTML.trim()).join(',');
}

/** Update the "Show All" button with the number of results */
function refreshShowResultsButton() {
  // Get the number of results: each result row contains one .resultsCounter element
  const nbResults = document.getElementsByClassName("resultsCounter").length;
  // Change the button label/text
  if (nbResults == 0) {
    getFilterHeaderButtonDiv().innerHTML = "No result";
    getFilterHeaderButtonDiv().disabled = true;
  } else {
    getFilterHeaderButtonDiv().disabled = false;
    getFilterHeaderButtonDiv().innerHTML = "Show " + nbResults + " results";
  }
}

/* =========================================================================
 * Details of one place
 * ========================================================================= */

/** Fetch the details fragment (e.g. /restaurants/details/12) and put it in the details div */
function ajaxDetails(url) {
  getDetailsDiv().innerHTML = "Loading...";
  console.info("GET: " + url);
  fetch(url)
    .then(response => response.ok ? response.text() : Promise.reject(response.status))
    .then(html => { getDetailsDiv().innerHTML = html; })
    .catch(() => {
      getDetailsDiv().innerHTML = "Sorry, detailed data could not be retrieved. Check your Internet connection and try again.";
    });
}

/** Called when a result name is clicked: load the details (unless already loaded) and show them instead of the list */
function showDetails(url, placeId) {
  // Only fetch if a different place than the one currently displayed
  if (placeId != getCurrentDetailsId()) {
    ajaxDetails(url);
  }
  // Swap the two panels (on a phone; side by side on a wide screen)
  if (isWideLayout()) {
    return;
  }
  getDetailsDiv().style.display = "block";
  getContentDiv().style.display = "none";
}

/** "BACK" button: swap the panels back */
function hideDetails() {
  if (isWideLayout()) {
    return;
  }
  getDetailsDiv().style.display = "none";
  getContentDiv().style.display = "flex";
}

/** Id of the place currently in the details div (from the detailId attribute of its name), or null */
function getCurrentDetailsId() {
  const name = document.getElementById("detailName");
  return name == null ? null : name.getAttribute("detailId");
}

/** "Copy URL" button: put the given string in the clipboard */
function copyStringToClipboard(str) {
  console.info("copyStringToClipboard: " + str);
  // Modern API, only available on HTTPS or localhost
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(str).catch(e => console.warn("Clipboard write failed: " + e));
    return;
  }
  // Fallback for plain HTTP: put the text in an invisible textarea, select it and copy
  const el = document.createElement('textarea');
  el.value = str;
  el.setAttribute('readonly', '');
  el.style.position = 'absolute';
  el.style.left = '-9999px';
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
}

/* =========================================================================
 * Lunch suggestion (home page)
 * ========================================================================= */

/** Where the suggestion is displayed */
function getSuggestionDiv() {
  return document.getElementById("suggestion");
}

/** "Get a suggestion" / "Try again" button: fetch a sentence from the server and show it */
function ajaxSuggestion() {
  // The suggestion needs the position too
  startGeolocation();
  // The URL is set by Blade on the suggestion div, the position comes from the browser
  const url = getSuggestionDiv().dataset.suggestionUrl + '?position=' + getLocation();
  console.info("GET: " + url);
  fetch(url)
    .then(response => response.ok ? response.text() : Promise.reject(response.status))
    .then(html => {
      // Replace the box content with the suggestion and a button to ask again
      getSuggestionDiv().innerHTML = html + '<button type="button" id="suggestButton" onclick="ajaxSuggestion()">Try again</button>';
    })
    .catch(() => {
      getSuggestionDiv().innerHTML = "Err(or), nothing from the top of my head, sorry.";
    });
}

/* =========================================================================
 * Theme (dark or light)
 * The stylesheet has both palettes and reads data-theme on <html>. The layout sets it before the first paint
 * from localStorage ("theme": "dark" or "light") or, without a choice, from the OS preference.
 * ========================================================================= */

/** The colour of the browser bar for each theme (same as the page background in the stylesheet) */
const THEME_COLORS = { dark: "#141422", light: "#f4f4f8" };

/** What the OS prefers: "light" or "dark" */
function systemTheme() {
  return window.matchMedia("(prefers-color-scheme: light)").matches ? "light" : "dark";
}

/** The theme shown now (set on <html> by the layout) */
function currentTheme() {
  return document.documentElement.dataset.theme === "light" ? "light" : "dark";
}

/** Shows the given theme: <html> attribute, browser bar colour, and the icon/label of the header button */
function applyTheme(theme) {
  // Anything but "light" (including a tampered localStorage value) is the dark theme
  theme = theme === "light" ? "light" : "dark";
  document.documentElement.dataset.theme = theme;
  const meta = document.querySelector('meta[name="theme-color"]');
  if (meta) {
    meta.setAttribute("content", THEME_COLORS[theme]);
  }
  const button = document.getElementById("themeButton");
  if (button) {
    // The button shows what you get by pressing it: a sun on the dark theme, a moon on the light one
    const next = theme === "dark" ? "light" : "dark";
    button.textContent = theme === "dark" ? "\u2600\uFE0E" : "\u263E";
    button.setAttribute("aria-label", "Switch to the " + next + " theme");
    button.title = "Switch to the " + next + " theme";
  }
}

/** The header button: switch to the other theme and remember it */
function toggleTheme() {
  const next = currentTheme() === "dark" ? "light" : "dark";
  store("theme", next);
  applyTheme(next);
}

/* =========================================================================
 * Page start
 * ========================================================================= */

// Theme: the layout already set data-theme before the paint; this fills the button and the bar colour.
// Without a stored choice, follow the OS when it changes (e.g. a phone switching to dark at sunset).
applyTheme(readStore("theme") || systemTheme());
try {
  window.matchMedia("(prefers-color-scheme: light)").addEventListener("change", () => {
    if (!readStore("theme")) {
      applyTheme(systemTheme());
    }
  });
} catch (e) {
  // Old Safari has no addEventListener on a media query: then the theme just does not follow the OS live
}

// "defer" guarantees the HTML is parsed when this runs. On the place pages (the ones with a results URL)
// start following the position right away; the home page starts it when a button is clicked instead,
// so the permission prompt does not appear before the visitor did anything.
if (getResultsUrl() !== null) {
  startGeolocation();
  // Show the remembered sort order and tag operator on their buttons
  markSortButton(readStore("orderBy") || "distance");
  markOperatorButton(getTagsOperator());
  // On a wide screen the results panel is visible from the start: fill it instead of waiting for "Show All"
  if (isWideLayout()) {
    getResults();
  }
}

// Keyboard: Enter or Space on an element with role="button" (the result names) acts like a click.
// Real <button> elements do this by themselves.
document.addEventListener("keydown", event => {
  if ((event.key === "Enter" || event.key === " ") && event.target.getAttribute("role") === "button") {
    event.preventDefault(); // Space would scroll the page
    event.target.click();
  }
});

// Register the service worker (public/sw.js): makes the app installable and usable offline.
// Needs HTTPS or localhost, like geolocation. Browsers without support simply skip this.
if ('serviceWorker' in navigator) {
  // The asset versions (data attributes of <html>) travel in the worker's URL: it pre-caches those exact files
  const versions = document.documentElement.dataset;
  navigator.serviceWorker.register('/sw.js?css=' + (versions.cssVersion || '') + '&js=' + (versions.jsVersion || ''))
    .catch(e => console.warn('Service worker registration failed: ' + e));
}
