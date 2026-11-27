<?php

namespace Tests\Feature;

use App\Enums\BusinessStatus;
use App\Enums\OrderBy;
use App\Enums\TagOperator;
use App\Models\Bar;
use App\Models\BarTag;
use App\Models\Karaoke;
use App\Models\KaraokeTag;
use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\Store;
use App\Models\StoreTag;
use App\Services\RestaurantSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * End-to-end checks of the public pages for each place type.
 * Positions: "near" is a few hundred metres from Shibuya Station, "far" about 1.5 km.
 */
class PlacesTest extends TestCase
{
    use RefreshDatabase;

    private const STATION = '35.658789,139.701262';

    /** @return array<string, array{string, string, class-string, class-string}> */
    public static function placeTypes(): array
    {
        return [
            'restaurants' => ['restaurants', 'restaurant', Restaurant::class, RestaurantTag::class],
            'stores' => ['stores', 'store', Store::class, StoreTag::class],
            'bars' => ['bars', 'bar', Bar::class, BarTag::class],
            'karaokes' => ['karaokes', 'karaoke', Karaoke::class, KaraokeTag::class],
        ];
    }

    /**
     * Two tagged places: "Near one" (tags a, b) and "Far one" (tag a).
     *
     * @param  class-string  $model
     * @param  class-string  $tagModel
     */
    private function seedTwo(string $model, string $tagModel): void
    {
        $tagModel::create(['label' => 'a', 'type' => 10, 'weight' => 1]);
        $tagModel::create(['label' => 'b', 'type' => 20, 'weight' => 1]);
        $near = $model::create(['name' => 'Near one', 'type' => 'T', 'lat' => 35.6586, 'lon' => 139.7016, 'points' => 'near', 'lunch_price' => 1200, 'score_lunch' => 1, 'score_food' => 1, 'score_place' => 1, 'score' => 1]);
        $far = $model::create(['name' => 'Far one', 'type' => 'T', 'lat' => 35.6700, 'lon' => 139.7100, 'points' => 'far', 'lunch_price' => 800, 'score_lunch' => 3, 'score_food' => 3, 'score_place' => 3, 'score' => 3]);
        $near->attachTags(['a', 'b']);
        $far->attachTags(['a']);
    }

    private function namesInOrder(string $html): array
    {
        // [^>]*: the div also carries lang="en" (the names are English on every page)
        preg_match_all('/class="rowName"[^>]*>([^<]+)</', $html, $m);

        return $m[1];
    }

    #[DataProvider('placeTypes')]
    public function test_filter_page_lists_tags(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $near = $model::where('name', 'Near one')->firstOrFail();
        $this->get("/$plural")->assertOk()->assertSee('tagType10')->assertSee('tagType20')
            // Tags are real buttons with a pressed state, the results div is a live region
            ->assertSee('<button type="button" class="tag tagType10"', false)
            ->assertSee('aria-pressed="false"', false)
            ->assertSee('id="results" loading="none" time="1" aria-live="polite"', false)
            // The default list is in the page, with a real link to each place (crawlable without JavaScript)
            ->assertSee('Near one')
            ->assertSee('href="'.route("$plural.details.single", $near->id).'"', false)
            // The JavaScript reads the results URL from this attribute
            ->assertSee('data-results-url="'.route("$plural.results").'"', false)
            // One static script for all the public pages, with a cache-busting version
            ->assertSee('src="'.asset('js/whereto.js').'?v=', false);
    }

    #[DataProvider('placeTypes')]
    public function test_results_sorted_by_distance(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $html = $this->get("/$plural/results?orderBy=distance&position=".self::STATION)->assertOk()
            ->assertSee('2 results') // the counter line above the list
            ->getContent();
        $this->assertSame(['Near one', 'Far one'], $this->namesInOrder($html));
        $this->assertStringContainsString('End of <strong>2</strong>', $html);
    }

    #[DataProvider('placeTypes')]
    public function test_results_filtered_by_tags_with_and(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $html = $this->get("/$plural/results?op=AND&tags=a,b&orderBy=distance&position=null")->assertOk()->getContent();
        $this->assertSame(['Near one'], $this->namesInOrder($html));
        $html = $this->get("/$plural/results?op=AND&tags=a&orderBy=distance&position=null")->assertOk()->getContent();
        $this->assertCount(2, $this->namesInOrder($html));
    }

