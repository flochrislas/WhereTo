<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class SuggestionController
{
    private const TIMEZONE = 'Asia/Tokyo';
    private const LUNCH_START = 'today 11am';
    private const LUNCH_END = 'today 2pm';

    private function isLunchTime() : bool {

        $lunchStart = Carbon::parse(self::LUNCH_START, self::TIMEZONE);
        $lunchEnd = Carbon::parse(self::LUNCH_END, self::TIMEZONE);
        $now = Carbon::now(self::TIMEZONE);

        if ($now->isBetween($lunchStart, $lunchEnd)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $position
     * @return string
     */
    public function suggestLunch($position) : string {

        if ($this->isLunchTime()) {
            return "It's about lunch time!";
        }


        // Get restaurants
        // Limit must be big cause we dont know location, distances are important
        // Adjust max distance if too hot or too cold or raining
        // Take a random one

        $restaurantController = new RestaurantController;
        // Read DB
        $places = $restaurantController->readFromDB(null, 'AND', 'ratings', 150);
        // Calculate distances from position, and sort them
        $places = $restaurantController->handleDistances($places, $position, 'distance');

        // take a restricted 35 bests

        // pick one randomly

        return '';
    }

}