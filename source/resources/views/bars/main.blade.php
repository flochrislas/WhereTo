@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - Places to drink in Shibuya</title>
  <meta name="description" content="Quickly find the best place for drinking in Shibuya.">
@endsection

@section('content')
<!-- data-results-url: the URL the JavaScript calls to get the results fragment -->
<div id="content" data-results-url="{{ route('bars.results') }}">

  <div id="mainFilter">
    @include('bars.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('bars.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

@endsection
