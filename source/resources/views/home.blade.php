@extends('layouts.app')

@section('content')
<div id="content">
  @if (session('status'))
      <div>
          {{ session('status') }}
      </div>
  @endif

  <a class="giantButton" href="{{ route('restaurants.main') }}">Lunch in<br/>Shibuya</a>

</div>
@endsection
