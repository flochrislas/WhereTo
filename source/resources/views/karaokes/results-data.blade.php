<!-- Number of results. count() on the collection; one sentence for one result, another for the others (Japanese
     has a single form, "1件" and ":count件") -->
<div class="resultsCount">{{ $places->count() === 1 ? __('1 result') : __(':count results', ['count' => $places->count()]) }}</div>
<div class="resultTable">
@foreach ($places as $karaoke)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <!-- A real link to the standalone page (crawlers and new tabs); a click opens the details in the page instead -->
    <!-- lang="en": the names, branches and descriptions are English on every page (doc/12-multilingual-ui.md) -->
    <a class="resultCell nameAndType" href="{{ \App\Support\LocaleUrl::route('karaokes.details.single', $karaoke->id) }}"
        onclick="showDetails(event, '{{ \App\Support\LocaleUrl::route('karaokes.details', $karaoke->id) }}', '{{ $karaoke->id }}')">
      <div class="rowName" lang="en">{{ $karaoke->name }}</div>
      <div class="rowType" lang="en">{{ $karaoke->location }}</div>
    </a>
    <div class="resultCell distance centerContent">
      <a target="_blank"
         href="{{ $karaoke->google_maps_link }}">
        <div class="oneDigit inline">
        {{ App\Tools\GeoUtils::walkingTime($karaoke->currentDistance/1000) }}
      </div>
        <br /><div class="minute inline">{{ __('min') }}</div>
      </a>
    </div>
    <div class="resultCell description">
        <div class="extract" lang="en">{{ $karaoke->points }}</div>
    </div>
  </div>
@endforeach
</div>
{{-- The number in bold inside a translated sentence: the sentence is cut at its :count placeholder, so each
     language places the number where it wants and the text stays escaped --}}
@php([$before, $after] = explode(':count', __('End of :count amazing results'), 2) + [1 => ''])
<div id="resultsEnd">{{ $before }}<strong>{{ $places->count() }}</strong>{{ $after }}</div>
