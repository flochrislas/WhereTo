<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Lunch suggestion: a random pick among the best restaurants close to the visitor.
 */
class SuggestionController
{
    private const TIMEZONE = 'Asia/Tokyo';

    private const LUNCH_START = 'today 11:30am';

    private const LUNCH_END = 'today 2pm';

    /** How many of the nearest best restaurants to pick from. */
    private const TOP_N = 35;

    /**
     * Makes a suggestion, given as an HTML fragment.
     * Reads the position from the request. Returns an empty string outside lunch time.
     */
    public function suggest(Request $request): string
    {
        if (! self::isLunchTime()) {
            return '';
        }

        // Current position from the client's GPS (null when missing or not a plain value)
        $position = $request->query('position');

        return $this->suggestLunch(is_scalar($position) ? (string) $position : null);
    }

    /**
     * Check the time in Tokyo (Shibuya) and if it is lunch time.
     *
     * @return bool true if it is time to have lunch in Shibuya
     */
    public static function isLunchTime(): bool
    {
        // Carbon::parse understands "today 11:30am" and builds the date in the given timezone
        $lunchStart = Carbon::parse(self::LUNCH_START, self::TIMEZONE);
        $lunchEnd = Carbon::parse(self::LUNCH_END, self::TIMEZONE);

        return Carbon::now(self::TIMEZONE)->isBetween($lunchStart, $lunchEnd);
    }

    /**
     * Makes a suggestion about lunch.
     *
     * @param  string|null  $position  "lat, lon" of the visitor
     */
    public function suggestLunch(?string $position): string
    {
        // Get the best restaurants.
        // The limit must be big because we don't know the location yet, and distances are important.
        // Idea: adjust the max distance if too hot, too cold or raining (weather: https://openweathermap.org/price)
        $restaurantController = new RestaurantController;

        // Read the cache or the DB: the 100 best restaurants by rating
        $places = $restaurantController->useCache(null, 'AND', 'ratings', 100);

        // Calculate the distances from the position, and sort by them (nearest first)
        $places = $restaurantController->handleDistances($places, $position, 'distance');
        if ($places->isEmpty()) {
            return '';
        }

        // Take the 35 nearest, and pick one randomly
        $selected = $places->take(self::TOP_N)->random();

        // Render the sentence with the link to the restaurant page
        return view('suggestions.suggestion-text', ['place' => $selected])->render();
    }
}
