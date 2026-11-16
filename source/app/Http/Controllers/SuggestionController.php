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
     * HTML fragment with a suggestion, or an empty string outside lunch time.
     */
    public function suggest(Request $request): string
    {
        if (! self::isLunchTime()) {
            return '';
        }

        $position = $request->query('position');

        return $this->suggestLunch(is_scalar($position) ? (string) $position : null);
    }

    /**
     * True between 11:30 and 14:00 in Tokyo.
     */
    public static function isLunchTime(): bool
    {
        $lunchStart = Carbon::parse(self::LUNCH_START, self::TIMEZONE);
        $lunchEnd = Carbon::parse(self::LUNCH_END, self::TIMEZONE);

        return Carbon::now(self::TIMEZONE)->isBetween($lunchStart, $lunchEnd);
    }

    public function suggestLunch(?string $position): string
    {
        $restaurantController = new RestaurantController;
        // The 100 best restaurants, then the nearest TOP_N, then one at random.
        $places = $restaurantController->useCache(null, 'AND', 'ratings', 100);
        $places = $restaurantController->handleDistances($places, $position, 'distance');
        if ($places->isEmpty()) {
            return '';
        }
        $selected = $places->take(self::TOP_N)->random();

        return view('suggestions.suggestion-text', ['place' => $selected])->render();
    }
}
