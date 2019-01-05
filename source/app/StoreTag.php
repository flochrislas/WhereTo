<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreTag extends Model
{
    protected $fillable = [
      'label',
      'type',
      'weight'
    ];

    /**
     * The stores that belong to the tag.
     */
    public function stores()
    {
        return $this->belongsToMany('App\Store');
    }
}
