<?php

namespace App\Services;

use App\Enums\OrderBy;
use App\Enums\TagOperator;
use App\Models\Place;
use App\Support\Position;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * The search of one kind of place: the tag query, its forever cache, the ordering, and the distances
 * from the visitor. One subclass per kind (RestaurantSearch, StoreSearch...) names the model and its
 * score columns. No HTTP here: the controllers read the request and call this.
 *
 * @template TModel of Model&Place  the model of the kind, e.g. Restaurant (used by PHPStan to type the collections)
 */
abstract class PlaceSearch
{
    /** Shibuya Station from Google Maps, used when the visitor sends no position or is too far away. */
    private const STATION_POSITION = '35.658789, 139.701262';

    /** Beyond this distance (metres) from the station, the visitor's position is ignored. */
    private const MAX_DISTANCE_FROM_STATION = 2500;

    /**
     * The model class of the kind, e.g. App\Models\Restaurant.
     *
     * @return class-string<TModel>
     */
    abstract protected function modelClass(): string;

    /**
     * The score columns that make the rating, best first. One column for most kinds;
     * the restaurants override this with their lunch/food/place scores.
     *
     * @return string[]
     */
    protected function ratingColumns(): array
    {
        return ['score'];
    }

    /**
     * The column used by the "cost" order, or null when the kind has no price (then "cost" sorts like "ratings").
     */
    protected function costColumn(): ?string
    {
        return null;
    }

    /** The station as a Position object */
    public static function station(): Position
    {
        return Position::fromString(self::STATION_POSITION);
    }

    /** The model name, e.g. 'Restaurant', used in the cache keys */
    private function modelName(): string
    {
        return class_basename($this->modelClass());
    }

    /**
     * Find the places matching the tags, ordered, through the cache.
     * Generate the key, read the cache, if not found then read the DB and fill the cache.
     *
     * @param  array<int, string>|null  $tags  used in the search
     * @param  TagOperator  $op  operator used in the search
     * @param  OrderBy  $orderBy  sorting used in the search
     * @param  int  $limit  maximum number of results (0 for no limit)
     * @return Collection<int, TModel>
     */
    public function find(?array $tags, TagOperator $op, OrderBy $orderBy, int $limit = 0): Collection
    {
        // The key needs to be made of all the used parameters except the coordinates
        $cacheKey = $this->cacheKey($op, $tags, $orderBy, $limit);

        // rememberForever returns the cached value, or runs the closure once and caches its result
        return Cache::rememberForever($cacheKey, function () use ($tags, $op, $orderBy, $limit) {
            return $this->readFromDB($tags, $op, $orderBy, $limit);
        });
    }

    /**
     * Generates the key used to put the main query into the cache.
     *
     * @param  TagOperator  $op  used in the search
     * @param  array<int, string>|null  $tags  used in the search. Can be empty for ALL, or an array of actual tags
     * @param  OrderBy  $orderBy  used in the search
     * @param  int  $limit  used in the search
     * @return string the key
     */
    public function cacheKey(TagOperator $op, ?array $tags, OrderBy $orderBy, int $limit = 0): string
    {
        if (empty($tags)) {
            // No tag selected: e.g. 'All-Restaurant'
            $cacheKey = 'All-'.$this->modelName();
        } else {
            // One hash per operator + tags combination, prefixed with the model so models never collide
            $cacheKey = $this->modelName().'-'.md5($op->value.','.implode(',', $tags));
        }
        if ($limit > 0) {
            // e.g. 'l100-All-Restaurant' for the 100 best restaurants
            $cacheKey = 'l'.$limit.'-'.$cacheKey;
        }
        // Only the "cost" order changes the SQL ordering (so the cached rows), and only for the kinds that have
        // a price column; the other orders are applied in PHP after reading the cache
        if ($orderBy === OrderBy::Cost && $this->costColumn() !== null) {
            $cacheKey = $orderBy->value.$cacheKey;
        }

        return $cacheKey;
    }

    /**
     * Read from the DB. No cache, just the DB.
     *
     * @param  array<int, string>|null  $tags
     * @return Collection<int, TModel>
     */
    public function readFromDB(?array $tags, TagOperator $op = TagOperator::And, OrderBy $orderBy = OrderBy::Distance, int $limit = 0): Collection
    {
        Log::debug('Hitting the DB with op '.$op->value.' orderBy '.$orderBy->value.' limit '.$limit);
        // Start a query on the model's table, e.g. Restaurant::query()
        $class = $this->modelClass();
        $query = $class::query();

        // Add the tag filter to the query
        $this->whereTags($query, $tags, $op);
        // Add the ORDER BY clause (the columns depend on the kind of place)
        $this->orderBy($query, $orderBy);
        if ($limit > 0) {
            $query->limit($limit);
        }

        // Run the query and return the rows as model objects
        return $query->get();
    }

    /**
     * Add the ORDER BY clause to the query.
     * Sorting by distance happens outside the query (in PHP), so "distance" and "ratings" are the same here:
     * the best places on top. "cost" puts the price first when the kind has one.
     *
     * @param  Builder<TModel>  $query
     */
    public function orderBy(Builder $query, OrderBy $orderBy = OrderBy::Distance): void
    {
        // Price first, only for the kinds that have a price column (restaurants: lunch_price)
        if ($orderBy === OrderBy::Cost && $this->costColumn() !== null) {
            $query->orderBy($this->costColumn());
        }
        // Then the best places on top: one orderByDesc per rating column
        foreach ($this->ratingColumns() as $column) {
            $query->orderByDesc($column);
        }
        $query->orderBy('id'); // deterministic order for ties
    }

    /**
     * Add the tag filter to the query.
     * AND: has all the tags; OR: has any of them; ANDNOT: has none of them; ORNOT: has a tag outside the list.
     * (An unknown op in the URL became And in TagOperator::fromRequest.)
     *
     * @param  Builder<TModel>  $query
     * @param  array<int, string>|null  $tags
     */
    public function whereTags(Builder $query, ?array $tags, TagOperator $op = TagOperator::And): void
    {
        // No tags means no filter: all the places
        if (empty($tags)) {
            return;
        }
        // Sub-query on the tags relation: "tag label is one of the given ones"
        $in = fn (Builder $q) => $q->whereIn('label', $tags);
        match ($op) {
            // whereHas: the place has at least one matching tag
            TagOperator::Or => $query->whereHas('tags', $in),
            // whereDoesntHave: the place has no matching tag at all
            TagOperator::AndNot => $query->whereDoesntHave('tags', $in),
            // the place has at least one tag that is NOT in the list
            TagOperator::OrNot => $query->whereHas('tags', fn (Builder $q) => $q->whereNotIn('label', $tags)),
            // AND: the number of matching tags must equal the number of tags asked
            TagOperator::And => $query->whereHas('tags', $in, '=', count($tags)),
        };
    }

    /**
     * Where the visitor is, as a Position: the given "lat, lon", or Shibuya Station when it is missing,
     * invalid, or more than 2.5 km away (the app is about Shibuya; a far position would give useless distances).
     */
    public function resolvePosition(?string $position): Position
    {
        // null when the browser sent nothing, "null" (no fix yet) or something that is not coordinates
        $current = Position::tryFromString($position);
        if ($current === null) {
            Log::debug('GPS position not set or invalid. Using Shibuya Station coordinates.');

            return self::station();
        }
        // We check the distance from Shibuya Station
        $distance = $current->distanceTo(self::station());
        Log::debug('Distance from Shibuya Station: '.$distance);
        if ($distance > self::MAX_DISTANCE_FROM_STATION) {
            Log::debug('Position is too far. Using Shibuya Station coordinates.');

            return self::station();
        }
        Log::debug('GPS position set from user: '.$current);

        return $current;
    }

    /**
     * Fill the distance from the visitor on each place, and sort by it when the order asks for it.
     *
     * @param  Collection<int, TModel>  $places
     * @param  string|null  $position  "lat, lon" from the browser, resolved with resolvePosition()
     * @return Collection<int, TModel> the places, filled with distances
     */
    public function withDistances(Collection $places, ?string $position, OrderBy $orderBy): Collection
    {
        $position = $this->resolvePosition($position);
        if ($orderBy === OrderBy::Distance) {
            return $this->sortByDistance($places, $position);
        }
        // We still need to fill the current distance of each place we show
        $this->generateCurrentDistances($places, $position);

        return $places;
    }

    /**
     * Return the places sorted by distance from the given position.
     * Places without coordinates are dropped.
     *
     * @param  iterable<TModel>  $places
     * @return Collection<int, TModel>
     */
    public function sortByDistance(iterable $places, Position $position): Collection
    {
        // Fill currentDistance on each place
        $this->generateCurrentDistances($places, $position);

        // Keep only the places with a distance, sort by it, and renumber the keys 0, 1, 2...
        /** @var Collection<int, TModel> $collection */
        $collection = Collection::make($places);

        /** @var Collection<int, TModel> $sorted  (PHPStan loses the model type through sortBy) */
        $sorted = $collection
            ->filter(fn ($place) => $place->currentDistance() !== null)
            ->sortBy(fn ($place) => $place->currentDistance())
            ->values();

        return $sorted;
    }

    /**
     * For all the given places, compute the distance from the given position.
     * Each result is stored in the place's model (currentDistance, in metres).
     *
     * @param  iterable<TModel>  $places
     */
    public function generateCurrentDistances(iterable $places, Position $position): void
    {
        foreach ($places as $place) {
            $placePosition = $place->position();
            if ($placePosition !== null) {
                $place->setCurrentDistance($position->distanceTo($placePosition));
            } else {
                Log::debug('No coordinates for: '.$place->getAttribute('name'));
            }
        }
    }
}
