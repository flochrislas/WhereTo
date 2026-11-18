@extends('layouts.app')

@section('title', $place->name.' - Karaoke info'.($place->visit_date ? ' & '.substr($place->visit_date, 0, 4).' review' : ''))
@section('description', 'Detailed information page for the karaoke '.$place->name.', in Tokyo Shibuya. '.($place->visit_date ? 'Full '.substr($place->visit_date, 0, 4).' review and ratings.' : 'Ratings and practical information.'))
@section('heading', $place->name)

@section('content')
  <x-structured-data :data="$place->structuredData(\App\Support\PublicUrl::to(request()->path()))" />
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
        <span class="tag tagType{{$placeTag->type}}">
          {{$placeTag->label}}
        </span>
    @endforeach
    </div>
  </div>

  @if ($place->visited != 1)
    <div class="mysteryDot"> . </div>
  @endif

</div>
@endsection
