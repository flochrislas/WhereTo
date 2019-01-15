<div id="resultsTop">

  <!-- buttons -->
  <div id="resultsControl">
    <button class="sortButton" onclick="refreshResults('ratings')">
      Best<br>first</button>
    <button class="sortButton" onclick="refreshResults('cost')">
      Cost<br>first</button>
    <button class="sortButton" onclick="refreshResults('distance')">
      Near<br>first</button>
    <button class="navButton" onclick="showFilter()">Show Filters</button>
  </div>

  <!-- fixed labels -->
    <div class="resultTable">
      <div class="resultRow header">
        <div class="resultCell nameAndType">
          <div class="label inline" style="color: var(--color-name);">Name</div>
          <div class="label inline" style="color: var(--color-txt-default);">&</div>
          <div class="label inline" style="color: var(--color-type);">Type</div>
        </div>
        <div class="resultCell distance centerContent">
          <div class="label distanceAdjust" style="color: var(--color-dst);">dst.</div>
        </div>
        <div class="resultCell centerContent">
          <div class="label fourDigitsAdjust" style="color: var(--color-cost);">cost</div>
        </div>
        <div class="resultCell centerContent">
          <div class="label" style="color: var(--color-lunch);">lunch</div>
        </div>
        <div class="resultCell centerContent">
          <div class="label" style="color: var(--color-food);">food</div>
        </div>
        <div class="resultCell centerContent">
          <div class="label" style="color: var(--color-place);">place</div>
        </div>
        <div class="resultCell centerContent">
          <div class="label" style="color: var(--color-price);">price</div>
        </div>
        <div class="resultCell centerContent">
          <div class="label" style="color: var(--color-date);">date</div>
        </div>
      </div>
    </div>
  </div>

<!-- data -->
<div id="results">empty</div>
