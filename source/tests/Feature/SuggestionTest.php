<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuggestionTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_empty_outside_lunch_time(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 16, 16, 0, 0, 'Asia/Tokyo'));
        $this->assertSame('', $this->get('/suggestions?position=null')->assertOk()->getContent());
    }

    public function test_suggests_a_restaurant_at_lunch_time(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 16, 12, 0, 0, 'Asia/Tokyo'));
        $r = Restaurant::create(['name' => 'Only one', 'type' => 'Ramen', 'lat' => 35.6586, 'lon' => 139.7016, 'score_lunch' => 3]);
        $this->get('/suggestions?position=null')->assertOk()->assertSee('Only one')->assertSee(route('restaurants.details.single', $r->id));
        $this->get('/')->assertOk()->assertSee('Get a suggestion')
            ->assertSee('data-suggestion-url="'.route('suggestions').'"', false);
    }
}
