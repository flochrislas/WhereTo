@extends('layouts.app')

@section('title', 'Restaurant Show')

@section('content')

    <div class="row">
        <div>
            <div>
                <strong>Name:</strong>
                {{ $restaurant->name}}
            </div>
        </div>
        <div>
            <div>
                <strong>Location:</strong>
                {{ $restaurant->location}}
            </div>
        </div>
        <div>
            <div>
                <strong>Coordinates:</strong>
                {{ $restaurant->coord}}
            </div>
        </div>
        <div>
            <div>
                <strong>Type:</strong>
                {{ $restaurant->type}}
            </div>
        </div>
        <div>
            <div>
                <strong>Lunch price:</strong>
                {{ $restaurant->lunch_price}}
            </div>
        </div>
        <div>
            <div>
                <strong>Points:</strong>
                {{ $restaurant->points}}
            </div>
        </div>
        <div>
            <div>
                <strong>Personal experience:</strong>
                {{ $restaurant->experience}}
            </div>
        </div>
        <div>
            <div>
                <strong>Visited:</strong>
                {{ $restaurant->visited}}
            </div>
        </div>
        <div>
            <div>
                <strong>Visit date:</strong>
                {{ $restaurant->visit_date}}
            </div>
        </div>
        <div>
            <div>
                <strong>Google maps:</strong>
                <a target="_blank"
                  href="{{ $restaurant->google_maps_link }}">
                  map
                </a>
            </div>
        </div>
        <div>
            <div>
                <strong>Tabelog:</strong>
                <a target="_blank"
                  href="{{ $restaurant->tabelog_link }}">
                  map
                </a>
            </div>
        </div>
        <div>
            <div>
                <strong>Website:</strong>
                <a target="_blank"
                  href="{{ $restaurant->official_website }}">
                  map
                </a>
            </div>
        </div>
        <div>
            <div>
                <strong>Visit date:</strong>
                {{ $restaurant->visit_date}}
            </div>
        </div>
        <div>
            <div>
                <strong>Visit date:</strong>
                {{ $restaurant->visit_date}}
            </div>
        </div>
        <div>
            <div>
                <strong>Tags:</strong>
                @foreach ($restaurant->tagsCached() as $tag)
                  {{ $tag->label }} |
                @endforeach
            </div>
        </div>
    </div>
@endsection
