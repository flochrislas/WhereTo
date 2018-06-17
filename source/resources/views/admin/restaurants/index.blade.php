@extends('layouts.app')

@section('title', 'Restaurant Index')

@section('content')
<div id="content">

    <h2>Restaurants CRUDIX [ currently displaying {{ $restaurants->count() }} ]</h2>

    <div>
        <a href="{{ route('restaurants.create') }}"> Create New restaurant</a>
        <a href="{{ route('restaurants.imex') }}"> ImEx restaurant</a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table>
      <tr>
          <th>No</th>
          <th>Name</th>
          <th>Type</th>
          <th>Tags</th>
          <th>lunch</th>
          <th>food</th>
          <th>place</th>
          <th>price</th>
          <th>date</th>
          <th>lat</th>
          <th>lon</th>
          <th>map</th>
          <th>tabelog</th>
          <th>official</th>
          <th>Action</th>
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
          <td>{{ $restaurant->score_lunch }}</td>
          <td>{{ $restaurant->score_food }}</td>
          <td>{{ $restaurant->score_place }}</td>
          <td>{{ $restaurant->score_price }}</td>
          <td>{{ $restaurant->score_date }}</td>
          <td>{{ number_format($restaurant->lat, 4, '.', ',') }}</td>
          <td>{{ number_format($restaurant->lon, 4, '.', ',') }}</td>
          <td class="center">
            <a target="_blank"
              href="{{ $restaurant->google_maps_link }}">
              map</a>
          </td>
          <td class="center">
            <a target="_blank"
              href="{{ $restaurant->tabelog_link }}">
              link</a>
          </td>
          <td class="center">
            <a target="_blank"
              href="{{ $restaurant->official_website }}">
              site</a>
          </td>
          <td>
              <a href="{{ route('restaurants.show', $restaurant->id) }}">Show</a>
              <a href="{{ route('restaurants.edit', $restaurant->id) }}">Edit</a>
              {!! Form::open(['method' => 'DELETE','route' => ['restaurants.destroy', $restaurant->id],'style'=>'display:inline']) !!}
              {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
              {!! Form::close() !!}
          </td>
      </tr>
    @endforeach
    </table>

</div>
@endsection
