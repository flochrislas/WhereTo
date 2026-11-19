<div id="resultsTop">

  <!-- messages -->

  <!-- buttons -->
  <div id="resultsControl">
    <button class="sortButton" data-order-by="ratings" aria-pressed="false" onclick="refreshResults('ratings')">
      {{ __('Best first') }}</button>
    <button class="sortButton" data-order-by="distance" aria-pressed="false" onclick="refreshResults('distance')">
      {{ __('Near first') }}</button>
    <button class="navButton" onclick="showFilter()">{{ __('Show Filters') }}</button>
  </div>

  <!-- fixed labels -->
  <div class="resultTable">
    <div class="resultRow header">
      <div class="resultCell nameAndType">
        <div class="label inline" style="color: var(--color-name);">{{ __('Name') }}</div>
        <div class="label inline" style="color: var(--color-txt-default);">&</div>
        <div class="label inline" style="color: var(--color-type);">{{ __('Branch') }}</div>
      </div>
      <div class="resultCell distance centerContent">
        <div class="label distanceAdjust" style="color: var(--color-dst);">{{ __('dst.') }}</div>
      </div>
      <div class="resultCell nameAndType">
        <div class="label">{{ __('Description') }}</div>
      </div>
    </div>
  </div>
</div>

<!-- data -->
<!-- aria-live: screen readers announce the new results when they arrive -->
<!-- The default list is already here (rendered by the server, distances from the station): time="1" tells the
     JavaScript it does not need to load it, only to refresh it once the position is known -->
<div id="results" loading="none" time="1" aria-live="polite">
  @include('karaokes.results-data')
</div>
