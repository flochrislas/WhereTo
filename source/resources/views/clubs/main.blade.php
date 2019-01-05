@extends('layouts.app')

@section('title', 'Clubs')

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('clubs.filter')
  </div>

  <div id="mainResults">
    @include('clubs.results')
  </div>

</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = 'clubs/results';
</script>
@include('inline-javascript.main')

@endsection
