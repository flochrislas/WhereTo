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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The stand-alone mode of the views (doc/16-standalone-apk-plan.md, Phase 2): what the pages of the Android app
 * look like when config('whereto.standalone') holds an export date, and that the website is unchanged without it.
 */
class StandaloneModeTest extends TestCase
{
    use RefreshDatabase;

    private const DATE = '2026-11-27T10:00:00+09:00';

    private const MAPS = 'https://maps.google.com/?cid=1';

    /** The external addresses a page may name: they are opened by other apps, never loaded by the page */
    private const EXTERNAL = [self::MAPS, 'https://tabelog.com/1', 'https://example.com/', 'https://facebook.com/enjoyShibuya/', 'https://www.instagram.com/enjoyshibuya/'];

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

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
     * Two places of a kind, one with a website and one without, both with a Maps link and a tag
     *
     * @param  class-string  $model
     * @param  class-string  $tagModel
     * @return array{object, object} with the website, without
     */
    private function seedTwo(string $model, string $tagModel): array
    {
        $tagModel::create(['label' => 'ramen', 'type' => 10, 'weight' => 1]);
        $common = ['type' => 'Ramen', 'lat' => 35.6586, 'lon' => 139.7016, 'google_maps_link' => self::MAPS, 'score_lunch' => 2, 'score' => 2];
        $with = $model::create($common + ['name' => 'With site', 'official_website' => 'https://example.com/', 'tabelog_link' => 'https://tabelog.com/1']);
        $without = $model::create($common + ['name' => 'No site', 'official_website' => null, 'tabelog_link' => 'https://tabelog.com/1']);
        $with->attachTags(['ramen']);
        $without->attachTags(['ramen']);

        return [$with, $without];
    }

    private function standalone(): void
    {
        config(['whereto.standalone' => self::DATE]);
    }

    /**
     * Every value of the attributes a page loads or follows (href, src, data-*, onclick) that names an http(s)
     * address other than the known external ones: none in the stand-alone mode
     *
     * @return list<string>
     */
    private function internalAbsoluteUrls(string $html): array
    {
        // The canonical and hreflang links name the public site on purpose (APP_URL, for the search engines): no
        // browser loads them
        $html = (string) preg_replace('/<link rel="(canonical|alternate)"[^>]*>/', '', $html);
        preg_match_all('/\s(href|src|data-[\w-]+|onclick)="([^"]*)"/', $html, $matches, PREG_SET_ORDER);
        $found = [];
        foreach ($matches as [, $attribute, $value]) {
            $value = html_entity_decode($value);
            // The known external links, removed: what is left must not name a site
            $rest = str_replace(self::EXTERNAL, '', $value);
            if (preg_match('#https?:#', $rest)) {
                $found[] = "$attribute=\"$value\"";
            }
        }

        return $found;
    }

    #[DataProvider('placeTypes')]
    public function test_standalone_pages_have_only_root_relative_internal_urls(string $plural, string $singular, string $model, string $tagModel): void
    {
        [$with] = $this->seedTwo($model, $tagModel);
        $this->standalone();
        foreach (['', '/ja'] as $prefix) {
            foreach (["$prefix/", "$prefix/$plural", "$prefix/$plural/details/{$with->id}", "$prefix/$singular/details/{$with->id}", "$prefix/$plural/results?orderBy=ratings"] as $path) {
                $html = $this->get($path)->assertOk()->getContent();
                $this->assertSame([], $this->internalAbsoluteUrls($html), $path);
            }
        }
        $main = $this->get("/ja/$plural")->assertOk()
            ->assertSee('data-standalone="'.self::DATE.'"', false)
            ->assertSee('data-results-url="/ja/'.$plural.'/results"', false)
            ->assertSee('data-details-url="/ja/'.$plural.'/details/PLACE_ID"', false)
            ->assertSee('data-twin-base="/'.$plural.'"', false)
            ->assertSee('href="/ja/'.$singular.'/details/'.$with->id.'"', false)
            ->assertSee('<link href="/css/style.css?v=', false)
            // whereto-local.js first: whereto.js finds window.whereToLocal when it starts (both deferred, run in order)
            ->assertSeeInOrder(['<script src="/js/whereto-local.js?v=', '<script src="/js/whereto.js?v='], false)
            // Neither the manifest of the website nor a CSRF token (a new value per session)
            ->assertDontSee('rel="manifest"', false)
            ->assertDontSee('csrf-token', false)
            ->getContent();
        // The row of a place says which place it is (whereto-local.js builds its lists from them)
        $this->assertStringContainsString('class="resultRow" data-place-id="'.$with->id.'"', $main);
    }

