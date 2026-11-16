<!-- The Javascript to operate the tag filter -->
<script>
/** Clicking a tag moves it between the available and selected lists, then refreshes the results. */
function moveTag(event) {
  getLocation();
  if (event.target.parentNode.id == "selectedTags") {
    document.getElementById("tags").appendChild(event.target);
    if (getSelectedTagsDiv().children.length > 0) {
      refreshResults();
    } else {
      resetFilterHeaderButton();
    }
  } else {
    getSelectedTagsDiv().appendChild(event.target);
    refreshResults();
  }
}

function resetFilterHeaderButton() {
  setResultsLoading("none");
  setResultsLoadingTime(0);
  getFilterHeaderButtonDiv().disabled = false;
  getFilterHeaderButtonDiv().innerHTML = "Show All";
}

/** Refreshes the list of results. orderBy: distance | ratings | cost (remembered in localStorage). */
function refreshResults(orderBy) {
  const tagsLabels = getTagsLabels(getSelectedTagsDiv().children);

  if (orderBy != null) {
    store("orderBy", orderBy);
  } else {
    orderBy = readStore("orderBy");
    if (orderBy == null) {
      orderBy = "distance";
      store("orderBy", orderBy);
    }
  }

  const op = 'AND'; // other operators (OR, ANDNOT, ORNOT) exist server-side but are not exposed yet
  ajaxResults(op, tagsLabels, orderBy);
}

/** Request the results fragment. Responses older than the latest request are ignored. */
function ajaxResults(op, tags, orderBy) {
  const url = resultsUrl
          + '?op=' + encodeURIComponent(op)
          + '&tags=' + encodeURIComponent(tags)
          + '&orderBy=' + encodeURIComponent(orderBy)
          + '&position=' + getLocation();
  console.info("GET: " + url);
  const now = nowStamp();
  setResultsLoadingTime(now);
  setResultsLoading("tag");
  fetch(url)
    .then(response => response.ok ? response.text() : Promise.reject(response.status))
    .then(html => {
      if (now < getResultsLoadingTime()) { // a more recent request is in flight
        getResultsDiv().innerHTML = "Updating results...";
        return;
      }
      getResultsDiv().innerHTML = html;
      refreshShowResultsButton();
    })
    .catch(() => {
      if (now >= getResultsLoadingTime()) {
        getResultsDiv().innerHTML = "Sorry, an error occurred during the search. Check your Internet connection and try again.";
      }
    })
    .finally(() => {
      if (now == getResultsLoadingTime()) {
        setResultsLoading("none");
      }
    });
}

/** From a NodeList of tag divs, the comma separated tag labels */
function getTagsLabels(tags) {
  return Array.from(tags).map(tag => tag.innerHTML.trim()).join(',');
}

function refreshShowResultsButton() {
  const nbResults = document.getElementsByClassName("resultsCounter").length;
  if (nbResults == 0) {
    getFilterHeaderButtonDiv().innerHTML = "No result";
    getFilterHeaderButtonDiv().disabled = true;
  } else {
    getFilterHeaderButtonDiv().disabled = false;
    getFilterHeaderButtonDiv().innerHTML = "Show " + nbResults + " results";
  }
}

/** localStorage helpers */
function store(key, value) {
  try {
    localStorage.setItem(key, value);
  } catch (e) {
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

/**** Important elements ****/
function getFilterHeaderButtonDiv() {
    return document.getElementById("filterHeaderButton");
}
function getSelectedTagsDiv() {
    return document.getElementById("selectedTags");
}

</script>
