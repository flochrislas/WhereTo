<?php

namespace Tests\Unit;

use App\Enums\OrderBy;
use App\Enums\TagOperator;
use App\Support\Position;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function test_from_string_parses_lat_and_lon(): void
    {
        $p = Position::fromString('35.658789, 139.701262');
        $this->assertSame(35.658789, $p->lat);
        $this->assertSame(139.701262, $p->lon);
        $this->assertSame('35.658789, 139.701262', (string) $p);
    }

    public function test_try_from_string_is_lenient(): void
    {
        $this->assertNull(Position::tryFromString(null));
        $this->assertNull(Position::tryFromString(''));
        $this->assertNull(Position::tryFromString('null')); // what the JavaScript sends before the first fix
        $this->assertNull(Position::tryFromString('garbage'));
        $this->assertInstanceOf(Position::class, Position::tryFromString('35.6586,139.7016'));
    }

    public function test_from_string_throws_on_garbage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Position::fromString('garbage');
    }

    public function test_distance_between_two_positions(): void
    {
        $station = Position::fromString('35.658789, 139.701262');
        $hachiko = new Position(35.659050, 139.700640);
        // About 64 m between the station and the Hachiko statue
        $this->assertEqualsWithDelta(64, $station->distanceTo($hachiko), 5);
        $this->assertSame(0.0, $station->distanceTo($station));
    }

    public function test_enums_fall_back_to_their_defaults(): void
    {
        $this->assertSame(OrderBy::Ratings, OrderBy::fromRequest('ratings'));
        $this->assertSame(OrderBy::Distance, OrderBy::fromRequest('nonsense'));
        $this->assertSame(OrderBy::Distance, OrderBy::fromRequest(null));
        $this->assertSame(OrderBy::Distance, OrderBy::fromRequest(['x']));
        $this->assertSame(TagOperator::Or, TagOperator::fromRequest('OR'));
        $this->assertSame(TagOperator::And, TagOperator::fromRequest('or')); // case matters, like before
        $this->assertSame(TagOperator::And, TagOperator::fromRequest(null));
    }
}
