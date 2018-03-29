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
    </button>
</form>
