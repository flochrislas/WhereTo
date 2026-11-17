<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Restaurant tags: the autocomplete (the filter page is RestaurantController::index()).
 */
class RestaurantTagController extends Controller
{
    /**
     * Method for the autocomplete (JSON). Caches the results per term.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        // The text typed by the user, from the query string (?term=...)
        $term = (string) $request->query('term', '');
        // The key is hashed so a user-supplied term cannot collide with the other cache keys
        $result = Cache::rememberForever('autocomplete-'.md5($term), fn () => $this->autocompleteDB($term));

        return response()->json($result);
    }

    /**
     * Method for the autocomplete query to the DB. No cache.
     *
     * @return Collection<int, RestaurantTag>
     */
    public function autocompleteDB(string $term): Collection
    {
        // Tags whose label contains the term; 'label as value' is the column name the widget expects
        $result = RestaurantTag::where('label', 'LIKE', '%'.$term.'%')->get(['id', 'label as value']);
        Log::debug('autocomplete result from DB: '.$result);

        return $result;
    }
}
