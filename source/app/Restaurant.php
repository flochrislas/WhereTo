<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    public $currentDistance;

    protected $fillable = [
        'name',
        'location',
        'lat',
        'lon',
        'type',
        'lunch_price',
        'points',
        'experience',
        'visited',
        'visit_date',
        'google_maps_link',
        'tabelog_link',
        'official_website',
        'score_lunch',
        'score_place',
        'score_food',
        'score_price',
        'score_date'
    ];

    /**
     * The tags that belong to the restaurant.
     * Read from the DB
     */
    public function tags()
    {
        return $this->belongsToMany(RestaurantTag::class);
    }

    /**
     * The tags that belong to the restaurant.
     * Read from the cache
     * https://ctf0.wordpress.com/2017/08/19/how-to-cache-models-relationships-in-laravel/
     */
    public function tagsCached()
    {
        return \Cache::rememberForever($this->id.'-tags', function () {
            return $this->tags();
        });
    }

    /**
     * Attach the given tags (that must already exist) to the restaurant
     */
    public function attachTags($labels)
    {
        if (!empty($labels))
        {
            $query = (new RestaurantTag)->newQuery();
            foreach ($labels as $label)
            {
                $query->orWhere('label', '=', $label);
            }
            $tags = $query->get();
            $this->tags()->attach($tags);
        }
        // Note:
        // attach just associate existing records: http://laraveldaily.com/pivot-tables-and-many-to-many-relationships/
        // otherwise use save or saveMany
        // awesome https://m.dotdev.co/writing-advanced-eloquent-search-query-filters-de8b6c2598db
    }

    /**
     * Try to fill lat and lon with the ones found in the Google Maps link
     */
    public function autofillCoordFromGoogleLink() : void
    {
      try {
        $coord = GeoUtils::google2coord($this->google_maps_link);
        $this->lat = $coord[0];
        $this->lon = $coord[1];
        $this->save();
      } catch (\ErrorException $e) {
        \Log::debug('Could not find coordinates from Google Maps link in restaurant id '.$this->id);
      }

    }

}
