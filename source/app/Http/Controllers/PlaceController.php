<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Tools\GeoUtils;

/**
* If you have a Model class that describes a place (such as Restaurant.php or Karaoke.php),
* you might want its Controller to extend this class
*/
abstract class PlaceController extends Controller
{
    // Force Extending class to define this method
    abstract protected function getModelName();

    /** Can force position for debug purposes */
    private function positionSpoofer($position)
    {
        // Office from google maps '35.656660, 139.699691'
        // $position = '35.656660, 139.699691';

        // Detect if the user is in the area
        // If more than 5Km from nearest stationLat
        // Switches position from current to nearest station

        // Shibuya Station from google maps is '35.658789, 139.701262'
        $closestStationPosition = '35.658789, 139.701262';
        if (!isset($position) || $position == "null") {
          $position = $closestStationPosition;
          Log::debug('GPS position not set. Using Shibuya Station coordinates.');
        }
        else {
          // We check the distance from shibuya Station
          $curCoords = GeoUtils::toPositionArray($position);
          $curLat = $curCoords[0];
          $curLon = $curCoords[1];
          $stationCoords = GeoUtils::toPositionArray($closestStationPosition);
          $stationLat = $stationCoords[0];
          $stationLon = $stationCoords[1];
          $distance = GeoUtils::distance($curLat, $curLon, $stationLat, $stationLon);
          Log::debug('Distance from Shibuya Station: '.$distance);
          if ($distance > 2500) {
            $position = $closestStationPosition;
            Log::debug('Position is too far. Using Shibuya Station coordinates.');
          }
          else {
            Log::debug('GPS position set from user: '.$position);
          }
        }
        return $position;
    }

    /**
     * Returns the Model class to use to read the data
     * @return string Model's fully specified class name as a string
     */
    public function getModelClass()
    {
        Log::debug('Auto-generated ModelClass name: '.'App\\'.$this->getModelName());
        return 'App\\'.$this->getModelName();
    }

    /**
     * Returns the view's name for the place's details
     * @param bool $single default is false, true if we want SINGLE details view (that can be shared),
     * instead of the view that fits into the one page design
     * @return string Model's details view name
     */
    public function getModelDetailsView($single=false) : string
    {
        $viewName = strtolower($this->getModelName()).'s.details';
        if ($single) {
          $viewName .= '-single';
        }
        Log::debug('Auto-generated ModelDetailsView name: '.$viewName);
        return $viewName;
    }

    public function getModelResultsDataView()
    {
        Log::debug('Auto-generated ModelResultsDataView name: '.strtolower($this->getModelName()).'s.results-data');
        return strtolower($this->getModelName()).'s.results-data';
    }

    /**
     * Get search results from parameters by using the cache.
     * Generate the key, read cache, if not found then read DB and fill cache
     * @param $tags used in the search
     * @param $op used in the search
     * @param $orderBy used in the search
     * @param int $limit used in the search
     * @return mixed
     */
    public function useCache($tags, $op, $orderBy, $limit = 0)
    {
        // key needs to be made of all the used parameters except coords
        $cacheKey = $this->cacheKey($op, $tags, $orderBy, $limit);

        $places = Cache::rememberForever($cacheKey, function() use ($tags, $op, $orderBy, $limit) {
            return $this->readFromDB($tags, $op, $orderBy, $limit);
        });

        return $places;
    }

    /**
     * Generates the key to use for putting the main query into cache
     * @param $op used in the search
     * @param $tags used in the search. Can be empty for ALL, or an array of actual tags
     * @param $orderBy used in the search
     * @param int $limit used in the search
     * @return string the key
     */
    public function cacheKey($op, $tags, $orderBy, $limit = 0) : string
    {
        if (empty($tags)) {
            $cacheKey = 'All-'.$this->getModelName();
        } else if($tags == 'suggestion') {
            $cacheKey = 'Suggestion-'.$this->getModelName();
        } else {
            $cacheKey = md5($op .','. implode(',',$tags));
        }
        if ($limit > 0) {
            $cacheKey = 'l'.$limit.'-'.$cacheKey;
        }
        // The logic below is a bit dangerous, but currently
        // only orderBy costs influence the cached results
        if ($orderBy == 'cost') {
            $cacheKey = $orderBy.$cacheKey;
        }
        return $cacheKey;
    }

