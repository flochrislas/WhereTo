<?php

namespace App\Http\Controllers;

use App\Models\RestaurantType;
use Illuminate\Http\Request;
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
    public function store(Request $request)
    {
        // Validate the form data (redirects back with the errors when it fails)
        $request->validate(['label' => 'required']);

        // Insert the type (only the $fillable columns of the model are taken from the form)
        RestaurantType::create($request->all());

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type added successfully!');
    }

    /**
     * Display the specified type.
     */
    public function show(int $id)
    {
        // Fetch the type by id (404 when it does not exist) and load the view
        return view('admin.restaurantType.details', ['restaurantType' => RestaurantType::findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified type.
     */
    public function edit(int $id)
    {
        // Fetch the type by id and load the form view
        return view('admin.restaurantType.edit', ['restaurantType' => RestaurantType::findOrFail($id)]);
    }

    /**
     * Update the specified type in the DB.
     */
    public function update(Request $request, int $id)
    {
        // Validate the form data
        $request->validate(['label' => 'required']);

        // Update the type with the form data
        RestaurantType::findOrFail($id)->update($request->all());

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type updated successfully!');
    }

    /**
     * Remove the specified type from the DB.
     */
    public function destroy(int $id)
    {
        // Delete the type
        RestaurantType::findOrFail($id)->delete();

        // Drop the whole cache so the public pages see the change
        Cache::flush();

        // Redirect to the list, with a status message in the session
        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type deleted successfully!');
    }
}
