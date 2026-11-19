@extends('layouts.app')

{{-- The year of the review comes from visit_date; the place's own text stays English (lang="en" in the details) --}}
@php($year = $place->visit_date ? substr($place->visit_date, 0, 4) : null)
@section('title', $year ? __(':name - Restaurant info & :year review', ['name' => $place->nameWithStatus(), 'year' => $year]) : __(':name - Restaurant info', ['name' => $place->nameWithStatus()]))
@section('description', $year ? __('Detailed information page for the restaurant :name, in Tokyo Shibuya. Full :year review and ratings.', ['name' => $place->nameWithStatus(), 'year' => $year]) : __('Detailed information page for the restaurant :name, in Tokyo Shibuya. Ratings and practical information.', ['name' => $place->nameWithStatus()]))
@section('heading', $place->name)

@section('content')
  <x-structured-data :place="$place" />
  <x-business-status :place="$place" />
  <x-indexing :place="$place" />
  <div id="content-unchained">
    @include('restaurants.details')
  </div>
@endsection
