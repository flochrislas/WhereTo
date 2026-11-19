@extends('layouts.app')

@section('title', __('Restaurants for lunch in Shibuya'))
@section('description', __('Quickly find the best restaurants for having lunch in Shibuya.'))

@section('content')
<x-indexing />
<!-- data-results-url: the URL the JavaScript calls to get the results fragment.
     data-details-url: the URL of the details fragment of a place, PLACE_ID standing for its id (the JavaScript opens
     the place of a shared link, ?place=12, with it) -->
<div id="content" data-results-url="{{ \App\Support\LocaleUrl::route('restaurants.results') }}"
     data-details-url="{{ \App\Support\LocaleUrl::route('restaurants.details', 'PLACE_ID') }}">

  <div id="mainFilter">
    @include('restaurants.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('restaurants.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

@endsection
