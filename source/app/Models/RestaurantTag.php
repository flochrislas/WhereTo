<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RestaurantTag extends Model
{
    // Accessible fields for mass update/create (the admin form posts these)
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * The restaurants that carry this tag (other side of Restaurant::tags()).
     */
    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class);
    }
}
