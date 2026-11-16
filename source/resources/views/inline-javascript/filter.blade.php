<!-- The Javascript to operate the tag filter -->
<script>
/** What happens when we click on a tag: it moves between the available and selected lists, then the results refresh */
function moveTag(event) {
  // Refresh the position (async and takes a while...)
  getLocation();

  // Move the tag in the proper div
  if (event.target.parentNode.id == "selectedTags") {
    // Was selected: back to the available tags
    document.getElementById("tags").appendChild(event.target);
    // Check if any selected tag is left
    if (getSelectedTagsDiv().children.length > 0) {
      // Run the search from the selected tags immediately in the background
      refreshResults();
    } else {
      // Reset the "Show All" button
      resetFilterHeaderButton();
    }
  } else {
    // Was available: add it to the selected tags
    getSelectedTagsDiv().appendChild(event.target);
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

  // Operator between the tags. Other operators (OR, ANDNOT, ORNOT) exist server-side but are not exposed yet
  const op = 'AND';

  // AJAX request to update the results
  ajaxResults(op, tagsLabels, orderBy);
}

/** Request the results fragment. Responses older than the latest request are ignored. */
function ajaxResults(op, tags, orderBy) {
  // Build the URL; resultsUrl is set by the page (e.g. /restaurants/results), the values are URL-encoded
  const url = resultsUrl
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
      if (now < getResultsLoadingTime()) { // a more recent request is in flight: ignore this response
        getResultsDiv().innerHTML = "Updating results...";
        return;
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

/**** Important elements ****/
function getFilterHeaderButtonDiv() {
    return document.getElementById("filterHeaderButton");
}
function getSelectedTagsDiv() {
    return document.getElementById("selectedTags");
}

</script>
