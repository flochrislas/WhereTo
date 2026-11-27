<?php

namespace Tests\Architecture;

use Tests\TestCase;

/**
 * The tag seeders: a label is the name of a tag for the whole app (the search, the translations, the icons).
 * Places are attached to their tags by label, so a label written twice makes two tags that every such place
 * gets: two identical chips in the filter and in the details.
 */
class TagSeedersTest extends TestCase
{
    public function test_no_tag_seeder_creates_the_same_label_twice(): void
    {
        $files = glob(database_path('seeders/*TagsTableSeeder.php')) ?: [];
        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            preg_match_all("/'label'\s*=>\s*'([^']+)'/", (string) file_get_contents($file), $found);
            $twice = array_keys(array_filter(array_count_values($found[1]), fn (int $count): bool => $count > 1));
            $this->assertSame([], $twice, basename($file).' creates these labels more than once');
        }
    }
}
