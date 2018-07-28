<table id="resultsTable">
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
          <div class="oneDigit inline">
          {{ App\GeoUtils::walkingTime($restaurant->currentDistance/1000) }}
        </div>
          <br /><div class="minute inline">min</div>
        </a>
      </td>
      <td>
        <table class="center">
          <tr>
            <th class="label">price</th>
            <th class="label">lunch</th>
            <th class="label">food</th>
            <th class="label">place</th>
            <th class="label">price</th>
            <th class="label">date</th>
          </tr>
          <tr>
            <td class="oneDigit">{{ $restaurant->lunch_price }}</td>
            <td class="oneDigit">{{ $restaurant->score_lunch }}</td>
            <td class="oneDigit">{{ $restaurant->score_food }}</td>
            <td class="oneDigit">{{ $restaurant->score_place }}</td>
            <td class="oneDigit">{{ $restaurant->score_price }}</td>
            <td class="oneDigit">{{ $restaurant->score_date }}</td>
          </tr>
        </table>
      </td>
  </tr>
@endforeach
</table>
<div id="resultsEnd">End of <strong>{{ $restaurants->count() }}</strong> amazing results</div>
