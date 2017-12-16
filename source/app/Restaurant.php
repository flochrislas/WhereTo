<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'location',
        'coord',
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
     */
    public function tags()
    {
        return $this->belongsToMany('App\RestaurantTag');
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

}
