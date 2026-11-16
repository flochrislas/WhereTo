<div id="resultsTop">

  <!-- messages -->

  <!-- buttons -->
  <div id="resultsControl">
    <button class="sortButton" data-order-by="ratings" aria-pressed="false" onclick="refreshResults('ratings')">
      Best<br>first</button>
    <button class="sortButton" data-order-by="cost" aria-pressed="false" onclick="refreshResults('cost')">
      Cost<br>first</button>
    <button class="sortButton" data-order-by="distance" aria-pressed="false" onclick="refreshResults('distance')">
      Near<br>first</button>
    <button class="navButton" onclick="showFilter()">Show Filters</button>
  </div>

  <!-- fixed labels -->
  <div class="resultTable">
    <div class="resultRow header">
      <div class="resultCell nameAndType">
        <div class="label inline" style="color: var(--color-name);">Name</div>
        <div class="label inline" style="color: var(--color-txt-default);">&</div>
        <div class="label inline" style="color: var(--color-type);">Branch</div>
      </div>
      <div class="resultCell distance centerContent">
        <div class="label distanceAdjust" style="color: var(--color-dst);">dst.</div>
      </div>
      <div class="resultCell nameAndType">
        <div class="label">Description</div>
      </div>
    </div>
  </div>
</div>

<!-- data -->
<!-- aria-live: screen readers announce the new results when they arrive -->
<div id="results" loading="none" time="0" aria-live="polite"></div>
