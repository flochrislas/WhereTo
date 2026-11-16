<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BarTag extends Model
{
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * The places that carry this tag.
     */
    public function bars(): BelongsToMany
    {
        return $this->belongsToMany(Bar::class);
    }
}
