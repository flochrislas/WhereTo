<?php

namespace App\Http\Controllers;

use App\Models\BarTag;
use Illuminate\Support\Facades\Cache;

class BarTagController extends Controller
{
    /**
     * Filter page with all the tags (cached forever).
     */
    public function viewAllTags()
    {
        $tags = Cache::rememberForever('all_bars_tags', function () {
            return BarTag::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });

        return view('bars.main', ['tags' => $tags]);
    }
}
