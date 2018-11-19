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
          'name'              => 'Yamada Denki LABI',
          'location'          => 'Shibuya',
          'type'              => 'Electronics',
          'points'            => 'Big store with lots of appliances, audio and video electronics, phones, cameras and games.',
          'experience'        => 'Spacious store, comfortable shopping, lots of choices.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Yamada+Denki+LABI/@35.6598002,139.6970517,18z/data=!3m2!4b1!5s0x60188ca9c7f11c9d:0x1426b2054802885b!4m5!3m4!1s0x60188ca9c8780fb7:0x9ca8fa1cb4dd1218!8m2!3d35.659798!4d139.698146',
          'official_website'  => 'http://www.yamadalabi.com/shibuya/',
          'score'             => 3
        ));
        $store->attachTags(['electronics','computers','phones','headphones','cameras','video games']);
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
        $store->attachTags(['men','fashion','mall']);
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
        $store->attachTags(['sports','shoes','sneakers']);
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
        $store->attachTags(['fashion','luxury','mall']);
        $store->fillCoordinatesFromString('35.660118, 139.700556');

        $store = Store::create(array(
          'name'              => 'Adidas',
          'location'          => 'Shibuya',
          'type'              => 'Sports',
          'points'            => 'Adidas "Brand Core" store. Nice but possibly pricey sneakers on the first floor. 2 upper floors of sports wear for men and women.',
          'experience'        => 'Nice staff. Size for shoes goes up to 30cm, maybe 30.5cm on some models. Slightly more expensive than other similar brands.',
          'google_maps_link'  => 'https://www.google.com/maps/place/adidas+Brand+Core+Store+Shibuya/@35.6587902,139.6979947,18z/data=!4m8!1m2!2m1!1sadidas!3m4!1s0x60188ca82fd3a799:0x421320b45b1ea565!8m2!3d35.6597771!4d139.6992642',
          'official_website'  => 'https://shop.adidas.jp/',
          'score'             => 2
        ));
        $store->attachTags(['sports','shoes','sneakers']);
        $store->fillCoordinatesFromString('35.659762, 139.699253');

        $store = Store::create(array(
          'name'              => 'Under Armor',
          'location'          => 'Shibuya',
          'type'              => 'Sports',
          'points'            => 'Under Armor flagship. Nice sneakers on the first floor. 2 upper floors of sports wear for men and women.',
          'experience'        => 'Kind of small room on the 1st floor for sneakers. Size for shoes goes up to 30cm.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Under+Armour+ClubHouse+Shibuya/@35.6589411,139.698767,18z/data=!4m12!1m6!3m5!1s0x60188ca82873f91d:0x383fe154199e264a!2sUnder+Armour+ClubHouse+Shibuya!8m2!3d35.6593203!4d139.6993785!3m4!1s0x60188ca82873f91d:0x383fe154199e264a!8m2!3d35.6593203!4d139.6993785',
          'official_website'  => 'https://www.underarmour.co.jp/ja-jp/shibuya.php',
          'score'             => 1
        ));
        $store->attachTags(['sports','shoes','sneakers']);
        $store->fillCoordinatesFromString('35.659352, 139.699383');

        $store = Store::create(array(
          'name'              => 'ABC Mart',
          'location'          => 'Shibuya',
          'type'              => 'Shoes',
          'points'            => 'Various sneakers from popular brands.',
          'experience'        => 'Gives an easy and nice overview of main sneakers brand\'s models. Sizes go up to 30cm on some (rare) models.',
          'google_maps_link'  => 'https://www.google.com/maps/place/ABC-MART+Grand+Stage+%E6%B8%8B%E8%B0%B7%E5%BA%97%EF%BB%BF/@35.6594121,139.6982193,18z/data=!4m5!3m4!1s0x60188ca9b45e6a51:0x48c45a8dbc85b19a!8m2!3d35.660238!4d139.698576',
          'official_website'  => 'https://www.abc-mart.net/shop/',
          'score'             => 2
        ));
        $store->attachTags(['shoes','sneakers']);
        $store->fillCoordinatesFromString('35.660326, 139.698601');        

        $store = Store::create(array(
          'name'              => 'Asics Walking',
          'location'          => 'Shibuya',
          'type'              => 'Shoes',
          'points'            => 'Dress shoes from Asics (Japanese sneakers brand).',
          'experience'        => 'Small shop selling dress shoes for men and woman. Leather, and more casual models as well. Most models go up to 28cm, some up to 30cm.',
          'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%A2%E3%82%B7%E3%83%83%E3%82%AF%E3%82%B9%E3%82%A6%E3%82%A9%E3%83%BC%E3%82%AD%E3%83%B3%E3%82%B0+%E6%B8%8B%E8%B0%B7/@35.6586334,139.7018858,17z/data=!4m8!1m2!2m1!1sasics!3m4!1s0x0:0xea2cf1b0e6c971b!8m2!3d35.6602551!4d139.7050811',
          'official_website'  => 'https://www.asics.com/jp/ja-jp/walking',
          'score'             => 1
        ));
        $store->attachTags(['shoes','fashion']);
        $store->fillCoordinatesFromString('35.660130, 139.705131');

        $store = Store::create(array(
          'name'              => 'Glitter',
          'location'          => 'Shibuya',
          'type'              => 'Shoes',
          'points'            => 'Small shoe shop',
          'experience'        => 'Dress shoes, and more casual models as well. Sizes up to 28cm.',
          'google_maps_link'  => 'https://www.google.com/maps/place/glitter+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6588939,139.7010323,17z/data=!4m5!3m4!1s0x60188b58c3d5573f:0x98707c0809e40e8f!8m2!3d35.6601753!4d139.7053399',
          'official_website'  => 'http://www.mar-c.jp/shop/shop_glitter/326/',
          'score'             => 1
        ));
        $store->attachTags(['shoes']);
        $store->fillCoordinatesFromString('35.660181, 139.705350');

        $store = Store::create(array(
          'name'              => 'Western Arms',
          'location'          => 'Shibuya',
          'type'              => 'Airsoft guns',
          'points'            => 'Airsoft guns, replicas from anime, movies and military.',
          'experience'        => 'Those replicas look nice. Mostly handguns, some rifles as well. Prices range mostly betweeen 30 and 45 thousand yen.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Western+Arms/@35.6596055,139.7037567,17z/data=!4m5!3m4!1s0x60188b58c16a7a17:0x10bb05fd1d6dfe91!8m2!3d35.6602002!4d139.7054408',
          'official_website'  => 'http://www.wa-gunnet.co.jp/',
          'score'             => 2
        ));
        $store->attachTags(['airsoft']);
        $store->fillCoordinatesFromString('35.660206, 139.705450');

        $store = Store::create(array(
          'name'              => 'Tokyu',
          'location'          => 'Shibuya',
          'type'              => 'Mall',
          'points'            => 'Mall merged with the Shibuya train station.',
          'experience'        => '2 buildings. Multitudes of shops. Convenient in case of bad weather because no need to get out from the station. Rather nice food section at the basement floors. Restaurants at the 9th floor.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Tokyu+Department+Store/@35.6587263,139.6986498,17z/data=!3m2!4b1!5s0x60188b59c00ac46b:0x89a02b538fc1dabb!4m5!3m4!1s0x60188b5818bf6c25:0xaa2d6c840dc985b9!8m2!3d35.658722!4d139.7008385',
          'official_website'  => 'https://www.tokyu-dept.co.jp/toyoko/',
          'score'             => 1
        ));
        $store->attachTags(['mall','fashion','food']);
        $store->fillCoordinatesFromString('35.658762, 139.700833');

        $store = Store::create(array(
          'name'              => 'Yamaya',
          'location'          => 'Shibuya',
          'type'              => 'World liquor',
          'points'            => 'Lots of Japanese and imported beverages. Some snacks, sweet, and other imported condiments as well.',
          'experience'        => 'Yamaya is a chain of stores selling mostly alcohol, but also a lot of imported beverages and condiments. This Yamaya is probably the most spacious I know. Very nice shopping there. They have tons of sake, wine, whiskey, beers, and many other stuff. Prices are quiet good as well. Common drinks are actually cheaper than combini or even supermarkets. It is a good place if you are looking for a nice bottle or some gifts. They provide with delivery service as well.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Yamaya+Dogenzaka/@35.6576688,139.6976373,17z/data=!4m8!1m2!2m1!1syamaya!3m4!1s0x0:0x122e5af592861839!8m2!3d35.6575447!4d139.6955234',
          'official_website'  => 'https://www.yamaya.jp/pages/store/store/413dougenn.html',
          'score'             => 2
        ));
        $store->attachTags(['drinks','gifts','food']);
        $store->fillCoordinatesFromString('35.657499, 139.695740');      

        $store = Store::create(array(
          'name'              => 'B&D',
          'location'          => 'Shibuya',
          'type'              => 'Sports',
          'points'            => 'Nice and relatively large sports shop.',
          'experience'        => 'Nice shop with a wide choice of sneakers from different brands, and equipement to test and help you to get the best fit for your feet. Also sells cloths, gears, protein packs...',
          'google_maps_link'  => 'https://www.google.co.jp/maps/place/B%26D/@35.6579884,139.7034932,17z/data=!4m12!1m6!3m5!1s0x60188bd7307763c5:0x204bb46fdc53f4f4!2zYmFyIMOgIHZpbiBDUk9JU8OJRSjvvbjvvpvvvpzvvb7vvp4p5riL6LC344K544OI44Oq44O844Og!8m2!3d35.657139!4d139.7030203!3m4!1s0x60188b59ac933b79:0x31446d6ad3a599b!8m2!3d35.6584251!4d139.7035358',
          'official_website'  => 'https://www.bnd.co.jp/shop_detail/sibuya',
          'score'             => 1
        ));
        $store->attachTags(['sports','sneakers','shoes']);
        $store->fillCoordinatesFromString('5.658365, 139.703552');

        $store = Store::create(array(
          'name'              => 'Daiso',
          'location'          => 'Shibuya',
          'type'              => '100yen shop',
          'points'            => 'All items are sold for 100 yen (+tax), and there is a lot of stuff.',
          'experience'        => 'Impressive choice of various items (gifts, housewares, snacks...) for 100 yen. 2 floors. A great place to find unexpensive souvenirs as well, as many things are very "Japanese".',
          'google_maps_link'  => 'https://www.google.com/maps/place/DAISO/@35.6614889,139.6949319,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca95fa1a9bd:0xaf5537d8a7346f7f!8m2!3d35.6614846!4d139.6971206',
          'official_website'  => 'https://www.daiso-sangyo.co.jp/shop/detail/8931',
          'score'             => 3
        ));
        $store->attachTags(['100yen','gifts','snacks','housewares']);
        $store->fillCoordinatesFromString('35.661462, 139.697177');

        $store = Store::create(array(
          'name'              => 'Can Do',
          'location'          => 'Shibuya',
          'type'              => '100yen shop',
          'points'            => 'All items are sold for 100 yen (+tax), and there is a lot of stuff.',
          'experience'        => 'Impressive choice of various items (gifts, housewares, snacks...) for 100 yen. A great place to find unexpensive souvenirs as well, as many things are very "Japanese". A bit less narrow and less crowded than Daiso.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Can+Do+Shibuya+Inokashira-dori/@35.6617661,139.6954257,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca96addf449:0x913ed7631d16f53c!8m2!3d35.6617618!4d139.6976144',
          'official_website'  => 'https://www.cando-web.co.jp/',
          'score'             => 2
        ));
        $store->attachTags(['100yen','gifts','snacks','housewares']);
        $store->fillCoordinatesFromString('35.661770, 139.697603');

        $store = Store::create(array(
          'name'              => 'Tokyu Hands',
          'location'          => 'Shibuya',
          'type'              => 'Gifts and housewares',
          'points'            => 'Big store offering an enormous variety of gifts and housewares',
          'experience'        => 'Nice and big, with a somewhat confusing floor system. Can find some great things in there, but be ready to pay a relatively high price.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Tokyu+Hands+Shibuya/@35.6607993,139.6969887,17z/data=!4m5!3m4!1s0x60188ca916447249:0x13ff108bf95bcbf1!8m2!3d35.6619892!4d139.6978121',
          'official_website'  => 'http://shibuya.tokyu-hands.co.jp/',
          'score'             => 2
        ));
        $store->attachTags(['gifts','housewares']);
        $store->fillCoordinatesFromString('35.662000, 139.697741');

        $store = Store::create(array(
          'name'              => 'Jampara',
          'location'          => 'Shibuya',
          'type'              => 'Used Electronics',
          'points'            => 'Small shop. Second hand electronics such as laptops and smartphones.',
          'experience'        => 'Interesting choice of second hand stuff such as laptops, smartphones, some heaphones, video games consoles or video cameras...',
          'google_maps_link'  => 'https://www.google.com/maps/place/%E3%81%98%E3%82%83%E3%82%93%E3%81%B1%E3%82%89+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6589056,139.6957593,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9d964652b:0xae9c85f47e4cae05!8m2!3d35.6589013!4d139.697948',
          'official_website'  => 'http://www.janpara.co.jp/',
          'score'             => 2
        ));
        $store->attachTags(['electronics','computers','phones']);
        $store->fillCoordinatesFromString('35.658948, 139.697916');

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
