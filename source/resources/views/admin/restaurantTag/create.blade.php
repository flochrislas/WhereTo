@extends('layouts.app')addadd

@section('title', 'Restaurant Tag Create')

@section('content')
<div class="row">
    <div class="col-lg-12">
        @if($errors->any())
            <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach()
            </div>
        @endif
        <div class="panel panel-default">
            <div class="panel-heading">
                Add a New Restaurant Tag <a href="{{ route('restaurantTag.index') }}" class="label label-primary pull-right">Back</a>
            </div>
            <div class="panel-body">
                <form action="{{ route('restaurantTag.store') }}" method="POST" class="form-horizontal">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label col-sm-2" >Label</label>
                        <div class="col-sm-10">
                            <input type="text" name="label" id="label" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" class="btn btn-default" value="Add Resaurant Tag" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
