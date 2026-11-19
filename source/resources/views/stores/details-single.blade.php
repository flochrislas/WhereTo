@extends('layouts.app')

@section('title', __(':name - Store info', ['name' => $place->nameWithStatus()]))
@section('description', __('Detailed information page for :name, in Tokyo Shibuya. Ratings and practical information.', ['name' => $place->nameWithStatus()]))
@section('heading', $place->name)

@section('content')
  <x-structured-data :place="$place" />
  <x-business-status :place="$place" />
  <x-indexing :place="$place" />
<div id="content-unchained">

  <div id="resultsControl">
    <button class="sortButton"
      onclick="window.open('{{ $place->google_maps_link }}', '_blank')">
      {{ __('Google maps') }}</button>
    <button class="sortButton"
      onclick="window.open('{{ $place->official_website }}', '_blank')">
      {{ __('Website') }}</button>
    <button class="navButton" onclick="history.back(-1)">{{ __('BACK') }}</button>
  </div>

  <div id="detailTitle" class="detailRow">
      <div id="detailName" class="detail" lang="en"><div>{{ $place->name }}</div></div>
      <div id="detailType" class="detail" lang="en"><div>{{ $place->type }}</div></div>
      <div id="detailLocation" class="detail" lang="en"><div>{{ $place->location }}</div></div>
  </div>

  <div class="detailRow">
      <div class="detail border15"
           overlay="{{ __('Main point') }}">
      <span lang="en">{{ $place->points }}</span></div>
  </div>

  <div class="detailRow">
      <div class="detail"
           overlay="{{ __('Personal experience') }}">
        {{-- Raw on purpose: two store texts in the seeder carry an <a href> link. The seeders are the only source of this text (the architecture test allows the raw syntax in these two files only) --}}
        <span lang="en">{!! $place->experience !!}</span>
      </div>
  </div>

  <div class="detailRow">
    <div class="detail">
    @foreach ($place->tagsCached() as $placeTag)
        <span class="tag tagType{{$placeTag->type}}" data-tag="{{$placeTag->label}}">
          {{ tag_label($placeTag->label) }}
        </span>
    @endforeach
    </div>
  </div>

  @if ($place->visited != 1)
    <div class="mysteryDot"> . </div>
  @endif

</div>
@endsection
