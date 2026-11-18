<?php

namespace App\Support;

/**
 * Absolute URLs for the search engines (sitemap, canonical, Open Graph, JSON-LD), always built on APP_URL,
 * the public address of the deployment, never on the hostname of the incoming request: a request through
 * another name (an IP, a staging alias) must still name the canonical page, and the sitemap is cached forever.
 */
final class PublicUrl
{
    /** APP_URL plus a path, e.g. to('/restaurant/details/1') or to(route('home', absolute: false)) */
    public static function to(string $path): string
    {
        $root = config('app.url');
        $root = is_string($root) && $root !== '' ? rtrim($root, '/') : 'http://localhost';

        return $root.'/'.ltrim($path, '/');
    }
}
