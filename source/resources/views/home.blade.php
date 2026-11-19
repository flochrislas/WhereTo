@extends('layouts.app')

@section('title', __('Find the best places in Shibuya'))
@section('description', __('Quickly find the best places in Shibuya. Search for restaurants, izakaya, bars, pubs, karaoke, stores, malls, and more.'))

@section('content')
{{-- hreflang links to this page in every language (the home page is indexable in all of them) --}}
<x-indexing />
<div id="content">
  @if (session('status'))
      <div>
          {{ session('status') }}
      </div>
  @endif

    @include('suggestions.suggestion')

  {{-- One whole sentence per button, so each language can use its own word order ("渋谷で ランチ"); the narrow
       button wraps it (style.css keeps the Japanese words whole, the space is where the line may break) --}}
  <div class="grid">
    <a class="giantButton" href="{{ \App\Support\LocaleUrl::route('restaurants.main') }}" onclick="startGeolocation()">
      {{ __('Lunch in Shibuya') }}
    </a>

    <a class="giantButton locked" href="{{ \App\Support\LocaleUrl::route('stores.main') }}">
      {{ __('Shopping in Shibuya') }}
      <div class="alphaVersion">{{ __('alpha version') }}</div>
    </a>

    <a class="giantButton locked" href="{{ \App\Support\LocaleUrl::route('bars.main') }}">
      {{ __('Drinking in Shibuya') }}
      <div class="alphaVersion">{{ __('alpha version') }}</div>
    </a>

    <a class="giantButton locked" href="{{ \App\Support\LocaleUrl::route('karaokes.main') }}">
      {{ __('Karaoke in Shibuya') }}
      <div class="alphaVersion">{{ __('alpha version') }}</div>
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
