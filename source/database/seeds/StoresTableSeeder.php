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
          'points'            => 'Japanese sports wear. Specialized in sneakers using gel technology.',
          'experience'        => 'Spacious store, comfortable shopping. Great Japanese sneakers. Sizes up to 32cm.',
          'google_maps_link'  => 'https://www.google.com/maps/place/ASICS+Harajuku+Flagship+Store/@35.6699328,139.7036335,17z/data=!4m8!1m2!2m1!1sAsics!3m4!1s0x60188ca4fac54abd:0xdac77910b1a5d314!8m2!3d35.6703773!4d139.7066161',
          'official_website'  => 'https://www.asics.com/jp/ja-jp/store/AJP-A09014A/%E3%82%A2%E3%82%B7%E3%83%83%E3%82%AF%E3%82%B9%E5%8E%9F%E5%AE%BF%E3%83%95%E3%83%A9%E3%83%83%E3%82%B0%E3%82%B7%E3%83%83%E3%83%97',
          'score'             => 3
        ));
        $store->attachTags(['sports','shoes','sneakers']);
        $store->fillCoordinatesFromString('35.670367, 139.706605');

        $store = Store::create(array(
          'name'              => '109 men',
          'location'          => 'Shibuya',
          'type'              => 'Fashion',
          'points'            => 'Small shops mostly for young men\'s fashion.',
          'experience'        => 'Interesting styles, targets (very) young Japanese guys with trendy clothes and accessories. Employees are quite pushy. Not easy to find a fitting size for foreigners. Some restaurants on 7F, and an access to the rooof top, wher eit is possible to have your picture taken from way above so you appear with the legendary crossing below.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Magnet+by+Shibuya+109/@35.6583877,139.7002794,18z/data=!4m5!3m4!1s0x0:0x6c103e05d0fa17a7!8m2!3d35.6597803!4d139.7009392',
          'official_website'  => 'https://www.shibuya109.jp/MAGNET/',
          'score'             => 1
        ));
        $store->attachTags(['men','fashion']);
        $store->fillCoordinatesFromString('35.659702, 139.700843');

        $store = Store::create(array(
          'name'              => 'Asics Tiger',
          'location'          => 'Shibuya',
          'type'              => 'Shoes',
          'points'            => 'Nice small shop for this cool Japanese sports brand. Great sneakers and some clothes. Style between Onitsuka and Asics.',
          'experience'        => 'Very nice. Friendly staff (English OK). Size up to 31cm.',
          'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%A2%E3%82%B7%E3%83%83%E3%82%AF%E3%82%B9%E3%82%BF%E3%82%A4%E3%82%AC%E3%83%BC%E6%B8%8B%E8%B0%B7(ASICSTIGER+SHIBUYA)/@35.6605018,139.7001443,18z/data=!4m5!3m4!1s0x60188ca8669cf941:0x62b0ed59c5df6ea2!8m2!3d35.6614803!4d139.7003428',
          'official_website'  => 'https://www.asicstiger.com/jp/ja-jp/store/asicstiger-shibuya',
          'score'             => 3
        ));
        $store->attachTags(['sports','shoes','sneakers']);
        $store->fillCoordinatesFromString('35.661466, 139.700333');

        $store = Store::create(array(
          'name'              => 'Onitsuka Tiger',
          'location'          => 'Shibuya',
          'type'              => 'Shoes',
          'points'            => 'Nice small shop for this cool Japanese sports brand. Great sneakers and some clothes.',
          'experience'        => 'Very nice. Friendly staff (English OK). Size up to 31cm. Don\'t forget to visit the second floor as well.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Onitsuka+Tiger+Shibuya/@35.6605388,139.7003651,18z/data=!4m5!3m4!1s0x60188ca864760f11:0x3496cc8b96beb28f!8m2!3d35.6613909!4d139.7004107',
          'official_website'  => 'https://www.onitsukatiger.com/jp/ja-jp',
          'score'             => 3
        ));
        $store->attachTags(['sports','shoes','sneakers');
        $store->fillCoordinatesFromString('35.661390, 139.700407');

        $store = Store::create(array(
          'name'              => 'Seibu',
          'location'          => 'Shibuya',
          'type'              => 'Fashion',
          'points'            => 'Big spacious mall with multitudes of small luxury shops.',
          'experience'        => 'Not crowded. Spacious, with many luxury fashion brands. Prices starts at about 10 thousand yen for the simplest white T-Shirt. The mall is divided in 2 buildings called A and B.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Seibu+Shibuya/@35.659452,139.7001252,18z/data=!3m1!5s0x60188ca85cf67879:0x8e906976d63cbfb6!4m8!1m2!2m1!1sSEIBU!3m4!1s0x0:0x5d0968ff236330a0!8m2!3d35.660616!4d139.7003089',
          'official_website'  => 'https://www.sogo-seibu.jp/foreign/welcome/',
          'score'             => 1
        ));
        $store->attachTags(['fashion','luxury']);
        $store->fillCoordinatesFromString('35.660118, 139.700556');

        /*
        $store = Store::create(array(
          'name'              => 'xxxx',
          'location'          => 'Shibuya',
          'type'              => 'xxxx',
          'points'            => 'xxxx',
          'experience'        => 'xxxx',
          'google_maps_link'  => 'xxxx',
          'official_website'  => 'xxxx',
          'score'             => 1
        ));
        $store->attachTags(['xxxx']);
        $store->fillCoordinatesFromString('xxxx');
        */

    }
}
