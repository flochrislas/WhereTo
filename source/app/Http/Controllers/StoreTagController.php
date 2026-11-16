<?php

namespace App\Http\Controllers;

use App\Models\StoreTag;
use Illuminate\Support\Facades\Cache;

class StoreTagController extends Controller
{
    /**
     * Filter page with all the tags (cached forever).
     */
    public function viewAllTags()
    {
        $tags = Cache::rememberForever('all_stores_tags', function () {
            return StoreTag::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });

        return view('stores.main', ['tags' => $tags]);
    }
}
