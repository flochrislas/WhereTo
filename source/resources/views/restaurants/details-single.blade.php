@extends('layouts.app')

@section('title', $place->nameWithStatus().' - Restaurant info'.($place->visit_date ? ' & '.substr($place->visit_date, 0, 4).' review' : ''))
@section('description', 'Detailed information page for the restaurant '.$place->nameWithStatus().', in Tokyo Shibuya. '.($place->visit_date ? 'Full '.substr($place->visit_date, 0, 4).' review and ratings.' : 'Ratings and practical information.'))
@section('heading', $place->name)

@section('content')
  <x-structured-data :place="$place" />
  <x-business-status :place="$place" />
  <div id="content-unchained">
    @include('restaurants.details')
  </div>
@endsection
