<!-- Number of results. count() on the collection; Str::plural adds the "s" when needed -->
<div class="resultsCount">{{ $places->count() }} {{ \Illuminate\Support\Str::plural('result', $places->count()) }}</div>
<div class="resultTable">
@foreach ($places as $karaoke)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <!-- A real link to the standalone page (crawlers and new tabs); a click opens the details in the page instead -->
    <a class="resultCell nameAndType" href="{{ route('karaokes.details.single', $karaoke->id) }}"
        onclick="showDetails(event, '{{ route('karaokes.details', $karaoke->id) }}', '{{ $karaoke->id }}')">
      <div class="rowName">{{ $karaoke->name }}</div>
      <div class="rowType">{{ $karaoke->location }}</div>
    </a>
    <div class="resultCell distance centerContent">
      <a target="_blank"
         href="{{ $karaoke->google_maps_link }}">
        <div class="oneDigit inline">
        {{ App\Tools\GeoUtils::walkingTime($karaoke->currentDistance/1000) }}
      </div>
        <br /><div class="minute inline">min</div>
      </a>
    </div>
    <div class="resultCell description">
        <div class="extract">{{ $karaoke->points }}</div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $places->count() }}</strong> amazing results</div>
