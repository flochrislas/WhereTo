<?php

namespace App\Http\Controllers;

use App\Tools\GeoUtils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * If you have a Model class that describes a place (such as Restaurant.php or Karaoke.php),
 * you want its controller to extend this class.
 * Subclasses only provide the model name; the model class and the view names derive from it.
 */
abstract class PlaceController extends Controller
{
    /** Shibuya Station from Google Maps, used when the visitor sends no position or is too far away. */
    private const STATION_POSITION = '35.658789, 139.701262';

    /** Beyond this distance (metres) from the station, the visitor's position is ignored. */
    private const MAX_DISTANCE_FROM_STATION = 2500;

    // Force the extending class to define this method
    abstract protected function getModelName(): string;

    /**
     * Detect if the user is in the area.
     * Fall back to Shibuya Station when the position is missing, invalid, or more than 2.5 km away.
     */
    private function positionSpoofer(?string $position): string
    {
        // The browser sends the string "null" when it has no position yet
        if (empty($position) || $position === 'null') {
            Log::debug('GPS position not set. Using Shibuya Station coordinates.');

            return self::STATION_POSITION;
        }
        // Parse "lat, lon" into two floats; throws when the string is not coordinates
        try {
            [$curLat, $curLon] = GeoUtils::toPositionArray($position);
        } catch (InvalidArgumentException $e) {
            Log::debug('Invalid GPS position. Using Shibuya Station coordinates.');

            return self::STATION_POSITION;
        }
        // We check the distance from Shibuya Station
        [$stationLat, $stationLon] = GeoUtils::toPositionArray(self::STATION_POSITION);
        $distance = GeoUtils::distance($curLat, $curLon, $stationLat, $stationLon);
        Log::debug('Distance from Shibuya Station: '.$distance);
        if ($distance > self::MAX_DISTANCE_FROM_STATION) {
            Log::debug('Position is too far. Using Shibuya Station coordinates.');

            return self::STATION_POSITION;
        }
        Log::debug('GPS position set from user: '.$position);

        return $position;
    }

    /**
     * Returns the Model class to use to read the data, e.g. App\Models\Restaurant.
     *
     * @return class-string<Model>
     */
    public function getModelClass(): string
    {
        return 'App\\Models\\'.$this->getModelName();
    }

    /**
     * Returns the view name for the place's details.
     *
     * @param  bool  $single  false (default) for the view that fits into the one page design,
     *                        true for the SINGLE details view (standalone page that can be shared)
     */
    public function getModelDetailsView(bool $single = false): string
    {
        // e.g. 'restaurants.details' for the Restaurant model
        $viewName = strtolower($this->getModelName()).'s.details';
        if ($single) {
            // e.g. 'restaurants.details-single'
            $viewName .= '-single';
        }

        return $viewName;
    }

    /**
     * Returns the view name for the rows of the results list, e.g. 'restaurants.results-data'.
     */
    public function getModelResultsDataView(): string
    {
        return strtolower($this->getModelName()).'s.results-data';
    }

    /**
     * Get search results from parameters by using the cache.
     * Generate the key, read the cache, if not found then read the DB and fill the cache.
     *
     * @param  array<int, string>|null  $tags  used in the search
     * @param  string|null  $op  operator used in the search (AND, OR, ANDNOT, ORNOT)
     * @param  string|null  $orderBy  sorting used in the search (distance, ratings, cost)
     * @param  int  $limit  maximum number of results (0 for no limit)
     */
    public function useCache(?array $tags, ?string $op, ?string $orderBy, int $limit = 0): Collection
    {
        // The key needs to be made of all the used parameters except the coordinates
        $cacheKey = $this->cacheKey($op, $tags, $orderBy, $limit);

        // rememberForever returns the cached value, or runs the closure once and caches its result
        return Cache::rememberForever($cacheKey, function () use ($tags, $op, $orderBy, $limit) {
            return $this->readFromDB($tags, $op ?? 'AND', $orderBy ?? 'distance', $limit);
        });
    }

    /**
     * Generates the key used to put the main query into the cache.
     *
     * @param  string|null  $op  used in the search
     * @param  array<int, string>|null  $tags  used in the search. Can be empty for ALL, or an array of actual tags
     * @param  string|null  $orderBy  used in the search
     * @param  int  $limit  used in the search
     * @return string the key
     */
    public function cacheKey(?string $op, ?array $tags, ?string $orderBy, int $limit = 0): string
    {
        if (empty($tags)) {
            // No tag selected: e.g. 'All-Restaurant'
            $cacheKey = 'All-'.$this->getModelName();
        } else {
            // One hash per operator + tags combination, prefixed with the model so models never collide
            $cacheKey = $this->getModelName().'-'.md5($op.','.implode(',', $tags));
        }
        if ($limit > 0) {
            // e.g. 'l100-All-Restaurant' for the 100 best restaurants
            $cacheKey = 'l'.$limit.'-'.$cacheKey;
        }
        // Only the "cost" order changes the SQL ordering (so the cached rows);
        // the other orders are applied in PHP after reading the cache
        if ($orderBy === 'cost') {
            $cacheKey = $orderBy.$cacheKey;
        }

        return $cacheKey;
    }

