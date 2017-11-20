// tuto https://appdividend.com/2017/10/15/laravel-5-5-crud-example-tutorial/

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
    <form method="post" action="{{action('restaurantController@update', $id)}}" >
        {{csrf_field()}}
        <input name="_method" type="hidden" value="PATCH">
        <div class="form-group">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <label for="name">restaurant name:</label>
            <input type="text" class="form-control" name="name" value={{$restaurant->name}} />
        </div>
        <div class="form-group">
            <label for="type">restaurant type:</label>
            <textarea cols="5" rows="5" class="form-control" name="type">{{$restaurant->type}}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection