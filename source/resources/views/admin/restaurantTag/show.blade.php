@extends('layouts.app')

@section('title', 'Restaurant Tag Show')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Read Tag</h2>
        </div>
        <div class="pull-right">
            <a href="{{ route('restaurantTag.index') }}" class="label label-primary pull-right"> Back</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Label:</strong>
            {{ $restaurantTag->label }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Published On:</strong>
            {{ $restaurantTag->created_at }}
        </div>
    </div>
</div>
@endsection
