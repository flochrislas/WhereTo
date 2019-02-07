<!-- The Javascript to show details from results list -->
<script>

/** Actually send the request to the server */
function ajaxDetails(url) {
  getDetailsDiv().innerHTML = "Loading...";
  var xhr = new XMLHttpRequest();
  xhr.open('GET', url);
  console.info("GET: " + url);
  xhr.onload = function() {
      if (xhr.status === 200) {
          getDetailsDiv().innerHTML = xhr.responseText;
      }
      else {
          getDetailsDiv().innerHTML = "Sorry, detailed data could not be retreived. Check your Internet connection and try again.";
      }
  };
  xhr.send();
}

function showDetails(url, placeId) {
  if (placeId != getCurrentDetailsId()) {
      ajaxDetails(url);
  }
  getDetailsDiv().style.display = "block";
  //document.getElementById("content-unchained").style.display = "block";
  document.getElementById("content").style.display = "none";
}

function hideDetails() {
  getDetailsDiv().style.display = "none";
  //document.getElementById("content-unchained").style.display = "none";
  document.getElementById("content").style.display = "flex";
}

function getCurrentDetailsId() {
  if (document.getElementById("detailName") == null) {
    return null;
  }
  return document.getElementById("detailName").getAttribute("detailId");
}

</script>
