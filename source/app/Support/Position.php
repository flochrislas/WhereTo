<?php

namespace App\Support;

use App\Tools\GeoUtils;
use InvalidArgumentException;
use Stringable;

/**
 * A pair of coordinates. A small "value object": once built it cannot change (readonly),
 * and it carries the operations that make sense on a position, instead of passing "lat, lon" strings around.
 */
final readonly class Position implements Stringable
{
    public function __construct(
        public float $lat,
        public float $lon,
    ) {}

    /**
     * From a "lat, lon" string (what the browser sends). Throws InvalidArgumentException when it is not one.
     */
    public static function fromString(string $position): self
    {
        [$lat, $lon] = GeoUtils::toPositionArray($position);

        return new self($lat, $lon);
    }

    /**
     * Same, but lenient: null when the string is empty, "null" (what the JavaScript sends before the first fix),
     * or not coordinates at all.
     */
    public static function tryFromString(?string $position): ?self
    {
        if ($position === null || $position === '' || $position === 'null') {
            return null;
        }
        try {
            return self::fromString($position);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /** Distance to another position, in metres (Haversine formula in GeoUtils) */
    public function distanceTo(Position $other): float
    {
        return GeoUtils::distance($this->lat, $this->lon, $other->lat, $other->lon);
    }

    /** Back to the "lat, lon" form, e.g. for the logs */
    public function __toString(): string
    {
        return $this->lat.', '.$this->lon;
    }
}
