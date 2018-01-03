<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\RestaurantTag;
use App\Http\Controllers\RestaurantTagController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class RestaurantTagControllerTest extends TestCase
{
    public function testCreateFromLabel(): void
    {
        $this->assertInstanceOf(
            RestaurantTag::class,
            RestaurantTag::create(['label'=>'unit test create'])
        );
    }

    public function testAutocompleteDB(): void
    {
        // 2 different types of collections in Laravel
        // https://medium.com/@lynn80827/two-types-of-collections-in-laravel-888d43858c4e
        $expectedArray = [
                      ["id"=>9,"value"=>"japanese"],
                      ["id"=>21,"value"=>"japanese curry"]
                    ];
        $expected = new EloquentCollection([
                      new RestaurantTag(["id"=>9,"value"=>"japanese"]),
                      new RestaurantTag(["id"=>21,"value"=>"japanese curry"])
                    ]);
        $result = (new RestaurantTagController)->autocompleteDB('jap');

        $this->assertInstanceOf(BaseCollection::class, $result);
        $this->assertInstanceOf(EloquentCollection::class, $result);

        $this->assertEquals(
            $expectedArray,
            $result->toArray()
        );

    }

}
