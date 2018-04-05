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

<BR><BR>
<button onclick="getResults()">Ey JaXx</button>
<BR><BR>

<div id="demo">
  demo
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

function getResults() {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'demo');
  xhr.onload = function() {
      if (xhr.status === 200) {
          document.getElementById("demo").innerHTML = xhr.responseText;
      }
  };
  xhr.send();
}

function updateResults() {
  var xhr = new XMLHttpRequest();
  xhr.open('PUT', 'restaurants');
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
      if (xhr.status === 200) {
          var userInfo = JSON.parse(xhr.responseText);
          document.getElementById("demo").innerHTML = xhr.responseText;
      }
  };
  xhr.send(JSON.stringify({
      name: 'John Smith',
      age: 34
  }));
}

</script>
@endsection
