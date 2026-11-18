<?php

namespace App\Models;

use App\Support\StructuredData;
use App\Traits\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Bar extends Model implements Place
{
    // Uses a trait to get the localisation related stuff (currentDistance, fillCoordinates...)
    use Location;

    // Accessible fields for mass update/create (Model::create($data), $model->update($data))
    protected $fillable = [
        'name', 'location', 'lat', 'lon', 'type', 'points', 'experience',
        'visited', 'visit_date', 'google_maps_link', 'tabelog_link', 'official_website',
        'score', 'score_place', 'score_mingling', 'score_price',
        'business_status', 'google_place_id',
    ];

    /**
     * The tags that belong to the bar.
     * Read from the DB, through the pivot table bar_bar_tag.
     *
     * @return BelongsToMany<BarTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BarTag::class);
    }

    /**
     * Returns the tags that belong to the bar.
     * Read from the cache (key: bar-<id>-tags); flush the cache after changing tags.
     * https://ctf0.wordpress.com/2017/08/19/how-to-cache-models-relationships-in-laravel/
     *
     * @return Collection<int, BarTag>
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
     * Attach the given tags (that must already exist) to the bar.
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
        $tags = BarTag::whereIn('label', $labels)->get();
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
        // A bar with our global score (out of 4) as the review
        return StructuredData::place($this, 'BarOrPub', $url, StructuredData::review($this, 'score', 4));
    }
}
