@extends('layouts.app')

@section('title', 'Restaurant details')

@section('content')
<div id="content">

  <div id="resultsControl">
    <button class="sortButton"
      onclick="window.open('{{ $place->google_maps_link }}', '_blank')">
      Google<br>maps</button>
    <button class="sortButton"
      onclick="window.open('{{ $place->tabelog_link }}', '_blank')">
      Tabelog</button>
    <button class="sortButton"
      onclick="window.open('{{ $place->official_website }}', '_blank')">
      Website</button>
    <button class="navButton" onclick="history.back(-1)">BACK</button>
  </div>

  <div id="detailTitle" class="detailRow">
      <div id="detailName" class="detail"><div>{{ $place->name }}</div></div>
      <div id="detailType" class="detail"><div>{{ $place->type }}</div></div>
      <div id="detailLocation" class="detail"><div>{{ $place->location }}</div></div>
  </div>

  <div class="detailRow">
      <div class="detail cost"
           absolute-tooltip="Estimated price for a lunch">
        <div>{{ $place->lunch_price }}&yen;</div>
      </div>
      <div class="detail score lunch"
           absolute-tooltip="Global score for enjoying the lunch &#xa;0: horrible, 1: whatever, 2: good, 3: great, &#xa;4: excellent">
        <div class="label">Lunch</div>
        <div class="grade">{{ $place->score_lunch }}/4</div>
      </div>
      <div class="detail score food"
           absolute-tooltip="How good is the food &#xa;0: poor, 1: good, 2: delicious, 3: divine">
        <div class="label">Food</div>
        <div class="grade">{{ $place->score_food }}/3</div>
      </div>
      <div class="detail score place"
           absolute-tooltip="How nice is the place &#xa;0: horrid, 1: decent, 2: very nice, &#xa;3: fantastic">
        <div class="label">Place</div>
        <div class="grade">{{ $place->score_place }}/3</div>
      </div>
      <div class="detail score price"
           absolute-tooltip="How appropriate feels the price &#xa;0: overpriced, 1: correct, 2: great deal">
        <div class="label">Cost</div>
        <div class="grade">{{ $place->score_price }}/2</div>
      </div>
      <div class="detail score date"
           absolute-tooltip="How appropriate is it for a date &#xa;0: bad, 1: decent, 2: great">
        <div class="label">Date</div>
        <div class="grade">{{ $place->score_date }}/2</div>
      </div>
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
              id="tagShow{{$placeTag->id}}"
              onClick="moveTag(event)">
          {{$placeTag->label}}
        </div>
    @endforeach
    </div>
  </div>

  @if ($place->visited != 1)
    <div class="mysteryDot"> . </div>
  @endif

</div>
@endsection
