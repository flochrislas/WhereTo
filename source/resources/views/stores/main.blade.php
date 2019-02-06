@extends('layouts.app')

@section('title', 'Stores')

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('stores.filter')
  </div>

  <div id="mainResults">
    @include('stores.results')
  </div>

</div>

<div id="content-unchained">
  <div id="mainDetails"></div>
</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = 'stores/results';
</script>
@include('inline-javascript.main')

@endsection
