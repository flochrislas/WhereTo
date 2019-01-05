@extends('layouts.app')

@section('title', 'Restaurants')

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('restaurants.filter')
  </div>

  <div id="mainResults">
    @include('restaurants.results')
  </div>

</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = 'restaurants/results';
</script>
@include('inline-javascript.main')

@endsection
