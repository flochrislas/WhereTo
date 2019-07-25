@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - Find the best places in Shibuya</title>
  <meta name="description" content="Quickly find the best places in Shibuya. Search for restaurants, izakaya, bars, pubs, karaoke, stores, malls, and more.">
@endsection

@section('content')
<div id="content">
  @if (session('status'))
      <div>
          {{ session('status') }}
      </div>
  @endif

    @include('suggestions.suggestion')

  <div class="grid">
    <a class="giantButton" href="{{ route('restaurants.main') }}" onclick="getLocation()">
      Lunch
      <br/>in<br/>Shibuya
    </a>

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

    <a class="giantButton sns" href="https://facebook.com/enjoyShibuya/" target="_blank">
      Facebook
    </a>

    <a class="giantButton sns" href="https://www.instagram.com/enjoyshibuya/" target="_blank">
      Instagram
    </a>

  </div>
  <div class="note"></div>
</div>
@endsection
