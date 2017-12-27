<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantTag extends Model
{
    protected $fillable = ['label'];

    /**
     * The restaurants that belong to the tag.
     */
    public function restaurants()
    {
        return $this->belongsToMany('App\Restaurant');
    }
}
