@extends('layouts.app')

@section('title', $place->name.' - Restaurant info'.($place->visit_date ? ' & '.substr($place->visit_date, 0, 4).' review' : ''))
@section('description', 'Detailed information page for the restaurant '.$place->name.', in Tokyo Shibuya. '.($place->visit_date ? 'Full '.substr($place->visit_date, 0, 4).' review and ratings.' : 'Ratings and practical information.'))
@section('heading', $place->name)

@section('content')
  <x-structured-data :data="$place->structuredData(url()->current())" />
  <div id="content-unchained">
    @include('restaurants.details')
  </div>
@endsection
