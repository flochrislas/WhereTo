<?php

namespace App\Enums;

/**
 * How the selected tags combine (the op query parameter).
 * AND: the place has all the tags; OR: any of them; ANDNOT: none of them; ORNOT: at least one tag outside the list.
 * The UI exposes AND and OR ("all" / "any").
 */
enum TagOperator: string
{
    case And = 'AND';
    case Or = 'OR';
    case AndNot = 'ANDNOT';
    case OrNot = 'ORNOT';

    /** From a query parameter: the matching case, or And when the value is missing or unknown */
    public static function fromRequest(mixed $value): self
    {
        return (is_string($value) ? self::tryFrom($value) : null) ?? self::And;
    }
}
