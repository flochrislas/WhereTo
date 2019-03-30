@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - Restaurants for lunch in Shibuya</title>
  <meta name="description" content="Quickly find the best restaurants for having lunch in Shibuya.">
@endsection

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('restaurants.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('restaurants.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = "{{ route('restaurants.results') }}";
</script>
@include('inline-javascript.main')

@endsection
