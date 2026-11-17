<?php

namespace Tests\Feature;

use App\Models\Bar;
use App\Models\Restaurant;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('<loc>'.route('karaokes.main').'</loc>', false)
            // The review date of a visited place is its last modification; a store has none
            ->assertSee('<loc>'.route('restaurants.details.single', $restaurant->id).'</loc><lastmod>2019-05-04</lastmod>', false)
            ->assertSee('<url><loc>'.route('stores.details.single', $store->id).'</loc></url>', false)
            ->assertSee('<loc>'.route('bars.details.single', $bar->id).'</loc>', false);
        // Well-formed XML (an unescaped & in a URL would break a crawler's parser)
        $this->assertNotFalse(simplexml_load_string($response->getContent()));
    }
}
