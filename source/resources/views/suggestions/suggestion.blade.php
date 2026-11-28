@inject('suggestionController', 'App\Http\Controllers\SuggestionController')

{{-- The stand-alone app (App\Support\Standalone) is exported once and used at any time of day: the control is always
     printed, hidden, and whereto-local.js shows it at lunch time by the phone's clock (Tokyo time) --}}
@if (\App\Support\Standalone::active())
    <div id="suggestion" data-suggestion-url="{{ \App\Support\LocaleUrl::route('suggestions') }}" aria-live="polite" hidden>
        {{ __("It's about lunch time!") }} <button type="button" id="suggestButton" onclick="ajaxSuggestion()">{{ __('Get a suggestion') }}</button>
    </div>
@elseif($suggestionController::isLunchTime())
    <!-- data-suggestion-url: the URL the JavaScript calls to get a suggestion -->
    <div id="suggestion" data-suggestion-url="{{ \App\Support\LocaleUrl::route('suggestions') }}" aria-live="polite">
        {{ __("It's about lunch time!") }} <button type="button" id="suggestButton" onclick="ajaxSuggestion()">{{ __('Get a suggestion') }}</button>
    </div>
@else
    <!--<div id="suggestion"></div>-->
@endif
