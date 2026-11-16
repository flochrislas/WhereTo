<?php

namespace Tests\Unit;

use App\Tools\GeoUtils;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Reference values checked with http://boulter.com/gps/distance/?from=32.9697%2C-96.80322&to=29.46786%2C-98.53506&units=k
 */
class GeoUtilsTest extends TestCase
{
    public function test_distance_returns_metres(): void
    {
        $this->assertEqualsWithDelta(422780.919, GeoUtils::distance(32.9697, -96.80322, 29.46786, -98.53506), 0.001);
    }

    public function test_distance_small_returns_metres(): void
    {
        $this->assertEqualsWithDelta(275.951, GeoUtils::distance(35.658285, 139.7004107, 35.6575683, 139.6974865), 0.001);
    }

    public function test_geodatasource_distance_units(): void
    {
        $this->assertEqualsWithDelta(262.6778, GeoUtils::geodatasourceDistance(32.9697, -96.80322, 29.46786, -98.53506, 'M'), 0.001);
        $this->assertEqualsWithDelta(228.1094, GeoUtils::geodatasourceDistance(32.9697, -96.80322, 29.46786, -98.53506, 'N'), 0.001);
        $this->assertEqualsWithDelta(422.7389, GeoUtils::geodatasourceDistance(32.9697, -96.80322, 29.46786, -98.53506, 'K'), 0.001);
    }

    public function test_flat_distance(): void
    {
        $this->assertEqualsWithDelta(392.43277533293377, GeoUtils::flatDistance(32.9697, -96.80322, 29.46786, -98.53506), 1e-9);
        $this->assertEqualsWithDelta(0.1662060955181428, GeoUtils::flatDistance(35.658285, 139.7004107, 35.6575683, 139.6974865), 1e-9);
    }

    public function test_equirectangular_approx(): void
    {
        $this->assertEqualsWithDelta(422.78091918393909, GeoUtils::equirectangularApprox(32.9697, -96.80322, 29.46786, -98.53506), 1e-9);
        $this->assertEqualsWithDelta(0.275951292425265, GeoUtils::equirectangularApprox(35.658285, 139.7004107, 35.6575683, 139.6974865), 1e-9);
    }

    public function test_google2coord_extracts_map_centre(): void
    {
        $url = 'https://www.google.co.jp/maps/place/%E3%82%BF%E3%83%AD%E3%82%B9/@35.658285,139.7004107,18z/data=!4m10!1m4!2m3!1srestaurant!5m1!4e3!3m4!1s0x0:0x895372195d90c946!8m2!3d35.658047!4d139.6995289?hl=en';
        $this->assertSame([35.658285, 139.7004107], GeoUtils::google2coord($url));
    }

    public function test_google2coord_rejects_urls_without_coordinates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GeoUtils::google2coord('https://maps.app.goo.gl/abc');
    }

    public function test_to_position_array(): void
    {
        $this->assertSame([35.656113, 139.699425], GeoUtils::toPositionArray('35.656113, 139.699425'));
        $this->assertSame([35.6610036, 139.6899592], GeoUtils::toPositionArray('35.6610036,139.6899592,15'), 'trailing zoom level is ignored');
        $this->expectException(InvalidArgumentException::class);
        GeoUtils::toPositionArray('null');
    }

    public function test_walking_time_default_pace(): void
    {
        $this->assertSame(20, GeoUtils::walkingTime(1));
    }

    public function test_within_distance_sql(): void
    {
        $this->assertSame(
            'pow(lat-35.658285, 2) + pow((lon-139.7004107)*cos(radians(35.658285)), 2) < pow(2/110.25, 2)',
            GeoUtils::withinDistanceSql(35.658285, 139.7004107, 2)
        );
    }

    public function test_format_distance(): void
    {
        $this->assertSame('1,234.57', GeoUtils::formatDistance(1234.567));
        $this->assertSame('N/A', GeoUtils::formatDistance(null));
    }
}
