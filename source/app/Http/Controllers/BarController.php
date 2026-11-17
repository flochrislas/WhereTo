<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Services\BarSearch;
use App\Services\PlaceSearch;
use Illuminate\Contracts\View\View;

class BarController extends PlaceController
{
    /** Name of the model, used to derive the model class (App\Models\Bar) and the view names. */
    public const MODEL_NAME = 'Bar';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /**
     * The search of the bars: its model and score columns live in App\Services\BarSearch
     *
     * @return BarSearch
     */
    protected function search(): PlaceSearch
    {
        return new BarSearch;
    }

    /**
     * Display the specified place in the one-page view (HTML fragment loaded by AJAX).
     */
    public function details(Bar $place): View
    {
        // Route model binding: Laravel loaded the Bar whose id is in the URL ({place}), or answered 404
        return view($this->getModelDetailsView(), compact('place'));
    }

    /**
     * Display the specified place in an independent page (shareable URL).
     */
    public function detailsSingle(Bar $place): View
    {

        return view($this->getModelDetailsView(true), compact('place'));
    }
}
