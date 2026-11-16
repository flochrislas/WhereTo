<div id="tagsTop">

  <div id="filterHeader">
    <div id="filterHeaderTitle">What kind of place?</div>
    <button id="filterHeaderButton" onclick="getResults()">Show All</button>
  </div>

  <!-- selected tags for active search -->
  <div id="selectedTags" aria-label="Selected tags">
  </div>

  <p>Available options</p>
</div>

<!-- showing available tags -->
<div id="tags" aria-label="Available tags">
  @foreach ($restaurantTags as $restaurantTag)
      <!-- A real button: works with the keyboard and screen readers. aria-pressed tells them if the tag is selected -->
      <button type="button" class="tag tagType{{$restaurantTag->type}}"
            tagId="{{$restaurantTag->id}}"
            id="tagShow{{$restaurantTag->id}}"
            aria-pressed="false"
            onClick="moveTag(event)">
        {{$restaurantTag->label}}
      </button>
  @endforeach
</div>

