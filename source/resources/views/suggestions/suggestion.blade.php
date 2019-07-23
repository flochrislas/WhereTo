@inject('suggestionController', 'App\Http\Controllers\SuggestionController')

@if($suggestionController::isLunchTime())
    <div id="suggestion">
        It's about lunch time! <div id="suggestButton" onclick="ajaxSuggestion()">Get a suggestion</div>
    </div>
@else
    <div id="suggestion">

    </div>
@endif

@include('inline-javascript.suggestion')