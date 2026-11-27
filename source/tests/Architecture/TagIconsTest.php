<?php

namespace Tests\Architecture;

use Tests\TestCase;

/**
 * The table of the tag icons (config/tag_icons.php) against the sprite and the tags (doc/13-tag-icons.md):
 * a typing mistake in a label or in a symbol id would only show as a missing picture on a page.
 */
class TagIconsTest extends TestCase
{
    public function test_every_icon_is_a_symbol_of_the_sprite(): void
    {
        preg_match_all('/<symbol id="([^"]+)"/', (string) file_get_contents(public_path('icons/tags.svg')), $found);
        $missing = array_diff(array_unique(array_values(config('tag_icons'))), $found[1]);
        $this->assertSame([], array_values($missing), 'Icons of config/tag_icons.php that public/icons/tags.svg does not have (run tools/tag-icons/build.mjs)');
    }

    public function test_every_label_with_an_icon_is_a_tag_of_the_seeders(): void
    {
        $labels = [];
        foreach (glob(database_path('seeders/*TagsTableSeeder.php')) ?: [] as $file) {
            preg_match_all("/'label'\s*=>\s*'([^']+)'/", (string) file_get_contents($file), $found);
            $labels = array_merge($labels, $found[1]);
        }
        $this->assertNotEmpty($labels);
        $unknown = array_diff(array_keys(config('tag_icons')), $labels);
        $this->assertSame([], array_values($unknown), 'Labels of config/tag_icons.php that are not a tag of any seeder');
    }

    public function test_the_licence_of_the_icon_set_is_published_with_the_sprite(): void
    {
        $this->assertFileExists(public_path('icons/LICENSE-fluent-emoji.txt'));
    }
}
