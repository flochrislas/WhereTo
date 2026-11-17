<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Services\PlaceSearch;
use App\Services\RestaurantSearch;
use Illuminate\Contracts\View\View;

class RestaurantController extends PlaceController
{
    /** Name of the model, used to derive the model class (App\Models\Restaurant) and the view names. */
    public const MODEL_NAME = 'Restaurant';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /**
     * The search of the restaurants: its model and score columns live in App\Services\RestaurantSearch
     *
     * @return RestaurantSearch
     */
    protected function search(): PlaceSearch
    {
        return new RestaurantSearch;
    }

    /**
     * Display the specified place in the one-page view (HTML fragment loaded by AJAX).
     */
    public function details(Restaurant $place): View
    {
        // Route model binding: Laravel loaded the Restaurant whose id is in the URL ({place}), or answered 404
        return view($this->getModelDetailsView(), compact('place'));
    }

    /**
     * Display the specified place in an independent page (shareable URL).
     */
    public function detailsSingle(Restaurant $place): View
    {

        return view($this->getModelDetailsView(true), compact('place'));
    }
}
