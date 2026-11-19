<?php

/*
 * Global helper functions for the views, loaded by Composer on every request ("files" in composer.json).
 * Keep them one-liners over a class of app/Support, where the logic and its tests live.
 */

use App\Support\Tags;

// function_exists: do not declare it twice if the file is ever loaded again
if (! function_exists('tag_label')) {
    /** The label of a tag in the language of the page, e.g. tag_label('ramen') gives "ラーメン" on a Japanese page */
    function tag_label(string $label): string
    {
        return Tags::label($label);
    }
}
