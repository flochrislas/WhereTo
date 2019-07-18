<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Restaurant;
use App\Http\Controllers\RestaurantController;

class RestaurantControllerTest extends TestCase
{
    const GMO_POSITION = '35.656113,139.699425';

    public function testCreateFromName(): void
    {
        $this->assertInstanceOf(
            Restaurant::class,
            Restaurant::create(['name'=>'PHPUnit test restaurant'])
        );
    }

    public function testGenerateCurrentDistances() : void
    {
        $restaurants = [
                    new Restaurant(["name"=>'Hakata Furyu',
                                    "lat"=>35.6585187,
                                    "lon"=>139.6983918]),
                    new Restaurant(["name"=>'Kindenmaru',
                                    "lat"=>35.6583502,
                                    "lon"=>139.6985862])
                  ];
        (new RestaurantController)->generateCurrentDistances($restaurants, self::GMO_POSITION);
        foreach ($restaurants as $resto) {
          $this->assertNotNull($resto->currentDistance);
        }

        $this->assertEquals(
            283.32113905081275,
            $restaurants[0]->currentDistance
        );

        $this->assertEquals(
            260.05262254116957,
            $restaurants[1]->currentDistance
        );
    }

}
