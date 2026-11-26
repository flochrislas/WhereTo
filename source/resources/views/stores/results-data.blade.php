<!-- Number of results. count() on the collection; one sentence for one result, another for the others (Japanese
     has a single form, "1件" and ":count件") -->
<div class="resultsCount">{{ $places->count() === 1 ? __('1 result') : __(':count results', ['count' => $places->count()]) }}</div>
<div class="resultTable">
@foreach ($places as $store)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <!-- A real link to the standalone page (crawlers and new tabs); a click opens the details in the page instead -->
    <!-- lang="en": the names, branches and descriptions are English; on a Japanese page, the Japanese name comes first when the place has one (doc/12-multilingual-ui.md) -->
    <a class="resultCell nameAndType" href="{{ \App\Support\LocaleUrl::route('stores.details.single', $store->id) }}"
        onclick="showDetails(event, '{{ \App\Support\LocaleUrl::route('stores.details', $store->id) }}', '{{ $store->id }}')">
      {{-- On a Japanese page, the Japanese name first when the place has one; only the English name is lang="en" --}}
      @php($translatedName = $store->translatedName(app()->getLocale()))
      @if ($translatedName === null)
        <div class="rowName" lang="en">{{ $store->name }}</div>
      @else
        <div class="rowName">{{ $translatedName }} <span lang="en">{{ $store->name }}</span></div>
      @endif
      <div class="rowType" lang="en">{{ $store->type }}</div>
    </a>
    <div class="resultCell distance centerContent">
      <a target="_blank"
         href="{{ $store->google_maps_link }}">
        <div class="oneDigit inline">
        {{ App\Tools\GeoUtils::walkingTime($store->currentDistance/1000) }}
        </div>
        <br /><div class="minute inline">{{ __('min') }}</div>
      </a>
    </div>
    <div class="resultCell description">
        <div class="extract" lang="en">{{ $store->points }}</div>
    </div>
  </div>
@endforeach
</div>
{{-- The number in bold inside a translated sentence: the sentence is cut at its :count placeholder, so each
     language places the number where it wants and the text stays escaped --}}
@php([$before, $after] = explode(':count', __('End of :count amazing results'), 2) + [1 => ''])
<div id="resultsEnd">{{ $before }}<strong>{{ $places->count() }}</strong>{{ $after }}</div>
