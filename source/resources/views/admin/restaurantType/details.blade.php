@extends('layouts.html')

@section('title', 'Restaurant Type Show')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Read Type</h2>
        </div>
        <div class="pull-right">
            <a href="{{ route('restaurantType.index') }}" class="label label-primary pull-right"> Back</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Label:</strong>
            {{ $restaurantType->label }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Published On:</strong>
            {{ $restaurantType->created_at }}
        </div>
    </div>
</div>
@endsection