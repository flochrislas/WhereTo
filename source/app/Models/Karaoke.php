<?php

namespace App\Models;

use App\Traits\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Karaoke extends Model
{
    // Uses a trait to get the localisation related stuff (currentDistance, fillCoordinates...)
    use Location;

    // Accessible fields for mass update/create (Model::create($data), $model->update($data))
    protected $fillable = [
        'name', 'area', 'location', 'lat', 'lon', 'points', 'experience',
        'visited', 'visit_date', 'google_maps_link', 'official_website', 'score',
    ];

    /**
     * The tags that belong to the karaoke.
     * Read from the DB, through the pivot table karaoke_karaoke_tag.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(KaraokeTag::class);
    }

    /**
     * Returns the tags that belong to the karaoke.
     * Read from the cache (key: karaoke-<id>-tags); flush the cache after changing tags.
     * https://ctf0.wordpress.com/2017/08/19/how-to-cache-models-relationships-in-laravel/
     */
    public function tagsCached()
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
     * Attach the given tags (that must already exist) to the karaoke.
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
        $tags = KaraokeTag::whereIn('label', $labels)->get();
        // Attach them (writes the pivot rows)
        $this->tags()->attach($tags);
    }
}
