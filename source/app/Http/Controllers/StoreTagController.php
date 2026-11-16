<?php

namespace App\Http\Controllers;

use App\Models\StoreTag;
use Illuminate\Support\Facades\Cache;

class StoreTagController extends Controller
{
    /**
     * Gets all the store tags for the filter page.
     */
    public function viewAllTags()
    {
        // Use the cache; the closure (reading the DB) only runs when the key is not cached yet
        $tags = Cache::rememberForever('all_stores_tags', function () {
            // Tag cloud order: tag family (type) first, then importance (weight), then newest
            return StoreTag::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });

        // Pass the tags to the view and load the page
        return view('stores.main', ['tags' => $tags]);
    }
}
