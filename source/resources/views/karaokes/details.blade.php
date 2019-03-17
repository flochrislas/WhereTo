<div id="resultsControl">
    <button class="sortButton"
      onclick="window.open('{{ $place->google_maps_link }}', '_blank')">
      Google<br>maps</button>
    <button class="sortButton"
      onclick="window.open('{{ $place->official_website }}', '_blank')">
      Website</button>
    <button class="sortButton"
      onclick="copyStringToClipboard('{{ route('karaokes.details.single',$place->id) }}')">
      Copy<br>URL</button>
    <button class="navButton" onclick="hideDetails()">BACK</button>
  </div>

  <div id="detailTitle" class="detailRow">
      <div id="detailName" class="detail" detailId="{{ $place->id }}"><div>{{ $place->name }}</div></div>
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
        {{ $place->experience }}
      </div>
  </div>

  <div class="detailRow">
    <div class="detail">
    @foreach ($place->tagsCached() as $placeTag)
        <div class="tag tagType{{$placeTag->type}}"
              tagId="{{$placeTag->id}}"
              id="tagShow{{$placeTag->id}}">
          {{$placeTag->label}}
        </div>
    @endforeach
    </div>
  </div>
