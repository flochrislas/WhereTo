<?php

namespace Tests\Unit;

use App\Enums\OrderBy;
use App\Enums\TagOperator;
use App\Models\Restaurant;
use App\Services\BarSearch;
use App\Services\PlaceSearch;
use App\Services\RestaurantSearch;
use App\Support\Position;
use Tests\TestCase;

/** The search service, without HTTP: distances, position fallback and cache keys */
class PlaceSearchTest extends TestCase
{
    private const GMO_POSITION = '35.656113,139.699425';

    public function test_generate_current_distances(): void
    {
        $restaurants = [
            new Restaurant(['name' => 'Hakata Furyu', 'lat' => 35.6585187, 'lon' => 139.6983918]),
            new Restaurant(['name' => 'Kindenmaru', 'lat' => 35.6583502, 'lon' => 139.6985862]),
        ];
        (new RestaurantSearch)->generateCurrentDistances($restaurants, Position::fromString(self::GMO_POSITION));

        $this->assertEqualsWithDelta(283.32113905081275, $restaurants[0]->currentDistance, 1e-6);
        $this->assertEqualsWithDelta(260.05262254116957, $restaurants[1]->currentDistance, 1e-6);
    }

    public function test_sort_by_distance_drops_places_without_coordinates(): void
    {
        $places = collect([
            new Restaurant(['name' => 'far', 'lat' => 35.66, 'lon' => 139.71]),
            new Restaurant(['name' => 'nowhere']),
            new Restaurant(['name' => 'near', 'lat' => 35.6562, 'lon' => 139.6995]),
        ]);
        $sorted = (new RestaurantSearch)->sortByDistance($places, Position::fromString(self::GMO_POSITION));

        $this->assertSame(['near', 'far'], $sorted->pluck('name')->all());
    }

    public function test_position_falls_back_to_the_station(): void
    {
        $search = new RestaurantSearch;
        $station = PlaceSearch::station();
        // Nothing, "null" (no fix yet), garbage, and Tokyo Station (4 km away): all become Shibuya Station
        foreach ([null, 'null', 'abc', '35.681236, 139.767125'] as $position) {
            $this->assertEquals($station, $search->resolvePosition($position), var_export($position, true));
        }
        // Close by: kept
        $this->assertEquals(Position::fromString(self::GMO_POSITION), $search->resolvePosition(self::GMO_POSITION));
    }

    public function test_cache_keys_depend_on_the_kind_and_the_parameters(): void
    {
        $restaurants = new RestaurantSearch;
        $bars = new BarSearch;
        $this->assertSame('All-Restaurant', $restaurants->cacheKey(TagOperator::And, null, OrderBy::Distance));
        $this->assertSame('l100-All-Restaurant', $restaurants->cacheKey(TagOperator::And, [], OrderBy::Ratings, 100));
        // "ratings" and "distance" share the cached rows; "cost" changes the SQL so it has its own key...
        $this->assertSame('costAll-Restaurant', $restaurants->cacheKey(TagOperator::And, null, OrderBy::Cost));
        // ...but only for the kinds that have a price
        $this->assertSame('All-Bar', $bars->cacheKey(TagOperator::And, null, OrderBy::Cost));
        // Tags and operator are hashed, per kind
        $and = $restaurants->cacheKey(TagOperator::And, ['ramen', 'noodles'], OrderBy::Distance);
        $or = $restaurants->cacheKey(TagOperator::Or, ['ramen', 'noodles'], OrderBy::Distance);
        $this->assertStringStartsWith('Restaurant-', $and);
        $this->assertNotSame($and, $or);
        $this->assertNotSame($and, $bars->cacheKey(TagOperator::And, ['ramen', 'noodles'], OrderBy::Distance));
    }
}
