@extends('layouts.app')

@section('title', 'Restaurant Index')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $restaurants->count() }} Restaurants</h2>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <!-- Auto complete Experimental -->
    <br>Autocomplete tags (script1)<br>
    <input type="search" name="term" id="q" data-action="{{ route('tags.autocomplete') }}">
    <br><input type="text" id="qid"><br>
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

    <BR>Autocomplete tags (script2)<BR>
    <input id="searchString" type="search" name="term"
      placeholder="Enter Search String" class="form-control" />
    <script type="text/javascript">
    $('#searchString').autocomplete({
                source: '{!!URL::route('tags.autocomplete')!!}',
                minLength: 2
            } );
    </script>

    <br>
    <!-- SEARCH http://justlaravel.com/search-functionality-laravel/ -->
    <form action="" method="GET" role="search">
        {{ csrf_field() }}
        <div class="input-group">
            <input type="search" class="form-control"
                    name="tags" value="{!! old('tags') !!}"
                    placeholder="Search tags">
            <input type="hidden" name="op" value="AND">
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

            <th>Tags</th>

            <th>distance (m)</th>
            <th>lunch</th>
            <th>food</th>
            <th>place</th>
            <th>price</th>
            <th>date</th>

            <th>Details</th>
        </tr>
    @foreach ($restaurants as $restaurant)
    <tr>
        <td>{{ $restaurant->id }}</td>
        <td>{{ $restaurant->name }}</td>
        <td>{{ $restaurant->type }}</td>

        <td>
          @foreach ($restaurant->tags as $tag)
            {{ $tag->label }} |
          @endforeach
        </td>

        <td>{{ number_format($restaurant->currentDistance, 2, '.', ',') }}</td>
        <td>{{ $restaurant->score_lunch }}</td>
        <td>{{ $restaurant->score_food }}</td>
        <td>{{ $restaurant->score_place }}</td>
        <td>{{ $restaurant->score_price }}</td>
        <td>{{ $restaurant->score_date }}</td>

        <td>
            <a class="btn btn-info" href="{{ route('restaurants.details',$restaurant->id) }}">Show</a>
        </td>
    </tr>
    @endforeach
    </table>

@endsection
