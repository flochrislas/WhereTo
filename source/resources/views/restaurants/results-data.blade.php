<div class="resultTable">
<!-- WE NEED A COUNTER HERE TO DISPLAY THE NUMBER OF RESULTS -->
@foreach ($places as $restaurant)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <div class="resultCell nameAndType"
        onclick="showDetails('{{ route('restaurants.details',$restaurant->id) }}','{{ $restaurant->id }}')">
      <div class="rowName">{{ $restaurant->name }}</div>
      <div class="rowType">{{ $restaurant->type }}</div>
    </div>
    <div class="resultCell distance centerContent">
      <a target="_blank"
         href="{{ $restaurant->google_maps_link }}">
        <div class="oneDigit inline">
        {{ App\Tools\GeoUtils::walkingTime($restaurant->currentDistance/1000) }}
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
    <!--
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-price);">
        <div class="oneDigit">{{ $restaurant->score_price }}</div>
      </div>
    </div>
    -->
    <!--
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-date);">
        <div class="oneDigit">{{ $restaurant->score_date }}</div>
      </div>
    </div> -->
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $places->count() }}</strong> amazing results</div>
