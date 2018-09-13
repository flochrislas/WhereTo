<?php

namespace App\Http\Controllers;

use App\Bar;
use Illuminate\Http\Request;

class BarController extends PlaceController
{
    const RESULTS_DATA_VIEW = 'bars.results-data';

    const DETAILS_VIEW = 'bars.details';

    const MODEL_CLASS = 'App\Bar';

    /**
    * Returns the Model class to use to read the data
    * @return Model's fully specified class name as a string
    */
    public function getModelClass()
    {
        return self::MODEL_CLASS;
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
        // makes tag into a traversable array
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
        return view(self::RESULTS_DATA_VIEW, compact('places'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function details($id)
    {
        $place = (self::MODEL_CLASS)::find($id);
        return view(self::DETAILS_VIEW, compact('place'));
    }
}
