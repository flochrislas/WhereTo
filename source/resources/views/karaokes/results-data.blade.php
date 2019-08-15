<div class="resultTable">
<!-- WE NEED A COUNTER HERE TO DISPLAY THE NUMBER OF RESULTS -->
@foreach ($places as $karaoke)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <div class="resultCell nameAndType"
        onclick="showDetails('{{ route('karaokes.details',$karaoke->id) }}','{{ $karaoke->id }}')">
      <div class="rowName">{{ $karaoke->name }}</div>
      <div class="rowType">{{ $karaoke->location }}</div>
    </div>
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
        <div class="extract">{!! $karaoke->points !!}</div>
    </div>
  </div>
@endforeach
</div>
<div id="resultsEnd">End of <strong>{{ $places->count() }}</strong> amazing results</div>
