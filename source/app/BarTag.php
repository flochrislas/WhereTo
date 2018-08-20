<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BarTag extends Model
{
    protected $fillable = [
      'label',
      'type',
      'weight'
    ];

    /**
     * The bars that belong to the tag.
     */
    public function bars()
    {
        return $this->belongsToMany('App\Bar');
    }
}
