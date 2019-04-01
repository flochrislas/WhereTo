<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    // Uses a trait to get localisation related stuff
    use Traits\Location;

    // Accessible fields for mass update/create
    /* protected $fillable = [
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
    ]; */

    /**
     * The tags that belong to the restaurant.
     * Read from the DB
     */
    public function tags()
    {
        return $this->belongsToMany(RestaurantTag::class);
    }

    /**
     * Returns the tags that belong to the restaurant.
     * Read from the cache
     * https://ctf0.wordpress.com/2017/08/19/how-to-cache-models-relationships-in-laravel/
     */
     public function tagsCached()
     {
         $cacheKey = strtolower(class_basename($this)).'-'.$this->id.'-tags';
         \Debugbar::debug('Cache key for those tags: '.$cacheKey);
         return \Cache::rememberForever($cacheKey, function () {
             \Debugbar::debug('Caching tags from DB: '.$this->tags);
             return  $this->tags;
         });
     }

    /**
     * Attach the given tags (that must already exist) to the restaurant
     */
    public function attachTags($labels)
    {
        if (!empty($labels))
        {
            // Get tags from the DB
            $query = (new RestaurantTag)->newQuery();
            foreach ($labels as $label)
            {
                $query->orWhere('label', '=', $label);
            }
            $tags = $query->get();
            // Attach them
            $this->tags()->attach($tags);
        }
        // Note:
        // attach just associate existing records: http://laraveldaily.com/pivot-tables-and-many-to-many-relationships/
        // otherwise use save or saveMany
        // awesome https://m.dotdev.co/writing-advanced-eloquent-search-query-filters-de8b6c2598db
    }

    /**
    * The users who have visited the restaurant
    */
    public function users()
    {
        //return $this->belongsToMany(User::class);
        //return $this->belongsToMany('App\User','user_restaurant')->withPivot('comment');
        return $this->belongsToMany(User::class)->withPivot('comment');
    }

}
