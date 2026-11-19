<?php

namespace Tests\Unit;

use App\Models\RestaurantTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_from_label(): void
    {
        $this->assertInstanceOf(RestaurantTag::class, RestaurantTag::create(['label' => 'unit test create']));
    }
}
