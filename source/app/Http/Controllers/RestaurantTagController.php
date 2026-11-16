<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RestaurantTagController extends Controller
{
    /**
     * Public filter page with all the restaurant tags (cached forever).
     */
    public function restaurantsFilter()
    {
        $restaurantTags = Cache::rememberForever('all_restaurants_tags', function () {
            return RestaurantTag::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });

        return view('restaurants.main', ['restaurantTags' => $restaurantTags]);
    }

    /**
     * Tag autocomplete (JSON), cached per term.
     */
    public function autocomplete(Request $request)
    {
        $term = (string) $request->query('term', '');
        $result = Cache::rememberForever('autocomplete-'.md5($term), fn () => $this->autocompleteDB($term));

        return response()->json($result);
    }

    public function autocompleteDB(string $term)
    {
        $result = RestaurantTag::where('label', 'LIKE', '%'.$term.'%')->get(['id', 'label as value']);
        Log::debug('autocomplete result from DB: '.$result);

        return $result;
    }

    // ---- Admin CRUD (Route::resource) ----

    public function index()
    {
        $restaurantTags = RestaurantTag::orderByDesc('created_at')->get();

        return view('admin.restaurantTag.index', ['restaurantTags' => $restaurantTags]);
    }

    public function create()
    {
        return view('admin.restaurantTag.create');
    }

    public function store(Request $request)
    {
        $request->validate(['label' => 'required']);
        RestaurantTag::create($request->all());
        Cache::flush();

        return redirect()->route('restaurantTag.index')->with('success_msg', 'Restaurant Tag added successfully!');
    }

    public function show(int $id)
    {
        return view('admin.restaurantTag.show', ['restaurantTag' => RestaurantTag::findOrFail($id)]);
    }

    public function edit(int $id)
    {
        return view('admin.restaurantTag.edit', ['restaurantTag' => RestaurantTag::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['label' => 'required']);
        RestaurantTag::findOrFail($id)->update($request->all());
        Cache::flush();

        return redirect()->route('restaurantTag.index')->with('success_msg', 'Restaurant Tag updated successfully!');
    }

    public function destroy(int $id)
    {
        RestaurantTag::findOrFail($id)->delete();
        Cache::flush();

        return redirect()->route('restaurantTag.index')->with('success_msg', 'Restaurant Tag deleted successfully!');
    }
}
