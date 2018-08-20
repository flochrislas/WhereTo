<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bar extends Model
{
    // Uses a trait to get localisation related stuff
    use Traits\Location;

    /**
     * The tags that belong to the bar.
     * Read from the DB
     */
    public function tags()
    {
        return $this->belongsToMany(BarTag::class);
    }

    /**
     * Returns the tags that belong to the bar.
     * Read from the cache
     * https://ctf0.wordpress.com/2017/08/19/how-to-cache-models-relationships-in-laravel/
     */
    public function tagsCached()
    {
        return \Cache::rememberForever($this->id.'-tags', function () {
            \Debugbar::debug('Caching tags from DB: '.($this->tags));
            return  $this->tags;
        });
    }

    /**
     * Attach the given tags (that must already exist) to the bar
     */
    public function attachTags($labels)
    {
        if (!empty($labels))
        {
            // Get tags from the DB
            $query = (new BarTag)->newQuery();
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

}
