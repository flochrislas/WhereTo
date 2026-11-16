@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - Karaoke in Shibuya</title>
  <meta name="description" content="Quickly find the best karaoke for you in Shibuya.">
@endsection

@section('content')
<!-- data-results-url: the URL the JavaScript calls to get the results fragment -->
<div id="content" data-results-url="{{ route('karaokes.results') }}">

  <div id="mainFilter">
    @include('karaokes.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('karaokes.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

@endsection
