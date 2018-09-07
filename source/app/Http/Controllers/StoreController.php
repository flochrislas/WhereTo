<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;

class StoreController extends PlaceController
{

  public function getClass()
  {
      // $class = get_class($this);
      $class = ClassName::class;
      return $class;
  }

  public function getModelClass()
  {
      return 'App\Store';
  }

  /**
   * Public
   * Search for the list to display.
   * Cache.
   * TODO: implement "see more" button
   *
   * @return \Illuminate\Http\Response
   */
  public function results(Request $request)
  {
      // Reads request parameters
      // Operator for the Tags
      $op = request('op');
      // tags as a comma separated list
      $tags = request('tags');
      $this->formatTags($tags);
      // Current position from client's GPS
      // Office from google maps '35.656660, 139.699691'
      $position = request('position');
      $position = '35.656660, 139.699691';

      $orderBy = request('orderBy');

      // key needs to be made of all the used parameters except coords
      $cacheKey = $this->cacheKey($op, $tags, $orderBy);

      // Reads from cache or DB
      $places = $this->useCache($cacheKey, $tags, $op, $orderBy);

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
            $distanceSortedPlaces = $this->generateCurrentDistances($places, $position);
        }
      }

      // Keeps the input of the user interface
      // https://laravel.com/docs/5.5/requests#old-input
      $request->flash();

      return view('stores.results-data', compact('places'));
  }
}
