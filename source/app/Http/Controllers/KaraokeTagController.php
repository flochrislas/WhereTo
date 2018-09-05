<?php

namespace App\Http\Controllers;

use App\KaraokeTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KaraokeTagController extends Controller
{

    /**
    * Get all the karaoke tags.
    * Using cache.
    */
    public function viewAllTags(Request $request)
    {
        $cacheKey = "all_karaokes_tags";
        $tags = Cache::rememberForever($cacheKey, function() {
              return KaraokeTag::orderBy('type','desc')
                                ->orderBy('weight','desc')
                                ->orderBy('created_at','desc')
                                ->get();
        });
        //pass tags data to view and load list view
        return view('karaokes.main', ['tags' => $tags]);
    }

      // If ever in need of all the default functions, just copy paste from restaurant tags controller.
}
