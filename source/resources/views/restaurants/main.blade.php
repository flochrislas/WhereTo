@extends('layouts.app')

@section('title', 'Restaurant Index')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Restaurants</h2>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <!-- Auto complete Experimental -->
    <form action='' method='post'>
      <p><label>Country:</label>
        <input type='text' name='country' value='' class='auto'>
      </p>
    </form>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
    <script type="text/javascript">
      $(function() {
        $(".auto").autocomplete({
          source: "autocomplete.php",
          minLength: 1
        });
      });
    </script>

    <br>
    <!-- SEARCH http://justlaravel.com/search-functionality-laravel/ -->
    <form action="/search" method="POST" role="search">
        {{ csrf_field() }}
        <div class="input-group">
            <input type="text" class="form-control" name="type"
                    placeholder="Search type">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-default">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
            </span>
        </div>
    </form>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Type</th>
            <th>Details</th>
        </tr>
    @foreach ($restaurants as $restaurant)
    <tr>
        <td>{{ $restaurant->id }}</td>
        <td>{{ $restaurant->name}}</td>
        <td>{{ $restaurant->type}}</td>
        <td>
            <a class="btn btn-info" href="{{ route('restaurants.details',$restaurant->id) }}">Show</a>
        </td>
    </tr>
    @endforeach
    </table>

@endsection
