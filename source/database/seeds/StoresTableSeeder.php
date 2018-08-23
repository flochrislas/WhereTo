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

        $store = Store::create(array(
          'name'              => 'Asics Flagship',
          'location'          => 'Harajuku',
          'type'              => 'Sports',
          'points'            => 'Japanese brand of sports wear. Specialized in sneakers using gel technology.',
          'experience'        => 'Spacious store, comfortable shopping. Great Japanese sneakers.',
          'google_maps_link'  => 'https://www.google.com/maps/place/ASICS+Harajuku+Flagship+Store/@35.6699328,139.7036335,17z/data=!4m8!1m2!2m1!1sAsics!3m4!1s0x60188ca4fac54abd:0xdac77910b1a5d314!8m2!3d35.6703773!4d139.7066161',
          'official_website'  => 'https://www.asics.com/jp/ja-jp/store/AJP-A09014A/%E3%82%A2%E3%82%B7%E3%83%83%E3%82%AF%E3%82%B9%E5%8E%9F%E5%AE%BF%E3%83%95%E3%83%A9%E3%83%83%E3%82%B0%E3%82%B7%E3%83%83%E3%83%97',
          'score'             => 3
        ));
        $store->attachTags(['sports','shoes','sneakers']);
        $store->fillCoordinatesFromString('35.658545, 139.698468');

         // https://www.google.com/maps/place/ASICS+Harajuku+Flagship+Store/@35.6699328,139.7036335,17z/data=!4m8!1m2!2m1!1sAsics!3m4!1s0x60188ca4fac54abd:0xdac77910b1a5d314!8m2!3d35.6703773!4d139.7066161


    }
}
