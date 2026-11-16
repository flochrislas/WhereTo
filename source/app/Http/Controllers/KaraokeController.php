<?php

namespace App\Http\Controllers;

use App\Enums\OrderBy;
use App\Enums\TagOperator;
use App\Models\Karaoke;
use Illuminate\Http\Request;

class KaraokeController extends PlaceController
{
    /** Name of the model, used to derive the model class (App\Models\Karaoke) and the view names. */
    public const MODEL_NAME = 'Karaoke';

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

        // Operator for the tags (AND unless the URL says OR, ANDNOT or ORNOT)
        $op = TagOperator::fromRequest($request->query('op'));

        // Tags as a comma separated list, made into an array
        $tags = $this->formatTags($request->query('tags'));

        // Current position from the client's GPS
        $position = $this->positionParam($request);

        // The sorting order for the results list (distance unless the URL says ratings or cost)
        $orderBy = OrderBy::fromRequest($request->query('orderBy'));

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
    public function details(Karaoke $place)
    {
        // Route model binding: Laravel loaded the Karaoke whose id is in the URL ({place}), or answered 404
        return view($this->getModelDetailsView(), compact('place'));
    }

    /**
     * Display the specified place in an independent page (shareable URL).
     */
    public function detailsSingle(Karaoke $place)
    {

        return view($this->getModelDetailsView(true), compact('place'));
    }
}
