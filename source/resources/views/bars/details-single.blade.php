@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - {{$place->name}} - Details & {{ str_limit($place->updated_at, $limit = 4, $end = '') }} review</title>
  <meta name="description" content="Detailed information page for {{$place->name}}, in Tokyo Shibuya. Full {{ str_limit($place->updated_at, $limit = 4, $end = '') }} review and ratings.">
@endsection

@section('content')
<div id="content-unchained">

  <div id="resultsControl">
    <button class="sortButton"
      onclick="window.open('{{ $place->google_maps_link }}', '_blank')">
      Google<br>maps</button>
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
      <div class="detail score lunch">
        <div class="label">Lunch</div>
        <div class="grade">{{ $place->score_lunch }}/4</div>
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
