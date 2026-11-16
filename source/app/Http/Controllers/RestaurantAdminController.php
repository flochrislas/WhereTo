<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Admin CRUD for restaurants.
 * Route::resource('restaurants') maps the 7 methods below to the URLs under /admin/restaurants.
 * Originally from the tutorial https://appdividend.com/2017/10/15/laravel-5-5-crud-example-tutorial/
 */
class RestaurantAdminController extends Controller
{
    /**
     * Display a listing of the restaurants.
     */
    public function index()
    {
        // All the restaurants, newest first; with('tags') loads the tags in one extra query instead of one per row
        return view('admin.restaurants.index', ['restaurants' => Restaurant::with('tags')->orderByDesc('id')->get()]);
    }

    /**
     * Show the form for creating a new restaurant.
     */
    public function create()
    {
        return view('admin.restaurants.create');
    }

    /**
     * Store a newly created restaurant in the DB.
     */
    public function store(Request $request)
    {
        // Validate the form data (redirects back with the errors when it fails)
        $request->validate(['name' => 'required', 'type' => 'required']);

        // Fill a new model from the form and save it
        $this->save(new Restaurant, $request);

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurants.index')->with('success', 'Restaurant created successfully');
    }

    /**
     * Display the specified restaurant.
     */
    public function show(int $id)
    {
        // findOrFail returns a 404 page when the id does not exist
        return view('admin.restaurants.show', ['restaurant' => Restaurant::findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified restaurant.
     */
    public function edit(int $id)
    {
        return view('admin.restaurants.edit', ['restaurant' => Restaurant::findOrFail($id)]);
    }

    /**
     * Update the specified restaurant in the DB.
     */
    public function update(Request $request, int $id)
    {
        // Validate the form data
        $request->validate(['name' => 'required', 'type' => 'required']);

        // Fill the existing model from the form and save it
        $this->save(Restaurant::findOrFail($id), $request);

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurants.index')->with('success', 'Restaurant updated successfully');
    }

    /**
     * Remove the specified restaurant from the DB.
     */
    public function destroy(int $id)
    {
        // Delete the restaurant (its pivot rows go with it, thanks to ON DELETE CASCADE)
        Restaurant::findOrFail($id)->delete();

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        return redirect()->route('restaurants.index')->with('success', 'Restaurant deleted successfully');
    }

    /**
     * Fill the model from the form, parse the optional "lat, lon" field, save,
     * and drop the forever-cache so the public pages see the change.
     */
    private function save(Restaurant $restaurant, Request $request): void
    {
        // Copy the form fields into the model (only the $fillable columns); 'coord' is not a column
        $restaurant->fill($request->except('coord'));

        // The coordinates come as one "lat, lon" text field
        $coord = trim((string) $request->input('coord', ''));
        if ($coord !== '') {
            // Parses the string into lat and lon (and saves); invalid input is logged and ignored
            $restaurant->fillCoordinatesFromString($coord);
        }

        // INSERT or UPDATE depending on whether the model already has an id
        $restaurant->save();

        // Drop the whole cache (results and tag lists) so the public pages see the change
        Cache::flush();
    }
}
