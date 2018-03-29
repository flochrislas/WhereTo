<div id="position-report"></div>

<div>Filtered {{ $restaurants->count() }} restaurants around Shibuya</div>

<table>
@foreach ($restaurants as $restaurant)
  <tr>
      <td id="nameAndType">
        <a href="{{ route('restaurants.details',$restaurant->id) }}">
        <div class="restaurantName">{{ $restaurant->name }}</div>
        <div class="restaurantType">{{ $restaurant->type }}</div>
        </a>
      </td>
      <td class="center">
        <a target="_blank"
          href="{{ $restaurant->google_maps_link }}">
          {{ App\GeoUtils::walkingTime($restaurant->currentDistance/1000) }}mn
        </a>
      </td>
      <td>
        <table class="center">
          <tr>
            <th>lunch</th>
            <th>food</th>
            <th>place</th>
            <th>price</th>
            <th>date</th>
          </tr>
          <tr>
            <td>{{ $restaurant->score_lunch }}</td>
            <td>{{ $restaurant->score_food }}</td>
            <td>{{ $restaurant->score_place }}</td>
            <td>{{ $restaurant->score_price }}</td>
            <td>{{ $restaurant->score_date }}</td>
          </tr>
        </table>
      </td>
  </tr>
@endforeach
</table>

<!-- Javascript for geolocaisation -->
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
