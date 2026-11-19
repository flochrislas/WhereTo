<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Locale;

/**
 * URLs in the language of the page (doc/12-multilingual-ui.md).
 * The default language (the first of config('app.locales'), English) lives at the plain URLs and route names
 * (/restaurants, 'restaurants.main'); every other one under its prefix (/ja/restaurants, 'ja.restaurants.main').
 * The views never call route() directly: route() would always give the English page.
 */
final class LocaleUrl
{
    /**
     * Every language of the site, the default one first, e.g. ['en', 'ja']
     *
     * @return list<string>
     */
    public static function locales(): array
    {
        $locales = config('app.locales');
        if (! is_array($locales) || $locales === []) {
            return ['en'];
        }

        // array_values: keys 0, 1, 2... whatever the config file wrote
        return array_values(array_map(fn ($locale) => (string) $locale, $locales));
    }

    /** The language of the plain URLs, e.g. 'en' */
    public static function default(): string
    {
        return self::locales()[0];
    }

    /**
     * The languages served under a prefix: every one but the default, e.g. ['ja']
     *
     * @return list<string>
     */
    public static function prefixed(): array
    {
        return array_slice(self::locales(), 1);
    }

    /** The language of this page (set by the SetLocale middleware from the route group) */
    public static function current(): string
    {
        return app()->getLocale();
    }

    /** The name of a route in a language: 'restaurants.main' stays as is in English, is 'ja.restaurants.main' in Japanese */
    public static function routeName(string $name, ?string $locale = null): string
    {
        // ??= : use the current language when none is given
        $locale ??= self::current();

        return $locale === self::default() ? $name : $locale.'.'.$name;
    }

    /**
     * The URL of a route in the language of the page (or the given one): route('restaurants.main') for the visitor's
     * language, e.g. LocaleUrl::route('restaurants.details', $place->id) gives /ja/restaurants/details/12 on a Japanese page.
     *
     * @param  mixed  $parameters  the route parameters, as for route()
     */
    public static function route(string $name, mixed $parameters = [], bool $absolute = true, ?string $locale = null): string
    {
        return route(self::routeName($name, $locale), $parameters, $absolute);
    }

    /**
     * The same page in another language (the language switch, the hreflang links), without the query string.
     * Outside a named route of the site (an error page), the home page of that language.
     */
    public static function twin(string $locale, bool $absolute = true): string
    {
        // The route that answered this request ('ja.restaurants.details.single'), null when there is none
        $route = Route::current();
        $name = $route?->getName();
        if ($route === null || $name === null || ! Route::has(self::routeName(self::baseName($name), $locale))) {
            return self::route('home', [], $absolute, $locale);
        }

        // Same route, same parameters (the place of a details page: route() turns the model back into its id).
        // Only the ones of the URI ({place}): a Route::view() also carries its view name and status as parameters,
        // which route() would add as a query string
        $parameters = array_intersect_key($route->parameters(), array_flip($route->parameterNames()));

        return self::route(self::baseName($name), $parameters, $absolute, $locale);
    }

    /** A route name without its language prefix: 'ja.restaurants.main' gives 'restaurants.main' */
    public static function baseName(string $name): string
    {
        foreach (self::prefixed() as $locale) {
            if (str_starts_with($name, $locale.'.')) {
                return substr($name, strlen($locale) + 1);
            }
        }

        return $name;
    }

    /**
     * The public URL (APP_URL, see PublicUrl) of this page in each of the given languages, e.g. for the hreflang links
     *
     * @param  list<string>  $locales
     * @return array<string, string> language => URL
     */
    public static function alternates(array $locales): array
    {
        $urls = [];
        foreach ($locales as $locale) {
            $urls[$locale] = PublicUrl::to(self::twin($locale, false));
        }

        return $urls;
    }

    /** The name of a language in that language, for the switch: 'ja' gives "日本語", 'fr' gives "Français" (PHP's intl extension) */
    public static function nativeName(string $locale): string
    {
        // mb_ucfirst: capital first letter, also for non-ASCII letters ("français" becomes "Français")
        // getDisplayLanguage() returns false on failure: the code itself then
        return mb_ucfirst(Locale::getDisplayLanguage($locale, $locale) ?: $locale);
    }

    /** The Open Graph form of a language, language_COUNTRY: 'en' gives "en_US", 'ja' gives "ja_JP" */
    public static function ogLocale(string $locale): string
    {
        // addLikelySubtags completes a bare language with its most likely script and country ('ja' -> 'ja_Jpan_JP')
        // (false on failure: the bare language then, which gives no region)
        $region = Locale::getRegion(Locale::addLikelySubtags($locale) ?: $locale);

        return $region ? $locale.'_'.$region : $locale;
    }
}
