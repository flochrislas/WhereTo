<!-- Number of results. count() on the collection; Str::plural adds the "s" when needed -->
<div class="resultsCount">{{ $places->count() }} {{ \Illuminate\Support\Str::plural('result', $places->count()) }}</div>
<div class="resultTable">
@foreach ($places as $store)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <!-- A real link to the standalone page (crawlers and new tabs); a click opens the details in the page instead -->
    <a class="resultCell nameAndType" href="{{ route('stores.details.single', $store->id) }}"
        onclick="showDetails(event, '{{ route('stores.details', $store->id) }}', '{{ $store->id }}')">
      <div class="rowName">{{ $store->name }}</div>
      <div class="rowType">{{ $store->type }}</div>
    </a>
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
        <div class="extract">{{ $store->points }}</div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $places->count() }}</strong> amazing results</div>
