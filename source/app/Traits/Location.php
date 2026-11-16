<?php

namespace App\Traits;

use App\Tools\GeoUtils;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Localisation related stuff shared by the place models (restaurants, stores, bars, karaoke).
 * A trait is a set of methods and properties copied into every class that "use"s it.
 */
trait Location
{
    /** Storing the calculated distance from the visitor, in metres. Computed per request, not a DB column. */
    public ?float $currentDistance = null;

    /**
     * Simply fill lat and lon from a string holding both ("lat, lon"), then save.
     */
    public function fillCoordinatesFromString(string $coordinates): void
    {
        try {
            // Parse the string into [lat, lon]
            $coord = GeoUtils::toPositionArray($coordinates);
            $this->lat = $coord[0];
            $this->lon = $coord[1];
            // Write the model to the DB
            $this->save();
        } catch (InvalidArgumentException $e) {
            // Not a "lat, lon" string: keep the current coordinates
            Log::debug('Invalid coordinates for '.class_basename($this).' '.$this->id.': '.$e->getMessage());
        }
    }

    /**
     * Try to fill lat and lon with the ones found in the Google Maps link, then save.
     * NOTE: the link's coordinates are actually the centre of the map, NOT the location we want.
     */
    public function fillCoordinatesFromGoogleLink(): void
    {
        try {
            // (string) turns a null link into '' instead of a type error
            $coord = GeoUtils::google2coord((string) $this->google_maps_link);
            $this->lat = $coord[0];
            $this->lon = $coord[1];
            $this->save();
        } catch (InvalidArgumentException $e) {
            Log::debug('Could not find coordinates in the Google Maps link of '.class_basename($this).' '.$this->id);
        }
    }
}
