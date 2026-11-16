@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - Stores in Shibuya</title>
  <meta name="description" content="Quickly find the best stores for shopping in Shibuya.">
@endsection

@section('content')
<!-- data-results-url: the URL the JavaScript calls to get the results fragment -->
<div id="content" data-results-url="{{ route('stores.results') }}">

  <div id="mainFilter">
    @include('stores.filter')
  </div>

  <div id="mainResults" style="display: none">
    @include('stores.results')
  </div>

</div>

<div id="mainDetails" class="content-unchained" style="display: none">
</div>

@endsection
