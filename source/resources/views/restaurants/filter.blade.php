<div id="position-report"></div>

<h2>What kind of restaurant?</h2>

<!-- Auto complete Experimental -->
<br>Filter:<br>
<input  type="search"
        name="tag-input"
        id="tag-input"
        placeholder=""
        data-action="{{ route('tags.autocomplete') }}" />
<br>
<input type="text" id="tag-selection"><br>
<script type="text/javascript">
  $('#tag-input').each(function() {
      var $this = $(this);
      var src = $this.data('action');

      $this.autocomplete({
          source: src,
          minLength: 2,
          select: function(event, ui) {
              $this.val(ui.item.value);
              $('#tag-selection').val(ui.item.id);
              $('#tag-search').val(ui.item.value);
          }
      });
  });
</script>

<br>

<!-- SEARCH http://justlaravel.com/search-functionality-laravel/ -->
<form action="" method="GET" role="search" name="mainSearch">
    {{ csrf_field() }}
    <input  type="search"
            name="tags"
            id="tag-search"
            value="{!! old('tags') !!}"
            placeholder="Search tags">
    <input type="hidden" name="op" value="AND">
    <input type="hidden" name="position" id="position">
    <button type="submit">
      <!-- search icon -->
      SUBMIT
    </button>
</form>

<p>drag and drop the following to reorder</p>
<div id="order1"></div>
<div id="order2"></div>
<div id="order3"></div>
<p>nearest first</p>
<p>best first</p>
<p>cheapest first</p>

<p>Selection</p>
<div id="selectedTags">
</div>

<p>Showing</p>
<div id="tags">
@foreach ($restaurantTags as $restaurantTag)
    <div class="tag"
          tagId="{{$restaurantTag->id}}"
          id="tagShow{{$restaurantTag->id}}"
          onClick="testA(event)">
      {{$restaurantTag->label}}
    </div>
@endforeach
</div>

<script>
function testA(event) {
    /*var wer = event.target.getAttribute("tagId");*/
  /*  alert(wer);*/
  if (event.target.parentNode.id == "selectedTags") {
    document.getElementById("tags").appendChild(event.target);
  } else {
    document.getElementById("selectedTags").appendChild(event.target);
  }
  /* Run the search from the selectedTags immediately in the background */
  refreshResults();
}
function refreshResults() {
  /* read all the div under selectedTags */
  /* make a list of all their ids to send as parameters */
  /* read order parameters */
  /* AJAX request to update the reuslts */
}
</script>
