<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KaraokeTag extends Model
{
    protected $fillable = [
      'label',
      'type',
      'weight'
    ];

    /**
     * The bars that belong to the tag.
     */
    public function karaokes()
    {
        return $this->belongsToMany('App\Karaoke');
    }
}
