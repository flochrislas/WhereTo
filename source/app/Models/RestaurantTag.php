<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RestaurantTag extends Model
{
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * The places that carry this tag.
     */
    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class);
    }
}
