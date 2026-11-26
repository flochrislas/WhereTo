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
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The languages of the site (doc/12-multilingual-ui.md): English at the plain URLs, Japanese under /ja.
 */
class LocaleTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, string, class-string, class-string, string}> plural, singular, model, tag model, a tag with its translation */
    public static function placeTypes(): array
    {
        return [
            'restaurants' => ['restaurants', 'restaurant', Restaurant::class, RestaurantTag::class, 'ramen'],
            'stores' => ['stores', 'store', Store::class, StoreTag::class, 'shoes'],
            'bars' => ['bars', 'bar', Bar::class, BarTag::class, 'wine'],
            'karaokes' => ['karaokes', 'karaoke', Karaoke::class, KaraokeTag::class, 'private rooms'],
        ];
    }

    /**
     * One open place with one tag
     *
     * @param  class-string  $model
     * @param  class-string  $tagModel
     */
    private function seedOne(string $model, string $tagModel, string $tag): object
    {
        $tagModel::create(['label' => $tag, 'type' => 10, 'weight' => 1]);
        $place = $model::create(['name' => 'Menya Test', 'type' => 'Ramen', 'location' => 'Dogenzaka', 'lat' => 35.6586, 'lon' => 139.7016,
            'points' => 'Rich broth', 'experience' => 'Went twice', 'lunch_price' => 1000, 'score_lunch' => 3, 'score' => 3]);
        $place->attachTags([$tag]);

        return $place;
    }

    public function test_english_urls_and_route_names_are_unchanged(): void
    {
        $this->assertSame(url('/restaurants'), route('restaurants.main'));
        $this->assertSame(url('/ja/restaurants'), route('ja.restaurants.main'));
        $this->get('/restaurants')->assertOk()->assertSee('<html lang="en"', false)
            ->assertSee('<meta property="og:locale" content="en_US">', false)
            ->assertSee('<meta property="og:locale:alternate" content="ja_JP">', false);
        $this->get('/')->assertOk()->assertSee('<html lang="en"', false);
    }

    public function test_japanese_pages_are_under_their_prefix(): void
    {
        $this->get('/ja/restaurants')->assertOk()->assertSee('<html lang="ja"', false)
            ->assertSee('<meta property="og:locale" content="ja_JP">', false)
            ->assertSee('<meta property="og:locale:alternate" content="en_US">', false)
            ->assertSee('<title>'.config('app.name').' - 渋谷のランチ</title>', false)
            // The URLs the script reads are Japanese too
            ->assertSee('data-results-url="'.url('/ja/restaurants/results').'"', false)
            ->assertSee('data-details-url="'.url('/ja/restaurants/details/PLACE_ID').'"', false);
        $this->get('/ja')->assertOk()->assertSee('<html lang="ja"', false)->assertSee('渋谷で ランチ')
            ->assertSee('href="'.url('/ja/restaurants').'"', false);
        // An English request after a Japanese one is English again (the tests answer every request in one process)
        $this->get('/restaurants')->assertOk()->assertSee('<html lang="en"', false)->assertSee('Show All');
        // No other language
        $this->get('/fr/restaurants')->assertNotFound();
    }

    public function test_the_switch_of_a_standalone_page_is_its_twin(): void
    {
        $place = $this->seedOne(Restaurant::class, RestaurantTag::class, 'ramen');
        $this->get("/restaurant/details/{$place->id}")->assertOk()
            ->assertSee('class="langSwitch" href="'.url("/ja/restaurant/details/{$place->id}").'" hreflang="ja" lang="ja"', false)
            ->assertSee('data-twin-base="'.url("/ja/restaurant/details/{$place->id}").'"', false)
            // The banner words are in the language they offer
            ->assertSee('data-banner="日本語で見る"', false)
            ->assertSee('>日本語</a>', false);
        $this->get("/ja/restaurant/details/{$place->id}")->assertOk()
            ->assertSee('class="langSwitch" href="'.url("/restaurant/details/{$place->id}").'" hreflang="en" lang="en"', false)
            ->assertSee('data-banner="Read this page in English"', false)
            ->assertSee('>English</a>', false);
        // The switch of the home pages: no query string (a Route::view() has hidden parameters)
        $this->get('/')->assertSee('class="langSwitch" href="'.url('/ja').'"', false);
        $this->get('/ja')->assertSee('class="langSwitch" href="'.url('/').'"', false);
        // The switch of a filter page is the plain twin; the script adds the state
        $this->get('/ja/restaurants')->assertSee('class="langSwitch" href="'.url('/restaurants').'"', false);
    }

    public function test_the_messages_of_the_script_are_in_the_page_language(): void
    {
        $this->get('/ja/restaurants')->assertOk()
            ->assertSee('window.whereToStrings = {', false)
            // Unicode as is (JSON_UNESCAPED_UNICODE), the HTML characters escaped
            ->assertSee('"showResults":":count件を表示"', false)
            ->assertSee('"suggestionError":"すみません、今は思いつきません。"', false);
        $this->get('/restaurants')->assertSee('"showResults":"Show :count results"', false);
    }

    #[DataProvider('placeTypes')]
    public function test_filter_page_in_japanese(string $plural, string $singular, string $model, string $tagModel, string $tag): void
    {
        $this->seedOne($model, $tagModel, $tag);
        $translated = trans('tags', [], 'ja')[$tag];
        $this->get("/ja/$plural")->assertOk()
            ->assertSee('すべて表示')
            ->assertSee('aria-label="選べるタグ"', false)
            ->assertSee('aria-label="選択したタグ"', false)
            // The tag: the English label is the value, the Japanese one the text
            ->assertSee('data-tag="'.$tag.'"', false)
            ->assertSeeInOrder(['data-tag="'.$tag.'"', $translated])
            // The place's own content stays English, marked as such
            ->assertSee('<div class="rowName" lang="en">Menya Test</div>', false)
            ->assertSee('1件');
        // The English page shows the English label
        $this->get("/$plural")->assertOk()->assertSee('data-tag="'.$tag.'"', false)->assertDontSee($translated);
    }

    #[DataProvider('placeTypes')]
    public function test_results_and_details_fragments_in_japanese(string $plural, string $singular, string $model, string $tagModel, string $tag): void
    {
        $place = $this->seedOne($model, $tagModel, $tag);
        // The search takes the English label, whatever the language of the page
        $this->get("/ja/$plural/results?op=AND&tags=".urlencode($tag).'&orderBy=distance&position=null')->assertOk()
            ->assertSee('1件')
            ->assertSee('href="'.url("/ja/$singular/details/{$place->id}").'"', false)
            ->assertSee(url("/ja/$plural/details/{$place->id}"), false);
        $this->get("/ja/$plural/details/{$place->id}")->assertOk()
            ->assertSee('戻る')
            ->assertSee('id="detailName" class="detail" detailId="'.$place->id.'" lang="en"', false)
            ->assertSee('data-tag="'.$tag.'"', false)
            ->assertSee(trans('tags', [], 'ja')[$tag])
            ->assertSee('title="このタグで絞り込む"', false)
            // The Japanese label on the box, the English note inside it
            ->assertSee('overlay="ポイント">', false)
            ->assertSee('<span lang="en">Rich broth</span>', false);
    }

    #[DataProvider('placeTypes')]
    public function test_standalone_page_in_japanese(string $plural, string $singular, string $model, string $tagModel, string $tag): void
    {
        $place = $this->seedOne($model, $tagModel, $tag);
        $this->get("/ja/$singular/details/{$place->id}")->assertOk()
            ->assertSee('<html lang="ja"', false)
            ->assertSee('<title>'.config('app.name').' - Menya Test - ', false)
            ->assertSee('<link rel="canonical" href="'.url("/ja/$singular/details/{$place->id}").'">', false)
            ->assertSee('lang="en">', false)
            ->assertSee('Rich broth')
            // noindex in Japanese: no JSON-LD naming this URL as the place's page
            ->assertDontSee('application/ld+json', false);
        $this->get("/$singular/details/{$place->id}")->assertOk()->assertSee('application/ld+json', false);
    }

    public function test_restaurant_page_title_in_japanese(): void
    {
        $place = $this->seedOne(Restaurant::class, RestaurantTag::class, 'ramen');
        $place->update(['visit_date' => '2019-05-04 12:00:00']);
        $this->get("/ja/restaurant/details/{$place->id}")->assertOk()
            ->assertSee('<title>'.config('app.name').' - Menya Test - レストラン情報・2019年のレビュー</title>', false);
        $place->update(['business_status' => 'CLOSED_PERMANENTLY']);
        $this->get("/ja/restaurant/details/{$place->id}")->assertOk()
            ->assertSee('Menya Test（閉店）')
            ->assertSee('<p class="businessStatus">閉店</p>', false);
    }

    public function test_home_and_filter_pages_have_hreflang_links_in_every_language(): void
    {
        foreach (['/', '/ja', '/restaurants', '/ja/restaurants', '/ja/karaokes'] as $path) {
            $english = $path === '/ja' ? '/' : (str_starts_with($path, '/ja/') ? substr($path, 3) : $path);
            $japanese = $english === '/' ? '/ja' : '/ja'.$english;
            // Reciprocal: both versions list both, and x-default is the English one
            $this->get($path)->assertOk()->assertHeaderMissing('X-Robots-Tag')
                ->assertSee('<link rel="alternate" hreflang="en" href="'.rtrim(url($english), '/').($english === '/' ? '/' : '').'">', false)
                ->assertSee('<link rel="alternate" hreflang="ja" href="'.url($japanese).'">', false)
                ->assertSee('<link rel="alternate" hreflang="x-default" href="'.rtrim(url($english), '/').($english === '/' ? '/' : '').'">', false)
                ->assertDontSee('name="robots"', false);
        }
    }

    #[DataProvider('placeTypes')]
    public function test_place_pages_are_indexable_in_english_only_until_they_have_japanese_content(string $plural, string $singular, string $model, string $tagModel, string $tag): void
    {
        $place = $this->seedOne($model, $tagModel, $tag);
        // English: indexable, but alone, so no hreflang pair
        $this->get("/$singular/details/{$place->id}")->assertOk()
            ->assertDontSee('name="robots"', false)
            ->assertDontSee('hreflang="ja" href', false)
            ->assertDontSee('rel="alternate"', false);
        // Japanese: a thin copy of the English page, noindex and in no hreflang list
        $this->get("/ja/$singular/details/{$place->id}")->assertOk()
            ->assertSee('<meta name="robots" content="noindex">', false)
            ->assertDontSee('rel="alternate"', false);
        // Gone for good: noindex in every language, no alternates
        $place->update(['business_status' => 'CLOSED_PERMANENTLY']);
        foreach (["/$singular/details/{$place->id}", "/ja/$singular/details/{$place->id}"] as $path) {
            $page = $this->get($path)->assertOk()->assertDontSee('rel="alternate"', false)->getContent();
            $this->assertSame(1, substr_count((string) $page, '<meta name="robots" content="noindex">'), $path);
        }
    }

    public function test_japanese_content_is_a_japanese_name(): void
    {
        // English: always; Japanese: with a name_ja (a blank one counts as none); no French content at all
        $named = new Restaurant(['name' => 'Menya Test', 'name_ja' => '麺屋テスト']);
        $this->assertTrue($named->isIndexableIn('en'));
        $this->assertTrue($named->isIndexableIn('ja'));
        $this->assertFalse($named->isIndexableIn('fr'));
        $this->assertSame('麺屋テスト', $named->nameIn('ja'));
        $this->assertSame('Menya Test', $named->nameIn('en'));
        $this->assertFalse((new Restaurant(['name' => 'Menya Test', 'name_ja' => ' ']))->isIndexableIn('ja'));
        $this->assertSame('Menya Test', (new Restaurant(['name' => 'Menya Test']))->nameIn('ja'));
        $this->assertTrue((new Restaurant)->isIndexableIn('en'));
        $this->assertFalse((new Restaurant)->isIndexableIn('ja'));
        // Gone for good: in no language, Japanese name or not
        $named->setRawAttributes(['name_ja' => '麺屋テスト', 'business_status' => 'REPLACED']);
        $this->assertFalse($named->isIndexableIn('en'));
        $this->assertFalse($named->isIndexableIn('ja'));
    }

    #[DataProvider('placeTypes')]
    public function test_a_place_with_a_japanese_name_is_indexable_in_both_languages(string $plural, string $singular, string $model, string $tagModel, string $tag): void
    {
        $place = $this->seedOne($model, $tagModel, $tag);
        $place->update(['name_ja' => '麺屋テスト']);
        $english = url("/$singular/details/{$place->id}");
        $japanese = url("/ja/$singular/details/{$place->id}");

        // Japanese page: indexable, both alternates, the Japanese name first (title, heading, name box, JSON-LD)
        $html = (string) $this->get("/ja/$singular/details/{$place->id}")->assertOk()
            ->assertDontSee('name="robots"', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="'.$english.'">', false)
            ->assertSee('<link rel="alternate" hreflang="ja" href="'.$japanese.'">', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.$english.'">', false)
            ->assertSee('<title>'.config('app.name').' - 麺屋テスト Menya Test - ', false)
            ->assertSee('<meta property="og:title" content="麺屋テスト Menya Test - ', false)
            ->assertSee('<meta name="description" content="', false)
            ->assertSee('<div>麺屋テスト <span lang="en">Menya Test</span></div>', false)
            ->assertSee('"name":"麺屋テスト"', false)
            ->assertSee('"url":"'.$japanese.'"', false)
            ->getContent();
        $this->assertMatchesRegularExpression('#<h1 class="visuallyHidden">\s*麺屋テスト <span lang="en">Menya Test</span>\s*</h1>#u', $html);
        $this->assertMatchesRegularExpression('#<meta name="description" content="[^"]*麺屋テスト Menya Test#u', $html);

        // English page: the same pair of alternates, and nothing Japanese
        $this->get("/$singular/details/{$place->id}")->assertOk()
            ->assertDontSee('name="robots"', false)
            ->assertSee('<link rel="alternate" hreflang="ja" href="'.$japanese.'">', false)
            ->assertSee('<title>'.config('app.name').' - Menya Test - ', false)
            ->assertSee('<h1 class="visuallyHidden">Menya Test</h1>', false)
            ->assertSee('"name":"Menya Test"', false)
            ->assertDontSee('麺屋テスト');

        // The list rows and the details fragment: Japanese name first on /ja, unchanged in English
        $results = "$plural/results?op=AND&tags=".urlencode($tag).'&orderBy=distance&position=null';
        $this->get("/ja/$results")->assertOk()
            ->assertSee('<div class="rowName">麺屋テスト <span lang="en">Menya Test</span></div>', false);
        $this->get("/$results")->assertOk()
            ->assertSee('<div class="rowName" lang="en">Menya Test</div>', false)
            ->assertDontSee('麺屋テスト');
        $this->get("/ja/$plural/details/{$place->id}")->assertOk()
            ->assertSee('id="detailName" class="detail" detailId="'.$place->id.'"><div>麺屋テスト <span lang="en">Menya Test</span></div>', false);
        $this->get("/$plural/details/{$place->id}")->assertOk()
            ->assertSee('id="detailName" class="detail" detailId="'.$place->id.'" lang="en"><div>Menya Test</div>', false)
            ->assertDontSee('麺屋テスト');
    }

    public function test_the_title_of_a_closed_place_with_a_japanese_name(): void
    {
        $place = $this->seedOne(Restaurant::class, RestaurantTag::class, 'ramen');
        $place->update(['name_ja' => '麺屋テスト', 'business_status' => 'CLOSED_TEMPORARILY']);
        // Both names, then the status written the Japanese way
        $this->get("/ja/restaurant/details/{$place->id}")->assertOk()
            ->assertSee('<title>'.config('app.name').' - 麺屋テスト Menya Test（', false);
    }

    public function test_the_english_only_info_page_is_noindex_in_japanese(): void
    {
        $this->get('/info/main')->assertOk()->assertDontSee('name="robots"', false)->assertDontSee('rel="alternate"', false);
        $this->get('/ja/info/main')->assertOk()
            ->assertSee('<html lang="ja"', false)
            ->assertSee('<meta name="robots" content="noindex">', false)
            ->assertSee('<div lang="en">', false)
            // The attribution of the Japanese names (OpenStreetMap licence), in the language of the page
            ->assertSee('店名の日本語表記： <a href="https://www.openstreetmap.org/copyright" lang="en">© OpenStreetMap contributors</a>', false);
        $this->get('/info/main')->assertSee('Japanese place names: <a href="https://www.openstreetmap.org/copyright" lang="en">© OpenStreetMap contributors</a>', false);
    }

    public function test_tag_label_falls_back_to_english(): void
    {
        app()->setLocale('ja');
        $this->assertSame('ラーメン', tag_label('ramen'));
        $this->assertSame('not a tag', tag_label('not a tag'));
        app()->setLocale('en');
        $this->assertSame('ramen', tag_label('ramen'));
    }

    public function test_suggestion_in_japanese(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 16, 12, 0, 0, 'Asia/Tokyo'));
        $place = Restaurant::create(['name' => 'Only one', 'type' => 'Ramen', 'lat' => 35.6586, 'lon' => 139.7016, 'score_lunch' => 3]);
        $this->get('/ja')->assertOk()->assertSee('おすすめを見る')
            ->assertSee('data-suggestion-url="'.url('/ja/suggestions').'"', false);
        // The whole sentence: the type is translated like a tag ("Ramen" -> ラーメン), the name stays English
        $sentence = trim((string) $this->get('/ja/suggestions?position=null')->assertOk()->getContent());
        $this->assertSame('<a href="'.url("/ja/restaurant/details/{$place->id}").'" lang="en">Only one</a>はいかが？おいしいラーメンのお店です。', $sentence);
        // And in English, lower case as before
        $english = trim((string) $this->get('/suggestions?position=null')->assertOk()->getContent());
        $this->assertSame('How about a nice ramen restaurant, <a href="'.url("/restaurant/details/{$place->id}").'" lang="en">Only one</a>?', $english);

        // With a Japanese name: shown on /ja without lang="en", the English name still in English
        $place->update(['name_ja' => 'ひとつだけ']);
        Cache::flush(); // the suggestion reads the places from the cache
        $sentence = trim((string) $this->get('/ja/suggestions?position=null')->assertOk()->getContent());
        $this->assertSame('<a href="'.url("/ja/restaurant/details/{$place->id}").'">ひとつだけ</a>はいかが？おいしいラーメンのお店です。', $sentence);
        $english = trim((string) $this->get('/suggestions?position=null')->assertOk()->getContent());
        $this->assertSame('How about a nice ramen restaurant, <a href="'.url("/restaurant/details/{$place->id}").'" lang="en">Only one</a>?', $english);
    }
}
