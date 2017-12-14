@extends('layouts.app')

@section('title', 'Restaurant Tag Index')

@section('content')
<div class="row">
    <div class="col-lg-12">
        @if(Session::has('success_msg'))
        <div class="alert alert-success">{{ Session::get('success_msg') }}</div>
        @endif
    <!-- Posts list -->
    @if(!empty($restaurantTags))
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Posts List </h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{ route('restaurantTag.create') }}"> Add New</a>
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
                    @foreach($restaurantTags as $restaurantTag)
                        <tr>
                            <td class="table-text">
                                <div>{{$restaurantTag->label}}</div>
                            </td>
                                <td class="table-text">
                                <div>{{$restaurantTag->created_at}}</div>
                            </td>
                            <td>
                                <a href="{{ route('restaurantTag.show', $restaurantTag->id) }}" class="label label-success">Details</a>
                                <a href="{{ route('restaurantTag.edit', $restaurantTag->id) }}" class="label label-warning">Edit</a>
                                <a href="{{ route('restaurantTag.destroy', $restaurantTag->id) }}" class="label label-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
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
