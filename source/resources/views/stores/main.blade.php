@extends('layouts.app')

@section('title', __('Stores in Shibuya'))
@section('description', __('Quickly find the best stores for shopping in Shibuya.'))

@section('content')
<x-indexing />
<!-- data-results-url: the URL the JavaScript calls to get the results fragment.
     data-details-url: the URL of the details fragment of a place, PLACE_ID standing for its id (the JavaScript opens
     the place of a shared link, ?place=12, with it) -->
<div id="content" data-results-url="{{ \App\Support\LocaleUrl::route('stores.results') }}"
     data-details-url="{{ \App\Support\LocaleUrl::route('stores.details', 'PLACE_ID') }}">

  <div id="mainFilter">
    @include('stores.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('stores.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

@endsection
