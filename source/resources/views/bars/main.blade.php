@extends('layouts.app')

@section('title', 'Bars')

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('bars.filter')
  </div>

  <div id="mainResults">
    @include('bars.results')
  </div>

</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = 'bars/results';
</script>
@include('inline-javascript.main')

@endsection
