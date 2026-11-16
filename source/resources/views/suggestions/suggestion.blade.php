@inject('suggestionController', 'App\Http\Controllers\SuggestionController')

@if($suggestionController::isLunchTime())
    <!-- data-suggestion-url: the URL the JavaScript calls to get a suggestion -->
    <div id="suggestion" data-suggestion-url="{{ route('suggestions') }}">
        It's about lunch time! <div id="suggestButton" onclick="ajaxSuggestion()">Get a suggestion</div>
    </div>
@else
    <!--<div id="suggestion"></div>-->
@endif
