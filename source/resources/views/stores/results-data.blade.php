<div class="resultTable">
<!-- WE NEED A COUNTER HERE TO DISPLAY THE NUMBER OF RESULTS -->
@foreach ($places as $store)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <div class="resultCell nameAndType"
        onclick="showDetails('{{ route('stores.details',$store->id) }}','{{ $store->id }}')">
      <div class="rowName">{{ $store->name }}</div>
      <div class="rowType">{{ $store->type }}</div>
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
    <div class="resultCell description">
        <div class="extract">{!! $store->points !!}</div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $places->count() }}</strong> amazing results</div>
