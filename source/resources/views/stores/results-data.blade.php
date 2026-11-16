<!-- Number of results. count() on the collection; Str::plural adds the "s" when needed -->
<div class="resultsCount">{{ $places->count() }} {{ \Illuminate\Support\Str::plural('result', $places->count()) }}</div>
<div class="resultTable">
@foreach ($places as $store)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <div class="resultCell nameAndType" role="button" tabindex="0"
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
