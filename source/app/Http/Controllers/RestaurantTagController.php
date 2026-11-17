<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Restaurant tags: the public filter page and the autocomplete.
 */
class RestaurantTagController extends Controller
{
    /**
     * Gets all the restaurant tags for the public filter page.
     */
    public function restaurantsFilter()
    {
        // Use the cache; the closure (reading the DB) only runs when the key is not cached yet
        $restaurantTags = Cache::rememberForever('all_restaurants_tags', function () {
            // Tag cloud order: tag family (type) first, then importance (weight), then newest
            return RestaurantTag::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });

        // Pass the tags to the view and load the page
        return view('restaurants.main', ['restaurantTags' => $restaurantTags]);
    }

    /**
     * Method for the autocomplete (JSON). Caches the results per term.
     */
    public function autocomplete(Request $request)
    {
        // The text typed by the user, from the query string (?term=...)
        $term = (string) $request->query('term', '');
        // The key is hashed so a user-supplied term cannot collide with the other cache keys
        $result = Cache::rememberForever('autocomplete-'.md5($term), fn () => $this->autocompleteDB($term));

        return response()->json($result);
    }

    /**
     * Method for the autocomplete query to the DB. No cache.
     */
    public function autocompleteDB(string $term)
    {
        // Tags whose label contains the term; 'label as value' is the column name the widget expects
        $result = RestaurantTag::where('label', 'LIKE', '%'.$term.'%')->get(['id', 'label as value']);
        Log::debug('autocomplete result from DB: '.$result);

        return $result;
    }
}
