@extends('layouts.app')

@section('title', 'Karaoke')

@section('content')
<div id="content">

  <div id="mainFilter">
    @include('karaokes.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('karaokes.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

<script>
  // To be used in ajax calls for reading results
  var resultsUrl = "{{ route('karaokes.results') }}";
</script>
@include('inline-javascript.main')

@endsection