    #[DataProvider('placeTypes')]
    public function test_standalone_details_have_no_copy_url_and_no_empty_website(string $plural, string $singular, string $model, string $tagModel): void
    {
        [$with, $without] = $this->seedTwo($model, $tagModel);
        $this->standalone();
        foreach (["/$plural/details/", "/$singular/details/"] as $path) {
            $this->get($path.$with->id)->assertOk()
                ->assertSee("window.open('https://example.com/', '_blank')", false)
                ->assertSee("window.open('".self::MAPS."', '_blank')", false)
                ->assertDontSee('Copy URL')->assertDontSee('copyStringToClipboard');
            $this->get($path.$without->id)->assertOk()
                ->assertDontSee('Website')->assertDontSee("window.open('', '_blank')", false)
                ->assertDontSee('Copy URL');
        }
    }

    #[DataProvider('placeTypes')]
    public function test_standalone_prints_no_maps_or_tabelog_link_for_a_place_without_one(string $plural, string $singular, string $model, string $tagModel): void
    {
        $this->seedTwo($model, $tagModel);
        $bare = $model::create(['name' => 'No links', 'type' => 'Ramen', 'lat' => 35.6586, 'lon' => 139.7016, 'score_lunch' => 2, 'score' => 2]);

        // On the website nothing changes: the buttons and the link of the row are printed, empty
        $this->get("/$plural/details/{$bare->id}")->assertOk()->assertSee('Google maps');
        $this->get("/$plural/results?orderBy=ratings")->assertOk()->assertSee('href=""', false);

        $this->standalone();
        foreach (["/$plural/details/", "/$singular/details/"] as $path) {
            $this->get($path.$bare->id)->assertOk()
                ->assertDontSee('Google maps')->assertDontSee('Tabelog')->assertDontSee('window.open', false);
        }
        // In the list the walking time of such a place is plain text; the other rows keep their link
        $rows = $this->get("/$plural/results?orderBy=ratings")->assertOk()
            ->assertDontSee('href=""', false)
            ->assertSee('href="'.self::MAPS.'"', false)
            ->getContent();
        $this->assertSame(3, substr_count($rows, 'class="oneDigit inline"'));
        $this->assertSame(2, substr_count($rows, '<a target="_blank"'));
    }

    public function test_standalone_home_page_has_the_hidden_suggestion_control_at_any_time(): void
    {
        $this->standalone();
        foreach ([9, 12, 23] as $hour) {
            Carbon::setTestNow(Carbon::create(2026, 11, 16, $hour, 0, 0, 'Asia/Tokyo'));
            $this->get('/')->assertOk()
                ->assertSee('<div id="suggestion" data-suggestion-url="/suggestions" aria-live="polite" hidden>', false)
                ->assertSee('Get a suggestion');
            $this->get('/ja')->assertOk()
                ->assertSee('<div id="suggestion" data-suggestion-url="/ja/suggestions" aria-live="polite" hidden>', false);
        }
    }

    #[DataProvider('placeTypes')]
    public function test_the_website_is_unchanged_without_the_mode(string $plural, string $singular, string $model, string $tagModel): void
    {
        [$with, $without] = $this->seedTwo($model, $tagModel);
        $this->get("/$plural")->assertOk()
            ->assertDontSee('data-standalone="', false)
            ->assertDontSee('whereto-local.js', false)
            ->assertSee('rel="manifest"', false)
            ->assertSee('<meta name="csrf-token"', false)
            ->assertSee('data-results-url="'.route("$plural.results").'"', false)
            ->assertSee('href="'.route("$plural.details.single", $with->id).'"', false)
            ->assertSee('<link href="'.asset('css/style.css').'?v=', false)
            // The one addition visible on the website: the place of each row
            ->assertSee('class="resultRow" data-place-id="'.$with->id.'"', false);
        // Copy URL on the fragment, and the Website button even without a website, as before
        $this->get("/$plural/details/{$without->id}")->assertOk()
            ->assertSee('Copy URL')->assertSee("window.open('', '_blank')", false);
        // No suggestion control outside lunch time
        Carbon::setTestNow(Carbon::create(2026, 11, 16, 9, 0, 0, 'Asia/Tokyo'));
        $this->get('/')->assertOk()->assertDontSee('<div id="suggestion" data-suggestion-url', false);
    }
}
