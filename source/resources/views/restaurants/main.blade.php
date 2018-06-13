@extends('layouts.app')

@section('title', 'Restaurants')

@section('content')
<div id="content">

  <div id="restaurantsFilter">
    
    @include('restaurants.filter')
  </div>

  <div id="restaurantsResults">
    <button onclick="showFilter()">Show Filter</button>
    @include('restaurants.results')
  </div>

</div>


<script>

function showResults() {
  var restaurantsFilter = document.getElementById("restaurantsFilter");
  var restaurantsResults = document.getElementById("restaurantsResults");
  restaurantsFilter.style.display = "none";
  restaurantsResults.style.display = "block";
}

function showFilter() {
  var restaurantsFilter = document.getElementById("restaurantsFilter");
  var restaurantsResults = document.getElementById("restaurantsResults");
  restaurantsFilter.style.display = "block";
  restaurantsResults.style.display = "none";
}

function getResults() {
  showResults();
  // Get all if no restaurants loaded
  if (isResultEmpty()) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'restaurants/results');
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById("results").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
  }
}

/** Check if there are restaurants in the results div */
function isResultEmpty() {
  return document.getElementById("results").innerHTML == 'empty';
}

/***************************phony**********************************/
function updateResults() {
  var xhr = new XMLHttpRequest();
  xhr.open('PUT', 'restaurants');
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
      if (xhr.status === 200) {
          var userInfo = JSON.parse(xhr.responseText);
          document.getElementById("demo").innerHTML = xhr.responseText;
      }
  };
  xhr.send(JSON.stringify({
      name: 'John Smith',
      age: 34
  }));
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
    y.value = position.coords.latitude + "," + position.coords.longitude;
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

@endsection
