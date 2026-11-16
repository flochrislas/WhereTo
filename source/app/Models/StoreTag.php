<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StoreTag extends Model
{
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * The places that carry this tag.
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class);
    }
}
