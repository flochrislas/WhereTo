<div id="resultsControl">
    @if (\App\Support\Standalone::offers($place->google_maps_link))
    <button class="sortButton"
      onclick="window.open('{{ $place->google_maps_link }}', '_blank')">
      {{ __('Google maps') }}</button>
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
      onclick="copyStringToClipboard('{{ \App\Support\LocaleUrl::route('karaokes.details.single',$place->id) }}')">
      {{ __('Copy URL') }}</button>
    @endunless
    <button class="navButton" onclick="hideDetails()">{{ __('BACK') }}</button>
  </div>

  <div id="detailTitle" class="detailRow">
      {{-- On a Japanese page, the Japanese name first when the place has one; only the English name is lang="en" --}}
      @php($translatedName = $place->translatedName(app()->getLocale()))
      @if ($translatedName === null)
        <div id="detailName" class="detail" detailId="{{ $place->id }}" lang="en"><div>{{ $place->name }}</div></div>
      @else
        <div id="detailName" class="detail" detailId="{{ $place->id }}"><div>{{ $translatedName }} <span lang="en">{{ $place->name }}</span></div></div>
      @endif
      <div id="detailLocation" class="detail" lang="en"><div>{{ $place->location }}</div></div>
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
        <x-tag :tag="$placeTag" use="details" />
    @endforeach
    </div>
  </div>
