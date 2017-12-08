@extends('layouts.app')

@section('title', 'Restaurant Index')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Restaurants</h2>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Type</th>
            <th>Details</th>
        </tr>
    @foreach ($restaurants as $restaurant)
    <tr>
        <td>{{ $restaurant->id }}</td>
        <td>{{ $restaurant->name}}</td>
        <td>{{ $restaurant->type}}</td>
        <td>
            <a class="btn btn-info" href="{{ route('restaurants.details',$restaurant->id) }}">Show</a>
        </td>
    </tr>
    @endforeach
    </table>

@endsection
