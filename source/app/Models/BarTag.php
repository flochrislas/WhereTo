<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class BarTag extends Model
{
    // Accessible fields for mass update/create (Model::create($data), used by the seeders)
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * All the tags for the filter page, in tag cloud order (family, then importance, then newest), cached forever
     * (key 'all_bars_tags'; the seeder flushes the cache).
     *
     * @return Collection<int, BarTag>
     */
    public static function allCached(): Collection
    {
        // The closure (reading the DB) only runs when the key is not cached yet
        return Cache::rememberForever('all_bars_tags', function () {
            return static::orderByDesc('type')->orderByDesc('weight')->orderByDesc('created_at')->get();
        });
    }

    /**
     * The bars that carry this tag (other side of Bar::tags()).
     *
     * @return BelongsToMany<Bar, $this>
     */
    public function bars(): BelongsToMany
    {
        return $this->belongsToMany(Bar::class);
    }
}
