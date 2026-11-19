<?php

namespace Tests\Feature;

use App\Models\Bar;
use App\Models\Karaoke;
use App\Models\Restaurant;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/** robots.txt and sitemap.xml, the two files the search engines read first (doc/10-seo.md) */
class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_allows_everything_and_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee("User-agent: *\nDisallow:\nSitemap: ".route('sitemap'), false);
    }

    public function test_sitemap_lists_the_pages_and_every_place(): void
    {
        $restaurant = Restaurant::create(['name' => 'Reviewed', 'visit_date' => '2019-05-04 12:00:00']);
        $store = Store::create(['name' => 'A shop']);
        $bar = Bar::create(['name' => 'A bar']);

        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<loc>'.route('home').'/</loc>', false)
            ->assertSee('<loc>'.route('karaokes.main').'</loc>', false)
            // The review date of a visited place is its last modification; a store has none
            ->assertSee('<loc>'.route('restaurants.details.single', $restaurant->id).'</loc><lastmod>2019-05-04</lastmod>', false)
            ->assertSee('<url><loc>'.route('stores.details.single', $store->id).'</loc></url>', false)
            ->assertSee('<loc>'.route('bars.details.single', $bar->id).'</loc>', false);
        // Well-formed XML (an unescaped & in a URL would break a crawler's parser)
        $this->assertNotFalse(simplexml_load_string($response->getContent()));
    }

    public function test_sitemap_urls_come_from_app_url_not_from_the_request_host(): void
    {
        // The sitemap is cached forever: the first request must not bake its own hostname into it
        config(['app.url' => 'https://whereto.example']);
        $this->get('http://192.0.2.1/robots.txt')->assertSee('Sitemap: https://whereto.example/sitemap.xml', false);
        $xml = $this->get('http://192.0.2.1/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('<loc>https://whereto.example/</loc>', $xml);
        $this->assertStringContainsString('<loc>https://whereto.example/restaurants</loc>', $xml);
        $this->assertStringNotContainsString('192.0.2.1', $xml);
    }

    /** The home and main pages in every language; the place pages in English only while no place has a Japanese name */
    public function test_sitemap_lists_the_pages_in_every_language_and_the_places_where_indexable(): void
    {
        $restaurant = Restaurant::create(['name' => 'Reviewed']);
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        foreach (['/ja', '/ja/restaurants', '/ja/stores', '/ja/bars', '/ja/karaokes'] as $path) {
            $this->assertStringContainsString('<loc>'.url($path).'</loc>', $xml);
        }
        $this->assertStringContainsString('<loc>'.route('restaurants.details.single', $restaurant->id).'</loc>', $xml);
        $this->assertStringNotContainsString(route('ja.restaurants.details.single', $restaurant->id), $xml);
        // Cached under a key that names the languages: a new language cannot be served the old sitemap
        $this->assertTrue(Cache::has('sitemap-en-ja'));
    }

    /** The page of a closed place stays reachable, but is not offered to the search engines */
    public function test_sitemap_leaves_out_the_closed_places(): void
    {
        $open = Restaurant::create(['name' => 'Still there']);
        $closed = [
            Restaurant::create(['name' => 'Gone', 'business_status' => 'CLOSED_PERMANENTLY']),
            Store::create(['name' => 'Gone', 'business_status' => 'REPLACED']),
            Bar::create(['name' => 'Gone', 'business_status' => 'CLOSED_TEMPORARILY']),
            Karaoke::create(['name' => 'Gone', 'business_status' => 'CLOSED_PERMANENTLY']),
        ];
        $kinds = ['restaurants', 'stores', 'bars', 'karaokes'];

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('<loc>'.route('restaurants.details.single', $open->id).'</loc>', $xml);
        foreach ($closed as $i => $place) {
            $this->assertStringNotContainsString('<loc>'.route($kinds[$i].'.details.single', $place->id).'</loc>', $xml);
        }
    }
}
