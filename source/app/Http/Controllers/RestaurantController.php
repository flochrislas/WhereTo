<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RestaurantController extends PlaceController
{
    /** Name of the model used for DB and views */
    const MODEL_NAME = 'Restaurant';

    public function getModelName()
    {
        return self::MODEL_NAME;
    }

    /**
     * Get the list to display.
     * TODO: implement "see more" button
     * @param Request $request
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
        // makes tag into a traversable array
        $this->formatTags($tags);

        // Current position from client's GPS
        $position = request('position');

        // the sorting order for the result list
        $orderBy = request('orderBy');

        // -----------------------------------
        // Reads from cache or DB
        $places = $this->useCache($tags, $op, $orderBy);

        // -----------------------------------
        // Calculate distances from position, and sort them if required
        $places = $this->handleDistances($places, $position, $orderBy);

        // -----------------------------------
        // Shorten the points to display in list
        foreach ($places as $place) {
          $place->points = $this->ellipsis($place->points, 50);
        }

        // Keeps the input of the user interface
        // https://laravel.com/docs/5.5/requests#old-input
        // TODO: check if working or if useful?
        $request->flash();

        // Return the view with the resulted places
        return view($this->getModelResultsDataView(), compact('places'));
    }

    /**
     * Display the specified resource in one-apge view
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function details($id)
    {
        $class = $this->getModelClass();
        $place = $class::find($id);
        return view($this->getModelDetailsView(), compact('place'));
    }

    /**
     * Display the specified resource in independent page
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detailsSingle($id)
    {
        $class = $this->getModelClass();
        $place = $class::find($id);
        return view($this->getModelDetailsView(true), compact('place'));
    }
}
