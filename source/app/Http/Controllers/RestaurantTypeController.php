<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantTypeRequest;
use App\Models\RestaurantType;
use Illuminate\Support\Facades\Cache;

/**
 * Admin CRUD for the restaurant types lookup table.
 * Originally from the tutorial https://www.codexworld.com/laravel-tutorial-crud-add-edit-delete-operations/
 */
class RestaurantTypeController extends Controller
{
    /**
     * Display a listing of the types.
     */
    public function index()
    {
        // Fetch all the types, newest first, and load the list view
        return view('admin.restaurantType.index', ['restaurantTypes' => RestaurantType::orderByDesc('created_at')->get()]);
    }

    /**
     * Show the form for creating a new type.
     */
    public function create()
    {
        // Load the form view
        return view('admin.restaurantType.add');
    }

    /**
     * Store a newly created type in the DB.
     */
    public function store(RestaurantTypeRequest $request)
    {
        // Validated by RestaurantTypeRequest (it redirected back with the errors otherwise):
        // insert the type from the validated fields
        RestaurantType::create($request->validated());

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type added successfully!');
    }

    /**
     * Display the specified type.
     */
    public function show(RestaurantType $restaurantType)
    {
        // Route model binding: Laravel loaded the type whose id is in the URL, or answered 404
        return view('admin.restaurantType.details', compact('restaurantType'));
    }

    /**
     * Show the form for editing the specified type.
     */
    public function edit(RestaurantType $restaurantType)
    {
        return view('admin.restaurantType.edit', compact('restaurantType'));
    }

    /**
     * Update the specified type in the DB.
     */
    public function update(RestaurantTypeRequest $request, RestaurantType $restaurantType)
    {
        // Update the type with the validated fields
        $restaurantType->update($request->validated());

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type updated successfully!');
    }

    /**
     * Remove the specified type from the DB.
     */
    public function destroy(RestaurantType $restaurantType)
    {
        // Delete the type
        $restaurantType->delete();

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type deleted successfully!');
    }
}
