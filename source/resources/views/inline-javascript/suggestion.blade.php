<!-- The Javascript to show a suggestion -->
<script>

    function getSuggestionDiv() {
        return document.getElementById("suggestion");
    }

    /**
     * Actually send the request to the server
     * use at the end of the page to load a suggestion
     */
    function ajaxSuggestion() {
        let url = "{{ e(route('suggestions')) }}"
                + '?position=' + getLocation();
        let xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        console.info("GET: " + url);
        xhr.onload = function() {
            if (xhr.status === 200) {
                getSuggestionDiv().innerHTML = xhr.responseText + '<div id="suggestButton" onclick="ajaxSuggestion()">Try again</div>';
            }
            else {
                getSuggestionDiv().innerHTML = "Err(or), nothing from the top of my head, sorry.";
            }
        };
        xhr.send();
    }

</script>
