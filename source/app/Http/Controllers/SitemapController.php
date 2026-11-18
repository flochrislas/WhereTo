<?php

namespace App\Http\Controllers;

use App\Enums\OrderBy;
use App\Enums\TagOperator;
use App\Services\BarSearch;
use App\Services\KaraokeSearch;
use App\Services\RestaurantSearch;
use App\Services\StoreSearch;
use App\Support\PublicUrl;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * What the search engines read first: robots.txt (what they may crawl, and where the sitemap is)
 * and sitemap.xml (every page worth indexing, with the date it last changed). See doc/10-seo.md.
 */
class SitemapController extends Controller
{
    /** The main page of each kind, the search service that lists its places, and the route of a place's own page */
    private const KINDS = [
        // route name of the main page => [search service, route name of the standalone page]
        'restaurants.main' => [RestaurantSearch::class, 'restaurants.details.single'],
        'stores.main' => [StoreSearch::class, 'stores.details.single'],
        'bars.main' => [BarSearch::class, 'bars.details.single'],
        'karaokes.main' => [KaraokeSearch::class, 'karaokes.details.single'],
    ];

    /**
     * robots.txt: everything may be crawled (the fragments say "noindex" themselves), and here is the sitemap.
     * A route rather than a static file because the sitemap line needs the absolute URL of this deployment (APP_URL).
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow:',
            'Sitemap: '.PublicUrl::to(route('sitemap', absolute: false)),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * sitemap.xml: the home page, the four main pages, and every place's standalone page.
     * Cached forever like the rest of the data (the seeder flushes the cache), so the URLs are built on APP_URL,
     * not on the hostname of whichever request came first.
     */
    public function sitemap(): Response
    {
        $xml = Cache::rememberForever('sitemap', fn () => $this->buildSitemap());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** The XML itself: one <url> per page, with <lastmod> when the place has a visit date */
    private function buildSitemap(): string
    {
        $urls = [[PublicUrl::to(route('home', absolute: false)), null]];
        foreach (self::KINDS as $mainRoute => [$searchClass, $singleRoute]) {
            $urls[] = [PublicUrl::to(route($mainRoute, absolute: false)), null];
            // Every place of the kind, through the same cached search as the main page ("All-Restaurant" and so on)
            foreach ((new $searchClass)->find(null, TagOperator::And, OrderBy::Distance) as $place) {
                // visit_date is the date of the review (the stores have no such column: null)
                $visited = $place->getAttribute('visit_date');
                $urls[] = [PublicUrl::to(route($singleRoute, $place->getAttribute('id'), false)), $visited ? substr((string) $visited, 0, 10) : null];
            }
        }

        $lines = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        foreach ($urls as [$url, $lastmod]) {
            // htmlspecialchars: the URL goes inside XML (& would break it)
            $lines[] = '  <url><loc>'.htmlspecialchars($url, ENT_XML1).'</loc>'.($lastmod ? '<lastmod>'.$lastmod.'</lastmod>' : '').'</url>';
        }
        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }
}
