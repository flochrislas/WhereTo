@extends('layouts.app')

@section('title', 'Restaurant details')

@section('content')
<div id="content">

  <div id="resultsControl">
    <button class="sortButton"
      onclick="window.open('{{ $restaurant->google_maps_link }}', '_blank')">
      Google<br>maps</button>
    <button class="sortButton"
      onclick="window.open('{{ $restaurant->tabelog_link }}', '_blank')">
      Tabelog</button>
    <button class="sortButton"
      onclick="window.open('{{ $restaurant->official_website }}', '_blank')">
      Website</button>
    <button class="navButton" onclick="history.back(-1)">BACK</button>
  </div>

  <div id="detailTitle" class="detailRow">
      <div id="detailName" class="detail">{{ $restaurant->name}}</div>
      <div id="detailType" class="detail">{{ $restaurant->type}}</div>
      <div class="detail">{{ $restaurant->location}}</div>
  </div>

  <div class="detailRow">
      <div class="detail">{{ $restaurant->lunch_price}}&yen;</div>
      <div class="detail"><p>Lunch</p>{{ $restaurant->score_lunch}}/4</div>
      <div class="detail"><p>Food</p>{{ $restaurant->score_food}}/3</div>
      <div class="detail"><p>Place</p>{{ $restaurant->score_place}}/3</div>
      <div class="detail"><p>Cost</p>{{ $restaurant->score_price}}/2</div>
      <div class="detail"><p>Date</p>{{ $restaurant->score_date}}/2</div>
  </div>

  <div class="detailRow">
      <div class="detail">{{ $restaurant->points}}</div>
  </div>

  <div class="detailRow justified">
      <div class="detail">{{ $restaurant->experience}}</div>
  </div>

  <div class="detail">
  @foreach ($restaurant->tagsCached() as $restaurantTag)
      <div class="tag tagType{{$restaurantTag->type}}"
            tagId="{{$restaurantTag->id}}"
            id="tagShow{{$restaurantTag->id}}"
            onClick="moveTag(event)">
        {{$restaurantTag->label}}
      </div>
  @endforeach
  </div>

  @if ($restaurant->visited != 1)
    <div class="mysteryDot"> . </div>
  @endif

</div>
@endsection
