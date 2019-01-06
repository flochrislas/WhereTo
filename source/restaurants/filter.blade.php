<div id="tagsTop">
  <div id="position-report"></div>

  <div id="filterHeader">
    <div id="filterHeaderTitle">What kind of place?</div>
    <button id="filterHeaderButton" onclick="getResults()">Show All</button>
  </div>

  <!-- selected tags for active search -->
  <div id="selectedTags">
  </div>

  <p>Available options</p>
</div>

<!-- showing available tags -->
<div id="tags">
  @foreach ($restaurantTags as $restaurantTag)
      <div class="tag tagType{{$restaurantTag->type}}"
            tagId="{{$restaurantTag->id}}"
            id="tagShow{{$restaurantTag->id}}"
            onClick="moveTag(event)">
        {{$restaurantTag->label}}
      </div>
  @endforeach
</div>

@include('inline-javascript.filter')
