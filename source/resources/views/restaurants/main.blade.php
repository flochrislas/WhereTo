@extends('layouts.app')

@section('title', 'Restaurant Index')

@section('content')
<div id="content">

    <div id="position-report"></div>

    <h2>{{ $restaurants->count() }} Restaurants</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <!-- Auto complete Experimental -->
    <br>Filter:<br>
    <input  type="search"
            name="tag-input"
            id="tag-input"
            placeholder=""
            data-action="{{ route('tags.autocomplete') }}" />
    <br>
    <input type="text" id="tag-selection"><br>
    <script type="text/javascript">
      $('#tag-input').each(function() {
          var $this = $(this);
          var src = $this.data('action');

          $this.autocomplete({
              source: src,
              minLength: 2,
              select: function(event, ui) {
                  $this.val(ui.item.value);
                  $('#tag-selection').val(ui.item.id);
                  $('#tag-search').val(ui.item.value);
              }lass="btn btn-info"
          });
      });
    </script>

    <br>

    <!-- SEARCH http://justlaravel.com/search-functionality-laravel/ -->
    <form action="" method="GET" role="search" name="mainSearch">
        {{ csrf_field() }}
        <input  type="search"
                name="tags"
                id="tag-search"
                value="{!! old('tags') !!}"
                placeholder="Search tags">
        <input type="hidden" name="op" value="AND">
        <input type="hidden" name="position" id="position">
        <button type="submit">
          <!-- search icon -->
        </button>
    </form>

    <table>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Tags</th>
            <th>distance (m)</th>
            <th>time (mn)</th>
            <th>map</th>
            <th>tabelog</th>
            <th>official</th>
            <th>lunch</th>
            <th>food</th>
            <th>place</th>
            <th>price</th>
            <th>date</th>
            <th>Details</th>
        </tr>
    @foreach ($restaurants as $restaurant)
      <tr>
          <td>{{ $restaurant->name }}</td>
          <td>{{ $restaurant->type }}</td>
          <td>
            @foreach ($restaurant->tagsCached() as $tag)
              {{ $tag->label }} |
            @endforeach
          </td>
          <td>{{ App\GeoUtils::formatDistance($restaurant->currentDistance) }}</td>
          <td>{{ App\GeoUtils::walkingTime($restaurant->currentDistance/1000) }}</td>
          <td>
            <a target="_blank"
              href="{{ $restaurant->google_maps_link }}">
              map</a>
          </td>
          <td>
            <a target="_blank"
              href="{{ $restaurant->tabelog_link }}">
              link</a>
          </td>
          <td>
            <a target="_blank"
              href="{{ $restaurant->official_website }}">
              site</a>
          </td>
          <td>{{ $restaurant->score_lunch }}</td>
          <td>{{ $restaurant->score_food }}</td>
          <td>{{ $restaurant->score_place }}</td>
          <td>{{ $restaurant->score_price }}</td>
          <td>{{ $restaurant->score_date }}</td>
          <td>
              <a href="{{ route('restaurants.details',$restaurant->id) }}">Show</a>
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
</div>
@endsection
