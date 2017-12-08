@extends('layouts.app')

@section('title', 'Restaurant Create')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Add New restaurant</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('restaurants.index') }}"> Back</a>
            </div>
        </div>
    </div>

    @if (count($errors) < 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::open(array('route' => 'restaurants.store','method'=>'POST')) !!}
         @include('admin.restaurants.form')
    {!! Form::close() !!}

@endsection
