<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\Store;
use App\Models\StoreTag;
use App\Services\StaticExporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use LogicException;
use Tests\TestCase;

/**
 * The export of the stand-alone Android app (doc/16-standalone-apk-plan.md, Phases 3 and 5), on the test database
 * with a few places made here. StaticExporter is called directly: the command's rebuild of whereto_export from the
 * full seeders is exercised by tools/android-app, not by PHPUnit.
 */
class StaticExporterTest extends TestCase
{
    use RefreshDatabase;

    private const DATE = '2026-11-27T10:00:00+09:00';

    private const MAPS = 'https://maps.google.com/?cid=';

    /** A temporary folder per test: the export, the "real" cache and session stores */
    private string $tmp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmp = sys_get_temp_dir().'/whereto-export-test-'.bin2hex(random_bytes(4));
        File::ensureDirectoryExists($this->tmp);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->tmp);
        parent::tearDown();
    }

    /**
     * Restaurants: the best one (with a website), a cheaper one without coordinates nor website, and a closed one
     * that would be first in both orders. One store, a kind without a price.
     *
     * @return array<string, int> name => id
     */
    private function seedPlaces(): array
    {
        foreach (['ramen', 'noodles'] as $label) {
            RestaurantTag::create(['label' => $label, 'type' => 10, 'weight' => 1]);
        }
        StoreTag::create(['label' => 'shoes', 'type' => 10, 'weight' => 1]);
        $common = ['type' => 'Ramen', 'points' => 'p', 'experience' => 'e', 'score_food' => 1, 'score_place' => 1];
        $best = Restaurant::create($common + ['name' => 'Best', 'lat' => 35.6586, 'lon' => 139.7016, 'lunch_price' => 1500, 'score_lunch' => 3,
            'google_maps_link' => self::MAPS.'1', 'official_website' => 'https://example.com/']);
        $nowhere = Restaurant::create($common + ['name' => 'Nowhere', 'lat' => null, 'lon' => null, 'lunch_price' => 800, 'score_lunch' => 2,
            'google_maps_link' => self::MAPS.'2', 'official_website' => null]);
        $closed = Restaurant::create($common + ['name' => 'Closed', 'lat' => 35.6590, 'lon' => 139.7010, 'lunch_price' => 500, 'score_lunch' => 4,
            'google_maps_link' => self::MAPS.'3', 'business_status' => 'CLOSED_PERMANENTLY']);
        $best->attachTags(['ramen']);
        $nowhere->attachTags(['ramen', 'noodles']);
        $closed->attachTags(['ramen']);
        $store = Store::create(['name' => 'Shoe shop', 'lat' => 35.6600, 'lon' => 139.7000, 'score' => 2, 'google_maps_link' => self::MAPS.'4']);
        $store->attachTags(['shoes']);

        return ['best' => $best->id, 'nowhere' => $nowhere->id, 'closed' => $closed->id, 'store' => $store->id];
    }

    private function exported(string $path): mixed
    {
        return json_decode(File::get($this->tmp.'/export/'.$path), true, flags: JSON_THROW_ON_ERROR);
    }

    public function test_export_needs_its_preparation(): void
    {
        $this->expectException(LogicException::class);
        (new StaticExporter)->export($this->tmp.'/export');
    }

    public function test_exports_every_page_and_place_in_both_languages_without_touching_the_real_stores(): void
    {
        $ids = $this->seedPlaces();
        // The stores of a real run (the dev one: database cache, file sessions), here files in the temporary folder
        config([
            'cache.stores.real' => ['driver' => 'file', 'path' => $this->tmp.'/cache'],
            'cache.default' => 'real',
            'session.driver' => 'file',
            'session.files' => $this->tmp.'/sessions',
        ]);
        File::ensureDirectoryExists($this->tmp.'/sessions');
        Cache::forever('kept', 'yes');
        $cacheFiles = count(File::allFiles($this->tmp.'/cache'));

        StaticExporter::prepare(self::DATE);
        $summary = (new StaticExporter)->export($this->tmp.'/export');

        // The real cache: its key still there, nothing new in it; no session file written
        $this->assertSame('yes', Cache::store('real')->get('kept'));
        $this->assertCount($cacheFiles, File::allFiles($this->tmp.'/cache'));
        $this->assertSame([], File::allFiles($this->tmp.'/sessions'));

        $dir = $this->tmp.'/export';
        $this->assertSame(['restaurants' => 3, 'stores' => 1, 'bars' => 0, 'karaokes' => 0], $summary['places']);
        foreach (['', 'ja/'] as $prefix) {
            foreach (['', 'info/main/', 'vip/main/', 'restaurants/', 'stores/', 'bars/', 'karaokes/'] as $page) {
                $this->assertFileExists("$dir/$prefix{$page}index.html");
            }
            // Every place, the closed one too: its address may have been kept
            foreach (['best', 'nowhere', 'closed'] as $name) {
                $this->assertFileExists("$dir/{$prefix}restaurants/details/{$ids[$name]}/index.html");
                $this->assertFileExists("$dir/{$prefix}restaurant/details/{$ids[$name]}/index.html");
            }
            $this->assertFileExists("$dir/{$prefix}store/details/{$ids['store']}/index.html");
        }
        $this->assertStringContainsString('<html lang="ja"', File::get("$dir/ja/restaurants/index.html"));
        $this->assertStringContainsString('<html lang="en"', File::get("$dir/restaurants/index.html"));
        $this->assertStringContainsString('data-standalone="'.self::DATE.'"', File::get("$dir/index.html"));

        // The assets the pages load, and nothing of the website's own machinery
        foreach (['css/style.css', 'js/whereto.js', 'js/whereto-local.js', 'icons/tags.svg', 'favicon.ico'] as $asset) {
            $this->assertFileExists("$dir/$asset");
        }
        foreach (['sw.js', 'manifest.json', 'index.php'] as $absent) {
            $this->assertFileDoesNotExist("$dir/$absent");
        }

        // No file names the site: every internal URL a browser would load or follow is root-relative
        foreach (File::allFiles($dir) as $file) {
            if ($file->getExtension() !== 'html') {
                continue;
            }
            $html = $file->getContents();
            $this->assertStringNotContainsString('phpdebugbar', $html, $file->getRelativePathname());
            // The canonical and hreflang links name the public site on purpose (for the search engines, never loaded)
            $html = (string) preg_replace('/<link rel="(canonical|alternate)"[^>]*>/', '', $html);
            preg_match_all('/\s(href|src|data-[\w-]+|onclick)="([^"]*)"/', $html, $matches, PREG_SET_ORDER);
            foreach ($matches as [, $attribute, $value]) {
                // The host of the internal requests (and of APP_URL in the tests); the external links stay as they are
                $this->assertStringNotContainsString('//localhost', html_entity_decode($value), $file->getRelativePathname().": $attribute=\"$value\"");
            }
        }

        $this->assertSame(self::DATE, $this->exported('app-data/meta.json')['exportDate']);
    }

    public function test_exports_the_search_data(): void
    {
        $ids = $this->seedPlaces();
        StaticExporter::prepare(self::DATE);
        (new StaticExporter)->export($this->tmp.'/export');

        // The two orders of the server; the closed place in none
        $restaurants = $this->exported('app-data/restaurants.json');
        $this->assertSame([$ids['best'], $ids['nowhere']], $restaurants['byRatings']);
        $this->assertSame([$ids['nowhere'], $ids['best']], $restaurants['byCost']);
        $this->assertSame([(string) $ids['best'], (string) $ids['nowhere']], array_map('strval', array_keys($restaurants['places'])));
        $this->assertSame(['tags' => ['ramen'], 'lat' => 35.6586, 'lon' => 139.7016], $restaurants['places'][$ids['best']]);
        $this->assertNull($restaurants['places'][$ids['nowhere']]['lat']);
        $this->assertNull($restaurants['places'][$ids['nowhere']]['lon']);
        $this->assertEqualsCanonicalizing(['ramen', 'noodles'], $restaurants['places'][$ids['nowhere']]['tags']);

        // No byCost for a kind without a price; an empty kind still has its file
        $stores = $this->exported('app-data/stores.json');
        $this->assertSame([$ids['store']], $stores['byRatings']);
        $this->assertArrayNotHasKey('byCost', $stores);
        $this->assertSame(['byRatings' => [], 'places' => []], $this->exported('app-data/bars.json'));

        // Per language: every row of the list, the sentences of the suggestion and of the count
        foreach (['en', 'ja'] as $locale) {
            $results = File::get($this->tmp."/export/app-data/$locale/restaurants-results.html");
            $this->assertStringContainsString('data-place-id="'.$ids['best'].'"', $results);
            $this->assertStringContainsString('data-place-id="'.$ids['nowhere'].'"', $results);
            $this->assertStringNotContainsString('data-place-id="'.$ids['closed'].'"', $results);
            $suggestions = $this->exported("app-data/$locale/suggestions.json");
            $this->assertEqualsCanonicalizing([$ids['best'], $ids['nowhere']], array_keys($suggestions));
            $prefix = $locale === 'en' ? '' : '/ja';
            $this->assertStringContainsString('href="'.$prefix.'/restaurant/details/'.$ids['best'].'"', $suggestions[$ids['best']]);
            $strings = $this->exported("app-data/$locale/strings.json");
            $this->assertSame(['one', 'many', 'end'], array_keys($strings));
            $this->assertStringContainsString(':count', $strings['many']);
            $this->assertStringContainsString(':count', $strings['end']);
        }
        $this->assertSame('1 result', $this->exported('app-data/en/strings.json')['one']);
        $this->assertStringContainsString('ラーメン', $this->exported('app-data/ja/suggestions.json')[$ids['best']]);
    }
}