    /** op=OR: the place needs any of the tags, not all of them */
    #[DataProvider('placeTypes')]
    public function test_results_filtered_by_tags_with_or(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        // Only "Near one" has tag b, but both have tag a: OR returns both
        $html = $this->get("/$plural/results?op=OR&tags=a,b&orderBy=distance&position=null")->assertOk()->getContent();
        $this->assertEqualsCanonicalizing(['Near one', 'Far one'], $this->namesInOrder($html));
        // The filter page offers the choice
        $this->get("/$plural")->assertOk()->assertSee('data-op="OR"', false);
    }

    #[DataProvider('placeTypes')]
    public function test_details_fragment_and_single_page(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $id = $model::where('name', 'Near one')->value('id');
        $details = $this->get("/$plural/details/$id")->assertOk()->assertSee('Near one');
        if (in_array($plural, ['restaurants', 'bars'], true)) {
            // These two show a grade out of 4; bars used to print the restaurant column, which they do not have
            $details->assertSee('1/4');
        }
        $this->get("/$singular/details/$id")->assertOk()->assertSee('Near one')->assertSee('<html', false);
        $this->get("/$plural/details/999")->assertNotFound();
    }

    /** Every kind sorts by its own score columns ("score" for stores and karaoke, several for restaurants and bars) */
    #[DataProvider('placeTypes')]
    public function test_results_sorted_by_ratings(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $html = $this->get("/$plural/results?orderBy=ratings&position=".self::STATION)->assertOk()->getContent();
        $this->assertSame(['Far one', 'Near one'], $this->namesInOrder($html), 'best scores first');
    }

    public function test_restaurants_sorted_by_ratings_and_cost(): void
    {
        $this->seedTwo(Restaurant::class, RestaurantTag::class);
        $html = $this->get('/restaurants/results?orderBy=ratings&position='.self::STATION)->assertOk()->getContent();
        $this->assertSame(['Far one', 'Near one'], $this->namesInOrder($html), 'best scores first');
        $html = $this->get('/restaurants/results?orderBy=cost&position='.self::STATION)->assertOk()->getContent();
        $this->assertSame(['Far one', 'Near one'], $this->namesInOrder($html), 'cheapest first');
    }

    /** The results fragment carries an ETag; asking again with it gives an empty 304 instead of the HTML */
    #[DataProvider('placeTypes')]
    public function test_results_fragment_revalidates_with_etag(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $first = $this->get("/$plural/results?op=AND&tags=a&orderBy=distance&position=null")->assertOk();
        $etag = $first->headers->get('ETag');
        $this->assertNotEmpty($etag);
        $this->assertStringContainsString('no-cache', (string) $first->headers->get('Cache-Control'));
        $this->get("/$plural/results?op=AND&tags=a&orderBy=distance&position=null", ['If-None-Match' => $etag])->assertStatus(304);
    }

    #[DataProvider('placeTypes')]
    public function test_fragments_are_noindex_and_pages_have_a_canonical_url(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $near = $model::where('name', 'Near one')->firstOrFail();
        // The pieces loaded by AJAX must not be indexed...
        $this->get("/$plural/results?position=null")->assertOk()->assertHeader('X-Robots-Tag', 'noindex');
        $this->get("/$plural/details/{$near->id}")->assertOk()->assertHeader('X-Robots-Tag', 'noindex');
        // ...the main page and the standalone page must, and each says its own URL
        $this->get("/$plural")->assertOk()->assertHeaderMissing('X-Robots-Tag')
            ->assertSee('<link rel="canonical" href="'.route("$plural.main").'">', false);
        $this->get("/$singular/details/{$near->id}")->assertOk()->assertHeaderMissing('X-Robots-Tag')
            ->assertSee('<link rel="canonical" href="'.route("$plural.details.single", $near->id).'">', false);
    }

    #[DataProvider('placeTypes')]
    public function test_pages_have_a_heading_and_sharing_tags(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $near = $model::where('name', 'Near one')->firstOrFail();
        $page = $this->get("/$singular/details/{$near->id}")->assertOk()
            ->assertSee('<h1 class="visuallyHidden">Near one</h1>', false)
            ->assertSee('<meta property="og:title" content="Near one - ', false)
            ->assertSee('<meta property="og:url" content="'.route("$plural.details.single", $near->id).'">', false)
            ->assertSee('<meta property="og:image" content="'.asset('icons/icon-512.png').'">', false)
            ->assertSee('<meta name="twitter:card" content="summary">', false);
        // The review year is the visit date (none here), never the row's update time
        $page->assertDontSee(date('Y').' review');
        $this->get("/$plural")->assertOk()->assertSee('<h1 class="visuallyHidden">', false)->assertSee('<meta name="description" content="Quickly find', false);
    }

