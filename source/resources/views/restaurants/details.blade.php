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


    <div class="row">
        <div>
            <div>
                <strong>Name:</strong>
                {{ $restaurant->name}}
            </div>
        </div>
        <div>
            <div>
                <strong>Location:</strong>
                {{ $restaurant->location}}
            </div>
        </div>
        <div>
            <div>
                <strong>Coordinates:</strong>
                {{ $restaurant->coord}}
            </div>
        </div>
        <div>
            <div>
                <strong>Type:</strong>
                {{ $restaurant->type}}
            </div>
        </div>
        <div>
            <div>
                <strong>Lunch price:</strong>
                {{ $restaurant->lunch_price}}
            </div>
        </div>
        <div>
            <div>
                <strong>Points:</strong>
                {{ $restaurant->points}}
            </div>
        </div>
        <div>
            <div>
                <strong>Personal experience:</strong>
                {{ $restaurant->experience}}
            </div>
        </div>
        <div>
            <div>
                <strong>Visited:</strong>
                {{ $restaurant->visited}}
            </div>
        </div>
        <div>
            <div>
                <strong>Tags:</strong>
                <div id="tags">
                @foreach ($restaurant->tagsCached() as $restaurantTag)
                    <div class="tag tagType{{$restaurantTag->type}}"
                          tagId="{{$restaurantTag->id}}"
                          id="tagShow{{$restaurantTag->id}}"
                          onClick="moveTag(event)">
                      {{$restaurantTag->label}}
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
