<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class RestaurantTag extends Model
{
    // Accessible fields for mass update/create (Model::create($data), used by the seeders)
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * All the tags for the filter page, in tag cloud order (family, then importance, then newest), cached forever
     * (key 'all_restaurants_tags'; the seeder flushes the cache).
     *
     * @return Collection<int, RestaurantTag>
     */
    public static function allCached(): Collection
    {
        // The closure (reading the DB) only runs when the key is not cached yet
        return Cache::rememberForever('all_restaurants_tags', function () {
            return static::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });
    }

    /**
     * The restaurants that carry this tag (other side of Restaurant::tags()).
     *
     * @return BelongsToMany<Restaurant, $this>
     */
    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class);
    }
}
