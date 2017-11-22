@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        @if(Session::has('success_msg'))
        <div class="alert alert-success">{{ Session::get('success_msg') }}</div>
        @endif
    <!-- Posts list -->
    @if(!empty($restaurantTypes))
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Posts List </h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{ route('restaurantType.add') }}"> Add New</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <table class="table table-striped task-table">
                    <!-- Table Headings -->
                    <thead>
                        <th width="65%">Label</th>
                        <th width="15%">Created</th>
                        <th width="20%">Action</th>
                    </thead>
    
                    <!-- Table Body -->
                    <tbody>
                    @foreach($restaurantTypes as $restaurantType)
                        <tr>
                            <td class="table-text">
                                <div>{{$restaurantType->label}}</div>
                            </td>
                                <td class="table-text">
                                <div>{{$restaurantType->created_at}}</div>
                            </td>
                            <td>
                                <a href="{{ route('restaurantType.details', $restaurantType->id) }}" class="label label-success">Details</a>
                                <a href="{{ route('restaurantType.edit', $restaurantType->id) }}" class="label label-warning">Edit</a>
                                <a href="{{ route('restaurantType.delete', $restaurantType->id) }}" class="label label-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    </div>
</div>
@endsection