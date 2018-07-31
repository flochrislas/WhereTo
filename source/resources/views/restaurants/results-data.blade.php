<div class="resultTable">

  <!-- fixed labels -->
  <div class="resultRow header">
    <div class="resultCell nameAndType">
      <div class="label inline" style="color: var(--color-name);">Name</div>
      <div class="label inline" style="color: var(--color-txt-default);">&</div>
      <div class="label inline" style="color: var(--color-type);">Type</div>
    </div>
    <div class="resultCell distance centerContent">
      <div class="label distanceAdjust" style="color: var(--color-dst);">dst.</div>
    </div>
    <div class="resultCell centerContent">
      <div class="label fourDigitsAdjust" style="color: var(--color-cost);">cost</div>
    </div>
    <div class="resultCell centerContent">
      <div class="label" style="color: var(--color-lunch);">lunch</div>
    </div>
    <div class="resultCell centerContent">
      <div class="label" style="color: var(--color-food);">food</div>
    </div>
    <div class="resultCell centerContent">
      <div class="label" style="color: var(--color-place);">place</div>
    </div>
    <div class="resultCell centerContent">
      <div class="label" style="color: var(--color-price);">price</div>
    </div>
    <div class="resultCell centerContent">
      <div class="label" style="color: var(--color-date);">date</div>
    </div>
  </div>

<!-- WE NEED A COUNTER HERE TO DISPLAY THE NUMBER OF RESULTS -->
@foreach ($restaurants as $restaurant)
  <div class="resultsCounter"></div>
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
      <div class="score" style="color: var(--color-cost);">
        <div class="fourDigits">{{ $restaurant->lunch_price }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-lunch);">
        <div class="oneDigit">{{ $restaurant->score_lunch }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-food);">
        <div class="oneDigit">{{ $restaurant->score_food }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-place);">
        <div class="oneDigit">{{ $restaurant->score_place }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-price);">
        <div class="oneDigit">{{ $restaurant->score_price }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-date);">
        <div class="oneDigit">{{ $restaurant->score_date }}</div>
      </div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $restaurants->count() }}</strong> amazing results</div>
