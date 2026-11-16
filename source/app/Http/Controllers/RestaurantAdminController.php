<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Admin CRUD for restaurants (Route::resource('restaurants')).
 */
class RestaurantAdminController extends Controller
{
    public function index()
    {
        return view('admin.restaurants.index', ['restaurants' => Restaurant::with('tags')->orderByDesc('id')->get()]);
    }

    public function create()
    {
        return view('admin.restaurants.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'type' => 'required']);
        $this->save(new Restaurant, $request);

        return redirect()->route('restaurants.index')->with('success', 'Restaurant created successfully');
    }

    public function show(int $id)
    {
        return view('admin.restaurants.show', ['restaurant' => Restaurant::findOrFail($id)]);
    }

    public function edit(int $id)
    {
        return view('admin.restaurants.edit', ['restaurant' => Restaurant::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['name' => 'required', 'type' => 'required']);
        $this->save(Restaurant::findOrFail($id), $request);

        return redirect()->route('restaurants.index')->with('success', 'Restaurant updated successfully');
    }

    public function destroy(int $id)
    {
        Restaurant::findOrFail($id)->delete();
        Cache::flush();

        return redirect()->route('restaurants.index')->with('success', 'Restaurant deleted successfully');
    }

    /**
     * Fill from the form, parse the optional "lat, lon" field, save and drop the forever-cache
     * so the public pages see the change.
     */
    private function save(Restaurant $restaurant, Request $request): void
    {
        $restaurant->fill($request->except('coord'));
        $coord = trim((string) $request->input('coord', ''));
        if ($coord !== '') {
            $restaurant->fillCoordinatesFromString($coord);
        }
        $restaurant->save();
        Cache::flush();
    }
}
