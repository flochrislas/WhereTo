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
   * Get the list to display.
   * TODO: implement "see more" button
   *
   * @return \Illuminate\Http\Response
   */
  public function results(Request $request)
  {
      // -----------------------------------
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

      // the sorting order for the result list
      $orderBy = request('orderBy');

      // -----------------------------------
      // Reads from cache or DB
      $places = $this->useCache($tags, $op, $orderBy);

      // -----------------------------------
      // Calculate distances from position, and sort them if required
      $places = $this->handleDistances($places, $position, $orderBy);

      // Keeps the input of the user interface
      // https://laravel.com/docs/5.5/requests#old-input
      $request->flash();

      // Return the view with the resulted places
      return view('stores.results-data', compact('places'));
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function details($id)
  {
      $class = $this->getModelClass();
      $place = $class::find($id);
      return view('stores.details', compact('place'));
  }

}
