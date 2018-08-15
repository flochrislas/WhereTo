<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
  public $currentDistance;

  protected $fillable = [
      'name',
      'location',
      'lat',
      'lon',
      'type',
      'points',
      'experience',
      'google_maps_link',
      'official_website',
      'score'
  ];
}
