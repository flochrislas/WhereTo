@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - Stores in Shibuya</title>
  <meta name="description" content="Quickly find the best stores for shopping in Shibuya.">
@endsection

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('stores.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('stores.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = "{{ route('stores.results') }}";
</script>
@include('inline-javascript.main')

@endsection
