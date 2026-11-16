<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantRequest;
use App\Models\Restaurant;
use Illuminate\Support\Arr;
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
    public function store(RestaurantRequest $request)
    {
        // The RestaurantRequest already validated the form (it redirected back with the errors otherwise)

        // Fill a new model from the form and save it
        $this->save(new Restaurant, $request);

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurants.index')->with('success', 'Restaurant created successfully');
    }

    /**
     * Display the specified restaurant.
     */
    public function show(Restaurant $restaurant)
    {
        // Route model binding: Laravel loaded the restaurant whose id is in the URL, or answered 404
        return view('admin.restaurants.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified restaurant.
     */
    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    /**
     * Update the specified restaurant in the DB.
     */
    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        // Validated by RestaurantRequest, loaded by route model binding

        // Fill the existing model from the form and save it
        $this->save($restaurant, $request);

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurants.index')->with('success', 'Restaurant updated successfully');
    }

    /**
     * Remove the specified restaurant from the DB.
     */
    public function destroy(Restaurant $restaurant)
    {
        // Delete the restaurant (its pivot rows go with it, thanks to ON DELETE CASCADE)
        $restaurant->delete();

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        return redirect()->route('restaurants.index')->with('success', 'Restaurant deleted successfully');
    }

    /**
     * Fill the model from the form, parse the optional "lat, lon" field, save,
     * and drop the forever-cache so the public pages see the change.
     */
    private function save(Restaurant $restaurant, RestaurantRequest $request): void
    {
        // Copy the validated fields into the model (only the ones with a rule); 'coord' is not a column
        $restaurant->fill(Arr::except($request->validated(), ['coord']));

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
