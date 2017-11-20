// CRUD tuto: https://appdividend.com/2017/10/15/laravel-5-5-crud-example-tutorial/

@extends('layouts.app')

@section('content')
<div class="container">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div><br />
@endif
    <div class="row">
    <form method="post" action="{{url('/create/restaurant')}}">
        <div class="form-group">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <label for="title">Restaurant Title:</label>
            <input type="text" class="form-control" name="title"/>
        </div>
        <div class="form-group">
            <label for="description">Restaurant Description:</label>
            <textarea cols="5" rows="5" class="form-control" name="description"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>
@endsection