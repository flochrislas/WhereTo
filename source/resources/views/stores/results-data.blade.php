<div class="resultTable">
<!-- WE NEED A COUNTER HERE TO DISPLAY THE NUMBER OF RESULTS -->
@foreach ($places as $store)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <div class="resultCell nameAndType">
      <a href="{{ route('stores.details',$store->id) }}">
      <div class="rowName">{{ $store->name }}</div>
      <div class="rowType">{{ $store->type }}</div>
      </a>
    </div>
    <div class="resultCell distance centerContent">
      <a target="_blank"
         href="{{ $store->google_maps_link }}">
        <div class="oneDigit inline">
        {{ App\Tools\GeoUtils::walkingTime($store->currentDistance/1000) }}
        </div>
        <br /><div class="minute inline">min</div>
      </a>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-place);">
        <div class="oneDigit">{{ $store->points }}</div>
      </div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $places->count() }}</strong> amazing results</div>
