<?php

namespace App\Services;

use App\Models\Store;

/**
 * @extends PlaceSearch<Store>
 *
 * The store search: one score column, no price (the defaults of PlaceSearch)
 */
class StoreSearch extends PlaceSearch
{
    protected function modelClass(): string
    {
        return Store::class;
    }
}
