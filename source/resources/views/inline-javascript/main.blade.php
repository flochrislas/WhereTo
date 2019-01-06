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
    var x = document.getElementById("position-report");
    var url = resultsUrl
            + '?position=' + x.value;
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

<!-- Javascript for geolocalisation -->
<script>
var x = document.getElementById("position-report");
// var y = document.getElementById("position");
getLocation();

/** Options for getCurrentPosition. Time in ms */
var getCurrentPositionOptions = {
  enableHighAccuracy: true,
  timeout: 5500,
  maximumAge: 1500
};

function getLocation() {
    if (navigator.geolocation) {
        // This needs valid HTTPS age, or localhost, to work in Chrome!!!
        // And beware the timeout or delay!!!
        navigator.geolocation.getCurrentPosition(onSuccess, onError, getCurrentPositionOptions);
        // it is also possible to get continuously updated coords from the method watchPosition()
        // see https://www.w3schools.com/html/html5_geolocation.asp
        // Console test:
        // navigator.geolocation.getCurrentPosition(function(position){console.info(position.coords.latitude+', '+position.coords.longitude)},function(error){console.warn(error.code+': '+error.message)});
    } else {
        x.innerHTML = "Geolocation is not supported by this browser.";
        console.warn("Geolocation is not supported by this browser.");
    }
}

/** getCurrentPosition sucess */
function onSuccess(position) {
    /*
    x.innerHTML = "Latitude: " + position.coords.latitude +
    "<br>Longitude: " + position.coords.longitude;
    */
    // TODO: use session store ?
    x.value = position.coords.latitude + "," + position.coords.longitude;
    // For debugging purposes:
    // console.info('Geolocalisation successfull: '+ position.coords.latitude +', '+ position.coords.longitude);
    console.info('Geolocalisation: '+ x.value);
}

/** getCurrentPosition failure */
function onError(error) {
    console.warn('Geolocalisation Failure (${error.code}): ${error.message}');
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
    if (error.message.startsWith("Only secure")) {
      x.innerHTML = " Please use the HTTPS version of the service to enable geolocalisation."
    }
    else {
      x.innerHTML = " ERREUR (${error.code}): ${error.message}"
    }
}
</script>
