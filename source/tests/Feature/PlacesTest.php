<?php

namespace Tests\Feature;

use App\Models\Bar;
use App\Models\BarTag;
use App\Models\Karaoke;
use App\Models\KaraokeTag;
use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\Store;
use App\Models\StoreTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        preg_match_all('/class="rowName">([^<]+)</', $html, $m);

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

    public function test_home_page(): void
    {
        $this->get('/')->assertOk()->assertSee('Shibuya')
            ->assertSee('<h1 class="visuallyHidden">Find the best places in Shibuya</h1>', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
            ->assertSee('src="'.asset('js/whereto.js').'?v=', false)
            ->assertSee('href="'.asset('css/style.css').'?v=', false)
            // Dark and light themes: the browser is told about both, and the header has the switch
            ->assertSee('<meta name="color-scheme" content="dark light">', false)
            ->assertSee('id="themeButton"', false)
            // The service worker is registered with the asset versions (data attributes of <html>)
            ->assertSee('data-css-version="'.filemtime(public_path('css/style.css')).'"', false)
            ->assertSee('data-js-version="'.filemtime(public_path('js/whereto.js')).'"', false)
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
