<?php

namespace Tests\Unit;

use App\Http\Controllers\RestaurantController;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_from_name(): void
    {
        $this->assertInstanceOf(Restaurant::class, Restaurant::create(['name' => 'PHPUnit test restaurant']));
        $this->assertDatabaseHas('restaurants', ['name' => 'PHPUnit test restaurant']);
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
