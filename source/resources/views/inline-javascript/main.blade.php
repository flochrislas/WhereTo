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
  if (isResultEmpty()) {
    var xhr = new XMLHttpRequest();
    var url = resultsUrl
            + '?position=' + getLocation();
    console.info('calling: '+url),
    xhr.open('GET', resultsUrl);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById("results").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
  }
}

/** Check if there are places in the results div */
function isResultEmpty() {
  return document.getElementById("results").innerHTML == 'empty';
}

</script>
