<!-- Javascript for geolocalisation -->
<script>
var geolocalisationMessage = document.getElementById("position-report");

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
        return readSessionStore("lastCoordinates");

    } else {
        geolocalisationMessage.innerHTML = "Geolocation is not supported by this browser.";
        console.warn("Geolocation is not supported by this browser.");
        return null;
    }
}

/** getCurrentPosition sucess */
function onSuccess(position) {
    sessionStore("lastCoordinates", position.coords.latitude + "," + position.coords.longitude);
    // For debugging purposes:
    // console.info('Geolocalisation successfull: '+ position.coords.latitude +', '+ position.coords.longitude);
    console.info('Geolocalisation successful: '+ position.coords.latitude + "," + position.coords.longitude);
}

/** getCurrentPosition failure */
function onError(error) {
    var additionalWarning = " Distances will be from the station."
    switch(error.code) {
        case error.PERMISSION_DENIED:
            geolocalisationMessage.innerHTML = "User denied the request for Geolocation." + additionalWarning
            break;
        case error.POSITION_UNAVAILABLE:
            geolocalisationMessage.innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            geolocalisationMessage.innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            geolocalisationMessage.innerHTML = "An unknown error occurred."
            break;
    }
    if (location.protocol !== "https:") {
      geolocalisationMessage.innerHTML = " Please use the HTTPS version of the service to enable geolocalisation."
    }
    else {
      geolocalisationMessage.innerHTML = " Error" + error.code +": " + error.message;
    }
    geolocalisationMessage.innerHTML += additionalWarning;
}

/** TO MOVE IN A SUPERIOR CLASS */
function sessionStore(key, value) {
  if (typeof(Storage) !== "undefined") {
    sessionStorage.setItem(key, value);
  } else {
      console.warn("No session storage support from the browser. Application's behavior may be affected.");
  }
}

function readSessionStore(key) {
    return sessionStorage.getItem(key);
}
</script>
