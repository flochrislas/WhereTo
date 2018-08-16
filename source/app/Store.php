<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    // Uses a trait to get localisation related stuff
    use Traits\Location;

    // Accessible fields for mass update/create
    /* protected $fillable = [
        'name',
        'location',
        'lat',
        'lon',
        'type',
        'points',
        'experience',
        'google_maps_link',
        'official_website',
        'score'
    ]; */
}
