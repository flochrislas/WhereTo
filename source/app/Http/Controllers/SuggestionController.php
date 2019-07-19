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

        $TOP_N = 35;

        if (!$this->isLunchTime()) {
            return "";
        }

        // Get restaurants
        // Limit must be big cause we dont know location, distances are important
        // Adjust max distance if too hot or too cold or raining
        // Take a random one

        $restaurantController = new RestaurantController;
        // Read DB
        $places = $restaurantController->readFromDB(null, 'AND', 'ratings', 150);

        // Use collection filter to remove places too close if its good weather, and too far if its cold hot or raining

        // Calculate distances from position, and sort them
        $places = $restaurantController->handleDistances($places, $position, 'distance');

        // take a chunk of the 35 bests
        //$topN = $places->chunk($TOP_N)->toArray()[0];
        $topN = $places->chunk($TOP_N)[0];
        //dump($topN->get(0)->currentDistance);

        // pick one randomly
        $selected = $topN->get(rand(0, $TOP_N-1));
        $url = route('restaurants.details.single', $selected->id);
        $aTagStart = '<a href="'.$url.'">';
        $aTagEnd = '</a>';

        $result = "It's about lunch time! ";
        $result .= "How about a nice";
        $result .= $selected->type . " restaurant, ";
        $result .= $aTagStart . $selected->name . $aTagEnd . "?";

        return $result;
    }

}