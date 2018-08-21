@extends('layouts.app')

@section('title', 'Karaoke')

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('karaokes.filter')
  </div>

  <div id="mainResults">
    @include('karaokes.results')
  </div>

</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = 'karaokes/results';
</script>
@include('inline-javascript.main')

@endsection
