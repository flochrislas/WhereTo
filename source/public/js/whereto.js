/**
 * WhereTo front-end JavaScript (public pages).
 *
 * One plain script, no build step. It is loaded once by layouts/app.blade.php with "defer",
 * so it runs after the HTML is parsed and the browser can cache it between pages.
 * The functions are global on purpose: the Blade views call them from onclick="..." attributes.
 *
 * Values that come from the server are read from data-* attributes:
 *   - #content[data-results-url]      URL of the results fragment, e.g. /restaurants/results
 *   - #content[data-details-url]      URL of a details fragment, PLACE_ID standing for the id of the place
 *   - #suggestion[data-suggestion-url] URL of the lunch suggestion
 *   - a.langSwitch[data-twin-base]    the same page in another language (header), e.g. /ja/restaurants
 * The URLs are in the language of the page (/ja/... on a Japanese page): the script never builds a path itself.
 * The messages it writes in the page come from window.whereToStrings, printed by the layout in the page's language.
 *
 * Sections: messages, geolocation, panels (filter/results/details), tag filter, results, details, state in the
 * address bar, suggestion, theme, language banner.
 */

/* =========================================================================
 * Messages (in the language of the page)
 * ========================================================================= */

/**
 * The message of the given key in the language of the page (window.whereToStrings, printed by the layout from
 * lang/<locale>.json), with its :placeholders replaced, e.g. t("showResults", { count: 12 }).
 * An unknown key gives the key itself, so a missing message shows up without breaking anything.
 */
function t(key, replacements) {
  let message = (window.whereToStrings || {})[key] || key;
  for (const name in replacements || {}) {
    message = message.replace(":" + name, replacements[name]);
  }
  return message;
}

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
    reportPosition(t("geolocationUnsupported"));
    console.warn("Geolocation is not supported by this browser.");
    return;
  }
  reportPosition(t("locating"));
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
  reportPosition(t("fromPosition"));

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
      message = t("geolocationDenied");
      break;
    case error.POSITION_UNAVAILABLE:
      message = t("geolocationUnavailable");
      break;
    case error.TIMEOUT:
      message = t("geolocationTimeout");
      break;
    default:
      message = t("geolocationUnknown", { error: error.code + ": " + error.message });
  }
  // Browsers refuse geolocation on plain HTTP (except localhost)
  if (location.protocol !== "https:" && location.hostname !== "localhost") {
    message = t("geolocationHttps");
  }
  console.warn("Geolocation error " + error.code + ": " + error.message);
  // Japanese sentences are not separated by a space
  const separator = document.documentElement.lang === "ja" ? "" : " ";
  reportPosition(message + separator + t("fromStation"));
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
  // textContent, not innerHTML: a message is text, never markup
  getResultsDiv().textContent = t("loadingAll");
  // The current sort order, so the list matches the highlighted sort button
  const orderBy = getOrderBy();
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
        getResultsDiv().textContent = t("searchError");
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

  // A sort button was clicked: that order from now on; otherwise the current one
  if (orderBy != null) {
    setOrderBy(orderBy);
  } else {
    orderBy = getOrderBy();
  }

  // Show which sort button is the current one
  markSortButton(orderBy);

  // Operator between the tags: AND (all of them) or OR (any of them), chosen with the "all / any" buttons.
  // ANDNOT and ORNOT exist server-side but are not exposed.
  const op = getTagsOperator();

  // AJAX request to update the results
  ajaxResults(op, tagsLabels, orderBy);
  // The address bar and the language switch follow the sort order
  syncState();
}

/**
 * The sort order and the tag operator of the page. They live here, in the page: localStorage only remembers them
 * for the next visit, and may be unavailable (private mode, storage disabled), in which case the page still
 * keeps what the visitor chose or what the address said.
 */
const state = { orderBy: null, op: null };

/** The current sort order: distance (default) | ratings | cost, the remembered one at first */
function getOrderBy() {
  if (state.orderBy === null) {
    const remembered = readStore("orderBy");
    state.orderBy = ["distance", "ratings", "cost"].includes(remembered) ? remembered : DEFAULT_ORDER_BY;
  }
  return state.orderBy;
}

/** A new sort order: in the page, and remembered for the next visit */
function setOrderBy(orderBy) {
  state.orderBy = orderBy;
  store("orderBy", orderBy);
}

/** The operator between the selected tags: "AND" (default) or "OR", the remembered one at first */
function getTagsOperator() {
  if (state.op === null) {
    state.op = readStore("op") === "OR" ? "OR" : DEFAULT_OP;
  }
  return state.op;
}

