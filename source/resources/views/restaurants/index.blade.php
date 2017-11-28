// restaurants index

@extends('layouts.html')

@section('title', 'Restaurant Index')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Restaurants CRUD</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('restaurants.create') }}"> Create New restaurant</a>
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
            <th width="280px">Action</th>
        </tr>
    @foreach ($restaurants as $restaurant)
    <tr>
        <td>{{ $restaurant->id }}</td>
        <td>{{ $restaurant->name}}</td>
        <td>{{ $restaurant->type}}</td>
        <td>
            <a class="btn btn-info" href="{{ route('restaurants.show',$restaurant->id) }}">Show</a>
            <a class="btn btn-primary" href="{{ route('restaurants.edit',$restaurant->id) }}">Edit</a>
            {!! Form::open(['method' => 'DELETE','route' => ['restaurants.destroy', $restaurant->id],'style'=>'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
    </table>

@endsection
