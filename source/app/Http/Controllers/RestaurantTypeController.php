<?php

namespace App\Http\Controllers;

use App\Models\RestaurantType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Admin CRUD for the restaurant types lookup table.
 */
class RestaurantTypeController extends Controller
{
    public function index()
    {
        return view('admin.restaurantType.index', ['restaurantTypes' => RestaurantType::orderByDesc('created_at')->get()]);
    }

    public function create()
    {
        return view('admin.restaurantType.add');
    }

    public function store(Request $request)
    {
        $request->validate(['label' => 'required']);
        RestaurantType::create($request->all());
        Cache::flush();

        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type added successfully!');
    }

    public function show(int $id)
    {
        return view('admin.restaurantType.details', ['restaurantType' => RestaurantType::findOrFail($id)]);
    }

    public function edit(int $id)
    {
        return view('admin.restaurantType.edit', ['restaurantType' => RestaurantType::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['label' => 'required']);
        RestaurantType::findOrFail($id)->update($request->all());
        Cache::flush();

        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type updated successfully!');
    }

    public function destroy(int $id)
    {
        RestaurantType::findOrFail($id)->delete();
        Cache::flush();

        return redirect()->route('restaurantType.index')->with('success_msg', 'Restaurant Type deleted successfully!');
    }
}
