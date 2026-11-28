<div id="resultsControl">
  @if (\App\Support\Standalone::offers($place->google_maps_link))
  <button class="sortButton"
    onclick="window.open('{{ $place->google_maps_link }}', '_blank')">
    {{ __('Google maps') }}</button>
  @endif
  @if (\App\Support\Standalone::offers($place->tabelog_link))
  <button class="sortButton"
    onclick="window.open('{{ $place->tabelog_link }}', '_blank')">
    Tabelog</button>
  @endif
  {{-- The stand-alone app (App\Support\Standalone) has no use for a button that opens nothing --}}
  @if (\App\Support\Standalone::offers($place->official_website))
  <button class="sortButton"
    onclick="window.open('{{ $place->official_website }}', '_blank')">
    {{ __('Website') }}</button>
  @endif
  {{-- Not in the stand-alone app: its pages have no address worth sharing --}}
  @unless (\App\Support\Standalone::active())
  <button class="sortButton"
    onclick="copyStringToClipboard('{{ \App\Support\LocaleUrl::route('restaurants.details.single',$place->id) }}')">
    {{ __('Copy URL') }}</button>
  @endunless
  <button class="navButton" onclick="hideDetails()">{{ __('BACK') }}</button>
</div>

{{-- lang="en": the name, type, location and notes are English, but for the Japanese name shown first on a Japanese page when the place has one (doc/12-multilingual-ui.md) --}}
<div id="detailTitle" class="detailRow">
    {{-- On a Japanese page, the Japanese name first when the place has one; only the English name is lang="en" --}}
    @php($translatedName = $place->translatedName(app()->getLocale()))
    @if ($translatedName === null)
      <div id="detailName" class="detail" detailId="{{ $place->id }}" lang="en"><div>{{ $place->name }}</div></div>
    @else
      <div id="detailName" class="detail" detailId="{{ $place->id }}"><div>{{ $translatedName }} <span lang="en">{{ $place->name }}</span></div></div>
    @endif
    <div id="detailType" class="detail" lang="en"><div>{{ $place->type }}</div></div>
    <div id="detailLocation" class="detail" lang="en"><div>{{ $place->location }}</div></div>
</div>

<div class="detailRow">
    <div class="detail cost"
         absolute-tooltip="{{ __('Estimated price for a lunch') }}">
      <div>{{ $place->lunch_price }}&yen;</div>
    </div>
    {{-- The tooltips break their lines at "&#xa;" (a newline in an attribute); in the translations it is a "\n" --}}
    <div class="detail score lunch"
         absolute-tooltip="{{ __("Global score for enjoying the lunch\n0: horrible, 1: whatever, 2: good, 3: great,\n4: excellent") }}">
      <div class="label">{{ __('Lunch') }}</div>
      <div class="grade">{{ $place->score_lunch }}/4</div>
    </div>
    <div class="detail score food"
         absolute-tooltip="{{ __("How good is the food\n0: poor, 1: good, 2: delicious, 3: divine") }}">
      <div class="label">{{ __('Food') }}</div>
      <div class="grade">{{ $place->score_food }}/3</div>
    </div>
    <div class="detail score place"
         absolute-tooltip="{{ __("How nice is the place\n0: horrid, 1: decent, 2: very nice,\n3: fantastic") }}">
      <div class="label">{{ __('Place') }}</div>
      <div class="grade">{{ $place->score_place }}/3</div>
    </div>
    <div class="detail score price"
         absolute-tooltip="{{ __("How appropriate feels the price\n0: overpriced, 1: correct, 2: great deal") }}">
      <div class="label">{{ __('Cost') }}</div>
      <div class="grade">{{ $place->score_price }}/2</div>
    </div>
    <div class="detail score date"
         absolute-tooltip="{{ __("How appropriate is it for a date\n0: bad, 1: decent, 2: great") }}">
      <div class="label">{{ __('Date') }}</div>
      <div class="grade">{{ $place->score_date }}/2</div>
    </div>
</div>

<div class="detailRow">
    <div class="detail border15"
         overlay="{{ __('Main point') }}">
    <span lang="en">{{ $place->points }}</span></div>
</div>

<div class="detailRow">
    <div class="detail"
         overlay="{{ __('Personal experience') }}">
      <span lang="en">{{ $place->experience }}</span>
    </div>
</div>

<div class="detailRow">
  <div class="detail">
  @foreach ($place->tagsCached() as $placeTag)
      {{-- $tagUse: set by the standalone page, which has no filter to add a tag to --}}
      <x-tag :tag="$placeTag" :use="$tagUse ?? 'details'" />
  @endforeach
  </div>
</div>

@if ($place->visited != 1)
  <div class="mysteryDot"> . </div>
@endif
