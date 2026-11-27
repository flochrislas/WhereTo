<?php

namespace App\Support;

/**
 * The label of a tag in the language of the page (doc/12-multilingual-ui.md), and its icon (doc/13-tag-icons.md).
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

    /** The id of the tag's symbol in the sprite of the tag icons (config/tag_icons.php), or null: text only */
    public static function icon(string $label): ?string
    {
        $icon = config('tag_icons')[$label] ?? null;

        return is_string($icon) && $icon !== '' ? $icon : null;
    }

    /**
     * The address of the sprite. ?v= is its modification time: new artwork has a new URL, past the caches.
     * A path of the site and never asset(): a browser draws a <use> only from the origin of its page, so a
     * sprite on an asset host (ASSET_URL) would leave every icon blank. It is also the path sw.js pre-caches.
     */
    public static function sprite(): string
    {
        return '/icons/tags.svg?v='.filemtime(public_path('icons/tags.svg'));
    }
}
