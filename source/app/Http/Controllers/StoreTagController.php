<?php

namespace App\Http\Controllers;

use App\StoreTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StoreTagController extends Controller
{
  /**
  * Get all the stores tags.
  * Using cache.
  */
  public function viewAllTags(Request $request)
  {
      $cacheKey = "all_stores_tags";
      $tags = Cache::rememberForever($cacheKey, function() {
            return StoreTag::orderBy('type','desc')
                              ->orderBy('weight','desc')
                              ->orderBy('created_at','desc')
                              ->get();
      });
      //pass tags data to view and load list view
      return view('stores.main', ['tags' => $tags]);
  }

    // If ever in need of all the default functions, just copy paste from restaurant tags controller.
}
