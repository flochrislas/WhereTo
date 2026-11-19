<?php

namespace Tests\Architecture;

use App\Support\LocaleUrl;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Every English sentence and every tag has a translation in every other language of the site
 * (doc/12-multilingual-ui.md, "Tests that catch a missing translation").
 * There is no lang/en.json: the English sentences are the keys, written in the views and the code as __('...').
 * So the tests read them from the source, and fail with the list of what is missing.
 * Adding a language: add it to config('app.locales'), run these tests, translate what they list.
 */
class TranslationCoverageTest extends TestCase
{
    /**
     * The sentences passed to __() as a literal in resources/views and app, e.g. __('Show All') gives "Show All".
     * A __() with a variable is not seen: write the sentences as literals.
     *
     * @return array<string, list<string>> sentence => the files using it
     */
    private function sentencesInTheSource(): array
    {
        $files = array_merge(File::allFiles(resource_path('views')), File::allFiles(app_path()));
        $sentences = [];
        foreach ($files as $file) {
            // __( then a quoted string: ' or " (group 1), then anything but that quote unless escaped (group 2)
            preg_match_all('/\b__\(\s*([\'"])((?:\\\\.|(?!\1).)*)\1/s', $file->getContents(), $matches, PREG_SET_ORDER);
            foreach ($matches as [, $quote, $literal]) {
                // Undo PHP's escapes: \' and \\ in single quotes; \n, \" and the others in double quotes
                $sentence = $quote === "'" ? str_replace(["\\'", '\\\\'], ["'", '\\'], $literal) : stripcslashes($literal);
                $sentences[$sentence][] = $file->getRelativePathname();
            }
        }

        return $sentences;
    }

    /**
     * The labels of the tags, from the four tag seeders ('label' => 'ramen')
     *
     * @return list<string>
     */
    private function tagLabelsInTheSeeders(): array
    {
        $labels = [];
        foreach (File::glob(database_path('seeders/*TagsTableSeeder.php')) as $seeder) {
            preg_match_all("/'label'\\s*=>\\s*'((?:\\\\.|[^'])*)'/", (string) file_get_contents($seeder), $matches);
            $labels = array_merge($labels, $matches[1]);
        }

        return array_values(array_unique($labels));
    }

    /**
     * The restaurant types, which the lunch suggestion shows through tag_label() ("How about a nice ramen restaurant"):
     * the labels of RestaurantTypesTableSeeder and the type column of RestaurantsTableSeeder, in lower case.
     *
     * @return list<string>
     */
    private function restaurantTypesInTheSeeders(): array
    {
        preg_match_all("/'label'\\s*=>\\s*'((?:\\\\.|[^'])*)'/", (string) file_get_contents(database_path('seeders/RestaurantTypesTableSeeder.php')), $labels);
        preg_match_all("/'type'\\s*=>\\s*'((?:\\\\.|[^'])*)'/", (string) file_get_contents(database_path('seeders/RestaurantsTableSeeder.php')), $types);
        $all = array_map(fn (string $type) => strtolower($type), array_merge($labels[1], $types[1]));

        // "xxxxx": a placeholder value in the data, not a word to translate (it shows as is)
        return array_values(array_diff(array_unique($all), ['xxxxx']));
    }

    /**
     * Sentences whose translation may leave out a placeholder, with the reason. Every other placeholder carries data
     * (a number, a name, a link) that must reach the page in every language.
     */
    private const PLACEHOLDERS_THAT_MAY_BE_DROPPED = [
        // :app is the site name, already in the title of the page: a language may write "VIP area, coming later"
        'VIP area of :app, coming later.' => [':app'],
    ];

    /** @return list<array{string}> one case per language other than the default, e.g. [['ja']] */
    public static function otherLocales(): array
    {
        // A data provider runs before the application starts: read the config file itself
        $config = require __DIR__.'/../../config/app.php';

        return array_map(fn ($locale) => [$locale], array_slice($config['locales'], 1));
    }

