@extends('layouts.app')

@section('title', 'Places to drink in Shibuya')
@section('description', 'Quickly find the best place for drinking in Shibuya.')

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
