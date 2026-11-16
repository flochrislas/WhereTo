<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StoreTag extends Model
{
    // Accessible fields for mass update/create (the admin form posts these)
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * The stores that carry this tag (other side of Store::tags()).
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class);
    }
}
