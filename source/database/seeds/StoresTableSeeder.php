<?php

use Illuminate\Database\Seeder;
use App\Store;

/**
* Stores, shops, retailers...
*/
class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear
        DB::table('stores')->delete();

        // Fill

        $store = Store::create(array(
          'name'              => 'Yamada Denki',
          'location'          => 'Shibuya',
          'type'              => 'Electronics',
          'points'            => 'Big store with lots of appliances.',
          'experience'        => 'Spacious store, comfortable shopping, lots of choices.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Yamada+Denki+LABI/@35.6598002,139.6970517,18z/data=!3m2!4b1!5s0x60188ca9c7f11c9d:0x1426b2054802885b!4m5!3m4!1s0x60188ca9c8780fb7:0x9ca8fa1cb4dd1218!8m2!3d35.659798!4d139.698146',
          'official_website'  => 'http://www.yamadalabi.com/shibuya/',
          'score'             => 3
        ));
        $store->attachTags(['electronics']);
        $store->fillCoordinatesFromString('35.658545, 139.698468');

    }
}
