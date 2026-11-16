<div id="tagsTop">

  <div id="filterHeader">
    <div id="filterHeaderTitle">What kind of place?</div>
    <button id="filterHeaderButton" onclick="getResults()">Show All</button>
  </div>

  <!-- selected tags for active search -->
  <div id="selectedTags" aria-label="Selected tags">
  </div>

  <!-- Operator between the selected tags: all of them (AND) or any of them (OR). Remembered in localStorage by the JavaScript -->
  <div id="tagsOperator" role="group" aria-label="Match the selected tags">
    <span>Match</span>
    <button type="button" class="opButton" data-op="AND" aria-pressed="true" onclick="setTagsOperator('AND')">all</button>
    <button type="button" class="opButton" data-op="OR" aria-pressed="false" onclick="setTagsOperator('OR')">any</button>
    <span>of the selected tags</span>
  </div>

  <p>Available options</p>
</div>

<!-- showing available tags -->
<div id="tags" aria-label="Available tags">
  @foreach ($tags as $tag)
      <!-- A real button: works with the keyboard and screen readers. aria-pressed tells them if the tag is selected -->
      <button type="button" class="tag tagType{{$tag->type}}"
            tagId="{{$tag->id}}"
            id="tagShow{{$tag->id}}"
            aria-pressed="false"
            onClick="moveTag(event)">
        {{$tag->label}}
      </button>
  @endforeach
</div>

