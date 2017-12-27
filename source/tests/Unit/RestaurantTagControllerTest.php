<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\RestaurantTag;
use App\Http\Controllers\RestaurantTagController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RestaurantTagControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTrue() : void
    {
        $this->assertTrue(true);
    }

    public function testCreateFromLabel(): void
    {
        $this->assertInstanceOf(
            RestaurantTag::class,
            RestaurantTag::create(['label'=>'unit test create'])
        );
    }

}
