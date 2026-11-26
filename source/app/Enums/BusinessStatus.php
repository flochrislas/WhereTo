<?php

namespace App\Enums;

/**
 * Is the place still in business (the business_status column, doc/11-data-freshness.md).
 * The values are the ones of Google's Places API, plus REPLACED (another business is at the address now)
 * and RELOCATED (the place moved to another address: ours is out of date).
 * Written into the seeders by tools/freshness; a place without the key was never checked and is OPERATIONAL (the column default).
 */
enum BusinessStatus: string
{
    case Operational = 'OPERATIONAL';
    case ClosedTemporarily = 'CLOSED_TEMPORARILY';
    case ClosedPermanently = 'CLOSED_PERMANENTLY';
    case Replaced = 'REPLACED';
    case Relocated = 'RELOCATED';

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

    /**
     * What the page says about the place, lower case ('' for an operational place: nothing to say), in the language
     * of the page (__() reads lang/<locale>.json)
     */
    public function label(): string
    {
        // match returns the value of the first arm whose case is identical (===) to $this
        return match ($this) {
            self::Operational => '',
            self::ClosedTemporarily => __('temporarily closed'),
            self::ClosedPermanently => __('permanently closed'),
            self::Replaced => __('replaced by another business'),
            self::Relocated => __('moved to another address'),
        };
    }

    /**
     * Should the search engines drop the page: yes when the place is gone for good from the address (nothing left to rank);
     * a temporarily closed place keeps its indexing.
     */
    public function isGone(): bool
    {
        return $this === self::ClosedPermanently || $this === self::Replaced || $this === self::Relocated;
    }
}
