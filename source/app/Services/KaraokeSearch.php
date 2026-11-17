<?php

namespace App\Services;

use App\Models\Karaoke;

/** The karaoke search: one score column, no price (the defaults of PlaceSearch) */
class KaraokeSearch extends PlaceSearch
{
    protected function modelClass(): string
    {
        return Karaoke::class;
    }
}
