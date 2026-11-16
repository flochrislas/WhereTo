<?php

namespace App\Tools;

use InvalidArgumentException;

/**
 * Tools for coordinates, distances and conversions.
 * Some of this could be done in PostgreSQL instead:
 * http://grokbase.com/t/postgresql/pgsql-general/1069rn3ca0/calculating-distance-between-longitude-and-latitude
 * or http://workshops.boundlessgeo.com/postgis-intro/
 */
class GeoUtils
{
    /**
     * The method used in the app to calculate a distance.
     *
     * @return float distance in metres
     */
    public static function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // The approximation returns km, we want metres
        return self::equirectangularApprox($lat1, $lon1, $lat2, $lon2) * 1000;
    }

    /**
     * Distance for display: 2 decimals with a thousands separator, or N/A.
     */
    public static function formatDistance(?float $distance): string
    {
        if (! empty($distance) && $distance > 0) {
            return number_format($distance, 2, '.', ',');
        }

        return 'N/A';
    }

    /**
     * Fast distance calculation, in km.
     * Possibly wrong (to be checked). Unused, kept for reference.
     */
    public static function flatDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // The length of a degree of longitude, in km
        $deglen = 110.25;
        $x = $lat1 - $lat2;
        $y = ($lon1 - $lon2) * cos($lat2);

        return $deglen * sqrt($x * $x + $y * $y);
    }

    /**
     * Fast distance calculation, from point 1 to point 2, in km (equirectangular approximation).
     * Input in degrees; the formula converts to radians itself.
     */
    public static function equirectangularApprox(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        // Longitude difference, scaled by the cosine of the mean latitude (meridians get closer near the poles)
        $p1 = deg2rad($lon2 - $lon1) * cos(deg2rad($lat1 + $lat2) / 2);
        // Latitude difference
        $p2 = deg2rad($lat1 - $lat2);

        // Pythagoras on the sphere's surface
        return $earthRadius * sqrt($p1 * $p1 + $p2 * $p2);
    }

    /**
     * Generate the SQL condition for finding points within a distance (km).
     * Unused. TODO: this formula doesn't seem very correct.
     */
    public static function withinDistanceSql(float $lat, float $lon, float $distance): string
    {
        return 'pow(lat-'.$lat.', 2) + pow((lon-'.$lon.')*cos(radians('.$lat.')), 2) < pow('.$distance.'/110.25, 2)';
    }

    /**
     * Convert miles to km.
     */
    public static function miles2km(float $miles): float
    {
        return $miles * 1.609344;
    }

    /**
     * Convert km to miles.
     */
    public static function km2miles(float $km): float
    {
        return $km / 1.609344;
    }

    /**
     * Estimate the walking time. Distance in km.
     * Pace is slow walking, 3 km/h by default.
     * Result in minutes, rounded up.
     */
    public static function walkingTime(float $distance, float $pace = 3): int
    {
        return (int) ceil($distance / $pace * 60);
    }

    /**
     * Convert a position string "lat, lon" to an array [lat, lon] of floats.
     * Extra parts (e.g. a zoom level pasted from a URL, "lat,lon,15") are ignored.
     *
     * @return array{0: float, 1: float}
     *
     * @throws InvalidArgumentException when the string does not hold two numbers
     */
    public static function toPositionArray(string $positionString): array
    {
        // Remove the spaces, then split on the commas
        $parts = explode(',', str_replace(' ', '', $positionString));
        if (count($parts) < 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
            throw new InvalidArgumentException('Not a "lat, lon" string: '.$positionString);
        }

        return [(float) $parts[0], (float) $parts[1]];
    }

    /**
     * Convert a Google Maps URL to coordinates.
     * Those are the coordinates of the centre of the map (the "/@lat,lon,zoomz/" part of the URL),
     * NOT the ones of the actual place/marker.
     * See: https://stackoverflow.com/questions/29335870/i-need-to-extract-lat-long-data-from-a-google-maps-url-that-uses-msa-and-msid
     *
     * @return array{0: float, 1: float}
     *
     * @throws InvalidArgumentException when the URL has no "/@lat,lon," part
     */
    public static function google2coord(string $url): array
    {
        // Regular expression: "/@" then two decimal numbers separated by a comma
        if (! preg_match('#/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?),#', $url, $m)) {
            throw new InvalidArgumentException('No coordinates in Google Maps URL: '.$url);
        }

        // $m[1] and $m[2] are the two captured numbers
        return [(float) $m[1], (float) $m[2]];
    }

    /**
     * Great-circle distance between two points, given their latitude/longitude in decimal degrees.
     * South latitudes are negative, east longitudes are positive.
     * Routine from GeoDataSource(TM), https://www.geodatasource.com (C) 2017. Unused by the app, covered by tests.
     *
     * @param  string  $unit  'M' statute miles (default), 'K' kilometres, 'N' nautical miles
     */
    public static function geodatasourceDistance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = 'M'): float
    {
        $theta = $lon1 - $lon2;
        // Spherical law of cosines
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        // Clamp to [-1, 1] before acos: rounding can push identical points slightly above 1
        $dist = rad2deg(acos(min(1.0, max(-1.0, $dist))));
        // 1 degree = 60 nautical miles = 60 * 1.1515 statute miles
        $miles = $dist * 60 * 1.1515;

        return match (strtoupper($unit)) {
            'K' => $miles * 1.609344,
            'N' => $miles * 0.8684,
            default => $miles,
        };
    }
}
