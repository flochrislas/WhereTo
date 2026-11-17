<?php

namespace App\Models;

use App\Support\Position;

/**
 * What every kind of place (Restaurant, Store, Bar, Karaoke) offers to the code that handles them all
 * (App\Services\PlaceSearch). The methods come from the Location trait; getAttribute() from the Model.
 */
interface Place
{
    /** The coordinates as a Position, or null when the place has none */
    public function position(): ?Position;

    /** The distance from the visitor computed for this request (metres), or null when not computed */
    public function currentDistance(): ?float;

    public function setCurrentDistance(?float $distance): void;

    /**
     * Any attribute of the model, e.g. getAttribute('name') (Illuminate\Database\Eloquent\Model)
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key);
}
