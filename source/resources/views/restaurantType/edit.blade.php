@extends('layouts.app')

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
                Edit restaurantType <a href="{{ route('restaurantType.index') }}" class="label label-primary pull-right">Back</a>
            </div>
            <div class="panel-body">
                <form action="{{ route('restaurantType.update', $restaurantType->id) }}" method="restaurantType" class="form-horizontal">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label col-sm-2" >Label</label>
                        <div class="col-sm-10">
                            <input type="text" name="label" id="label" class="form-control" value="{{ $restaurantType->label }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" class="btn btn-default" value="Update Restaurant Type" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection