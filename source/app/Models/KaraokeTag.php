<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KaraokeTag extends Model
{
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * The places that carry this tag.
     */
    public function karaokes(): BelongsToMany
    {
        return $this->belongsToMany(Karaoke::class);
    }
}
