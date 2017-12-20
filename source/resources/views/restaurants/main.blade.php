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
    <br>Autocomplete tags:
    <input type="text" name="term" id="q" data-action="{{ route('tags.autocomplete') }}">
    <br><input type="text" id="qid">
    <br>
    <script type="text/javascript">
      $('#q').each(function() {
          var $this = $(this);
          var src = $this.data('action');

          $this.autocomplete({
              source: src,
              minLength: 2,
              select: function(event, ui) {
                  $this.val(ui.item.value);
                  $('#qid').val(ui.item.id);
              }
          });
      });
    </script>

<BR><BR>

    <input id="searchString" type="text" name="term"
      placeholder="Enter Search String" class="form-control" />
    <script type="text/javascript">
    $( '#searchString' ).autocomplete({
                source: '{!!URL::route('tags.autocomplete')!!}',
                minLength: 2
            } );
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
