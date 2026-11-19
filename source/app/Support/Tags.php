<?php

namespace App\Support;

/**
 * The label of a tag in the language of the page (doc/12-multilingual-ui.md).
 * The tags stay English in the database, the search and the URLs (data-tag="ramen"); only the text shown changes:
 * lang/ja/tags.php maps 'ramen' => 'ラーメン'.
 */
final class Tags
{
    /** The translated label, or the English one when the language has no entry for it */
    public static function label(string $label): string
    {
        // trans('tags') gives the whole lang/<locale>/tags.php array, or the string 'tags' when the file does not
        // exist (English). Not a lookup of the key "tags.ramen" itself: a missing entry would print "tags.ramen",
        // and a dot in a label would be read as a nested key.
        $labels = trans('tags');
        $translated = is_array($labels) ? ($labels[$label] ?? null) : null;

        return is_string($translated) && $translated !== '' ? $translated : $label;
    }
}
