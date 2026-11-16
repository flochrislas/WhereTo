<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaraokeController extends PlaceController
{
    /** Name of the model, used to derive the model class and the view names. */
    public const MODEL_NAME = 'Karaoke';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /**
     * Results list (HTML fragment loaded by AJAX).
     * Parameters: op (AND/OR/ANDNOT/ORNOT), tags (comma separated), position (lat,lon), orderBy (distance/ratings/cost).
     */
    public function results(Request $request)
    {
        $op = $request->query('op');
        $tags = $this->formatTags($request->query('tags'));
        $position = $this->positionParam($request);
        $orderBy = $request->query('orderBy');

        $places = $this->useCache($tags, $op, $orderBy);
        $places = $this->handleDistances($places, $position, $orderBy);

        foreach ($places as $place) {
            $place->setAttribute('points', $this->ellipsis($place->getAttribute('points'), 50));
        }

        return view($this->getModelResultsDataView(), compact('places'));
    }

    /**
     * Details fragment for the one-page view.
     */
    public function details(int $id)
    {
        $class = $this->getModelClass();
        $place = $class::findOrFail($id);

        return view($this->getModelDetailsView(), compact('place'));
    }

    /**
     * Details as a standalone, shareable page.
     */
    public function detailsSingle(int $id)
    {
        $class = $this->getModelClass();
        $place = $class::findOrFail($id);

        return view($this->getModelDetailsView(true), compact('place'));
    }
}
