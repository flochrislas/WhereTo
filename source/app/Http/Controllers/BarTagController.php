<?php

namespace App\Http\Controllers;

use App\BarTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BarTagController extends Controller
{
    /**
    * Get all the tags.
    * Using cache.
    */
    public function viewAllTags(Request $request)
    {
        $cacheKey = "all_bars_tags";
        $tags = Cache::rememberForever($cacheKey, function() {
              return BarTag::orderBy('type','desc')
                                ->orderBy('weight','desc')
                                ->orderBy('created_at','desc')
                                ->get();
        });
        //pass tags data to view and load list view
        return view('bars.main', ['tags' => $tags]);
    }
}
