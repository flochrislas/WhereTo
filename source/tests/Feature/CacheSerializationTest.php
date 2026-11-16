<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\RestaurantTag;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * The forever-cache stores Eloquent objects. The test suite uses the array store, which does
 * not serialize, so this test goes through the file store to prove every cached class is in
 * cache.serializable_classes (otherwise they come back as __PHP_Incomplete_Class).
 */
class CacheSerializationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'file']);
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    public function test_cached_tags_with_pivot_survive_a_serializing_store(): void
    {
        $restaurant = Restaurant::create(['name' => 'Cached', 'type' => 'Ramen']);
        RestaurantTag::create(['label' => 'ramen', 'type' => 5, 'weight' => 1]);
        $restaurant->attachTags(['ramen']);

        $restaurant->tagsCached(); // fills the cache
        $tag = Restaurant::find($restaurant->id)->tagsCached()->first(); // reads it back

        $this->assertInstanceOf(RestaurantTag::class, $tag);
        $this->assertSame('ramen', $tag->label);
        $this->assertInstanceOf(Pivot::class, $tag->getRelation('pivot'));
    }

    public function test_cached_results_survive_a_serializing_store(): void
    {
        Restaurant::create(['name' => 'Cached', 'type' => 'Ramen', 'lat' => 35.6586, 'lon' => 139.7016, 'score_lunch' => 3]);

        $this->get('/restaurants/results?orderBy=distance&position=null')->assertOk()->assertSee('Cached');
        $this->get('/restaurants/results?orderBy=distance&position=null')->assertOk()->assertSee('Cached');
        $this->assertInstanceOf(Restaurant::class, Cache::get('All-Restaurant')->first());
    }
}
