<!-- Javascript for handling results and filters display -->
<script>

function showResults() {
  var mainFilter = document.getElementById("mainFilter");
  var mainResults = document.getElementById("mainResults");
  mainFilter.style.display = "none";
  mainResults.style.display = "flex"; // was block
}

function showFilter() {
  var mainFilter = document.getElementById("mainFilter");
  var mainResults = document.getElementById("mainResults");
  mainFilter.style.display = "flex"; // was block
  mainResults.style.display = "none";
}

function getResults() {
  // This function should exist (maybe in parent/main)
  showResults();
  // Refresh position
  getLocation();
  // Get all if no places loaded
  if (getResultsLoading() == "none" && getResultsLoadingTime() == 0) {
    setResultsLoading("all");
    getResultsDiv().innerHTML = "Loading ALL entries. Please wait or select a tag...";
    var xhr = new XMLHttpRequest();
    var url = resultsUrl
            + '?position=' + getLocation();
    console.info('calling: '+url),
    xhr.open('GET', resultsUrl);
    xhr.onload = function() {
        if (getResultsLoading() == "all") {
            if (xhr.status === 200) {
                getResultsDiv().innerHTML = xhr.responseText;
                // We allow this query just once
                setResultsLoadingTime(1);
            }
            else {
                getResultsDiv().innerHTML = "Sorry, an error occured during the search. Check your Internet connection and try again.";
            }
            setResultsLoading("none");
        }
    };
    xhr.send();
  }
}

/** Where the results would be displayed */
function getResultsDiv() {
  return document.getElementById("results");
}
/** Could be none, all, tag */
function getResultsLoading() {
  console.info('getResultsLoading: ' + getResultsDiv().getAttribute("loading"));
  return getResultsDiv().getAttribute("loading");
}
/** State could be none, all, tag */
function setResultsLoading(state) {
  console.info('setResultsLoading: ' + state);
  return getResultsDiv().setAttribute("loading", state);
}
/** results loading time methods */
function nowStamp() {
  if (!Date.now) {
      return new Date().getTime();
  }
  return Date.now();
}
function getResultsLoadingTime() {
  return getResultsDiv().getAttribute("time");
}
function setResultsLoadingTime(time) {
  return getResultsDiv().setAttribute("time", time);
}

/** Where the details would be displayed */
function getDetailsDiv() {
  return document.getElementById("mainDetails");
}

</script>
