<!-- The Javascript to show details from the results list -->
<script>

/** Fetch the details fragment (e.g. /restaurants/details/12) and put it in the details div */
function ajaxDetails(url) {
  getDetailsDiv().innerHTML = "Loading...";
  console.info("GET: " + url);
  fetch(url)
    .then(response => response.ok ? response.text() : Promise.reject(response.status))
    .then(html => { getDetailsDiv().innerHTML = html; })
    .catch(() => {
      getDetailsDiv().innerHTML = "Sorry, detailed data could not be retrieved. Check your Internet connection and try again.";
    });
}

/** Called when a result name is clicked: load the details (unless already loaded) and show them instead of the list */
function showDetails(url, placeId) {
  // Only fetch if a different place than the one currently displayed
  if (placeId != getCurrentDetailsId()) {
      ajaxDetails(url);
  }
  // Swap the two panels
  getDetailsDiv().style.display = "block";
  document.getElementById("content").style.display = "none";
}

/** "BACK" button: swap the panels back */
function hideDetails() {
  getDetailsDiv().style.display = "none";
  document.getElementById("content").style.display = "flex";
}

/** Id of the place currently in the details div (from the detailId attribute of its name), or null */
function getCurrentDetailsId() {
  const name = document.getElementById("detailName");
  return name == null ? null : name.getAttribute("detailId");
}

</script>
