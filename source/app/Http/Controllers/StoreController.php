<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreController extends PlaceController
{
    /** Name of the model, used to derive the model class (App\Models\Store) and the view names. */
    public const MODEL_NAME = 'Store';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /**
     * Get the list to display (HTML fragment loaded by AJAX).
     * Parameters: op (AND/OR/ANDNOT/ORNOT), tags (comma separated), position (lat,lon), orderBy (distance/ratings/cost).
     */
    public function results(Request $request)
    {
        // -----------------------------------
        // Reads the request parameters (from the query string)

        // Operator for the tags
        $op = $request->query('op');

        // Tags as a comma separated list, made into an array
        $tags = $this->formatTags($request->query('tags'));

        // Current position from the client's GPS
        $position = $this->positionParam($request);

        // The sorting order for the results list
        $orderBy = $request->query('orderBy');

        // -----------------------------------
        // Reads from the cache or the DB
        $places = $this->useCache($tags, $op, $orderBy);

        // -----------------------------------
        // Calculates the distances from the position, and sorts by them if required
        $places = $this->handleDistances($places, $position, $orderBy);

        // -----------------------------------
        // Shortens the points to display in the list
        foreach ($places as $place) {
            $place->setAttribute('points', $this->ellipsis($place->getAttribute('points'), 50));
        }

        // Returns the view with the resulting places
        return view($this->getModelResultsDataView(), compact('places'));
    }

    /**
     * Display the specified place in the one-page view (HTML fragment loaded by AJAX).
     */
    public function details(int $id)
    {
        // findOrFail returns a 404 page when the id does not exist
        $class = $this->getModelClass();
        $place = $class::findOrFail($id);

        return view($this->getModelDetailsView(), compact('place'));
    }

    /**
     * Display the specified place in an independent page (shareable URL).
     */
    public function detailsSingle(int $id)
    {
        $class = $this->getModelClass();
        $place = $class::findOrFail($id);

        return view($this->getModelDetailsView(true), compact('place'));
    }
}
