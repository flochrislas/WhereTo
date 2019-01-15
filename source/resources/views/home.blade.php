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
  <div class="note"><b>Note:</b> The app will try to get your GPS coordinates.<br>
    If you refuse, or if it fails, or if you are too far from Shibuya, the app will give distances from Shibuya's station, instead of your current position.<br>
    Coming back to this home page would refresh your position.</div>
</div>
@endsection
