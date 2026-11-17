<?php

namespace App\Services;

use App\Models\Restaurant;

/**
 * @extends PlaceSearch<Restaurant>
 *
 * The restaurant search: rated by lunch, food and place, priced by the lunch
 */
class RestaurantSearch extends PlaceSearch
{
    protected function modelClass(): string
    {
        return Restaurant::class;
    }

    /** The rating of a restaurant: lunch first, then food, then the place */
    protected function ratingColumns(): array
    {
        return ['score_lunch', 'score_food', 'score_place'];
    }

    /** The "cost" order uses the lunch price */
    protected function costColumn(): ?string
    {
        return 'lunch_price';
    }
}
