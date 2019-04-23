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
          'name'              => 'Shibuya109',
          'location'          => 'Shibuya',
          'type'              => 'Fashion',
          'points'            => 'The famous 109. Fashion for girls.',
          'experience'        => 'Multitude of small fashion stores for girls.',
          'google_maps_link'  => 'https://www.google.com/maps/place/109/@35.6595752,139.696562,17z/data=!3m2!4b1!5s0x60188ca9cd4c44b5:0xc538795a27b68b27!4m5!3m4!1s0x60188ca9cd6c1c87:0x55a2a75ba417e072!8m2!3d35.6595709!4d139.6987507',
          'official_website'  => 'https://www.shibuya109.jp',
          'score'             => 1
        ));
        $store->attachTags(['women','fashion','mall']);
        $store->fillCoordinatesFromString('35.659551, 139.698904');

        $store = Store::create(array(
          'name'              => 'Magnet by Shibuya109',
          'location'          => 'Shibuya',
          'type'              => 'Fashion',
          'points'            => 'Previously called 109men. Small shops mostly for young men\'s fashion.',
          'experience'        => 'Interesting styles, targets (very) young Japanese guys with trendy clothes and accessories. Employees are quite pushy. Not easy to find a fitting size for foreigners. Some restaurants on 7F, and an access to the rooof top, with a nice view, where it is possible to have your picture taken from way above so you appear with the legendary crossing below.',
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
          'experience'        => 'Multitudes of shops. Convenient in case of bad weather because no need to get out from the station. Rather nice food section at the basement floors. Restaurants at the 9th floor.',
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
          'google_maps_link'  => 'https://www.google.com/maps/place/B%26D/@35.658425,139.7013473,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b59ac933b79:0x31446d6ad3a599b!8m2!3d35.658425!4d139.703536',
          'official_website'  => 'https://www.bnd.co.jp/shop_detail/sibuya',
          'score'             => 1
        ));
        $store->attachTags(['sports','sneakers','shoes']);
        $store->fillCoordinatesFromString('35.658336, 139.703584');

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
        $store->attachTags(['100yen','gifts','snacks','housewares','stationery']);
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
        $store->attachTags(['100yen','gifts','snacks','housewares','stationery']);
        $store->fillCoordinatesFromString('35.661770, 139.697603');

        $store = Store::create(array(
          'name'              => 'Tokyu Hands',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Creative Gifts',
          'points'            => 'Big store offering an enormous variety of gifts, craft and... lots of stuff.',
          'experience'        => 'Nice and big, with a somewhat confusing floors system. Can find some great things in there, but be ready to pay a relatively high price.
          <ul>
              <li>A B1F: DIY creative materials</li>
              <li>A 1F: Outdoor goods</li>
              <li>A 2F: Variety & mobile</li>
              <li>A 3F: Health care</li>
              <li>A 4F: Interior (furniture and storage)</li>
              <li>A 5F: Stationery</li>
              <li>A 6F: Craft and workshop</li>
              <li>A 7F: Hint7, cafe</li>
          </ul>
          <ul>
              <li>B B1F: DIY woodwork</li>
              <li>B 1F: Wallets, watches, accessories</li>
              <li>B 2F: Anniversary</li>
              <li>B 3F: Cleaning & laundry</li>
              <li>B 4F: Interior (bed and pillows)</li>
              <li>B 5F: Stationery (office supplies)</li>
              <li>B 6F: Craft</li>
              <li>B 7F: Miniature models and hobbies</li>
          </ul>
          <ul>
              <li>C B2F: Cyclings</li>
              <li>C B1F: DIY home fixtures</li>
              <li>C 1F: Bags & travel</li>
              <li>C 2F: Beauty & cosmetic</li>
              <li>C 3F: Kitchen supplies</li>
              <li>C 4F: Interior (toilets & bath)</li>
              <li>C 5F: Stationery</li>
              <li>C 6F: Pet</li>
          </ul>',
          'google_maps_link'  => 'https://www.google.com/maps/place/Tokyu+Hands+Shibuya/@35.6607993,139.6969887,17z/data=!4m5!3m4!1s0x60188ca916447249:0x13ff108bf95bcbf1!8m2!3d35.6619892!4d139.6978121',
          'official_website'  => 'http://shibuya.tokyu-hands.co.jp/',
          'score'             => 2
        ));
        $store->attachTags(['gifts','housewares','stationery']);
        $store->fillCoordinatesFromString('35.662000, 139.697741');

        $store = Store::create(array(
          'name'              => 'Janpara',
          'location'          => 'Shibuya, Dogenzaka',
          'type'              => 'Used Electronics',
          'points'            => 'Small shop. Buy and sell second hand smartphones and electronics..',
          'experience'        => 'Interesting choice of second hand stuff such as laptops, smartphones, some heaphones, video games consoles or video cameras...',
          'google_maps_link'  => 'https://www.google.com/maps/place/%E3%81%98%E3%82%83%E3%82%93%E3%81%B1%E3%82%89+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6589056,139.6957593,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9d964652b:0xae9c85f47e4cae05!8m2!3d35.6589013!4d139.697948',
          'official_website'  => 'http://www.janpara.co.jp/',
          'score'             => 2
        ));
        $store->attachTags(['electronics','computers','phones']);
        $store->fillCoordinatesFromString('35.658948, 139.697916');

        $store = Store::create(array(
          'name'              => 'Janpara',
          'location'          => 'Shibuya, Udagawa',
          'type'              => 'Used Electronics',
          'points'            => 'Small shop, on 2 floors. Buy and sell second hand smartphones and electronics.',
          'experience'        => 'Interesting choice of second hand stuff such as laptops, smartphones, some heaphones, video games consoles or video cameras...',
          'google_maps_link'  => 'https://www.google.com/maps/place/Jun-Para+Shibuya+Udagawa/@35.6605314,139.6963436,16.75z/data=!4m5!3m4!1s0x60188ca90a37c15f:0xdecb0d4d9500cb3f!8m2!3d35.661687!4d139.6978643',
          'official_website'  => 'http://www.janpara.co.jp/',
          'score'             => 2
        ));
        $store->attachTags(['electronics','computers','phones']);
        $store->fillCoordinatesFromString('35.661688, 139.697853');

        
        $store = Store::create(array(
          'name'              => 'Mark City',
          'location'          => 'Shibuya, station',
          'type'              => 'Mall',
          'points'            => 'Big mall attached to Shibuya station.',
          'experience'        => 'Generally opened from 10am to 9pm. The mall might be tricky because of its first 3 floors are separated in 2 different buildings, the west and east malls. You might really need to <a target="blank" href="http://www.s-markcity.co.jp/foreign/floormap.html">lookup the map</a>.
          <ul>
              <li>B1F: Tokyu Food Show: lots of very nice food to buy for oneself or as a present</li>
              <li>1F: Fashion, interior, restaurants and cafes, beauty, services.</li>
              <li>2F: Fashion, interior, restaurants and cafes, shoes, services</li>
              <li>3F: Cosmetics, restaurants and cafes</li>
              <li>4F: Restaurants and cafes, shopping (fashion shoes, goods, glasses), services</li>
              <li>5F: Cafe lounge, access to offices and the hotel</li>
              <li>6F: Conference and banquet rooms</li>
              <li>7-24F: hotel rooms</li>
              <li>25F: 1 French restaurant and 1 Japanese restaurant, with nice view.</li>
          </ul>',
          'google_maps_link'  => 'https://www.google.com/maps/place/Shibuya+Mark+City/@35.6579883,139.6958671,17z/data=!3m2!4b1!5s0x60188b5631c5f5a9:0xf2c804904456f99b!4m5!3m4!1s0x60188caa6707c341:0xe0f267e59f717458!8m2!3d35.6579883!4d139.6980558',
          'official_website'  => 'http://www.s-markcity.co.jp/',
          'score'             => 1
        ));
        $store->attachTags(['mall','fashion','food']);
        $store->fillCoordinatesFromString('35.658766, 139.699672');

        $store = Store::create(array(
          'name'              => 'Seibu',
          'location'          => 'Shibuya, Seibu',
          'type'              => 'Mall',
          'points'            => 'Big spacious mall with multitudes of small luxury shops.',
          'experience'        => 'Not crowded. Spacious, with many luxury fashion brands. Prices starts at about 10 thousand yen for the simplest white T-Shirt. The mall is divided in 2 buildings called A and B, that connects to each other on some floors. You can connect to their free wifi using the password "seibu123". Tax free service is available at A 7F.
          <ul>
              <li>A B2F: Restaurants</li>
              <li>A B1F: Japanese sweets</li>
              <li>A 1F: Cosmetics</li>
              <li>A 2F: Fashion (Chanel)</li>
              <li>A 3F: Fashion (women)</li>
              <li>A 4F: Fashion (women)</li>
              <li>A 5F: Fashion (women)</li>
              <li>A 6F: Fashion (women and children)</li>
              <li>A 7F: Handcraft goods, event space, tax exemption counter</li>
              <li>A 8F: Japanese restaurants</li>
          </ul>
          <ul>
              <li>B B1F: Beauty</li>
              <li>B 1F: Luxury brands</li>
              <li>B 2F: Luxury brands</li>
              <li>B 3F: Fashion (women)</li>
              <li>B 4F: Fashion (men)</li>
              <li>B 5F: Fashion (men)</li>
              <li>B 6F: Fashion (men)</li>
              <li>B 7F: Tablewear, interior, furniture, gifts</li>
              <li>B 8F: Watches, jewelry</li>
              <li>B RoofTop: Pet shop</li>
          </ul>',
          'google_maps_link'  => 'https://www.google.com/maps/place/Seibu+Shibuya/@35.659452,139.7001252,18z/data=!3m1!5s0x60188ca85cf67879:0x8e906976d63cbfb6!4m8!1m2!2m1!1sSEIBU!3m4!1s0x0:0x5d0968ff236330a0!8m2!3d35.660616!4d139.7003089',
          'official_website'  => 'https://www.sogo-seibu.jp/foreign/welcome/',
          'score'             => 1
        ));
        $store->attachTags(['fashion','luxury','mall']);
        $store->fillCoordinatesFromString('35.660118, 139.700556');

        $store = Store::create(array(
          'name'              => 'Hikarie ShinQs',
          'location'          => 'Shibuya, Hikarie',
          'type'              => 'Mall',
          'points'            => 'Fancy mall, connected to Shibuya station.',
          'experience'        => 'Nice things. No need to go outside from the station to get there. Can get a nice view from higher floor.
          <ul>
              <li>B3F: Foods, sweets</li>
              <li>B2F: Foods, bakery, sweets</li>
              <li>B1F: Cosmetics, beauty</li>
              <li>1F: Beauty, fashion</li>
              <li>2F: Fashion</li>
              <li>3F: Career fashion</li>
              <li>4F: Casual fashion</li>
              <li>5F: Lifestyle goods</li>
              <li>6F: Restaurants</li>
              <li>7F: Restaurants</li>
              <li>8F: Restaurants</li>
              <li>11F: Restaurants</li>
          </ul>',
          'google_maps_link'  => 'https://www.google.com/maps/place/Hikarie+ShinQs+Shibuya/@35.6590292,139.7012843,17z/data=!3m2!4b1!5s0x60188b58f894f891:0x4ceb5b05dd6f9d0b!4m5!3m4!1s0x60188b5789e3c191:0x118f18fc8ef872eb!8m2!3d35.6590249!4d139.703473',
          'official_website'  => 'http://www.hikarie.jp/',
          'score'             => 1
        ));
        $store->attachTags(['mall','fashion']);
        $store->fillCoordinatesFromString('35.658929, 139.703265');

        $store = Store::create(array(
          'name'              => 'Tokyu Honten',
          'location'          => 'Shibuya, Bunkamura',
          'type'              => 'Mall',
          'points'            => 'Tokyu main department store. Luxury stuff.',
          'experience'        => 'Big spacious department store. Mostly luxury fashion and goods.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Tokyu+Honten/@35.6606425,139.6938291,17z/data=!3m2!4b1!5s0x60188cabd76c1a77:0xea9b2a49da49661e!4m5!3m4!1s0x60188cabdc972d2f:0x7e09efa541645aa5!8m2!3d35.6606382!4d139.6960178',
          'official_website'  => 'https://www.tokyu-dept.co.jp/honten/',
          'score'             => 1
        ));
        $store->attachTags(['mall','fashion','luxury','jewelry','watches']);
        $store->fillCoordinatesFromString('35.660640, 139.696345');

        $store = Store::create(array(
          'name'              => 'Lighthouse Records',
          'location'          => 'Shibuya',
          'type'              => 'Records',
          'points'            => 'Cool shop, great for electronic and house music.',
          'experience'        => 'Very easy to miss from the street, but very nice shop. Great selection of electronic music, nu disco and house.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Lighthouse+Records/@35.6581766,139.6970925,17.25z/data=!4m5!3m4!1s0x60188b56282f48a7:0x34eb8d132bc55332!8m2!3d35.658761!4d139.698286',
          'official_website'  => 'http://lighthouserecords.jp/',
          'score'             => 1
        ));
        $store->attachTags(['music','records','vinyl']);
        $store->fillCoordinatesFromString('35.658782, 139.698320');

        $store = Store::create(array(
          'name'              => 'HMV Record Shop',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Records',
          'points'            => 'Big chain of records shops.',
          'experience'        => 'Big chain, selling new and second hand music of all genre. Seems good to find some Japanese independant pop or some soul and jazz imports. Also sells some turntables, some CDs, DVDs, and books. The shop is spacious enough for comfortable shopping.',
          'google_maps_link'  => 'https://www.google.com/maps/place/HMV+record+shop+Shibuya/@35.6617985,139.6954999,17.25z/data=!4m5!3m4!1s0x60188ca9415924a3:0x81eea8f480cb05d5!8m2!3d35.6620243!4d139.6972123',
          'official_website'  => 'http://recordshop.hmv.co.jp/',
          'score'             => 1
        ));
        $store->attachTags(['music','records','vinyl']);
        $store->fillCoordinatesFromString('35.662127, 139.697295');
        
        $store = Store::create(array(
          'name'              => 'Face Records',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Records',
          'points'            => 'Great for soul, jazz and reggae on vinyl',
          'experience'        => 'Great atmosphere. Sells vinyls, but also some CDs, DVDs, and books.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Face+Record/@35.66268,139.6949487,17z/data=!3m1!4b1!4m5!3m4!1s0x60188cac07d0dd05:0x7aba033eb37ed2ce!8m2!3d35.6626757!4d139.6971374',
          'official_website'  => 'http://www.facerecords.com/',
          'score'             => 1
        ));
        $store->attachTags(['music','records','vinyl']);
        $store->fillCoordinatesFromString('35.662674, 139.697147');

        $store = Store::create(array(
          'name'              => 'Next Records',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Records',
          'points'            => 'Vinyls for funk, disco, dance or RnB.',
          'experience'        => 'Always up to date when it comes to funk, disco, dance or RnB, with imports from Europe and U.S.A. No bootlegs. About 10,000 original 12" singles. Also selling some second hand Technics turntables (and a DJ equipment shop is next door).',
          'google_maps_link'  => 'https://www.google.com/maps/place/next+records/@35.6625535,139.6950264,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca94de4afa3:0x5436e42f3cf7da0!8m2!3d35.6625492!4d139.6972151',
          'official_website'  => 'http://www.nextrecordsjapan.net/for-foreign-visitor.html',
          'score'             => 1
        ));
        $store->attachTags(['music','records','vinyl']);
        $store->fillCoordinatesFromString('35.662570, 139.697193');

        $store = Store::create(array(
          'name'              => 'Technique',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Records',
          'points'            => 'Great for electronic music on vinyl.',
          'experience'        => 'Highly specialized in house, dance, and any kind of electronic music. Small, but nice shop, where you can even have a beer, and check out flyers for upcoming DJ event in town.',
          'google_maps_link'  => 'https://www.google.com/maps/place/TECHNIQUE/@35.6609314,139.6948219,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca970e7aaab:0xbc1e41d88c7706d6!8m2!3d35.6609271!4d139.6970106',
          'official_website'  => 'https://technique.co.jp/',
          'score'             => 1
        ));
        $store->attachTags(['music','records','vinyl']);
        $store->fillCoordinatesFromString('35.660924, 139.697028');

        $store = Store::create(array(
          'name'              => 'Manhattan Records',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Records',
          'points'            => 'Awesome for rap and hip-hop.',
          'experience'        => 'Higly specilized in rap and hip-hop, with lots of American and Japanese vinyls, old and new.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Manhattan+Records/@35.6625155,139.6947012,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca94e710061:0x1d13fad0be6bdbfb!8m2!3d35.6625112!4d139.6968899',
          'official_website'  => 'http://manhattanrecords.jp/',
          'score'             => 1
        ));
        $store->attachTags(['music','records','vinyl']);
        $store->fillCoordinatesFromString('35.662505, 139.69688');

        $store = Store::create(array(
          'name'              => 'Disc Union Shibuya',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Records',
          'points'            => 'Very popular store for music and vinyls discs.',
          'experience'        => 'This store often appears in the world\'s top 10 best record shops... The first two floors have tons of used vinyl discs of every kind. Then going up there is dance and hip hop, and up again for punk, hardcore and heavy metal. In the basement is where you’ll find the some rare groove, soul and jazz. If you are a music enthusiast looking for rarities, this is a very exiting place.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Disk+Union+Record+Vinyl+Store/@35.6608982,139.6958764,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9a7bb87cd:0x116297446f5495a9!8m2!3d35.6608939!4d139.6980651',
          'official_website'  => 'http://diskunion.net/',
          'score'             => 2
        ));
        $store->attachTags(['music','records','vinyl']);
        $store->fillCoordinatesFromString('35.660934, 139.698086');
        
        $store = Store::create(array(
          'name'              => 'Tower Records Shibuya',
          'location'          => 'Shibuya, Jinnan',
          'type'              => 'Records',
          'points'            => 'Biggest records store in Japan.',
          'experience'        => '7 floors, selling mostly CDs, for all kind of music. Big, peculiar building, with another statue of Hachiko at its feet, it makes for another popular meeting points for young people.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Tower+Records+Shibuya/@35.6619233,139.6988062,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca87eba86d5:0xc878b2fe3fed54f0!8m2!3d35.661919!4d139.7009949',
          'official_website'  => 'http://tower.jp/',
          'score'             => 1
        ));
        $store->attachTags(['music','records','vinyl']);
        $store->fillCoordinatesFromString('35.661911, 139.700972');

        $store = Store::create(array(
          'name'              => 'Mont-Bell',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Outdoor',
          'points'            => 'Big store selling all kind of outdoor clothes and gear.',
          'experience'        => 'Spacious and nice, it provides a relaxing shopping experience. There are a lot of clothes and gear for moutains, snow, and cycling. Mont-Bell is a Japanese brand, making high quality stuff and selling them slightly cheaper than big names such as The North Face. There is an English version of their website here: <a href="https://en.montbell.jp/">https://en.montbell.jp/.</a>.
          <ul>
              <li>B1F: Restaurant Spice Magic</li>
              <li>1F: Accessory</li>
              <li>2F: Women and children</li>
              <li>3F: Men</li>
              <li>4F: Outdoor gears</li>
              <li>5F: Salon</li>
          </ul>',
          'google_maps_link'  => 'https://www.google.com/maps/place/mont-bell+Shibuya/@35.6623943,139.6953583,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca93864d0d7:0xa311ee2a69accd57!8m2!3d35.66239!4d139.697547',
          'official_website'  => 'http://store.montbell.jp/',
          'score'             => 1
        ));
        $store->attachTags(['outdoor','sports','clothes']);
        $store->fillCoordinatesFromString('35.662339, 139.697618');

        $store = Store::create(array(
          'name'              => 'Function Junction',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Outdoor',
          'points'            => 'Small shop, cool outdoor clothes and gear.',
          'experience'        => 'Small shop, very nice staff. Selling high quality, expensive clothes and gears from various brands. There are also some Tokyo souvenirs T-shirts and whatnot.',
          'google_maps_link'  => 'https://www.google.com/maps/place/FUNCTION+JUNCTION+TOKYO/@35.6614306,139.6964239,17z/data=!4m5!3m4!1s0x60188ca93fbd2021:0xdd16ce3769b16d28!8m2!3d35.6620372!4d139.6975833',
          'official_website'  => 'http://www.functionjunctiontokyo.com/',
          'score'             => 1
        ));
        $store->attachTags(['outdoor','sports','clothes']);
        $store->fillCoordinatesFromString('35.662035, 139.697575');        

        $store = Store::create(array(
          'name'              => 'A&F Country',
          'location'          => 'Shibuya, Hikarie',
          'type'              => 'Outdoor',
          'points'            => 'Nice outdoor shop inside the Hikarie building.',
          'experience'        => 'Nice shop at the 4th floor of Hikarie. Helpful staff. Selling high quality brand, such as Outdoor Research. Also in the same alley with small shops for The North Face and Sora, which is selling Patagonia.',
          'google_maps_link'  => 'https://www.google.com/maps/place/A%26F%E3%82%AB%E3%83%B3%E3%83%88%E3%83%AA%E3%83%BC+%E6%B8%8B%E8%B0%B7%E3%83%92%E3%82%AB%E3%83%AA%E3%82%A8ShinQs%E5%BA%97/@35.6591053,139.7015284,16.75z/data=!3m1!5s0x60188b58f894f891:0x4ceb5b05e50a8109!4m5!3m4!1s0x60188b58f9ad804b:0x8ca51d84e75fc700!8m2!3d35.6592766!4d139.7040703',
          'official_website'  => 'https://www.aandf.co.jp/stores/afcountry/shibuya_hikarie',
          'score'             => 1
        ));
        $store->attachTags(['outdoor','sports','clothes']);
        $store->fillCoordinatesFromString('35.659258, 139.704020');

        /*
        // Seems like the building is closing
        $store = Store::create(array(
          'name'              => 'Colombia sportswear',
          'location'          => 'Shibuya, Udagawacho',
          'type'              => 'Outdoor',
          'points'            => 'Famous outdoor brand.',
          'experience'        => 'Smaller than the one in Harajuku, but still, nice shop.',
          'google_maps_link'  => 'https://www.google.com/maps/place/Columbia+Sportswear+Shibuya/@35.6601673,139.6967184,16.5z/data=!4m5!3m4!1s0x60188ca9042e31f5:0x21538d45887342c3!8m2!3d35.6615303!4d139.698513',
          'official_website'  => 'https://www.columbiasports.co.jp/',
          'score'             => 1
        ));
        $store->attachTags(['outdoor','sports','clothes']);
        $store->fillCoordinatesFromString('35.661549, 139.698497');
        */

        $store = Store::create(array(
          'name'              => 'Ikebe Digital Tower',
          'location'          => 'Shibuya',
          'type'              => 'Music gear',
          'points'            => 'Sells and show stuff for DJs and home studio, keyboards and pianos.',
          'experience'        => 'Crappy, easy to miss entrance in a back street, but this is a nice shop with a lot of stuff you can see and try.
          <ul>
              <li>3F: DJs controller, turntables, mixers, speakers...</li>
              <li>5F: DAW, audio interfaces, midi controllers.../li>
              <li>6F: Mics, headphones, studio monitors and cables</li>
              <li>7F: Keyboards, synthesizers, piano and accordions</li>
          </ul>',
          'google_maps_link'  => 'https://www.google.com/maps/place/Ikebe+musical+instrument+Kenbando/@35.6574117,139.6994381,17.25z/data=!4m5!3m4!1s0x60188b576570fcd1:0x8a5b1ebf3eea54e4!8m2!3d35.6567834!4d139.7007594',
          'official_website'  => 'http://www.ikebe-digital.com/',
          'score'             => 1
        ));
        $store->attachTags(['music instruments','music gear']);
        $store->fillCoordinatesFromString('35.656830, 139.700697');
        

        /*
        ///AAA///
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

/*
nitori

8 floors

small ikea
*/
        // Loft

        // http://www.tokyopocketguide.com/tokyo/shibuya.shopping.html

        // https://www.google.com/search?rlz=1C1GGRV_enJP759JP759&ei=pT12XKL8MtGUmAXVqobQDg&q=biggest+stationery+shop+in+tokyo&oq=stationery&gs_l=psy-ab.1.0.0i71l8.0.0..341850...0.0..0.0.0.......0......gws-wiz.Q7xVS2tN4_o&safe=active&ssui=on

        // https://on-mag.fr/index.php/topaudio/musique/18317-vinyles-a-tokyo-8-disquaires-incontournables-du-quartier-de-shibuya

    }
}