    /**
     * Read from the DB. No cache, just the DB.
     *
     * @param  array<int, string>|null  $tags
     */
    public function readFromDB(?array $tags, string $op = 'AND', string $orderBy = 'distance', int $limit = 0): Collection
    {
        Log::debug('Hitting the DB with op '.$op.' orderBy '.$orderBy.' limit '.$limit);
        // Start a query on the model's table, e.g. Restaurant::query()
        $class = $this->getModelClass();
        $query = $class::query();

        // Add the tag filter to the query
        $this->whereTags($query, $tags, $op);
        // Only restaurants have the score columns used for sorting
        if ($this->getModelName() === 'Restaurant') {
            $this->orderBy($query, $orderBy);
        }
        if ($limit > 0) {
            $query->limit($limit);
        }

        // Run the query and return the rows as model objects
        return $query->get();
    }

    /**
     * Add the ORDER BY clause to the query (restaurants only).
     */
    public function orderBy(Builder $query, string $orderBy = 'distance'): void
    {
        if ($orderBy === 'distance' || $orderBy === 'ratings') {
            // Sorting by distance happens outside the query (in PHP).
            // We still put the best places on top (rating).
            $query->orderByDesc('score_lunch')
                ->orderByDesc('score_food')
                ->orderByDesc('score_place')
                ->orderBy('id'); // deterministic order for ties
        }
        if ($orderBy === 'cost') {
            // Price first, but we still try to put the best places on top
            $query->orderBy('lunch_price')
                ->orderByDesc('score_lunch')
                ->orderByDesc('score_food')
                ->orderByDesc('score_place')
                ->orderBy('id');
        }
    }

    /**
     * Add the tag filter to the query.
     * AND: has all the tags; OR: has any of them; ANDNOT: has none of them; ORNOT: has a tag outside the list.
     * Unknown operators behave like AND (the legacy code applied no filter at all in that case; the UI only sends AND).
     *
     * @param  array<int, string>|null  $tags
     */
    public function whereTags(Builder $query, ?array $tags, string $op = 'AND'): void
    {
        // No tags means no filter: all the places
        if (empty($tags)) {
            return;
        }
        // Sub-query on the tags relation: "tag label is one of the given ones"
        $in = fn (Builder $q) => $q->whereIn('label', $tags);
        match ($op) {
            // whereHas: the place has at least one matching tag
            'OR' => $query->whereHas('tags', $in),
            // whereDoesntHave: the place has no matching tag at all
            'ANDNOT' => $query->whereDoesntHave('tags', $in),
            // the place has at least one tag that is NOT in the list
            'ORNOT' => $query->whereHas('tags', fn (Builder $q) => $q->whereNotIn('label', $tags)),
            // AND: the number of matching tags must equal the number of tags asked
            default => $query->whereHas('tags', $in, '=', count($tags)),
        };
    }

    /**
     * Return the places sorted by distance from the given position.
     * Places without coordinates are dropped.
     */
    public function sortByDistance(iterable $places, string $position): Collection
    {
        // Fill currentDistance on each place
        $this->generateCurrentDistances($places, $position);

        // Keep only the places with a distance, sort by it, and renumber the keys 0, 1, 2...
        return Collection::make($places)
            ->filter(fn ($place) => $place->currentDistance !== null)
            ->sortBy('currentDistance')
            ->values();
    }

    /**
     * For all the given places, compute the distance from the given position.
     * Each result is stored in the place's model (currentDistance, in metres).
     */
    public function generateCurrentDistances(iterable $places, string $position): void
    {
        [$curLat, $curLon] = GeoUtils::toPositionArray($position);
        foreach ($places as $place) {
            if ($place->lat !== null && $place->lon !== null) {
                // The DB returns decimals as strings, hence the (float) casts
                $place->currentDistance = GeoUtils::distance($curLat, $curLon, (float) $place->lat, (float) $place->lon);
            } else {
                Log::debug('No coordinates for: '.$place->name);
            }
        }
    }

    /**
     * The "position" query parameter as a string (null when missing or not a scalar, e.g. position[]=x).
     */
    protected function positionParam(Request $request): ?string
    {
        $position = $request->query('position');

        return is_scalar($position) ? (string) $position : null;
    }

    /**
     * Calculate distances from the position, and sort by them if required.
     *
     * @return Collection the places, filled with distances
     */
    public function handleDistances(Collection $places, ?string $position, ?string $orderBy): Collection
    {
        // Replace the position by Shibuya Station if needed
        $position = $this->positionSpoofer($position);
        if ($orderBy === 'distance') {
            return $this->sortByDistance($places, $position);
        }
        // We still need to fill the current distance of each place we show
        $this->generateCurrentDistances($places, $position);

        return $places;
    }

    /**
     * Make the tags into an array, if it is not already the case.
     * "ramen, noodles" becomes ['ramen', 'noodles']; empty input becomes null.
     *
     * @return array<int, string>|null
     */
    public function formatTags(mixed $tags): ?array
    {
        if (empty($tags)) {
            return null;
        }
        // Already a list (array or iterator): just make sure the keys are 0, 1, 2...
        if (is_iterable($tags)) {
            return array_values(is_array($tags) ? $tags : iterator_to_array($tags));
        }

        // Comma separated string: remove the spaces after the commas, then split
        return explode(',', str_replace(', ', ',', (string) $tags));
    }

    /**
     * Shorten a string to the given width and end it with "…" (the HTML entity counts in the width).
     */
    public function ellipsis(?string $string, int $length): string
    {
        return mb_strimwidth($string ?? '', 0, $length, '&hellip;');
    }
}
