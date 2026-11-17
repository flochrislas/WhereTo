@inject('suggestionController', 'App\Http\Controllers\SuggestionController')

@if($suggestionController::isLunchTime())
    <!-- data-suggestion-url: the URL the JavaScript calls to get a suggestion -->
    <div id="suggestion" data-suggestion-url="{{ route('suggestions') }}" aria-live="polite">
        It's about lunch time! <button type="button" id="suggestButton" onclick="ajaxSuggestion()">Get a suggestion</button>
    </div>
@else
    <!--<div id="suggestion"></div>-->
@endif
