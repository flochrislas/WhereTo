<div id="position-report"></div>

<div id="filterHeader">
<div id="filterHeaderTitle" class="orange title">What kind of place?</div>
<button id="filterHeaderButton" onclick="getResults()">Show All</button>
</div>

<!-- selected tags for active search -->
<div id="selectedTags">
</div>

<!-- showing available tags -->
<div id="tags">
  <p>Available options</p>
  @foreach ($restaurantTags as $restaurantTag)
      <div class="tag tagType{{$restaurantTag->type}}"
            tagId="{{$restaurantTag->id}}"
            id="tagShow{{$restaurantTag->id}}"
            onClick="moveTag(event)">
        {{$restaurantTag->label}}
      </div>
  @endforeach
</div>

<!-- The Javascript to operate this filter -->
<script>
/** What happens when we click on a tag */
function moveTag(event) {
  // Move the tag in the proper div
  if (event.target.parentNode.id == "selectedTags") {
    document.getElementById("tags").appendChild(event.target);
  } else {
    document.getElementById("selectedTags").appendChild(event.target);
  }
  /* Run the search from the selectedTags immediately in the background */
  refreshResults();
}

/** Refreshes the list of results */
function refreshResults(orderBy) {
  // Default parameter doc: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/Default_parameters
  /* read all the div under selectedTags */
  var tags = document.getElementById("selectedTags").children;
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
  var url = 'restaurants/results?op=' + op
          + '&tags=' + tags
          + '&orderBy=' + orderBy;
  xhr.open('GET', url);
  xhr.onload = function() {
      if (xhr.status === 200) {
          /* write results */
          document.getElementById("results").innerHTML = xhr.responseText;
          /* update the show result button with the number of results */
          refreshShowResultsButton();
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
  var nbResults = document.getElementById('resultsTable').rows.length;
  /* Change button label/text */
  document.getElementById("filterHeaderButton").innerHTML = "Show " + nbResults + " results";
}

/* TO MOVE IN A SUPERIOR CLASS */
function store(key, value) {
  // Look up for different options, chose the best
  if (typeof(Storage) !== "undefined") {
    // Code for localStorage/sessionStorage.
    localStorage.setItem(key, value);
    // sessionStorage.setItem(key value);
  } else {
      // Sorry! No Web Storage support..
  }
}

function readStore(key) {
    localStorage.getItem(key);
}

</script>
