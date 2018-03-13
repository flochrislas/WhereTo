@extends('layouts.app')

@section('content')
<div id="content">
  <div>Home</div>
  <div>
      @if (session('status'))
          <div>
              {{ session('status') }}
          </div>
      @endif
      <a href="{{ route('restaurants.main') }}">Voir les restos</a>
  </div>
</div>
@endsection
