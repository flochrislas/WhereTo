<?php

namespace Tests\Unit;

use App\Http\Controllers\RestaurantController;
use App\Models\Restaurant;
use App\Support\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantControllerTest extends TestCase
{
    use RefreshDatabase;

    private const GMO_POSITION = '35.656113,139.699425';

    public function test_create_from_name(): void
    {
        $this->assertInstanceOf(Restaurant::class, Restaurant::create(['name' => 'PHPUnit test restaurant']));
        $this->assertDatabaseHas('restaurants', ['name' => 'PHPUnit test restaurant']);
    }

    public function test_generate_current_distances(): void
    {
        $restaurants = [
            new Restaurant(['name' => 'Hakata Furyu', 'lat' => 35.6585187, 'lon' => 139.6983918]),
            new Restaurant(['name' => 'Kindenmaru', 'lat' => 35.6583502, 'lon' => 139.6985862]),
        ];
        (new RestaurantController)->generateCurrentDistances($restaurants, Position::fromString(self::GMO_POSITION));

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
        $sorted = (new RestaurantController)->sortByDistance($places, Position::fromString(self::GMO_POSITION));

        $this->assertSame(['near', 'far'], $sorted->pluck('name')->all());
    }

    public function test_format_tags(): void
    {
        $controller = new RestaurantController;
        $this->assertNull($controller->formatTags(null));
        $this->assertNull($controller->formatTags(''));
        $this->assertSame(['ramen', 'japanese'], $controller->formatTags('ramen, japanese'));
        $this->assertSame(['a'], $controller->formatTags(['a']));
    }

    public function test_ellipsis_accepts_null(): void
    {
        $controller = new RestaurantController;
        $this->assertSame('', $controller->ellipsis(null, 10));
        $this->assertSame('abcdefghijkl&hellip;', $controller->ellipsis('abcdefghijklmnopqrstuvwxyz', 20)); // the 8-char marker counts in the width
    }
}
