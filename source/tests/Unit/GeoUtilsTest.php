<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\GeoUtils;

class GeoUtilsTest extends TestCase
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

    public function testMiles() : void
    {
        $this->assertEquals(
          '262.67779380543',
          GeoUtils::distance(32.9697, -96.80322, 29.46786, -98.53506, "M")
        );
    }

    public function testKm() : void
    {
        $this->assertEquals(
          '422.73893139401',
          GeoUtils::distance(32.9697, -96.80322, 29.46786, -98.53506, "K")
        );
    }

    public function testN() : void
    {
        $this->assertEquals(
          '228.10939614064',
          GeoUtils::distance(32.9697, -96.80322, 29.46786, -98.53506, "N")
        );
    }

    public function testDistance() : void
    {
        // google
        // @35.658285,139.7004107,18z
        // google
        // @35.6575683,139.6974865,18z
        $this->assertEquals(
          '0.27593804662795907',
          GeoUtils::distance(35.658285, 139.7004107, 35.6575683, 139.6974865, "K")
        );
    }

    public function testgoogle2coord() : void
    {
        echo GeoUtils::google2coord("https://www.google.co.jp/maps/place/%E3%82%BF%E3%83%AD%E3%82%B9/@35.658285,139.7004107,18z/data=!4m10!1m4!2m3!1srestaurant!5m1!4e3!3m4!1s0x0:0x895372195d90c946!8m2!3d35.658047!4d139.6995289?hl=en");
        $this->assertTrue(true);
    }


}
