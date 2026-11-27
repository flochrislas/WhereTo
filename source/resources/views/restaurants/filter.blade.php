<div id="tagsTop">

  <div id="filterHeader">
    <div id="filterHeaderTitle">{{ __('What kind of place?') }}</div>
    <button id="filterHeaderButton" onclick="getResults()">{{ __('Show All') }}</button>
  </div>

  <!-- selected tags for active search -->
  <div id="selectedTags" role="group" aria-label="{{ __('Selected tags') }}">
  </div>

  <!-- Operator between the selected tags: all of them (AND) or any of them (OR). Remembered in localStorage by the JavaScript -->
  {{-- One sentence with the two buttons as placeholders ("Match :all or :any"): each language puts the buttons where
       its word order wants them ("[すべてのタグ] か [いずれかのタグ] に一致"). preg_split cuts the sentence at the
       placeholders and keeps them (DELIM_CAPTURE), so the loop prints the text parts and a button for each placeholder --}}
  <div id="tagsOperator" role="group" aria-label="{{ __('Match the selected tags') }}">
    @foreach (preg_split('/(:all|:any)/', __('Match :all or :any'), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) as $part)
      @if ($part === ':all')
        <button type="button" class="opButton" data-op="AND" aria-pressed="true" onclick="setTagsOperator('AND')">{{ __('all tags') }}</button>
      @elseif ($part === ':any')
        <button type="button" class="opButton" data-op="OR" aria-pressed="false" onclick="setTagsOperator('OR')">{{ __('any tag') }}</button>
      @elseif (trim($part) !== '')
        <span>{{ trim($part) }}</span>
      @endif
    @endforeach
  </div>

  <p>{{ __('Available options') }}</p>
</div>

<!-- showing available tags -->
<div id="tags" role="group" aria-label="{{ __('Available tags') }}">
  @foreach ($tags as $restaurantTag)
      <x-tag :tag="$restaurantTag" use="filter" />
  @endforeach
</div>
