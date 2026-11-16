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
 * Shared logic for the controllers of place models (Restaurant, Store, Bar, Karaoke).
 * Subclasses only provide the model name; views and class names derive from it.
 */
abstract class PlaceController extends Controller
{
    /** Shibuya Station, used when the visitor sends no position or is too far away. */
    private const STATION_POSITION = '35.658789, 139.701262';

    /** Beyond this distance (metres) from the station, the visitor's position is ignored. */
    private const MAX_DISTANCE_FROM_STATION = 2500;

    abstract protected function getModelName(): string;

    /**
     * Fall back to Shibuya Station when the position is missing, invalid, or too far away.
     */
    private function positionSpoofer(?string $position): string
    {
        if (empty($position) || $position === 'null') {
            Log::debug('GPS position not set. Using Shibuya Station coordinates.');

            return self::STATION_POSITION;
        }
        try {
            [$curLat, $curLon] = GeoUtils::toPositionArray($position);
        } catch (InvalidArgumentException $e) {
            Log::debug('Invalid GPS position. Using Shibuya Station coordinates.');

            return self::STATION_POSITION;
        }
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
     * Fully qualified model class, e.g. App\Models\Restaurant.
     *
     * @return class-string<Model>
     */
    public function getModelClass(): string
    {
        return 'App\\Models\\'.$this->getModelName();
    }

    /**
     * View name for the place details: <places>.details, or <places>.details-single for the standalone page.
     */
    public function getModelDetailsView(bool $single = false): string
    {
        $viewName = strtolower($this->getModelName()).'s.details';
        if ($single) {
            $viewName .= '-single';
        }

        return $viewName;
    }

    public function getModelResultsDataView(): string
    {
        return strtolower($this->getModelName()).'s.results-data';
    }

    /**
     * Search results, from the cache or the DB (the cache key ignores the visitor's position).
     *
     * @param  array<int, string>|null  $tags
     */
    public function useCache(?array $tags, ?string $op, ?string $orderBy, int $limit = 0): Collection
    {
        $cacheKey = $this->cacheKey($op, $tags, $orderBy, $limit);

        return Cache::rememberForever($cacheKey, function () use ($tags, $op, $orderBy, $limit) {
            return $this->readFromDB($tags, $op ?? 'AND', $orderBy ?? 'distance', $limit);
        });
    }

    /**
     * Cache key for a search: all parameters except the coordinates.
     *
     * @param  array<int, string>|null  $tags
     */
    public function cacheKey(?string $op, ?array $tags, ?string $orderBy, int $limit = 0): string
    {
        if (empty($tags)) {
            $cacheKey = 'All-'.$this->getModelName();
        } else {
            $cacheKey = $this->getModelName().'-'.md5($op.','.implode(',', $tags));
        }
        if ($limit > 0) {
            $cacheKey = 'l'.$limit.'-'.$cacheKey;
        }
        // Only the "cost" order changes the SQL ordering; the other orders are applied in PHP.
        if ($orderBy === 'cost') {
            $cacheKey = $orderBy.$cacheKey;
        }

        return $cacheKey;
    }

    /**
     * Read from the DB, no cache.
     *
     * @param  array<int, string>|null  $tags
     */
    public function readFromDB(?array $tags, string $op = 'AND', string $orderBy = 'distance', int $limit = 0): Collection
    {
        Log::debug('Hitting the DB with op '.$op.' orderBy '.$orderBy.' limit '.$limit);
        $class = $this->getModelClass();
        $query = $class::query();

        $this->whereTags($query, $tags, $op);
        if ($this->getModelName() === 'Restaurant') {
            $this->orderBy($query, $orderBy);
        }
        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function orderBy(Builder $query, string $orderBy = 'distance'): void
    {
        if ($orderBy === 'distance' || $orderBy === 'ratings') {
            // Distance sorting happens in PHP; best places first as a base order.
            $query->orderByDesc('score_lunch')
                ->orderByDesc('score_food')
                ->orderByDesc('score_place')
                ->orderBy('id'); // deterministic order for ties
        }
        if ($orderBy === 'cost') {
            $query->orderBy('lunch_price')
                ->orderByDesc('score_lunch')
                ->orderByDesc('score_food')
                ->orderByDesc('score_place')
                ->orderBy('id');
        }
    }

    /**
     * Tag filter. AND: has all tags; OR: has any; ANDNOT: has none of them; ORNOT: has a tag outside the list.
     *
     * @param  array<int, string>|null  $tags
     */
    /**
     * Restrict the query to places matching the tags. Unknown operators behave like AND
     * (the legacy code applied no filter at all in that case; the UI only ever sends AND).
     */
    public function whereTags(Builder $query, ?array $tags, string $op = 'AND'): void
    {
        if (empty($tags)) {
            return;
        }
        $in = fn (Builder $q) => $q->whereIn('label', $tags);
        match ($op) {
            'OR' => $query->whereHas('tags', $in),
            'ANDNOT' => $query->whereDoesntHave('tags', $in),
            'ORNOT' => $query->whereHas('tags', fn (Builder $q) => $q->whereNotIn('label', $tags)),
            default => $query->whereHas('tags', $in, '=', count($tags)),
        };
    }

    /**
     * Places sorted by distance from the position. Places without coordinates are dropped.
     */
    public function sortByDistance(iterable $places, string $position): Collection
    {
        $this->generateCurrentDistances($places, $position);

        return Collection::make($places)
            ->filter(fn ($place) => $place->currentDistance !== null)
            ->sortBy('currentDistance')
            ->values();
    }

    /**
     * Fill currentDistance (metres) on each place from the given position.
     */
    public function generateCurrentDistances(iterable $places, string $position): void
    {
        [$curLat, $curLon] = GeoUtils::toPositionArray($position);
        foreach ($places as $place) {
            if ($place->lat !== null && $place->lon !== null) {
                $place->currentDistance = GeoUtils::distance($curLat, $curLon, (float) $place->lat, (float) $place->lon);
            } else {
                Log::debug('No coordinates for: '.$place->name);
            }
        }
    }

    /**
     * Compute distances from the visitor's position and sort by them when requested.
     */
    /**
     * The "position" query parameter as a string (null when missing or not a scalar).
     */
    protected function positionParam(Request $request): ?string
    {
        $position = $request->query('position');

        return is_scalar($position) ? (string) $position : null;
    }

    public function handleDistances(Collection $places, ?string $position, ?string $orderBy): Collection
    {
        $position = $this->positionSpoofer($position);
        if ($orderBy === 'distance') {
            return $this->sortByDistance($places, $position);
        }
        $this->generateCurrentDistances($places, $position);

        return $places;
    }

    /**
     * Comma separated tags string to array (null when empty).
     *
     * @return array<int, string>|null
     */
    public function formatTags(mixed $tags): ?array
    {
        if (empty($tags)) {
            return null;
        }
        if (is_iterable($tags)) {
            return array_values(is_array($tags) ? $tags : iterator_to_array($tags));
        }

        return explode(',', str_replace(', ', ',', (string) $tags));
    }

    public function ellipsis(?string $string, int $length): string
    {
        return mb_strimwidth($string ?? '', 0, $length, '&hellip;');
    }
}
