@extends('layouts.html')

@section('title', 'Restaurant Create')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left"><h2>Add New restaurant</h2></div>
            <div class="pull-right"><a class="btn btn-primary" href="{{ route('restaurants.index') }}"> Back</a></div>
        </div>
    </div>

    @include('admin.restaurants.errors')

    <form method="POST" action="{{ route('restaurants.store') }}">
        @csrf
        @include('admin.restaurants.form', ['restaurant' => null])
    </form>
@endsection
