<div class="resultTable">
<!-- WE NEED A COUNTER HERE TO DISPLAY THE NUMBER OF RESULTS -->
@foreach ($places as $bar)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <div class="resultCell nameAndType"
        onclick="showDetails('{{ route('bars.details',$bar->id) }}','{{ $bar->id }}')">
      <div class="rowName">{{ $bar->name }}</div>
      <div class="rowType">{{ $bar->type }}</div>
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
    <div class="resultCell description">
        <div class="extract">{!! $bar->points !!}</div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $places->count() }}</strong> amazing results</div>
