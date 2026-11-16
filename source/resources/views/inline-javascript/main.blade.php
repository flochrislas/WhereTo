<!-- Javascript for handling results and filters display -->
<script>

/** Switch from the tags panel to the results panel */
function showResults() {
  getMainFilterDiv().style.display = "none";
  getMainResultsDiv().style.display = "flex";
}

/** Switch from the results panel back to the tags panel */
function showFilter() {
  getMainFilterDiv().style.display = "flex";
  getMainResultsDiv().style.display = "none";
}

/** "Show All" button: load every place once (sorted by distance) and switch to the results panel. */
function getResults() {
  showResults();
  // Refresh the position (async)
  getLocation();
  // Get all the places only if nothing has been loaded yet (loading time 0 = initial state)
  if (getResultsLoading() == "none" && getResultsLoadingTime() == 0) {
    setResultsLoading("all");
    getResultsDiv().innerHTML = "Loading ALL entries. Please wait or select a tag...";
    // No tags parameter = all the places
    const url = resultsUrl + '?position=' + getLocation() + '&orderBy=distance';
    console.info('GET: ' + url);
    fetch(url)
      .then(response => response.ok ? response.text() : Promise.reject(response.status))
      .then(html => {
        // Only write if a tag search did not start in the meantime
        if (getResultsLoading() == "all") {
          getResultsDiv().innerHTML = html;
          setResultsLoadingTime(1); // we allow this query just once
        }
      })
      .catch(() => {
        getResultsDiv().innerHTML = "Sorry, an error occurred during the search. Check your Internet connection and try again.";
      })
      .finally(() => {
        if (getResultsLoading() == "all") {
          setResultsLoading("none");
        }
      });
  }
}

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

/**** Important elements ****/
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

</script>
