@extends('layouts.app')

@section('metatags')
  <title>{{config('app.name')}} - {{$place->name}} - Restaurant info & {{ str_limit($place->updated_at, $limit = 4, $end = '') }} review</title>
  <meta name="description" content="Detailed information page for the restaurant {{$place->name}}, in Tokyo Shibuya. Full {{ str_limit($place->updated_at, $limit = 4, $end = '') }} review and ratings.">
@endsection

@section('content')
  <div id="content-unchained">
    @include('restaurants.details')
  </div>
@endsection