/** A new operator: in the page, and remembered for the next visit */
function rememberTagsOperator(op) {
  state.op = op;
  store("op", op);
}

/** "all" / "any" buttons: remember the operator, show it on the buttons, and refresh the results if tags are selected */
function setTagsOperator(op) {
  rememberTagsOperator(op);
  markOperatorButton(op);
  if (getSelectedTagsDiv().children.length > 0) {
    refreshResults(); // which also updates the address bar
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
        getResultsDiv().textContent = t("searchError");
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
  // A tag shown in the details of a place (not in the filter): select its twin in the available tags, if any.
  // Found by data-tag, the English label both carry (the text may be a translation)
  if (tag.parentNode.id !== "selectedTags" && tag.parentNode.id !== "tags") {
    const twin = findFilterTag(tag.dataset.tag);
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
  // The address bar and the language switch follow the selected tags
  syncState();
}

/** The button of the given tag (data-tag, the English label) among the available tags, or undefined */
function findFilterTag(label) {
  return Array.from(document.querySelectorAll("#tags .tag")).find(button => button.dataset.tag === label);
}

/** Back to the initial state of the results panel and its button */
function resetFilterHeaderButton() {
  setResultsLoading("none");
  setResultsLoadingTime(0);
  getFilterHeaderButtonDiv().disabled = false;
  getFilterHeaderButtonDiv().textContent = t("showAll");
}

/** From a NodeList of tag buttons, the comma separated tag labels */
function getTagsLabels(tags) {
  // NodeList -> array -> labels -> "a,b,c". The label is data-tag, the English one the search knows: the text
  // shown may be a translation ("ラーメン" for data-tag="ramen")
  return Array.from(tags).map(tag => tag.dataset.tag).join(',');
}

/** Update the "Show All" button with the number of results */
function refreshShowResultsButton() {
  // Get the number of results: each result row contains one .resultsCounter element
  const nbResults = document.getElementsByClassName("resultsCounter").length;
  // Change the button label/text
  if (nbResults == 0) {
    getFilterHeaderButtonDiv().textContent = t("noResult");
    getFilterHeaderButtonDiv().disabled = true;
  } else {
    getFilterHeaderButtonDiv().disabled = false;
    getFilterHeaderButtonDiv().textContent = t("showResults", { count: nbResults });
  }
}

/* =========================================================================
 * Details of one place
 * ========================================================================= */

/** Number of the latest details request: an older one answering late may neither fill nor close the panel */
let detailsRequest = 0;

/** Fetch the details fragment (e.g. /restaurants/details/12) and put it in the details div */
function ajaxDetails(url) {
  const request = ++detailsRequest;
  getDetailsDiv().textContent = t("loading");
  console.info("GET: " + url);
  fetch(url)
    .then(response => response.ok ? response.text() : Promise.reject(response.status))
    .then(html => {
      if (request === detailsRequest) {
        getDetailsDiv().innerHTML = html;
      }
    })
    .catch(status => {
      if (request !== detailsRequest) {
        return; // another place was asked for since: its answer decides
      }
      getDetailsDiv().textContent = t("detailsError");
      // 404: no such place (an old shared address, ?place=999). Forget it: out of the address bar, and on a phone
      // back to the list instead of an error panel
      if (status === 404) {
        closeDetails();
      }
    });
}

/** The id of the place open in the details panel, null when none (kept for the address bar: ?place=12) */
var shownPlaceId = null;

/**
 * Called when a result name (a link to the standalone page) is clicked: load the details fragment (unless already
 * loaded) and show them in the page instead of following the link. A modified click (Ctrl, Cmd, Shift, Alt) or a
 * non-primary button is left to the browser: it opens the standalone page in a new tab or window, as any link.
 */
function showDetails(event, url, placeId) {
  if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) {
    return;
  }
  event.preventDefault();
  openDetails(url, placeId);
}

/** Show the details of a place in the page: fetch them (unless already there), swap the panels on a phone */
function openDetails(url, placeId) {
  // Only fetch if a different place than the one currently displayed
  if (placeId != getCurrentDetailsId()) {
    ajaxDetails(url);
  }
  shownPlaceId = String(placeId);
  syncState();
  // Swap the two panels (on a phone; side by side on a wide screen)
  if (isWideLayout()) {
    return;
  }
  getDetailsDiv().style.display = "block";
  getContentDiv().style.display = "none";
}

/** The URL of the details fragment of a place, from the template the page gives (data-details-url), or null */
function getDetailsUrl(placeId) {
  const template = getContentDiv()?.dataset.detailsUrl;
  return template ? template.replace("PLACE_ID", encodeURIComponent(placeId)) : null;
}

/** "BACK" button: swap the panels back (on a wide screen the details stay next to the list) */
function hideDetails() {
  if (isWideLayout()) {
    return;
  }
  closeDetails();
}

/** No place open any more: it leaves the address bar, and on a phone the list comes back */
function closeDetails() {
  shownPlaceId = null;
  syncState();
  if (isWideLayout() || getContentDiv() === null) {
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
 * State of a filter page in the address bar (doc/12-multilingual-ui.md)
 * The selected tags, the operator, the sort order and the open place live in the page. They are also written in
 * the query string (?tags=ramen,noodles&op=OR&orderBy=cost&place=12), so that the address bar can be shared or
 * bookmarked, and so that the language switch opens the other language in the same state. A page opened with
 * such a query string starts in that state (restoreState).
 * ========================================================================= */

/** The defaults, left out of the query string so that a plain page keeps its plain address */
const DEFAULT_OP = "AND";
const DEFAULT_ORDER_BY = "distance";

/** The state of the page as a query string ("?tags=ramen&place=12"), or "" for the default state */
function stateQuery() {
  // URLSearchParams builds a query string and encodes the values (a tag with a space or an "&" stays intact)
  const params = new URLSearchParams();
  const tags = getTagsLabels(getSelectedTagsDiv().children);
  if (tags !== "") {
    params.set("tags", tags);
    // The operator only matters with tags
    if (getTagsOperator() !== DEFAULT_OP) {
      params.set("op", getTagsOperator());
    }
  }
  const orderBy = getOrderBy();
  if (orderBy !== DEFAULT_ORDER_BY) {
    params.set("orderBy", orderBy);
  }
  if (shownPlaceId !== null) {
    params.set("place", shownPlaceId);
  }
  // The commas between the tags stay readable (%2C is a comma, decoded the same way)
  const query = params.toString().replace(/%2C/gi, ",");
  return query === "" ? "" : "?" + query;
}

/**
 * Write the state of the page in the address bar and in the language switch links. Called on every change: tag,
 * operator, sort order, place opened or closed. replaceState changes the address without a new history entry
 * (the back button still leaves the page) and without loading anything.
 */
function syncState() {
  if (getResultsUrl() === null) {
    return; // not a filter page: the address and the switch links are already right
  }
  const query = stateQuery();
  history.replaceState(history.state, "", location.pathname + query);
  // The switch links: the twin page (data-twin-base, printed by the server) with the same state
  document.querySelectorAll("a[data-twin-base]").forEach(link => {
    link.href = link.dataset.twinBase + query;
  });
}

/**
 * Start in the state the query string gives, if any: select the tags, set the operator and the sort order (they
 * are also remembered, as if chosen on the buttons). Without any of them the remembered choices stay.
 * Returns the place to open (its id as a string) or null, and whether tags were selected.
 */
function restoreState() {
  const params = new URLSearchParams(location.search);
  const known = ["tags", "op", "orderBy", "place"].some(name => params.has(name));
  if (!known) {
    return { tags: false, place: null };
  }
  // A shared address carries the whole state: what it leaves out is the default, not this browser's last choice.
  // The operator is only in the address with tags (stateQuery): without tags, keep the remembered one
  if (params.has("tags")) {
    rememberTagsOperator(params.get("op") === "OR" ? "OR" : DEFAULT_OP);
  }
  const orderBy = params.get("orderBy");
  setOrderBy(["distance", "ratings", "cost"].includes(orderBy) ? orderBy : DEFAULT_ORDER_BY);
  let selected = false;
  (params.get("tags") || "").split(",").forEach(label => {
    const tag = findFilterTag(label.trim());
    if (tag) {
      // As moveTag does, without a search per tag: the caller searches once
      getSelectedTagsDiv().appendChild(tag);
      tag.setAttribute("aria-pressed", "true");
      selected = true;
    }
  });
  // Digits only: the id goes into a URL
  const place = /^\d+$/.test(params.get("place") || "") ? params.get("place") : null;
  return { tags: selected, place: place };
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
      getSuggestionDiv().innerHTML = html;
      const again = document.createElement("button");
      again.type = "button";
      again.id = "suggestButton";
      again.textContent = t("tryAgain");
      again.onclick = ajaxSuggestion;
      getSuggestionDiv().appendChild(again);
    })
    .catch(() => {
      getSuggestionDiv().textContent = t("suggestionError");
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
    const label = t(next === "light" ? "switchToLight" : "switchToDark");
    button.setAttribute("aria-label", label);
    button.title = label;
  }
}

/** The header button: switch to the other theme and remember it */
function toggleTheme() {
  const next = currentTheme() === "dark" ? "light" : "dark";
  store("theme", next);
  applyTheme(next);
}

/* =========================================================================
 * Language banner
 * When the browser prefers another language of the site than the page's, a one-line banner offers it ("日本語で見る").
 * Shown once per browser: following it or closing it remembers the answer (localStorage). No redirection, no cookie:
 * the page is the same for everyone, the banner is built here from the header's language switch links.
 * ========================================================================= */

/** The localStorage key of the answer */
const LANGUAGE_BANNER_KEY = "languageBannerDismissed";

/**
 * The switch link to show in the banner: the first language of the browser (navigator.languages, most preferred
 * first) that the site has; none when that language is the page's own, or none is available.
 */
function preferredLanguageLink() {
  const pageLanguage = document.documentElement.lang;
  const links = Array.from(document.querySelectorAll("a.langSwitch[hreflang]"));
  const browserLanguages = navigator.languages && navigator.languages.length ? navigator.languages : [navigator.language || ""];
  for (const browserLanguage of browserLanguages) {
    // "ja-JP" -> "ja": the site has one version per language, not per country
    const language = browserLanguage.toLowerCase().split("-")[0];
    if (language === pageLanguage) {
      return null; // the visitor reads the page in a language they prefer
    }
    const link = links.find(a => a.hreflang === language);
    if (link) {
      return link;
    }
  }
  return null;
}

/** Show the banner when the browser prefers another language of the site and the visitor has not answered yet */
function showLanguageBanner() {
  const banner = document.getElementById("languageBanner");
  if (banner === null || readStore(LANGUAGE_BANNER_KEY)) {
    return;
  }
  const link = preferredLanguageLink();
  if (link === null) {
    return;
  }
  const dismiss = () => store(LANGUAGE_BANNER_KEY, "1");
  // The link: the words of the banner in that language (data-banner), going where the switch link goes now
  const offer = document.createElement("a");
  offer.textContent = link.dataset.banner;
  offer.lang = link.lang;
  offer.hreflang = link.hreflang;
  offer.href = link.href;
  // The switch link may have changed since (the state of a filter page): take its address at the last moment
  offer.addEventListener("click", () => {
    offer.href = link.href;
    dismiss();
  });
  const close = document.createElement("button");
  close.type = "button";
  close.className = "languageBannerClose";
  close.textContent = "\u00D7"; // the multiplication sign, a common "close" icon
  close.setAttribute("aria-label", link.dataset.close);
  close.lang = link.lang;
  close.addEventListener("click", () => {
    dismiss();
    banner.hidden = true;
  });
  banner.replaceChildren(offer, close);
  banner.hidden = false;
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
  // A shared address (?tags=...&place=12): select its tags, operator and sort order first
  const restored = restoreState();
  // Show the remembered sort order and tag operator on their buttons
  markSortButton(getOrderBy());
  markOperatorButton(getTagsOperator());
  // The default list came with the page (nearest to the station first): show its size on the "Show All" button
  refreshShowResultsButton();
  if (restored.tags) {
    // Tags from the address: search them, and on a phone show the list they give
    refreshResults();
    showResults();
  } else {
    // Load the list again in the remembered order when that is not the default one
    if (getOrderBy() !== DEFAULT_ORDER_BY) {
      loadAllResults();
    }
    // On a wide screen the results panel is visible from the start: fill it instead of waiting for "Show All"
    if (isWideLayout()) {
      getResults();
    }
  }
  // The place of the address, in the details panel
  if (restored.place !== null && getDetailsUrl(restored.place) !== null) {
    openDetails(getDetailsUrl(restored.place), restored.place);
  }
  // The switch links get the state the page started with
  syncState();
}

// The suggestion to read the page in the browser's language (first visit only)
showLanguageBanner();

// Register the service worker (public/sw.js): makes the app installable and usable offline.
// Needs HTTPS or localhost, like geolocation. Browsers without support simply skip this.
if ('serviceWorker' in navigator) {
  // The asset versions (data attributes of <html>) travel in the worker's URL: it pre-caches those exact files
  // The languages under a prefix travel there too: the worker pre-caches their pages (/ja/restaurants...)
  const versions = document.documentElement.dataset;
  navigator.serviceWorker.register('/sw.js?css=' + (versions.cssVersion || '') + '&js=' + (versions.jsVersion || '')
      + '&locales=' + encodeURIComponent(versions.localePrefixes || ''))
    .catch(e => console.warn('Service worker registration failed: ' + e));
}
