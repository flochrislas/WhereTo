<?php

namespace App\Tools;

use InvalidArgumentException;

/**
 * Coordinates, distances and conversions.
 */
class GeoUtils
{
    /**
     * The distance used throughout the app.
     *
     * @return float metres
     */
    public static function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        return self::equirectangularApprox($lat1, $lon1, $lat2, $lon2) * 1000;
    }

    public static function formatDistance(?float $distance): string
    {
        if (! empty($distance) && $distance > 0) {
            return number_format($distance, 2, '.', ',');
        }

        return 'N/A';
    }

    /**
     * Fast flat-earth distance in km. Possibly wrong (unused, kept for reference).
     */
    public static function flatDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $deglen = 110.25; // length of a degree, km
        $x = $lat1 - $lat2;
        $y = ($lon1 - $lon2) * cos($lat2);

        return $deglen * sqrt($x * $x + $y * $y);
    }

    /**
     * Equirectangular approximation, in km. Input in degrees.
     */
    public static function equirectangularApprox(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $p1 = deg2rad($lon2 - $lon1) * cos(deg2rad($lat1 + $lat2) / 2);
        $p2 = deg2rad($lat1 - $lat2);

        return $earthRadius * sqrt($p1 * $p1 + $p2 * $p2);
    }

    /**
     * SQL condition for points within a distance (km). Unused, formula unverified.
     */
    public static function withinDistanceSql(float $lat, float $lon, float $distance): string
    {
        return 'pow(lat-'.$lat.', 2) + pow((lon-'.$lon.')*cos(radians('.$lat.')), 2) < pow('.$distance.'/110.25, 2)';
    }

    public static function miles2km(float $miles): float
    {
        return $miles * 1.609344;
    }

    public static function km2miles(float $km): float
    {
        return $km / 1.609344;
    }

    /**
     * Walking time in minutes (rounded up) for a distance in km, at a slow 3 km/h by default.
     */
    public static function walkingTime(float $distance, float $pace = 3): int
    {
        return (int) ceil($distance / $pace * 60);
    }

    /**
     * "lat, lon" string to [lat, lon] floats. Extra parts (e.g. a zoom level pasted from a URL) are ignored.
     *
     * @return array{0: float, 1: float}
     */
    public static function toPositionArray(string $positionString): array
    {
        $parts = explode(',', str_replace(' ', '', $positionString));
        if (count($parts) < 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
            throw new InvalidArgumentException('Not a "lat, lon" string: '.$positionString);
        }

        return [(float) $parts[0], (float) $parts[1]];
    }

    /**
     * Extract the map-centre coordinates from a Google Maps URL (.../@lat,lon,zoomz/data=...).
     *
     * @return array{0: float, 1: float}
     */
    public static function google2coord(string $url): array
    {
        if (! preg_match('#/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?),#', $url, $m)) {
            throw new InvalidArgumentException('No coordinates in Google Maps URL: '.$url);
        }

        return [(float) $m[1], (float) $m[2]];
    }

    /**
     * Great-circle distance between two points (GeoDataSource formula).
     *
     * @param  string  $unit  'M' statute miles (default), 'K' kilometres, 'N' nautical miles
     */
    public static function geodatasourceDistance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = 'M'): float
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = rad2deg(acos(min(1.0, max(-1.0, $dist))));
        $miles = $dist * 60 * 1.1515;

        return match (strtoupper($unit)) {
            'K' => $miles * 1.609344,
            'N' => $miles * 0.8684,
            default => $miles,
        };
    }
}
