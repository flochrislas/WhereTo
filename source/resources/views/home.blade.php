@extends('layouts.app')

@section('content')
<div id="content">
  @if (session('status'))
      <div>
          {{ session('status') }}
      </div>
  @endif
  <div class="grid">
    <a class="giantButton" href="{{ route('restaurants.main') }}" onclick="getLocation()">
      Lunch
      <br/>in<br/>Shibuya</a>

    <a class="giantButton locked" href="{{ route('stores.main') }}">
      Shopping
      <br/>in<br/>Shibuya
      <div class="alphaVersion">alpha version</div>
    </a>

    <a class="giantButton locked" href="{{ route('bars.main') }}">
      Drinking in<br/>Shibuya
      <div class="alphaVersion">alpha version</div>
    </a>

    <a class="giantButton locked" href="{{ route('karaokes.main') }}">
      Karaoke
      <br/>in<br/>Shibuya
      <div class="alphaVersion">alpha version</div>
    </a>

  </div>
  <div class="note"></div>
</div>
@endsection
