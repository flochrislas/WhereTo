<?php

namespace App\Traits;

use App\Tools\GeoUtils;

trait Location
{
    // Storing calculated distance
    public $currentDistance;

    // Required fields
    /*
    protected $fillable = [
        'lat',
        'lon'
    ];*/

    /**
     * Simply fill lat and lon fron a string with both
     */
    public function fillCoordinatesFromString(string $coordinates) : void
    {
        try {
          $coord = GeoUtils::toPositionArray($coordinates);
          $this->lat = $coord[0];
          $this->lon = $coord[1];
          $this->save();
        } catch (\ErrorException $e) {
          \Log::debug('Invalid coordinates for restaurant '.$this->id);
        }
    }

    /**
     * Try to fill lat and lon with the ones found in the Google Maps link
     * NOTE: link's coordiantes are actually for the center of the map, NOT the location we want
     */
    public function fillCoordinatesFromGoogleLink() : void
    {
        try {
          $coord = GeoUtils::google2coord($this->google_maps_link);
          $this->lat = $coord[0];
          $this->lon = $coord[1];
          $this->save();
        } catch (\ErrorException $e) {
          \Log::debug('Could not find coordinates from Google Maps link in restaurant id '.$this->id);
        }
    }
}
