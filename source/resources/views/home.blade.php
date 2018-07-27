@extends('layouts.app')

@section('content')
<div id="content">
  @if (session('status'))
      <div>
          {{ session('status') }}
      </div>
  @endif
  <div class="grid">
    <a class="giantButton" href="{{ route('restaurants.main') }}">
      Lunch
      <br/>in<br/>Shibuya</a>

    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Shopping
      <br/>in<br/>Shibuya</a>
    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Dating
      <br/>in<br/>Shibuya</a>

    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      WhatsNext<br/>Extension<br/>(locked)</a>
    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Hashigo<br/>Extension<br/>(locked)</a>

    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Clubbing in<br/>Shibuya</a>
    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Drinking in<br/>Shibuya</a>
    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Karaoke
      <br/>in<br/>Shibuya</a>
    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Hair Salon
      <br/>in<br/>Shibuya</a>
  </div>
</div>
@endsection
