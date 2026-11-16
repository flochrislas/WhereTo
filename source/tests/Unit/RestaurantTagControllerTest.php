<?php

namespace Tests\Unit;

use App\Http\Controllers\RestaurantTagController;
use App\Models\RestaurantTag;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantTagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_from_label(): void
    {
        $this->assertInstanceOf(RestaurantTag::class, RestaurantTag::create(['label' => 'unit test create']));
    }

    public function test_autocomplete_db_matches_labels(): void
    {
        RestaurantTag::create(['label' => 'japanese']);
        RestaurantTag::create(['label' => 'ramen']);
        RestaurantTag::create(['label' => 'japanese curry']);

        $result = (new RestaurantTagController)->autocompleteDB('jap');

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertSame(['japanese', 'japanese curry'], $result->pluck('value')->all());
    }
}
