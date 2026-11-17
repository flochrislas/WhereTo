<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KaraokeTag extends Model
{
    // Accessible fields for mass update/create (Model::create($data), used by the seeders)
    protected $fillable = [
        'label',
        'type',
        'weight',
    ];

    /**
     * The karaokes that carry this tag (other side of Karaoke::tags()).
     */
    public function karaokes(): BelongsToMany
    {
        return $this->belongsToMany(Karaoke::class);
    }
}
