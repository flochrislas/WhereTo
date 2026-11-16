<?php

namespace App\Traits;

use App\Tools\GeoUtils;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Coordinates helpers shared by the place models (restaurants, stores, bars, karaoke).
 */
trait Location
{
    /** Distance from the visitor, in metres, computed per request (not stored). */
    public ?float $currentDistance = null;

    /**
     * Fill lat and lon from a "lat, lon" string and save.
     */
    public function fillCoordinatesFromString(string $coordinates): void
    {
        try {
            $coord = GeoUtils::toPositionArray($coordinates);
            $this->lat = $coord[0];
            $this->lon = $coord[1];
            $this->save();
        } catch (InvalidArgumentException $e) {
            Log::debug('Invalid coordinates for '.class_basename($this).' '.$this->id.': '.$e->getMessage());
        }
    }

    /**
     * Fill lat and lon from the Google Maps link and save.
     * NOTE: the link's coordinates are the centre of the map, NOT the marker.
     */
    public function fillCoordinatesFromGoogleLink(): void
    {
        try {
            $coord = GeoUtils::google2coord((string) $this->google_maps_link);
            $this->lat = $coord[0];
            $this->lon = $coord[1];
            $this->save();
        } catch (InvalidArgumentException $e) {
            Log::debug('Could not find coordinates in the Google Maps link of '.class_basename($this).' '.$this->id);
        }
    }
}
