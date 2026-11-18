<?php

namespace App\Models;

use App\Support\StructuredData;
use App\Traits\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Restaurant extends Model implements Place
{
    // Uses a trait to get the localisation related stuff (currentDistance, fillCoordinates...)
    use Location;

    // Accessible fields for mass update/create (Model::create($data), $model->update($data))
    protected $fillable = [
        'name', 'location', 'lat', 'lon', 'type', 'lunch_price', 'points', 'experience',
        'visited', 'visit_date', 'google_maps_link', 'tabelog_link', 'official_website',
        'score_lunch', 'score_place', 'score_food', 'score_price', 'score_date',
    ];

    /**
     * The tags that belong to the restaurant.
     * Read from the DB, through the pivot table restaurant_restaurant_tag.
     *
     * @return BelongsToMany<RestaurantTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(RestaurantTag::class);
    }

    /**
     * Returns the tags that belong to the restaurant.
     * Read from the cache (key: restaurant-<id>-tags); flush the cache after changing tags.
     * https://ctf0.wordpress.com/2017/08/19/how-to-cache-models-relationships-in-laravel/
     *
     * @return Collection<int, RestaurantTag>
     */
    public function tagsCached(): Collection
    {
        // One key per place, e.g. 'restaurant-12-tags'
        $cacheKey = strtolower(class_basename($this)).'-'.$this->id.'-tags';

        // Returns the cached tags, or runs the closure once (DB read) and caches its result
        return Cache::rememberForever($cacheKey, function () use ($cacheKey) {
            Log::debug('Caching tags from DB for '.$cacheKey);

            // $this->tags (no parentheses) runs the tags() relation and returns the collection
            return $this->tags;
        });
    }

    /**
     * Attach the given tags (that must already exist) to the restaurant.
     * attach() just associates existing records (inserts pivot rows): http://laraveldaily.com/pivot-tables-and-many-to-many-relationships/
     *
     * @param  array<int, string>  $labels
     */
    public function attachTags(array $labels): void
    {
        if (empty($labels)) {
            return;
        }
        // Get the tags from the DB by their labels
        $tags = RestaurantTag::whereIn('label', $labels)->get();
        // Attach them (writes the pivot rows)
        $this->tags()->attach($tags);
    }

    /**
     * The JSON-LD data of the standalone page (doc/10-seo.md, item 5)
     *
     * @return array<string, mixed>
     */
    public function structuredData(string $url): array
    {
        // A Restaurant with its lunch price and our lunch score (out of 4) as the review
        return StructuredData::place($this, 'Restaurant', $url, array_merge(
            $this->lunch_price !== null ? ['priceRange' => '¥'.$this->lunch_price] : [],
            StructuredData::review($this, 'score_lunch', 4),
        ));
    }
}
