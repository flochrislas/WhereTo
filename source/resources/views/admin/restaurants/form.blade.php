{{-- Fields of the restaurant form, shared by create and edit. $restaurant is null when creating. --}}
@php($r = $restaurant)
<div class="row">
    {{-- One input per field: name => [input type, label]. The loop below builds them all. --}}
    @foreach ([
        'name' => ['text', 'Name'],
        'location' => ['text', 'Location'],
        'type' => ['text', 'Type'],
        'lunch_price' => ['number', 'Typical lunch price'],
        'points' => ['textarea', 'Points'],
        'experience' => ['textarea', 'Personal experience'],
        'visit_date' => ['date', 'Visit date'],
        'google_maps_link' => ['url', 'Google Maps Link'],
        'tabelog_link' => ['url', 'Tabelog Link'],
        'official_website' => ['url', 'Official Website'],
        'score_lunch' => ['number', 'Score Lunch'],
        'score_place' => ['number', 'Score Place'],
        'score_food' => ['number', 'Score Food'],
        'score_price' => ['number', 'Score Price'],
        'score_date' => ['number', 'Score Date'],
    ] as $field => [$kind, $label])
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{ $label }}:</strong>
            {{-- old($field, ...) shows what the user typed if validation failed, else the current value ($r?->$field is null when creating) --}}
            @if ($kind === 'textarea')
                <textarea name="{{ $field }}" placeholder="{{ $label }}" class="form-control" style="height:{{ $field === 'experience' ? 150 : 100 }}px">{{ old($field, $r?->$field) }}</textarea>
            @elseif ($kind === 'date')
                <input type="date" name="{{ $field }}" class="form-control" value="{{ old($field, $r?->visit_date ? substr($r->visit_date, 0, 10) : '') }}">
            @else
                <input type="{{ $kind }}" name="{{ $field }}" placeholder="{{ $label }}" class="form-control" value="{{ old($field, $r?->$field) }}">
            @endif
        </div>
    </div>
    @endforeach
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            {{-- Not a column: parsed by RestaurantAdminController::save() into lat and lon --}}
            <strong>Coordinates (lat, lon):</strong>
            <input type="text" name="coord" placeholder="35.658789, 139.701262" class="form-control" value="{{ old('coord', $r?->lat !== null ? $r->lat.', '.$r->lon : '') }}">
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Visited:</strong>
            <label><input type="radio" name="visited" value="1" @checked(old('visited', $r?->visited ?? true))> yes</label>
            <label><input type="radio" name="visited" value="0" @checked(! old('visited', $r?->visited ?? true))> no</label>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
