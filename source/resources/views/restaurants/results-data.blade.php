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
        <div class="Rtable Rtable--6cols">
          <!-- labels -->
          <div class="Rtable-cell label">price</div>
          <div class="Rtable-cell label">lunch</div>
          <div class="Rtable-cell label">food</div>
          <div class="Rtable-cell label">place</div>
          <div class="Rtable-cell label">price</div>
          <div class="Rtable-cell label">date</div>
          <!-- scores -->
          <div class="Rtable-cell oneDigit">{{ $restaurant->lunch_price }}</div>
          <div class="Rtable-cell oneDigit">{{ $restaurant->score_lunch }}</div>
          <div class="Rtable-cell oneDigit">{{ $restaurant->score_food }}</div>
          <div class="Rtable-cell oneDigit">{{ $restaurant->score_place }}</div>
          <div class="Rtable-cell oneDigit">{{ $restaurant->score_price }}</div>
          <div class="Rtable-cell oneDigit">{{ $restaurant->score_date }}</div>
        </div>

        <!--
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
      -->
      </td>
  </tr>
@endforeach
</table>
<div id="resultsEnd">End of <strong>{{ $restaurants->count() }}</strong> amazing results</div>

<BR><BR><BR><BR>

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



<!-- --- -- -- ---- -- -- --- --- -- -->
<BR><BR><BR><BR>
<div class="resultTable">
@foreach ($restaurants as $restaurant)
  <div class="resultRow">
    <div class="resultCell nameAndType">
      <a href="{{ route('restaurants.details',$restaurant->id) }}">
      <div class="restaurantName">{{ $restaurant->name }}</div>
      <div class="restaurantType">{{ $restaurant->type }}</div>
      </a>
    </div>
    <div class="resultCell distance centerContent">
      <a target="_blank"
         href="{{ $restaurant->google_maps_link }}">
        <div class="oneDigit inline">
        {{ App\GeoUtils::walkingTime($restaurant->currentDistance/1000) }}
      </div>
        <br /><div class="minute inline">min</div>
      </a>
    </div>
    <div class="resultCell centerContent">
      <div class="score">
        <div class="label">price</div>
        <div class="oneDigit">{{ $restaurant->lunch_price }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score">
        <div class="label">lunch</div>
        <div class="oneDigit">{{ $restaurant->score_lunch }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score">
        <div class="label">food</div>
        <div class="oneDigit">{{ $restaurant->score_food }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score">
        <div class="label">place</div>
        <div class="oneDigit">{{ $restaurant->score_place }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score">
        <div class="label">price</div>
        <div class="oneDigit">{{ $restaurant->score_price }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score">
        <div class="label">date</div>
        <div class="oneDigit">{{ $restaurant->score_date }}</div>
      </div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $restaurants->count() }}</strong> amazing results</div>


<!-- --- -- -- ---- -- -- --- --- -- -->
<BR><BR><BR><BR>
<div class="Rtable Rtable--3cols">
@foreach ($restaurants as $restaurant)
  <div class="Rtable-cell nameAndType">
    <a href="{{ route('restaurants.details',$restaurant->id) }}">
    <div class="restaurantName">{{ $restaurant->name }}</div>
    <div class="restaurantType">{{ $restaurant->type }}</div>
    </a>
  </div>
  <div class="Rtable-cell center">
    <a target="_blank"
       href="{{ $restaurant->google_maps_link }}">
      <div class="oneDigit inline">
      {{ App\GeoUtils::walkingTime($restaurant->currentDistance/1000) }}
    </div>
      <br /><div class="minute inline">min</div>
    </a>
  </div>
  <div class="Rtable-cell">
      <div class="Rtable Rtable--6cols">
        <!-- labels -->
        <div class="Rtable-cell label">price</div>
        <div class="Rtable-cell label">lunch</div>
        <div class="Rtable-cell label">food</div>
        <div class="Rtable-cell label">place</div>
        <div class="Rtable-cell label">price</div>
        <div class="Rtable-cell label">date</div>
        <!-- scores -->
        <div class="Rtable-cell oneDigit">{{ $restaurant->lunch_price }}</div>
        <div class="Rtable-cell oneDigit">{{ $restaurant->score_lunch }}</div>
        <div class="Rtable-cell oneDigit">{{ $restaurant->score_food }}</div>
        <div class="Rtable-cell oneDigit">{{ $restaurant->score_place }}</div>
        <div class="Rtable-cell oneDigit">{{ $restaurant->score_price }}</div>
        <div class="Rtable-cell oneDigit">{{ $restaurant->score_date }}</div>
      </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $restaurants->count() }}</strong> amazing results</div>





<!-- --- -- -- ---- -- -- --- --- -- -->
<BR><BR><BR><BR>
<div class="Rtable Rtable--8cols">
@foreach ($restaurants as $restaurant)
  <div class="Rtable-cell nameAndType">
    <a href="{{ route('restaurants.details',$restaurant->id) }}">
    <div class="restaurantName">{{ $restaurant->name }}</div>
    <div class="restaurantType">{{ $restaurant->type }}</div>
    </a>
  </div>
  <div class="Rtable-cell center">
    <a target="_blank"
       href="{{ $restaurant->google_maps_link }}">
      <div class="oneDigit inline">
      {{ App\GeoUtils::walkingTime($restaurant->currentDistance/1000) }}
    </div>
      <br /><div class="minute inline">min</div>
    </a>
  </div>
  <!-- labels -->
  <div class="Rtable-cell">
    <div class="label">price</div>
    <div class="oneDigit">{{ $restaurant->lunch_price }}</div>
  </div>
  <div class="Rtable-cell">
    <div class="label">lunch</div>
    <div class="oneDigit">{{ $restaurant->score_lunch }}</div>
  </div>
  <div class="Rtable-cell">
    <div class="label">food</div>
    <div class="oneDigit">{{ $restaurant->score_food }}</div>
  </div>
  <div class="Rtable-cell">
    <div class="label">place</div>
    <div class="oneDigit">{{ $restaurant->score_place }}</div>
  </div>
  <div class="Rtable-cell">
    <div class="label">price</div>
    <div class="oneDigit">{{ $restaurant->score_price }}</div>
  </div>
  <div class="Rtable-cell">
    <div class="label">date</div>
    <div class="oneDigit">{{ $restaurant->score_date }}</div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $restaurants->count() }}</strong> amazing results</div>
