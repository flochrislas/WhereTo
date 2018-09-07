<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Tools\GeoUtils;

/**
* Gather methods for entities that use tags
*
* Instantiate classes and call methods from strings: https://stackoverflow.com/questions/4578335/creating-php-class-instance-with-a-string
*/
class TaggedController extends Controller
{
    protected $class;

    public function useCache($cacheKey, $tags, $op, $orderBy)
    {
        $places = Cache::rememberForever($cacheKey, function() use ($class, $tags, $op, $orderBy) {
            return $this->mainDB($class, $tags, $op, $orderBy);
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
    public function mainDB($class, $tags, $op = 'AND', $orderBy = 'distance')
    {
        Log::debug('Hitting the DB with mainDB with op '.$op.' orderBy '.$orderBy);
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
     * Return the restaurants sorted by distance from given position
     * @return array
     */
    public function sortByDistance($restaurants, $position)
    {
        Log::debug('sortByDistance from  '.$position.' for '.count($restaurants).' restaurants' );
        // Log::debug('For restaurants '.print_r($restaurants, true));
        // Map the restaurants with their current distance
        $curCoords = GeoUtils::toPositionArray($position);
        $curLat = $curCoords[0];
        $curLon = $curCoords[1];
        $map = array();
        foreach ($restaurants as $resto) {
          if(!is_null($resto->lat)) {
            $distance = GeoUtils::distance($curLat, $curLon, $resto->lat, $resto->lon);
            // Log::debug('Distance found: '.$distance);
            $resto->currentDistance = $distance;
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
            $map[$distanceAsString] = $resto;
          }
          else {
            Log::debug('The following restaurant does NOT have coordinates: '.$resto->name);
          }
        }
        // Sort the map by key
        ksort($map);
        // Log::debug('sortByDistance ksort the map '.print_r($map, true));
        Log::debug('sortByDistance AFTER ksort the map with '.count($map).' elements');
        // Return just the list of restaurants
        return array_values($map);
    }

    /**
     * For all the given restaurants, compute the distance from the given position
     * Each result is stored in the restaurant's model
     * @return void
     */
    public function generateCurrentDistances($restaurants, $position)
    {
        Log::debug('generateCurrentDistances from  '.$position);
        // Map the restaurants with their current distance
        $curCoords = GeoUtils::toPositionArray($position);
        $curLat = $curCoords[0];
        $curLon = $curCoords[1];
        foreach ($restaurants as $resto) {
          if(!is_null($resto->lat)) {
            $distance = GeoUtils::distance($curLat, $curLon, $resto->lat, $resto->lon);
            $resto->currentDistance = $distance;
          }
        }
    }
}
