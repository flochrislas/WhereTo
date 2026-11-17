<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\PlaceSearch;
use App\Services\StoreSearch;
use Illuminate\Contracts\View\View;

class StoreController extends PlaceController
{
    /** Name of the model, used to derive the model class (App\Models\Store) and the view names. */
    public const MODEL_NAME = 'Store';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /**
     * The search of the stores: its model and score columns live in App\Services\StoreSearch
     *
     * @return StoreSearch
     */
    protected function search(): PlaceSearch
    {
        return new StoreSearch;
    }

    /**
     * Display the specified place in the one-page view (HTML fragment loaded by AJAX).
     */
    public function details(Store $place): View
    {
        // Route model binding: Laravel loaded the Store whose id is in the URL ({place}), or answered 404
        return view($this->getModelDetailsView(), compact('place'));
    }

    /**
     * Display the specified place in an independent page (shareable URL).
     */
    public function detailsSingle(Store $place): View
    {

        return view($this->getModelDetailsView(true), compact('place'));
    }
}
