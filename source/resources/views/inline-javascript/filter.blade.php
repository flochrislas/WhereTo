<!-- The Javascript to operate this filter -->
<script>
/** What happens when we click on a tag */
function moveTag(event) {
  // Refresh position (async and takes a while...)
  getLocation();

  // Move the tag in the proper div
  if (event.target.parentNode.id == "selectedTags") {
    document.getElementById("tags").appendChild(event.target);
    // Check if any selectedTag left
    if (getSelectedTagsDiv().children.length > 0) {
      /* Run the search from the selectedTags immediately in the background */
      refreshResults();
    }
    else {
      // Reset the showResults button
      resetFilterHeaderButton();
    }
  } else {
    getSelectedTagsDiv().appendChild(event.target);
    /* Run the search from the selectedTags immediately in the background */
    refreshResults();
  }
}

function resetFilterHeaderButton() {
  setResultsLoading("none");
  setResultsLoadingTime(0);
  getFilterHeaderButtonDiv().disabled = false;
  getFilterHeaderButtonDiv().innerHTML = "Show All";
}

/** Refreshes the list of results */
function refreshResults(orderBy) {
  // Default parameter doc: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/Default_parameters
  /* read all the div under selectedTags */
  var tags = getSelectedTagsDiv().children;
  /* make a list of all their ids or labels to send as parameters */
  var tagsLabels = getTagsLabels(tags);

  /* check for orderBy choice */
  if (orderBy != null) {
    // priority to the current parameter
    store("orderBy", orderBy);
  } else {
    // if no param then try read store
    var storeOrderBy = readStore("orderBy");
    if (storeOrderBy != null) {
      orderBy = storeOrderBy;
    } else {
      // if nothing in param or store use default value
      orderBy = "distance";
      store("orderBy", orderBy);
    }
  }

  /* read order/operand/options parameters */
  // We will implement that later
  var op = 'AND';

  /* AJAX request to update the results */
  ajaxResults(op, tagsLabels, orderBy);
}

/** Actually send the request to the server */
function ajaxResults(op, tags, orderBy) {
  var xhr = new XMLHttpRequest();
  var url = resultsUrl
          + '?op=' + op
          + '&tags=' + tags
          + '&orderBy=' + orderBy
          + '&position=' + getLocation();
  xhr.open('GET', url);
  console.info("GET: " + url);
  var now = nowStamp();
  setResultsLoadingTime(now);
  setResultsLoading("tag");
  xhr.onload = function() {
      if (now < getResultsLoadingTime()) { // if a more recent request has been nameAndType
          getResultsDiv().innerHTML = "Updating results...";
      }
      else {
          if (xhr.status === 200) {
              /* write results */
              getResultsDiv().innerHTML = xhr.responseText;
              /* update the show result button with the number of results */
              refreshShowResultsButton();
          }
          else {
              getResultsDiv().innerHTML = "Sorry, an error occured during the search. Check your Internet connection and try again.";
          }
          if (now == getResultsLoadingTime()) {
              setResultsLoading("none");
          }
      }
  };
  xhr.send();
}

/** From NodeList of tag div, get concatained string of tags labels */
function getTagsLabels(tags) {
  var tagsLabels = "";
  for (i = 0; i < tags.length; ++i) {
    tagsLabels = tagsLabels + tags[i].innerHTML.trim() + ',';
  }
  // Slice removes the last comma
  return tagsLabels.slice(0,-1);
}

/** From NodeList of tag div, get array of tags ids */
function getTagsIdArray(op, tags) {
  var tagsIds = [];
  for (i = 0; i < tags.length; ++i) {
    tagsIds.push(tags[i].getAttribute("tagId"));
  }
  return tagsIds;
}

function refreshShowResultsButton() {
  /* get number of results */
  // var nbResults = document.getElementById("results").children.count();
  // var nbResults = document.getElementById('resultsTable').rows.length;
  var nbResults = document.getElementsByClassName("resultsCounter").length;
  /* Change button label/text */
  if (nbResults == 0) {
    getFilterHeaderButtonDiv().innerHTML = "No result";
    getFilterHeaderButtonDiv().disabled = true;
  }
  else {
      getFilterHeaderButtonDiv().disabled = false;
      getFilterHeaderButtonDiv().innerHTML = "Show " + nbResults + " results";
  }

}

/** Store methods  TO MOVE IN A SUPERIOR CLASS */
function store(key, value) {
    // Look up for different options, chose the best
    if (typeof(Storage) !== "undefined") {
        // Code for localStorage/sessionStorage.
        localStorage.setItem(key, value);
        // sessionStorage.setItem(key value);
    } else {
        console.warn("No local storage support from the browser. Application's behavior may be affected.");
    }
}

function readStore(key) {
    return localStorage.getItem(key);
}
/** * * * * * * * * * * * * * * * * * * * * * *  */

/**** Get important elements ****/
function getFilterHeaderButtonDiv() {
    return document.getElementById("filterHeaderButton");
}
function getSelectedTagsDiv() {
    return document.getElementById("selectedTags");
}

</script>
