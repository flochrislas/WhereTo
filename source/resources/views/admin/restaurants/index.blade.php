@extends('layouts.html')

@section('title', 'Restaurant Index')

@section('content')
<div id="content">

    <h2>Restaurants [ currently displaying {{ $restaurants->count() }} ]</h2>

    <div>
        <a href="{{ route('restaurants.create') }}"> Create New restaurant</a>
        <a href="{{ route('admin.dashboard') }}"> Dashboard</a>
    </div>

    @if ($message = session('success'))
        <div class="alert alert-success"><p>{{ $message }}</p></div>
    @endif

    <table class="table table-condensed">
      <tr>
          <th>No</th><th>Name</th><th>Type</th><th>Tags</th>
          <th>lunch</th><th>food</th><th>place</th><th>price</th><th>date</th>
          <th>lat</th><th>lon</th><th>map</th><th>tabelog</th><th>official</th><th>Action</th>
      </tr>
    @foreach ($restaurants as $restaurant)
      <tr>
          <td>{{ $restaurant->id }}</td>
          <td>{{ $restaurant->name }}</td>
          <td>{{ $restaurant->type }}</td>
          <td>@foreach ($restaurant->tags as $tag){{ $tag->label }} | @endforeach</td>
          <td>{{ $restaurant->score_lunch }}</td>
          <td>{{ $restaurant->score_food }}</td>
          <td>{{ $restaurant->score_place }}</td>
          <td>{{ $restaurant->score_price }}</td>
          <td>{{ $restaurant->score_date }}</td>
          <td>{{ $restaurant->lat !== null ? number_format((float) $restaurant->lat, 4, '.', ',') : '' }}</td>
          <td>{{ $restaurant->lon !== null ? number_format((float) $restaurant->lon, 4, '.', ',') : '' }}</td>
          <td class="center"><a target="_blank" href="{{ $restaurant->google_maps_link }}">map</a></td>
          <td class="center"><a target="_blank" href="{{ $restaurant->tabelog_link }}">link</a></td>
          <td class="center"><a target="_blank" href="{{ $restaurant->official_website }}">site</a></td>
          <td>
              <a href="{{ route('restaurants.show', $restaurant->id) }}">Show</a>
              <a href="{{ route('restaurants.edit', $restaurant->id) }}">Edit</a>
              <form method="POST" action="{{ route('restaurants.destroy', $restaurant->id) }}" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
              </form>
          </td>
      </tr>
    @endforeach
    </table>

</div>
@endsection