    public function test_restaurant_page_titles_carry_the_visit_year(): void
    {
        $this->seedTwo(Restaurant::class, RestaurantTag::class);
        $near = Restaurant::where('name', 'Near one')->firstOrFail();
        $near->update(['visited' => true, 'visit_date' => '2019-05-04 12:00:00']);
        $this->get("/restaurant/details/{$near->id}")->assertOk()
            ->assertSee('<title>'.config('app.name').' - Near one - Restaurant info &amp; 2019 review</title>', false);
    }

    /**
     * The JSON-LD block of a standalone page, decoded.
     *
     * @return array<string, mixed>
     */
    private function structuredData(string $html): array
    {
        $this->assertSame(1, preg_match('#<script type="application/ld\+json">(.+?)</script>#s', $html, $m), 'the page has one JSON-LD block');

        return json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);
    }

    #[DataProvider('placeTypes')]
    public function test_single_page_carries_structured_data(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $id = $model::where('name', 'Near one')->value('id');
        $url = route("$plural.details.single", $id);

        $data = $this->structuredData($this->get($url)->assertOk()->getContent());

        $this->assertSame('https://schema.org', $data['@context']);
        $this->assertContains($data['@type'], ['Restaurant', 'Store', 'BarOrPub', 'EntertainmentBusiness']);
        $this->assertSame('Near one', $data['name']);
        $this->assertSame($url, $data['url']);
        $this->assertSame(['@type' => 'GeoCoordinates', 'latitude' => 35.6586, 'longitude' => 139.7016], $data['geo']);
        // The address is the ward only (the location column is a neighbourhood label, never a street); no links: the key is absent
        $this->assertSame(['@type' => 'PostalAddress', 'addressLocality' => 'Shibuya', 'addressRegion' => 'Tokyo', 'addressCountry' => 'JP'], $data['address']);
        $this->assertArrayNotHasKey('sameAs', $data);
        // The fragment (one-page view) has no structured data: only the real page describes the place
        $this->assertStringNotContainsString('ld+json', $this->get(route("$plural.details", $id))->getContent());
    }

    public function test_restaurant_structured_data_has_price_links_and_review(): void
    {
        $restaurant = Restaurant::create([
            'name' => 'Sushi <b>& co</b>', 'location' => '1-2-3 Dogenzaka', 'lat' => 35.6586, 'lon' => 139.7016,
            'lunch_price' => 900, 'score_lunch' => 3, 'visited' => true, 'visit_date' => '2018-05-01',
            'google_maps_link' => 'https://maps.google.com/?q=sushi', 'tabelog_link' => 'https://tabelog.com/x/', 'official_website' => '',
        ]);

        $html = $this->get(route('restaurants.details.single', $restaurant->id))->assertOk()->getContent();
        // The dangerous characters are encoded (no < > & in the script), and the JSON still decodes to the name
        $this->assertStringContainsString('\\u003Cb\\u003E', $html);
        $data = $this->structuredData($html);

        $this->assertSame('Restaurant', $data['@type']);
        $this->assertSame('Sushi <b>& co</b>', $data['name']);
        // The location text is not an address: it never becomes a street line
        $this->assertArrayNotHasKey('streetAddress', $data['address']);
        $this->assertSame('JP', $data['address']['addressCountry']);
        $this->assertSame('¥900', $data['priceRange']);
        // The empty website is left out
        $this->assertSame(['https://maps.google.com/?q=sushi', 'https://tabelog.com/x/'], $data['sameAs']);
        $this->assertSame('Review', $data['review']['@type']);
        $this->assertSame(['@type' => 'Rating', 'ratingValue' => 3, 'bestRating' => 4, 'worstRating' => 0], $data['review']['reviewRating']);
        $this->assertSame('2018-05-01', $data['review']['datePublished']);
        $this->assertSame(config('app.name'), $data['review']['author']['name']);
    }

    public function test_public_urls_come_from_app_url_not_from_the_request_host(): void
    {
        config(['app.url' => 'https://whereto.example']);
        $restaurant = Restaurant::create(['name' => 'Anywhere', 'lat' => 35.6586, 'lon' => 139.7016]);

        // Requested through another hostname: the canonical, og:url and JSON-LD still name the public page
        $html = $this->get('http://192.0.2.1/restaurant/details/'.$restaurant->id)->assertOk()->getContent();
        $this->assertStringContainsString('<link rel="canonical" href="https://whereto.example/restaurant/details/'.$restaurant->id.'">', $html);
        $this->assertStringContainsString('<meta property="og:url" content="https://whereto.example/restaurant/details/'.$restaurant->id.'">', $html);
        $this->assertSame('https://whereto.example/restaurant/details/'.$restaurant->id, $this->structuredData($html)['url']);
        $this->assertStringContainsString('<link rel="canonical" href="https://whereto.example/">', $this->get('http://192.0.2.1/')->getContent());
    }

    public function test_unscored_places_have_no_review(): void
    {
        $restaurant = Restaurant::create(['name' => 'Unknown', 'lat' => 35.6586, 'lon' => 139.7016]);
        $data = $this->structuredData($this->get(route('restaurants.details.single', $restaurant->id))->getContent());

        $this->assertArrayNotHasKey('review', $data);
        $this->assertArrayNotHasKey('priceRange', $data);
    }

    /**
     * A third place, closed, with the tag a and the best scores (it would come first in every list if it were listed).
     *
     * @param  class-string  $model
     */
    private function seedClosed(string $model, string $status = 'CLOSED_PERMANENTLY'): int
    {
        $closed = $model::create(['name' => 'Gone one', 'business_status' => $status, 'type' => 'T', 'lat' => 35.6587, 'lon' => 139.7015, 'points' => 'gone', 'lunch_price' => 500, 'score_lunch' => 4, 'score_food' => 4, 'score_place' => 4, 'score' => 4]);
        $closed->attachTags(['a']);

        return $closed->id;
    }

    /** A closed place is in the database (the seeders keep it) but never in a list: the open() scope of the search */
    #[DataProvider('placeTypes')]
    public function test_closed_places_are_not_listed(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $this->seedClosed($model);
        // The results fragment, with and without a tag filter, and in every order
        foreach (['', '&op=AND&tags=a', '&op=OR&tags=a,b'] as $filter) {
            foreach (['distance', 'ratings', 'cost'] as $orderBy) {
                $html = $this->get("/$plural/results?orderBy=$orderBy&position=null$filter")->assertOk()->getContent();
                $this->assertNotContains('Gone one', $this->namesInOrder($html), "$filter $orderBy");
            }
        }
        $this->get("/$plural/results?position=null")->assertSee('2 results');
        // The default list rendered in the filter page
        $this->get("/$plural")->assertOk()->assertSee('Near one')->assertDontSee('Gone one');
    }

    /** @return array<string, array{string, string, bool}> */
    public static function closedStatuses(): array
    {
        return [
            // status => [label shown on the page, noindex expected]
            'permanently closed' => ['CLOSED_PERMANENTLY', 'Permanently closed', true],
            'replaced' => ['REPLACED', 'Replaced by another business', true],
            'relocated' => ['RELOCATED', 'Moved to another address', true],
            'temporarily closed' => ['CLOSED_TEMPORARILY', 'Temporarily closed', false],
        ];
    }

    /** The page of a closed place still renders, says what became of it, and leaves the index when it is gone for good */
    #[DataProvider('closedStatuses')]
    public function test_closed_place_page_shows_the_status(string $status, string $label, bool $noindex): void
    {
        foreach (self::placeTypes() as [$plural, $singular, $model, $tagModel]) {
            $this->seedTwo($model, $tagModel);
            $id = $this->seedClosed($model, $status);
            $page = $this->get("/$singular/details/$id")->assertOk()
                ->assertSee('<p class="businessStatus">'.$label.'</p>', false)
                // The heading is the name alone, the title and the descriptions carry the status
                ->assertSee('<h1 class="visuallyHidden">Gone one</h1>', false)
                ->assertSee('<title>'.config('app.name').' - Gone one, '.lcfirst($label).' - ', false)
                ->assertSee('<meta property="og:title" content="Gone one, '.lcfirst($label).' - ', false)
                ->assertSee('Gone one, '.lcfirst($label).', in Tokyo Shibuya.', false)
                // The visit and the notes are still there, and so is the Google Maps link (Maps shows the same status)
                ->assertSee('gone');
            if ($noindex) {
                // Gone for good: out of the index, and no JSON-LD (it would describe a live business with a review)
                $page->assertSee('<meta name="robots" content="noindex">', false)->assertDontSee('ld+json', false);
            } else {
                // Temporarily closed: still indexed, and still described
                $page->assertDontSee('name="robots"', false);
                $this->assertSame('Gone one', $this->structuredData($page->getContent())['name']);
            }
        }
    }

    /**
     * A hand-typed unknown status (here 'CLOSED') must not break the page: it is shown as open. The open() scope
     * compares with 'OPERATIONAL', so the place is out of the lists all the same.
     */
    #[DataProvider('placeTypes')]
    public function test_unknown_status_shows_the_page_as_open(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $near = $model::where('name', 'Near one')->firstOrFail();
        // Straight into the table, as a seeder typo would end up
        DB::table((new $model)->getTable())->where('id', $near->id)->update(['business_status' => 'CLOSED']);

        $this->get("/$singular/details/{$near->id}")->assertOk()
            ->assertDontSee('businessStatus')
            ->assertDontSee('name="robots"', false)
            ->assertSee('ld+json', false);
        $this->get("/$plural")->assertOk()->assertDontSee('Near one');
    }

    /** The lists are cached forever: what comes back from a serializing cache store must still say who is open */
    public function test_cached_results_keep_the_open_places_only(): void
    {
        config(['cache.default' => 'file']); // the array store of the tests does not serialize
        Cache::flush();
        try {
            $this->seedTwo(Restaurant::class, RestaurantTag::class);
            $this->seedClosed(Restaurant::class);
            $search = new RestaurantSearch;
            $search->find(null, TagOperator::And, OrderBy::Distance); // fills the cache

            $cached = Cache::get($search->cacheKey(TagOperator::And, null, OrderBy::Distance));
            $this->assertEqualsCanonicalizing(['Near one', 'Far one'], $cached->pluck('name')->all());
            $this->assertInstanceOf(Restaurant::class, $cached->first());
            $this->assertSame(BusinessStatus::Operational, $cached->first()->businessStatus());
        } finally {
            Cache::flush();
        }
    }

    #[DataProvider('placeTypes')]
    public function test_operational_place_page_has_no_status_banner(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        // One created without the key (the column default) and one with it written explicitly, as the tool may do
        $near = $model::where('name', 'Near one')->firstOrFail();
        $far = $model::where('name', 'Far one')->firstOrFail();
        $far->update(['business_status' => 'OPERATIONAL', 'google_place_id' => 'ChIJtest']);
        foreach ([$near, $far] as $place) {
            $this->get("/$singular/details/{$place->id}")->assertOk()
                ->assertDontSee('businessStatus')
                ->assertDontSee('name="robots"', false)
                ->assertSee('<title>'.config('app.name').' - '.$place->name.' - ', false);
        }
        $this->assertSame('ChIJtest', $far->fresh()?->google_place_id);
    }

    public function test_home_page(): void
    {
        $this->get('/')->assertOk()->assertSee('Shibuya')
            ->assertSee('<h1 class="visuallyHidden">Find the best places in Shibuya</h1>', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'/">', false)
            ->assertSee('src="'.asset('js/whereto.js').'?v=', false)
            ->assertSee('href="'.asset('css/style.css').'?v=', false)
            // Dark and light themes: the browser is told about both, and the header has the switch
            ->assertSee('<meta name="color-scheme" content="dark light">', false)
            ->assertSee('id="themeButton"', false)
            // The service worker is registered with the asset versions (data attributes of <html>)
            ->assertSee('data-css-version="'.filemtime(public_path('css/style.css')).'"', false)
            ->assertSee('data-js-version="'.filemtime(public_path('js/whereto.js')).'"', false)
            ->assertSee('data-icons-version="'.filemtime(public_path('icons/tags.svg')).'"', false)
            ->assertSee("localStorage.getItem('theme')", false)
            // Progressive Web App: manifest and home screen icon
            ->assertSee('rel="manifest" href="'.asset('manifest.json').'"', false)
            ->assertSee('rel="apple-touch-icon"', false);
    }

    /** The site is read-only: the legacy login and admin are gone (404, not a redirect to a login page). */
    public function test_login_and_admin_do_not_exist(): void
    {
        $this->get('/login')->assertNotFound();
        $this->get('/admin')->assertNotFound();
        $this->get('/admin/restaurants')->assertNotFound();
        $this->post('/login', ['email' => 'a@b.c', 'password' => 'x'])->assertNotFound();
    }
}
