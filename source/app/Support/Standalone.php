<?php

namespace App\Support;

/**
 * The stand-alone mode of the views: the pages written into the Android app by "php artisan app:export-static"
 * (doc/16-standalone-apk-plan.md). The export command turns it on by setting config('whereto.standalone') to the
 * export date; the website never does.
 *
 * In that mode the same files must work at any origin (the app's https://appassets.androidplatform.net/, the desktop
 * preview's http://localhost:<port>/), so every URL a page loads or follows is root-relative: /css/style.css,
 * /ja/restaurants, /restaurants/details/12, with no scheme and no host.
 */
final class Standalone
{
    /** The date of the export (ISO 8601, e.g. "2026-11-27T10:15:00+09:00"), or null on the website */
    public static function exportDate(): ?string
    {
        $date = config('whereto.standalone');

        return is_string($date) && $date !== '' ? $date : null;
    }

    /** Are the views rendered for the stand-alone app */
    public static function active(): bool
    {
        return self::exportDate() !== null;
    }

    /**
     * The URL of a public file (css/style.css): root-relative in the stand-alone mode, asset() on the website
     * (absolute, on ASSET_URL when one is set)
     */
    public static function asset(string $path): string
    {
        return self::active() ? '/'.ltrim($path, '/') : asset($path);
    }

    /**
     * Is a link button worth printing: always on the website (unchanged), and in the stand-alone app only when
     * the place has that link. In the app a button with no address would do nothing at all when tapped.
     */
    public static function offers(?string $url): bool
    {
        return ! self::active() || filled($url);
    }
}
