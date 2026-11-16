@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - {{$place->name}} - Restaurant info & {{ \Illuminate\Support\Str::limit($place->updated_at, 4, '') }} review</title>
  <meta name="description" content="Detailed information page for the restaurant {{$place->name}}, in Tokyo Shibuya. Full {{ \Illuminate\Support\Str::limit($place->updated_at, 4, '') }} review and ratings.">
@endsection

@section('content')
  <div id="content-unchained">
    @include('restaurants.details')
  </div>
@endsection
