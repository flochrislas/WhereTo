<?php

namespace App\Http\Controllers;

use App\Models\KaraokeTag;
use Illuminate\Support\Facades\Cache;

class KaraokeTagController extends Controller
{
    /**
     * Gets all the karaoke tags for the filter page.
     */
    public function viewAllTags()
    {
        // Use the cache; the closure (reading the DB) only runs when the key is not cached yet
        $tags = Cache::rememberForever('all_karaokes_tags', function () {
            // Tag cloud order: tag family (type) first, then importance (weight), then newest
            return KaraokeTag::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });

        // Pass the tags to the view and load the page
        return view('karaokes.main', ['tags' => $tags]);
    }
}
