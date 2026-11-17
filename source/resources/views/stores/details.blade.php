<div id="resultsControl">
  <button class="sortButton"
    onclick="window.open('{{ $place->google_maps_link }}', '_blank')">
    Google<br>maps</button>
  <button class="sortButton"
    onclick="window.open('{{ $place->official_website }}', '_blank')">
    Website</button>
  <button class="sortButton"
    onclick="copyStringToClipboard('{{ route('stores.details.single',$place->id) }}')">
    Copy<br>URL</button>
  <button class="navButton" onclick="hideDetails()">BACK</button>
</div>

<div id="detailTitle" class="detailRow">
    <div id="detailName" class="detail" detailId="{{ $place->id }}"><div>{{ $place->name }}</div></div>
    <div id="detailType" class="detail"><div>{{ $place->type }}</div></div>
    <div id="detailLocation" class="detail"><div>{{ $place->location }}</div></div>
</div>

<div class="detailRow">
    <div class="detail border15"
         overlay="Main point">
    {{ $place->points }}</div>
</div>

<div class="detailRow">
    <div class="detail"
         overlay="Personal experience">
      {{-- Raw on purpose: two store texts in the seeder carry an <a href> link. The seeders are the only source of this text (the architecture test allows the raw syntax in these two files only) --}}
      {!! $place->experience !!}
    </div>
</div>

<div class="detailRow">
  <div class="detail">
  @foreach ($place->tagsCached() as $placeTag)
      <button type="button" class="tag tagType{{$placeTag->type}}" title="Add this tag to the filter" onclick="moveTag(event)">
        {{$placeTag->label}}
      </button>
  @endforeach
  </div>
</div>
