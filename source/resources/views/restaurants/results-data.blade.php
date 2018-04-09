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
