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

    <a class="giantButton locked" href="{{ route('stores.main') }}">
      Shopping
      <br/>in<br/>Shibuya
      <div class="purchasePrice">50</div>
    </a>

    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Dating
      <br/>in<br/>Shibuya
      <div class="purchasePrice">50</div>
    </a>

    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      WhatsNext<br/>Extension
      <div class="purchasePrice">120</div>
    </a>
    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Hashigo<br/>Extension<br/>
      <div class="purchasePrice">120</div>
    </a>

    <a class="giantButton locked" href="{{ route('clubs.main') }}">
      Clubbing in<br/>Shibuya
      <div class="purchasePrice">50</div>
    </a>
    <a class="giantButton locked" href="{{ route('bars.main') }}">
      Drinking in<br/>Shibuya
      <div class="purchasePrice">50</div>
    </a>

    <a class="giantButton locked" href="{{ route('karaokes.main') }}">
      Karaoke
      <br/>in<br/>Shibuya
      <div class="purchasePrice">50</div>
    </a>

    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Hair Salon
      <br/>in<br/>Shibuya
      <div class="purchasePrice">50</div>
    </a>

    <a class="giantButton locked" href="{{ route('restaurants.main') }}">
      Shibuya's Secrets
      <br/>MEGAPACK
      <div class="purchasePrice">500</div>
    </a>
  </div>
</div>
@endsection
