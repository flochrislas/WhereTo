@extends('layouts.app')

@section('title', 'Restaurants')

@section('content')
<div id="content">

  <div id="restaurantsFilter">
    @include('restaurants.filter')

    <button onclick="showResults()">Display Results</button>

  </div>

  <div id="restaurantsResults">
    @include('restaurants.results')

    <button onclick="showFilter()">Display Filter</button>
  </div>

</div>

<script>
function showResults() {
  var restaurantsFilter = document.getElementById("restaurantsFilter");
  var restaurantsResults = document.getElementById("restaurantsResults");
  restaurantsFilter.style.display = "none";
  restaurantsResults.style.display = "block";
}
function showFilter() {
  var restaurantsFilter = document.getElementById("restaurantsFilter");
  var restaurantsResults = document.getElementById("restaurantsResults");
  restaurantsFilter.style.display = "block";
  restaurantsResults.style.display = "none";
}
</script>
@endsection
