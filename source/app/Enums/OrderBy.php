<?php

namespace App\Enums;

/**
 * The sort orders a visitor can ask for (the orderBy query parameter).
 * A "backed enum": each case has a string value, the one used in the URL and the cache keys.
 */
enum OrderBy: string
{
    case Distance = 'distance';
    case Ratings = 'ratings';
    case Cost = 'cost';

    /**
     * From a query parameter: the matching case, or Distance when the value is missing or unknown.
     * tryFrom() returns null instead of throwing for an unknown value.
     */
    public static function fromRequest(mixed $value): self
    {
        return (is_string($value) ? self::tryFrom($value) : null) ?? self::Distance;
    }
}
