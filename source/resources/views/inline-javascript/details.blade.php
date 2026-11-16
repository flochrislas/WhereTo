<!-- The Javascript to show details from the results list -->
<script>

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

function showDetails(url, placeId) {
  if (placeId != getCurrentDetailsId()) {
      ajaxDetails(url);
  }
  getDetailsDiv().style.display = "block";
  document.getElementById("content").style.display = "none";
}

function hideDetails() {
  getDetailsDiv().style.display = "none";
  document.getElementById("content").style.display = "flex";
}

function getCurrentDetailsId() {
  const name = document.getElementById("detailName");
  return name == null ? null : name.getAttribute("detailId");
}

</script>
