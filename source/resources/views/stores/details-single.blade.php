@extends('layouts.app')

@section('title', $place->nameWithStatus().' - Store info')
@section('description', 'Detailed information page for '.$place->nameWithStatus().', in Tokyo Shibuya. Ratings and practical information.')
@section('heading', $place->name)

@section('content')
  <x-structured-data :place="$place" />
  <x-business-status :place="$place" />
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
