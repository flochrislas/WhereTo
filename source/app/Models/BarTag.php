<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BarTag extends Model
{
    // Accessible fields for mass update/create (the admin form posts these)
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * The bars that carry this tag (other side of Bar::tags()).
     */
    public function bars(): BelongsToMany
    {
        return $this->belongsToMany(Bar::class);
    }
}
