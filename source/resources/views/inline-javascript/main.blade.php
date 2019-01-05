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
  // Get all if no places loaded
  if (isResultEmpty()) {
    var xhr = new XMLHttpRequest();
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

<!-- Javascript for geolocalisation -->
<script>
var x = document.getElementById("position-report");
var y = document.getElementById("position");
getLocation();

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
        // it is also possible to get continuously updated coords from the method watchPosition()
        // see https://www.w3schools.com/html/html5_geolocation.asp
    } else {
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    /*
    x.innerHTML = "Latitude: " + position.coords.latitude +
    "<br>Longitude: " + position.coords.longitude;
    */
    // Was y !!
    x.value = position.coords.latitude + "," + position.coords.longitude;
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            x.innerHTML = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            x.innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            x.innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            x.innerHTML = "An unknown error occurred."
            break;
    }
}
</script>
