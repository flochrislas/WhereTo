<?php

namespace App\Http\Controllers;

use App\Models\Karaoke;
use App\Services\KaraokeSearch;
use App\Services\PlaceSearch;
use Illuminate\Contracts\View\View;

class KaraokeController extends PlaceController
{
    /** Name of the model, used to derive the model class (App\Models\Karaoke) and the view names. */
    public const MODEL_NAME = 'Karaoke';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /**
     * The search of the karaokes: its model and score columns live in App\Services\KaraokeSearch
     *
     * @return KaraokeSearch
     */
    protected function search(): PlaceSearch
    {
        return new KaraokeSearch;
    }

    /**
     * Display the specified place in the one-page view (HTML fragment loaded by AJAX).
     */
    public function details(Karaoke $place): View
    {
        // Route model binding: Laravel loaded the Karaoke whose id is in the URL ({place}), or answered 404
        return view($this->getModelDetailsView(), compact('place'));
    }

    /**
     * Display the specified place in an independent page (shareable URL).
     */
    public function detailsSingle(Karaoke $place): View
    {

        return view($this->getModelDetailsView(true), compact('place'));
    }
}
