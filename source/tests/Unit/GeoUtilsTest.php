<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

use App\GeoUtils;

/**
* Tools on the Internet allow for some verifications:
* http://boulter.com/gps/distance/?from=32.9697%2C-96.80322&to=29.46786%2C-98.53506&units=k
*
* PHPUnit has a nice manual
* https://phpunit.de/manual/current/en/appendixes.assertions.html
*/
class GeoUtilsTest extends TestCase
{
    const GMO_POSITION = '35.656113,139.699425';

    public function testMiles() : void
    {
        $this->assertEquals(
          262.67779380543,
          GeoUtils::geodatasourceDistance(32.9697, -96.80322, 29.46786, -98.53506, "M")
        );
    }

    public function testN() : void
    {
        $this->assertEquals(
          228.10939614064,
          GeoUtils::geodatasourceDistance(32.9697, -96.80322, 29.46786, -98.53506, "N")
        );
    }

    public function testKm() : void
    {
        $this->assertEquals(
          422.73893139401,
          GeoUtils::geodatasourceDistance(32.9697, -96.80322, 29.46786, -98.53506, "K")
        );
    }

    public function testFlatDistance2() : void
    {
        $this->assertEquals(
          392.43277533293377,
          GeoUtils::flatDistance(32.9697, -96.80322, 29.46786, -98.53506)
        );
    }

    public function testEquirectangularApprox() : void
    {
        $this->assertEquals(
          422.78091918393909,
          GeoUtils::equirectangularApprox(32.9697, -96.80322, 29.46786, -98.53506)
        );
    }

    public function testDistanceSmall() : void
    {
        $this->assertEquals(
          0.27593804662795907,
          GeoUtils::geodatasourceDistance(35.658285, 139.7004107, 35.6575683, 139.6974865, "K")
        );
    }

    public function testFlatDistance2Small() : void
    {
        $this->assertEquals(
          0.1662060955181428,
          GeoUtils::flatDistance(35.658285, 139.7004107, 35.6575683, 139.6974865)
        );
    }

    public function testEquirectangularApproxSmall() : void
    {
        $this->assertEquals(
          0.275951292425265,
          GeoUtils::equirectangularApprox(35.658285, 139.7004107, 35.6575683, 139.6974865)
        );
    }

    public function testGoogle2coord() : void
    {
        $expected = ['35.658285', '139.7004107', '18'];
        $this->assertEquals(
          $expected,
          GeoUtils::google2coord("https://www.google.co.jp/maps/place/%E3%82%BF%E3%83%AD%E3%82%B9/@35.658285,139.7004107,18z/data=!4m10!1m4!2m3!1srestaurant!5m1!4e3!3m4!1s0x0:0x895372195d90c946!8m2!3d35.658047!4d139.6995289?hl=en")
        );
    }

    public function testWalkingTimeDefault1Km() : void
    {
        $this->assertEquals(
          20,
          GeoUtils::walkingTime(1)
        );
    }

    public function testWithinDistanceSql() : void
    {
        $this->assertEquals(
          'pow(lat-35.658285, 2) + pow((lon-139.7004107)*cos(radians(35.658285)), 2) < pow(2/110.25, 2)',
          GeoUtils::withinDistanceSql(35.658285, 139.7004107, 2)
        );
    }

}
