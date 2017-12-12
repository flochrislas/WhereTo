@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Admin Dashboard</div>

                <div class="panel-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in as {{Auth::user()}}.
                    <br>
                    <br>
                    <a class="btn btn-info" href="{{ route('restaurants.index') }}">Manage restaurants</a>
                    <a class="btn btn-info" href="{{ route('restaurantTag.index') }}">Manage restaurants tags</a>
                    <a class="btn btn-info" href="{{ route('restaurantType.index') }}">Manage restaurants types</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
