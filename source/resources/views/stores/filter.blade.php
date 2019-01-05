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
  @foreach ($tags as $tag)
      <div class="tag tagType{{$tag->type}}"
            tagId="{{$tag->id}}"
            id="tagShow{{$tag->id}}"
            onClick="moveTag(event)">
        {{$tag->label}}
      </div>
  @endforeach
</div>

@include('inline-javascript.filter')
