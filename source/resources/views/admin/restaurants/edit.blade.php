@extends('layouts.html')

@section('title', 'Restaurant Edit')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left"><h2>Edit restaurant</h2></div>
            <div class="pull-right"><a class="btn btn-primary" href="{{ route('restaurants.index') }}"> Back</a></div>
        </div>
    </div>

    @include('admin.restaurants.errors')

    <form method="POST" action="{{ route('restaurants.update', $restaurant->id) }}">
        @csrf
        @method('PATCH')
        @include('admin.restaurants.form')
    </form>
@endsection
