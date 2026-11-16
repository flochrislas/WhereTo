<!-- The Javascript to show a suggestion -->
<script>

    function getSuggestionDiv() {
        return document.getElementById("suggestion");
    }

    function ajaxSuggestion() {
        const url = "{{ e(route('suggestions')) }}" + '?position=' + getLocation();
        console.info("GET: " + url);
        fetch(url)
            .then(response => response.ok ? response.text() : Promise.reject(response.status))
            .then(html => {
                getSuggestionDiv().innerHTML = html + '<div id="suggestButton" onclick="ajaxSuggestion()">Try again</div>';
            })
            .catch(() => {
                getSuggestionDiv().innerHTML = "Err(or), nothing from the top of my head, sorry.";
            });
    }

</script>
