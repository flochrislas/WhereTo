@extends('layouts.app')

@section('title', __('VIP'))
@section('description', __('VIP area of :app, coming later.', ['app' => config('app.name')]))

@section('content')
<div id="content">
  <div class="note">{{ __('VIP area: coming later.') }}</div>
</div>
@endsection