    /**
     * Read from the DB.
     * No cache, just the DB.
     * @param array $tags
     * @param string $op
     * @param string $orderBy
     * @param integer $limit
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function readFromDB($tags, $op = 'AND', $orderBy = 'distance', $limit = 0)
    {
        Log::debug('Hitting the DB with mainDB with op '.$op.' orderBy '.$orderBy.' limit '.$limit);
        $class = $this->getModelClass();
        $query = (new $class)->newQuery();

        $this->whereTags($query, $tags, $op);
        if ($class == 'App\Restaurant') {
          $this->orderBy($query, $orderBy);
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $places = $query->get();

        return $places;
    }

    public function orderBy(&$query, $orderBy = 'distance') : void
    {
        Log::debug('orderBy: '.$orderBy);

        if ($orderBy == 'distance' || $orderBy == 'ratings') {
            // Sorting by distance will happens outside the query
            // We still try to put best places on top (rating)
            $query->orderBy('score_lunch', 'desc')
                  ->orderBy('score_food', 'desc')
                  ->orderBy('score_place', 'desc');
        }

        if ($orderBy == 'cost') {
            // Price first, but we still try to put best places on top
            $query->orderBy('lunch_price', 'asc')
                  ->orderBy('score_lunch', 'desc')
                  ->orderBy('score_food', 'desc')
                  ->orderBy('score_place', 'desc');
        }
    }

    public function whereTags(&$query, $tags, $op = 'AND') : void
    {
        Log::debug('whereTags: '.print_r($tags, true).'('.$op.')');
        if (isset($tags) && !empty($tags)) {
            if ($op == 'AND') {
                $query->whereHas('tags', function ($query) use ($tags) {
                      $query->whereIn('label', $tags);
                }, '=', count($tags));
            }

            if ($op == 'OR') {
                $query->whereHas('tags', function ($query) use ($tags) {
                      $query->whereIn('label', $tags);
                });
            }

            if ($op == 'ANDNOT') {
                $query->whereDoesntHave('tags', function ($query) use ($tags) {
                      $query->whereIn('label', $tags);
                }, '=', count($tags));
            }

            if ($op == 'ORNOT') {
                $query->whereHas('tags', function ($query) use ($tags) {
                      $query->whereNotIn('label', $tags);
                });
            }
        }
    }

    /**
     * Return the places sorted by distance from given position
     * @return array
     */
    public function sortByDistance($places, $position)
    {
        Log::debug('sortByDistance from  '.$position.' for '.count($places).' places' );
        // Log::debug('For places '.print_r($places, true));
        // Map the places with their current distance
        $curCoords = GeoUtils::toPositionArray($position);
        $curLat = $curCoords[0];
        $curLon = $curCoords[1];
        $map = array();
        foreach ($places as $place) {
          if(!is_null($place->lat)) {
            $distance = GeoUtils::distance($curLat, $curLon, $place->lat, $place->lon);
            //Log::debug('Distance found: '.$distance);
            $place->currentDistance = $distance;
            /*
            if (array_key_exists((string)$distance, $map)) {
                Log::debug('UH OH, this distance is already there '.$distance.', byebye '.$map[$distance]);
            }
            if (isset($map[$distance]))
            {
              Log::debug('OOOOOOH, this distance is already there '.$distance.', byebye '.$map[$distance]);
            }
            */
            // We must use a string or else the number will be truncated
            // and easily collide with previous keys
            $distanceAsString = (string)$distance;
            $map[$distanceAsString] = $place;
          }
          else {
            Log::debug('The following restaurant does NOT have coordinates: '.$place->name);
          }
        }
        // Sort the map by key
        ksort($map);
        // Log::debug('sortByDistance ksort the map '.print_r($map, true));
        Log::debug('sortByDistance AFTER ksort the map with '.count($map).' elements');
        // Return just the list of places
        return array_values($map);
    }

    /**
     * For all the given places, compute the distance from the given position
     * Each result is stored in the restaurant's model
     * @return void
     */
    public function generateCurrentDistances($places, $position)
    {
        Log::debug('generateCurrentDistances from  '.$position);
        // Map the places with their current distance
        $curCoords = GeoUtils::toPositionArray($position);
        $curLat = $curCoords[0];
        $curLon = $curCoords[1];
        foreach ($places as $place) {
          if(!is_null($place->lat)) {
            $distance = GeoUtils::distance($curLat, $curLon, $place->lat, $place->lon);
            $place->currentDistance = $distance;
          }
        }
    }

    /**
    * Calculate distances from position, and sort them if required
    * @return places (filled with distances)
    */
    public function handleDistances($places, $position, $orderBy)
    {
        /** Can force position for debug purposes */
        $position = $this->positionSpoofer($position);

        if ($orderBy == 'distance')
        {
          if (!empty($position)) {
              $distanceSortedPlaces = $this->sortByDistance($places, $position);
          }
          $places = collect($distanceSortedPlaces);
        }
        else // we still need to fill the current distance data for each place we show
        {
          if (!empty($position)) {
              $this->generateCurrentDistances($places, $position);
          }
        }
        return $places;
    }

    /**
     * Make the tags into an array, if it is not already the case
     * The reference is passed in parameter, so the function directly modifies the tags
     * @return void
     */
    public function formatTags(&$tags)
    {
        if (empty($tags)) {
            $tags = null;
        } elseif (!($tags instanceof Traversable)) {
            Log::debug('tags is being transformed from: '.$tags);
            $tags = explode(',',str_replace(', ', ',', $tags));
            Log::debug('to this: '.print_r($tags, true));
        }
    }

    public function ellipsis($string, $length) {
        $ellipsis = "&hellip;";
        return mb_strimwidth($string, 0, $length, $ellipsis);
    }

}
