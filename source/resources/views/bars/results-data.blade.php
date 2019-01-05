<div class="resultTable">
<!-- WE NEED A COUNTER HERE TO DISPLAY THE NUMBER OF RESULTS -->
@foreach ($places as $bar)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <div class="resultCell nameAndType">
      <a href="{{ route('bars.details',$bar->id) }}">
      <div class="rowName">{{ $bar->name }}</div>
      <div class="rowType">{{ $bar->type }}</div>
      </a>
    </div>
    <div class="resultCell distance centerContent">
      <a target="_blank"
         href="{{ $bar->google_maps_link }}">
        <div class="oneDigit inline">
        {{ App\Tools\GeoUtils::walkingTime($bar->currentDistance/1000) }}
        </div>
        <br /><div class="minute inline">min</div>
      </a>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-place);">
        <div class="oneDigit">{{ $bar->points }}</div>
      </div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $places->count() }}</strong> amazing results</div>
