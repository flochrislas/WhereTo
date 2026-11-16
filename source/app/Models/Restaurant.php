<?php

namespace App\Models;

use App\Traits\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Restaurant extends Model
{
    use Location;

    protected $fillable = [
        'name', 'location', 'lat', 'lon', 'type', 'lunch_price', 'points', 'experience',
        'visited', 'visit_date', 'google_maps_link', 'tabelog_link', 'official_website',
        'score_lunch', 'score_place', 'score_food', 'score_price', 'score_date',
    ];

    /**
     * The tags that belong to the place (read from the DB).
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(RestaurantTag::class);
    }

    /**
     * The tags that belong to the place, cached forever.
     * Cache key: <model>-<id>-tags. Flush the cache after changing tags.
     */
    public function tagsCached()
    {
        $cacheKey = strtolower(class_basename($this)).'-'.$this->id.'-tags';

        return Cache::rememberForever($cacheKey, function () use ($cacheKey) {
            Log::debug('Caching tags from DB for '.$cacheKey);

            return $this->tags;
        });
    }

    /**
     * Attach the given tags (that must already exist) to the place.
     *
     * @param  array<int, string>  $labels
     */
    public function attachTags(array $labels): void
    {
        if (empty($labels)) {
            return;
        }
        $tags = RestaurantTag::whereIn('label', $labels)->get();
        $this->tags()->attach($tags);
    }

    /**
     * The users who have visited the restaurant (pivot: comment).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('comment');
    }
}
