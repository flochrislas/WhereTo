// from CRUD tuto https://appdividend.com/2017/10/15/laravel-5-5-crud-example-tutorial/
@extends('layouts.app')

@section('content')
<div class="container">
    @if(\Session::has('success'))
        <div class="alert alert-success">
            {{\Session::get('success')}}
        </div>
    @endif
   
    <div class="row">
       <a href="{{url('/create/restaurant')}}" class="btn btn-success">Create restaurant</a>
       <a href="{{url('/restaurants')}}" class="btn btn-default">All restaurants</a>
    </div>
</div>
@endsection