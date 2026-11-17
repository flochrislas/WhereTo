<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Lookup list of cuisine labels, filled by the seeder. Not linked to restaurants.type (free text).
 */
class RestaurantType extends Model
{
    // Accessible fields for mass update/create
    protected $fillable = ['label'];
}
