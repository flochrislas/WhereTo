<!-- Number of results. count() on the collection; one sentence for one result, another for the others (Japanese
     has a single form, "1件" and ":count件") -->
<div class="resultsCount">{{ $places->count() === 1 ? __('1 result') : __(':count results', ['count' => $places->count()]) }}</div>
<div class="resultTable">
@foreach ($places as $bar)
  <div class="resultsCounter"></div>
  {{-- data-place-id: the stand-alone app (whereto-local.js) finds the row of a place by it, to build its own lists --}}
  <div class="resultRow" data-place-id="{{ $bar->id }}">
    <!-- A real link to the standalone page (crawlers and new tabs); a click opens the details in the page instead -->
    <!-- lang="en": the names, branches and descriptions are English; on a Japanese page, the Japanese name comes first when the place has one (doc/12-multilingual-ui.md) -->
    <a class="resultCell nameAndType" href="{{ \App\Support\LocaleUrl::route('bars.details.single', $bar->id) }}"
        onclick="showDetails(event, '{{ \App\Support\LocaleUrl::route('bars.details', $bar->id) }}', '{{ $bar->id }}')">
      {{-- On a Japanese page, the Japanese name first when the place has one; only the English name is lang="en" --}}
      @php($translatedName = $bar->translatedName(app()->getLocale()))
      @if ($translatedName === null)
        <div class="rowName" lang="en">{{ $bar->name }}</div>
      @else
        <div class="rowName">{{ $translatedName }} <span lang="en">{{ $bar->name }}</span></div>
      @endif
      <div class="rowType" lang="en">{{ $bar->type }}</div>
    </a>
    <div class="resultCell distance centerContent">
      {{-- The walking time links to Google Maps; in the stand-alone app a place without that link shows it as plain text --}}
      @if (\App\Support\Standalone::offers($bar->google_maps_link))
      <a target="_blank"
         href="{{ $bar->google_maps_link }}">
      @endif
        <div class="oneDigit inline">
        {{ App\Tools\GeoUtils::walkingTime($bar->currentDistance/1000) }}
        </div>
        <br /><div class="minute inline">{{ __('min') }}</div>
      @if (\App\Support\Standalone::offers($bar->google_maps_link))
      </a>
      @endif
    </div>
    <div class="resultCell description">
        <div class="extract" lang="en">{{ $bar->points }}</div>
    </div>
  </div>
@endforeach
</div>
{{-- The number in bold inside a translated sentence: the sentence is cut at its :count placeholder, so each
     language places the number where it wants and the text stays escaped --}}
@php([$before, $after] = explode(':count', __('End of :count amazing results'), 2) + [1 => ''])
<div id="resultsEnd">{{ $before }}<strong>{{ $places->count() }}</strong>{{ $after }}</div>
