@extends('layouts.app')

@section('title', 'Restaurants for lunch in Shibuya')
@section('description', 'Quickly find the best restaurants for having lunch in Shibuya.')

@section('content')
<!-- data-results-url: the URL the JavaScript calls to get the results fragment -->
<div id="content" data-results-url="{{ route('restaurants.results') }}">

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
