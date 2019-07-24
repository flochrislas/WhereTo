<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class SuggestionController
{
    private const TIMEZONE = 'Asia/Tokyo';
    private const LUNCH_START = 'today 11am';
    private const LUNCH_END = 'today 2pm';

    /**
     * Makes a suggestions, given in a string
     * Will read position from the request
     * @return string
     */
    public function suggest() : string {

        // Current position from client's GPS
        $position = request('position');

        if (!$this->isLunchTime()) {
            return $this->suggestLunch($position);
        }

        return "";
    }

    /**
     * Check time in Tokyo Shibuya and if it is lunch time
     * @return bool true if it is time to have lunch in Shibuya
     */
    public static function isLunchTime() : bool {

        $lunchStart = Carbon::parse(self::LUNCH_START, self::TIMEZONE);
        $lunchEnd = Carbon::parse(self::LUNCH_END, self::TIMEZONE);
        $now = Carbon::now(self::TIMEZONE);

        if ($now->isBetween($lunchStart, $lunchEnd)) {
            return true;
        }
        return false;
    }

    /**
     * Makes a suggestions about lunch
     * @param string $position
     * @return string
     */
    public function suggestLunch($position) : string {

        $TOP_N = 35;

        // Get best restaurants
        // Limit must be big cause we dont know location, distances are important
        // Adjust max distance if too hot or too cold or raining
        // Weather : https://openweathermap.org/price direct api impl, or use some laravel composer package

        $restaurantController = new RestaurantController;
        // Read Cache or DB
        $places = $restaurantController->useCache(null, 'AND', 'ratings', 100);

        // Use collection filter to remove places too close if its good weather, and too far if its cold hot or raining

        // Calculate distances from position, and sort them
        $places = $restaurantController->handleDistances($places, $position, 'distance');

        // take a chunk of the 35 bests
        $topN = $places->chunk($TOP_N)[0];

//---------------- SHOULD CACHE THE ABOVE topN? no because depends on distance? maybe store in user session? or in browser store?

        // pick one randomly
        $selected = $topN->get(rand(0, $TOP_N-1));
        $url = route('restaurants.details.single', $selected->id);
        $aTagStart = '<a href="'.$url.'">';
        $aTagEnd = '</a>';

        $result = "How about a nice ";
        $result .= strtolower($selected->type) . " restaurant, ";
        $result .= $aTagStart . $selected->name . $aTagEnd . "?";

        return $result;
    }

}