<?php

namespace App\Services;

use App\Models\Bar;

/**
 * @extends PlaceSearch<Bar>
 *
 * The bar search: rated by the overall score, then the place, then how easy it is to mingle
 */
class BarSearch extends PlaceSearch
{
    protected function modelClass(): string
    {
        return Bar::class;
    }

    protected function ratingColumns(): array
    {
        return ['score', 'score_place', 'score_mingling'];
    }
}
