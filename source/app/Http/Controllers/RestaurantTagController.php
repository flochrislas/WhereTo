<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Support\Facades\Cache;
use App\RestaurantTag;
use Session;

class RestaurantTagController extends Controller
{

  public function restaurantsFilter(Request $request)
  {
      // Fetch all restaurantTags data
      // $restaurantTags = RestaurantTag::orderBy('created_at','desc')->get();
      // Use cache
      $cacheKey = "all_restaurants_tags";
      $restaurantTags = Cache::rememberForever($cacheKey, function() {
            return RestaurantTag::orderBy('type','desc')->orderBy('weight','desc')->orderBy('created_at','desc')->get();
      });

      //pass restaurantTags data to view and load list view
      return view('restaurants.main', ['restaurantTags' => $restaurantTags]);
      // return view('restaurants.main', compact('restaurantTags'));
  }

  /**
   * Method for the autocomplete.
   * Cache the results.
   *
   * @return \Illuminate\Http\Response
   */
  public function autocomplete(Request $request)
  {
      $term = request('term');
      Log::debug('autocomplete term: '.$term);
      $result = Cache::rememberForever($term, function() use ($term) {
            return $this->autocompleteDB($term);
        });
      return response()->json($result);
  }

  /**
   * Method for the autocomplete query to DB.
   * No cache.
   *
   * @return Illuminate\Database\Eloquent\Collection
   */
  public function autocompleteDB(string $term)
  {
      $result = RestaurantTag::where('label', 'LIKE', '%' . $term . '%')
                              ->get(['id', 'label as value']);
      Log::debug('autocomplete result from DB: '.$result);
      \Debugbar::info('Hitting DB from autocomplete with term: '.$result);
      return $result;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      //fetch all restaurantTags data
      $restaurantTags = RestaurantTag::orderBy('created_at','desc')->get();

      //pass restaurantTags data to view and load list view
      return view('admin.restaurantTag.index', ['restaurantTags' => $restaurantTags]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      //load form view
      return view('admin.restaurantTag.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
      //validate restaurantTag data
      $this->validate($request, [
          'label' => 'required'
      ]);

      //get restaurantTag data
      $restaurantTagData = $request->all();

      //insert restaurantTag data
      RestaurantTag::create($restaurantTagData);

      //store status message
      Session::flash('success_msg', 'Restaurant Tag added successfully!');

      return redirect()->route('restaurantTag.index');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      //fetch restaurantTag data
      $restaurantTag = RestaurantTag::find($id);

      //pass restaurantTags data to view and load list view
      return view('admin.restaurantTag.show', ['restaurantTag' => $restaurantTag]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      //get restaurantTag data by id
      $restaurantTag = RestaurantTag::find($id);

      //load form view
      return view('admin.restaurantTag.edit', ['restaurantTag' => $restaurantTag]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      //validate restaurantTag data
      $this->validate($request, [
          'label' => 'required'
      ]);

      //get restaurantTag data
      $restaurantTagData = $request->all();

      //update restaurantTag data
      RestaurantTag::find($id)->update($restaurantTagData);

      //store status message
      Session::flash('success_msg', 'Restaurant Tag updated successfully!');

      return redirect()->route('restaurantTag.index');

      //return redirect('/restaurants-list')->with('success', 'The restaurant has been updated!');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      //update restaurantTag data
      RestaurantTag::find($id)->delete();

      //store status message
      Session::flash('success_msg', 'Restaurant Tag deleted successfully!');

      return redirect()->route('restaurantTag.index');
  }

}
