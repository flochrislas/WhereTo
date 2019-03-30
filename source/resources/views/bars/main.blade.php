@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - Places to drink in Shibuya</title>
  <meta name="description" content="Quickly find the best place for drinking in Shibuya.">
@endsection

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('bars.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('bars.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = "{{ route('bars.results') }}";
</script>
@include('inline-javascript.main')

@endsection
