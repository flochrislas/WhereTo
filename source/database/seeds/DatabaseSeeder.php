<?php

use Illuminate\Database\Seeder;

/**
* Register seeders here in order to run them
*/
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RestaurantTypesTableSeeder::class);
        $this->call(RestaurantTagsTableSeeder::class);
        $this->call(RestaurantsTableSeeder::class);
        $this->call(StoreTagsTableSeeder::class);
        $this->call(StoresTableSeeder::class);
        $this->call(BarTagsTableSeeder::class);
        $this->call(BarsTableSeeder::class);
        $this->call(KaraokeTagsTableSeeder::class);
        $this->call(KaraokesTableSeeder::class);
        // Clear the cache
        \Cache::flush();
    }
}
