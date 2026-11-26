<!-- Number of results. count() on the collection; one sentence for one result, another for the others (Japanese
     has a single form, "1件" and ":count件") -->
<div class="resultsCount">{{ $places->count() === 1 ? __('1 result') : __(':count results', ['count' => $places->count()]) }}</div>
<div class="resultTable">
@foreach ($places as $restaurant)
  <div class="resultsCounter"></div>
  <div class="resultRow">
    <!-- A real link to the standalone page (crawlers and new tabs); a click opens the details in the page instead -->
    <!-- lang="en": the names and types are English; on a Japanese page, the Japanese name comes first when the place has one (doc/12-multilingual-ui.md) -->
    <a class="resultCell nameAndType" href="{{ \App\Support\LocaleUrl::route('restaurants.details.single', $restaurant->id) }}"
        onclick="showDetails(event, '{{ \App\Support\LocaleUrl::route('restaurants.details', $restaurant->id) }}', '{{ $restaurant->id }}')">
      {{-- On a Japanese page, the Japanese name first when the place has one; only the English name is lang="en" --}}
      @php($translatedName = $restaurant->translatedName(app()->getLocale()))
      @if ($translatedName === null)
        <div class="rowName" lang="en">{{ $restaurant->name }}</div>
      @else
        <div class="rowName">{{ $translatedName }} <span lang="en">{{ $restaurant->name }}</span></div>
      @endif
      <div class="rowType" lang="en">{{ $restaurant->type }}</div>
    </a>
    <div class="resultCell distance centerContent">
      <a target="_blank"
         href="{{ $restaurant->google_maps_link }}">
        <div class="oneDigit inline">
        {{ App\Tools\GeoUtils::walkingTime($restaurant->currentDistance/1000) }}
      </div>
        <br /><div class="minute inline">{{ __('min') }}</div>
      </a>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-cost);">
        <div class="fourDigits">{{ $restaurant->lunch_price }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-lunch);">
        <div class="oneDigit">{{ $restaurant->score_lunch }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-food);">
        <div class="oneDigit">{{ $restaurant->score_food }}</div>
      </div>
    </div>
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-place);">
        <div class="oneDigit">{{ $restaurant->score_place }}</div>
      </div>
    </div>
    <!--
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-price);">
        <div class="oneDigit">{{ $restaurant->score_price }}</div>
      </div>
    </div>
    -->
    <!--
    <div class="resultCell centerContent">
      <div class="score" style="color: var(--color-date);">
        <div class="oneDigit">{{ $restaurant->score_date }}</div>
      </div>
    </div> -->
  </div>
@endforeach
</div>
{{-- The number in bold inside a translated sentence: the sentence is cut at its :count placeholder, so each
     language places the number where it wants and the text stays escaped --}}
@php([$before, $after] = explode(':count', __('End of :count amazing results'), 2) + [1 => ''])
<div id="resultsEnd">{{ $before }}<strong>{{ $places->count() }}</strong>{{ $after }}</div>
