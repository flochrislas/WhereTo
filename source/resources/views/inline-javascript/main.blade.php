<!-- Javascript for handling results and filters display -->
<script>

function showResults() {
  getMainFilterDiv().style.display = "none";
  getMainResultsDiv().style.display = "flex";
}

function showFilter() {
  getMainFilterDiv().style.display = "flex";
  getMainResultsDiv().style.display = "none";
}

/** "Show All" button: load every place once (sorted by distance) and switch to the results panel. */
function getResults() {
  showResults();
  getLocation();
  if (getResultsLoading() == "none" && getResultsLoadingTime() == 0) {
    setResultsLoading("all");
    getResultsDiv().innerHTML = "Loading ALL entries. Please wait or select a tag...";
    const url = resultsUrl + '?position=' + getLocation() + '&orderBy=distance';
    console.info('GET: ' + url);
    fetch(url)
      .then(response => response.ok ? response.text() : Promise.reject(response.status))
      .then(html => {
        if (getResultsLoading() == "all") {
          getResultsDiv().innerHTML = html;
          setResultsLoadingTime(1); // this query is allowed just once
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
/** Loading state: none, all, tag */
function getResultsLoading() {
  return getResultsDiv().getAttribute("loading");
}
function setResultsLoading(state) {
  return getResultsDiv().setAttribute("loading", state);
}
function nowStamp() {
  return Date.now();
}
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
function getDetailsDiv() {
  return document.getElementById("mainDetails");
}

</script>
