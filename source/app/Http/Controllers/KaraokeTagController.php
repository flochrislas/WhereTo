<?php

namespace App\Http\Controllers;

use App\Models\KaraokeTag;
use Illuminate\Support\Facades\Cache;

class KaraokeTagController extends Controller
{
    /**
     * Filter page with all the tags (cached forever).
     */
    public function viewAllTags()
    {
        $tags = Cache::rememberForever('all_karaokes_tags', function () {
            return KaraokeTag::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });

        return view('karaokes.main', ['tags' => $tags]);
    }
}
