<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Restaurant tags: the public filter page, the autocomplete, and the admin CRUD.
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

    // ===============================================
    // Admin CRUD (the 7 methods of Route::resource)
    // ===============================================

    /**
     * Display a listing of the tags.
     */
    public function index()
    {
        // Fetch all the tags, newest first
        $restaurantTags = RestaurantTag::orderByDesc('created_at')->get();

        // Pass the tags to the view and load the list view
        return view('admin.restaurantTag.index', ['restaurantTags' => $restaurantTags]);
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        // Load the form view
        return view('admin.restaurantTag.create');
    }

    /**
     * Store a newly created tag in the DB.
     */
    public function store(Request $request)
    {
        // Validate the form data (redirects back with the errors when it fails)
        $request->validate(['label' => 'required']);

        // Insert the tag (only the $fillable columns of the model are taken from the form)
        RestaurantTag::create($request->all());

        // Drop the whole cache so the public pages see the new tag
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantTag.index')->with('success_msg', 'Restaurant Tag added successfully!');
    }

    /**
     * Display the specified tag.
     */
    public function show(int $id)
    {
        // Fetch the tag by id (404 when it does not exist) and load the view
        return view('admin.restaurantTag.show', ['restaurantTag' => RestaurantTag::findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(int $id)
    {
        // Fetch the tag by id and load the form view
        return view('admin.restaurantTag.edit', ['restaurantTag' => RestaurantTag::findOrFail($id)]);
    }

    /**
     * Update the specified tag in the DB.
     */
    public function update(Request $request, int $id)
    {
        // Validate the form data
        $request->validate(['label' => 'required']);

        // Update the tag with the form data
        RestaurantTag::findOrFail($id)->update($request->all());

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantTag.index')->with('success_msg', 'Restaurant Tag updated successfully!');
    }

    /**
     * Remove the specified tag from the DB.
     */
    public function destroy(int $id)
    {
        // Delete the tag (the pivot rows go with it, thanks to ON DELETE CASCADE)
        RestaurantTag::findOrFail($id)->delete();

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantTag.index')->with('success_msg', 'Restaurant Tag deleted successfully!');
    }
}