    public function test_the_scan_finds_the_sentences_and_the_tags(): void
    {
        // Guards the two scans themselves: a broken regex would find nothing and pass every test below
        $this->assertArrayHasKey('Show All', $this->sentencesInTheSource());
        $this->assertArrayHasKey("It's about lunch time!", $this->sentencesInTheSource());
        $this->assertContains('ramen', $this->tagLabelsInTheSeeders());
        $this->assertContains('CD/DVD', $this->tagLabelsInTheSeeders());
        $this->assertContains('japanese curry', $this->restaurantTypesInTheSeeders());
        $this->assertNotEmpty($this->otherLocales(), 'config/app.php must list English and at least one other language');
        $this->assertSame(LocaleUrl::locales(), array_merge([LocaleUrl::default()], array_column($this->otherLocales(), 0)));
    }

    #[DataProvider('otherLocales')]
    public function test_every_sentence_is_translated(string $locale): void
    {
        $file = lang_path($locale.'.json');
        $this->assertFileExists($file, "lang/$locale.json is missing");
        $translations = json_decode((string) file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);

        $missing = [];
        foreach ($this->sentencesInTheSource() as $sentence => $usedIn) {
            if (! is_string($translations[$sentence] ?? null) || $translations[$sentence] === '') {
                $missing[] = '"'.$sentence.'" ('.implode(', ', array_unique($usedIn)).')';
            }
        }
        $this->assertSame([], $missing, "Sentences without a translation in lang/$locale.json:\n".implode("\n", $missing));
    }

    #[DataProvider('otherLocales')]
    public function test_translations_keep_the_placeholders(string $locale): void
    {
        // "Show :count results" must keep :count, or the number disappears from the page (allowed exceptions above);
        // and a translation must not invent one: ":counts" or a typo would print as is
        $translations = json_decode((string) file_get_contents(lang_path($locale.'.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($translations as $sentence => $translation) {
            // :name, :Name and :NAME are the same placeholder for Laravel (the case of the replacement changes)
            preg_match_all('/:[a-z]+/i', $sentence, $inKey);
            preg_match_all('/:[a-z]+/i', $translation, $inTranslation);
            $key = array_unique(array_map('strtolower', $inKey[0]));
            $translated = array_unique(array_map('strtolower', $inTranslation[0]));
            $mayDrop = self::PLACEHOLDERS_THAT_MAY_BE_DROPPED[$sentence] ?? [];
            foreach (array_diff($key, $translated, $mayDrop) as $lost) {
                $this->fail("lang/$locale.json: \"$sentence\" lost $lost");
            }
            foreach (array_diff($translated, $key) as $unknown) {
                $this->fail("lang/$locale.json: \"$sentence\" has $unknown, which the sentence does not have");
            }
        }
        $this->assertNotEmpty($translations);
    }

    #[DataProvider('otherLocales')]
    public function test_no_translation_is_left_unused(string $locale): void
    {
        // A sentence changed in a view leaves its old translation behind: remove it from the file
        $translations = json_decode((string) file_get_contents(lang_path($locale.'.json')), true, flags: JSON_THROW_ON_ERROR);
        $unused = array_diff(array_keys($translations), array_keys($this->sentencesInTheSource()));
        $this->assertSame([], array_values($unused), "Translations of lang/$locale.json that no __() uses any more");
    }

    #[DataProvider('otherLocales')]
    public function test_every_tag_is_translated(string $locale): void
    {
        $file = lang_path($locale.'/tags.php');
        $this->assertFileExists($file, "lang/$locale/tags.php is missing");
        $tags = require $file;

        $words = array_unique(array_merge($this->tagLabelsInTheSeeders(), $this->restaurantTypesInTheSeeders()));
        $missing = array_filter($words, fn ($label) => ! is_string($tags[$label] ?? null) || $tags[$label] === '');
        $this->assertSame([], array_values($missing), "Tags and restaurant types without a translation in lang/$locale/tags.php");

        // The other way: an entry that is neither a tag nor a type (a tag renamed in a seeder) is left behind
        $unused = array_diff(array_keys($tags), $words);
        $this->assertSame([], array_values($unused), "Entries of lang/$locale/tags.php that no seeder uses");
    }
}
