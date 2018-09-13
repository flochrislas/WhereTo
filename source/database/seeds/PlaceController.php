<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Tools\GeoUtils;

abstract class PlaceController extends Controller
{
    // Force Extending class to define this method
    abstract protected function getModelClass();

    /**
    * Get search results from paramters by using the cache.
    * Generate the key, read cache, if not found then read DB and fill cache
    */
    public function useCache($tags, $op, $orderBy)
    {
        // key needs to be made of all the used parameters except coords
        $cacheKey = $this->cacheKey($op, $tags, $orderBy);

        $places = Cache::rememberForever($cacheKey, function() use ($tags, $op, $orderBy) {
            return $this->mainDB($tags, $op, $orderBy);
        });

        return $places;
    }

    /**
     * Generate the key to use for puting the main query into cache
     * @return string
     */
    public function cacheKey($op, $tags, $orderBy) : string
    {
        if (empty($tags)) {
            $cacheKey = 'all';
        } else {
            $cacheKey = md5($op .','. implode(',',$tags));
        }
        // The logic below is a bit dangerous, but currently
        // only orderBy costs influence the cached results
        if ($orderBy == 'cost') {
            $cacheKey = $orderBy.$cacheKey;
        }
        return $cacheKey;
    }

    /**
     * Make the tags into an array, if it is not already the case
     * The reference is passed in paramter, so the function directly modifies the tags
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

    /**
     * Search for the list to display.
     * No cache, just the DB.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function mainDB($tags, $op = 'AND', $orderBy = 'distance')
    {
        Log::debug('Hitting the DB with mainDB with op '.$op.' orderBy '.$orderBy);
        $class = $this->getModelClass();
        $query = (new $class)->newQuery();

        $this->whereTags($query, $tags, $op);
        if ($class == 'App\Restaurant') {
          $this->orderBy($query, $orderBy);
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

    public function whereTypes(&$query, $types, $op = 'AND') : void
    {
        if (isset($types) && !empty($types)) {

            if ($op == 'AND') {
                $query->whereHas('tags', function ($query) use ($types) {
                    foreach ($types as $type) {
                      $query->where('label', '=', $type);
                    }
                });
            }

            if ($op == 'OR') {
                $query->whereHas('tags', function ($query) use ($types) {
                      $query->whereIn('label', $types);
                });
            }

            if ($op == 'ANDNOT') {
                $query->whereDoesntHave('tags', function ($query) use ($types) {
                    foreach ($types as $type) {
                      $query->where('label', '=', $type);
                    }
                });
            }

            if ($op == 'ORNOT') {
                $query->whereHas('tags', function ($query) use ($types) {
                      $query->whereNotIn('label', $types);
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
            // Log::debug('Distance found: '.$distance);
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
}
