<?php

namespace App\Enums;

/**
 * Is the place still in business (the business_status column, doc/11-data-freshness.md).
 * The values are the ones of Google's Places API, plus REPLACED: another business is at the address now.
 * Written into the seeders by tools/freshness; a place without the key is OPERATIONAL (the column default).
 */
enum BusinessStatus: string
{
    case Operational = 'OPERATIONAL';
    case ClosedTemporarily = 'CLOSED_TEMPORARILY';
    case ClosedPermanently = 'CLOSED_PERMANENTLY';
    case Replaced = 'REPLACED';

    /**
     * From any value (a column, a test): the matching case, or Operational when the value is missing or unknown.
     * Not a query parameter, but the same rule as the other enums: an unknown value never breaks a page.
     */
    public static function fromRequest(mixed $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return (is_string($value) ? self::tryFrom($value) : null) ?? self::Operational;
    }

    /** Only an operational place is listed (in the search results and the sitemap) */
    public function isOpen(): bool
    {
        return $this === self::Operational;
    }

    /** What the page says about the place, lower case ('' for an operational place: nothing to say) */
    public function label(): string
    {
        // match returns the value of the first arm whose case is identical (===) to $this
        return match ($this) {
            self::Operational => '',
            self::ClosedTemporarily => 'temporarily closed',
            self::ClosedPermanently => 'permanently closed',
            self::Replaced => 'replaced by another business',
        };
    }

    /**
     * Should the search engines drop the page: yes when the place is gone for good (nothing left to rank);
     * a temporarily closed place keeps its indexing.
     */
    public function isGone(): bool
    {
        return $this === self::ClosedPermanently || $this === self::Replaced;
    }
}
