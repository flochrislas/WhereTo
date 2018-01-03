<?php

namespace App;

use Illuminate\Support\Facades\Log;

/**
* Tools for coordiantes, distances...
* Some stuff might be put in Postgres : http://grokbase.com/t/postgresql/pgsql-general/1069rn3ca0/calculating-distance-between-longitude-and-latitude
* or http://workshops.boundlessgeo.com/postgis-intro/
*/
class GeoUtils
{
    /**
    * The method used in the app for calculated the distance
    */
    public static function distance(float $lat1, float $lon1, float $lat2, float $lon2) : float
    {
        // returns meters
        return GeoUtils::equirectangularApprox($lat1, $lon1, $lat2, $lon2) * 1000;
    }

    // faster distance calculation, in Km
    public static function flatDistance(float $lat1, float $lon1, float $lat2, float $lon2) : float
    {
        // The length of a degree of longitude
        $deglen = 110.25;
        $x = $lat1 - $lat2;
        $y = ($lon1 - $lon2) * cos($lat2);
        return $deglen * sqrt($x*$x + $y*$y);
    }

    // faster distance calculation, in Km, from 1 to 2
    // lat lon must be in radians
    public static function equirectangularApprox(float $lat1, float $lon1, float $lat2, float $lon2) : float
    {
        $earthRadius = 6371; //km
        $p1 = deg2rad($lon2 - $lon1) * cos( deg2rad($lat1+$lat2) / 2 );
        $p2 = deg2rad($lat1 - $lat2);
        $distance = $earthRadius * sqrt($p1*$p1 + $p2*$p2);
        return $distance;
    }

    /**
    * Generate SQL string for finding points within distance
    * TODO: make it faster by having a table with the results for the cosinus already
    * or check http://grokbase.com/t/postgresql/pgsql-general/1069rn3ca0/calculating-distance-between-longitude-and-latitude
    */
    public static function withinDistanceSql(float $lat, float $lon, float $distance) : string
    {
        // select lat, lon from points where
        // TODO: this formula doesnt seem very correct
        return 'pow(lat-'.$lat.', 2) + pow((lon-'.$lon.')*cos(radians('.$lat.')), 2) < pow('.$distance.'/110.25, 2)';
    }

    // convert miles to km
    public static function miles2km(float $miles) : float
    {
        return $miles * 1.609344;
    }

    // convert km to miles
    public static function km2miles(float $km) : float
    {
        return $km / 1.609344;
    }

    /**
    * Estimate walking time. Distance in Km.
    * Pace is slow walking 3Km/h by default.
    * Result in minutes is rounded up.
    */
    public static function walkingTime(float $distance, float $pace = 3) : int
    {
        return ceil($distance / $pace * 60);
    }

    // convert google URL to coordinate
    public static function google2coord(string $url) : array
    {
        Log::debug($url);
        $split1 = explode('/@', $url);
        Log::debug($split1);
        $split2 = explode('z/data', $split1[1]);
        Log::debug($split2);
        $coord = explode(',', $split2[0]);
        Log::debug($coord);
        return $coord;
    }


    // Get real distance and time by foot (maybe using google api)

    // Get real distance and time by transport (maybe using google api)


    // Curisous functions

// https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key=YOUR_API_KEY
// https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=-33.8670,151.1957&radius=500&types=food&name=cruise&key=
    function getCoordinates($address)
    {
        $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
        $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
        $response = file_get_contents($url);
        $json = json_decode($response,TRUE); //generate array object from the response from the web
        return ($json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng']);
    }

    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    /*::                                                                         :*/
    /*::  This routine calculates the distance between two points (given the     :*/
    /*::  latitude/longitude of those points). It is being used to calculate     :*/
    /*::  the distance between two locations using GeoDataSource(TM) Products    :*/
    /*::                                                                         :*/
    /*::  Definitions:                                                           :*/
    /*::    South latitudes are negative, east longitudes are positive           :*/
    /*::                                                                         :*/
    /*::  Passed to function:                                                    :*/
    /*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
    /*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
    /*::    unit = the unit you desire for results                               :*/
    /*::           where: 'M' is statute miles (default)                         :*/
    /*::                  'K' is kilometers                                      :*/
    /*::                  'N' is nautical miles                                  :*/
    /*::  Worldwide cities and other features databases with latitude longitude  :*/
    /*::  are available at https://www.geodatasource.com                         :*/
    /*::                                                                         :*/
    /*::  For enquiries, please contact sales@geodatasource.com                  :*/
    /*::                                                                         :*/
    /*::  Official Web site: https://www.geodatasource.com                       :*/
    /*::                                                                         :*/
    /*::         GeoDataSource.com (C) All Rights Reserved 2017		   		         :*/
    /*::                                                                         :*/
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    public static function geodatasourceDistance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit) : float
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
          return ($miles * 1.609344);
        } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
          return $miles;
        }
    }

}
