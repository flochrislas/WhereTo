<div id="resultsControl">
  <button class="sortButton"
    onclick="window.open('{{ $place->google_maps_link }}', '_blank')">
    {{ __('Google maps') }}</button>
  <button class="sortButton"
    onclick="window.open('{{ $place->official_website }}', '_blank')">
    {{ __('Website') }}</button>
  <button class="sortButton"
    onclick="copyStringToClipboard('{{ \App\Support\LocaleUrl::route('stores.details.single',$place->id) }}')">
    {{ __('Copy URL') }}</button>
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
    <div id="detailType" class="detail" lang="en"><div>{{ $place->type }}</div></div>
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
      {{-- Raw on purpose: two store texts in the seeder carry an <a href> link. The seeders are the only source of this text (the architecture test allows the raw syntax in these two files only) --}}
      <span lang="en">{!! $place->experience !!}</span>
    </div>
</div>

<div class="detailRow">
  <div class="detail">
  @foreach ($place->tagsCached() as $placeTag)
      <x-tag :tag="$placeTag" use="details" />
  @endforeach
  </div>
</div>
