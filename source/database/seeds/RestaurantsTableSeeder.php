<?php

use Illuminate\Database\Seeder;
use App\Restaurant;
use App\RestaurantTag;

/**
 * Seeds restaurants.
 * https://laravel.com/docs/master/seeding
 */
class RestaurantsTableSeeder extends Seeder
{
    /**
     * Fill the restaurants table
     * Tags are added with via a method.
     * Coordiantes can be added by running a method that reads the Google Maps URL
     *
     * @return void
     */
    public function run()
    {
        DB::table('restaurants')->delete();

        // DB method
        /*
        DB::table('restaurants')->insert([
            'name'              => str_random(10),
            'email'             => str_random(10).'@gmail.com',
            'password'          => bcrypt('secret'),
        ]);
        */

        $resto = Restaurant::create(array(
            'name'              => 'Hakata Furyu',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 800,
            'points'            => 'Tonkostsu miso available, up to 2 free kaidama, choice soft/hard for the noodles',
            'experience'        => 'Very good ramen, strong taste, good price, not very confortable (narrow counter, crappy pop music...)',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Hakata+Furyu/@35.6585187,139.6983918,21z/data=!4m5!3m4!1s0x60188b5629325fa5:0x59eb6bd6fac9b856!8m2!3d35.6585287!4d139.6984651',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13160788/',
            'official_website'  => 'https://fu-ryu.net/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','miso']);
        $resto->fillCoordinatesFromString('35.658545, 139.698468');

        $resto = Restaurant::create(array(
            'name'              => 'Kindenmaru',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'A regular ramen restaurant. Good ramen, but nothing really noteworthy.',
            'experience'        => 'Good ramen, slightly more confortable than Hakata Furyu, but slightly less tasty',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Ramen+Restaurant/@35.6583502,139.6985862,17.54z/data=!4m8!1m2!2m1!1sramen!3m4!1s0x0:0xc4dc5dbbf0f11f0c!8m2!3d35.658872!4d139.698187',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13050657/',
            'official_website'  => null,
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles']);
        $resto->fillCoordinatesFromString('35.659122, 139.698168');

        $resto = Restaurant::create(array(
            'name'              => 'Hikarihishio',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'A regular ramen restaurant. Nothing noteworthy.',
            'experience'        => 'Decent ramen, I did not like much the meat which was thin and containing too much fat for my enjoyment.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E5%85%89%E9%86%A4/@35.6585586,139.698453,21z/data=!4m8!1m2!2m1!1sramen!3m4!1s0x0:0x67f08a7325ab57c2!8m2!3d35.658574!4d139.698606',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13069220/',
            'official_website'  => null,
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles']);
        $resto->fillCoordinatesFromString('35.658591, 139.698608');

        $resto = Restaurant::create(array(
            'name'              => 'Nanashi tonkotsuhen',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 800,
            'points'            => 'Tonkotsu available, a bit more confortable than the average ramen restaurant (larger counter, quiet jazz music).',
            'experience'        => 'Decent ramen, but taste too light for me (I tried the awakoku aji tamago ramen).',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Nanashi+Ramen/@35.6585975,139.6988097,17.5z/data=!4m12!1m6!3m5!1s0x60188ca9d9c63a9f:0x35786ce804bf31d7!2sNanashi+Ramen!8m2!3d35.6588803!4d139.6980901!3m4!1s0x60188ca9d9c63a9f:0x35786ce804bf31d7!8m2!3d35.6588803!4d139.6980901',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13014309/',
            'official_website'  => 'http://www.nanashi-food.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['ramen','noodles','tonkotsu']);
        $resto->fillCoordinatesFromString('35.659177, 139.698102');

        $resto = Restaurant::create(array(
            'name'              => 'Denzo',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'Miso available (red, white and black), a bit more confortable than the average ramen restaurant (larger counter). Jazz music, but not so quiet. Some small tables are also available. No customization / question asked for the ramens.',
            'experience'        => 'Disapointing: maybe decent ramen, but taste way too light for me. Could not feel the miso, men were a bit thick and the yellow kind (not my favorite). Meat was good.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E5%91%B3%E5%99%8C%E9%BA%BA%E5%87%A6+%E4%BC%9D%E8%94%B5+%E6%B8%8B%E8%B0%B7%E3%82%BB%E3%83%B3%E3%82%BF%E3%83%BC%E8%A1%97%E5%BA%97/@35.6588444,139.6977058,17z/data=!4m5!3m4!1s0x60188ca9b10fe35b:0x6fcec3fc7221b07f!8m2!3d35.6603328!4d139.6984279',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13178158/',
            'official_website'  => 'http://www.gmfoods.co.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 0,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles']);
        $resto->fillCoordinatesFromString('35.660566, 139.698425');

        $resto = Restaurant::create(array(
            'name'              => 'Ippudo',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Ramen',
            'lunch_price'       => 800,
            'points'            => 'Fairly customizable ramen, one free kaidama available. Feels "cleaner" that regular ramen places.',
            'experience'        => 'Very good ramen. Good miso.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/IPPUDO+SHIROMARU+BASE+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6603496,139.6970402,17z/data=!4m12!1m6!3m5!1s0x60188ca849c5a249:0xaa481a46e532685c!2zSVBQVURPIFNISVJPTUFSVSBCQVNFIOa4i-iwt-W6lw!8m2!3d35.6603496!4d139.6992289!3m4!1s0x60188ca849c5a249:0xaa481a46e532685c!8m2!3d35.6603496!4d139.6992289',
            'tabelog_link'      => 'https://tabelog.com/en/tokyo/A1303/A130301/13176753/',
            'official_website'  => 'http://www.ippudo.com/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','miso','tonkotsu']);
        $resto->fillCoordinatesFromString('35.660598, 139.699240');

        $resto = Restaurant::create(array(
            'name'              => 'Burger King',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'burger',
            'lunch_price'       => 900,
            'points'            => 'Fast-food. Good affordable burgers.',
            'experience'        => 'I think the burgers are very good for the price. I like to seat in front of the windows bay on the second floor, that gives a view on the main street.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Burger+King/@35.6599084,139.6967043,16.25z/data=!4m12!1m6!3m5!1s0x60188ca84b0da8d5:0x369a2c854f4f93f!2sBurger+King!8m2!3d35.6603857!4d139.6989233!3m4!1s0x60188ca84b0da8d5:0x369a2c854f4f93f!8m2!3d35.6603857!4d139.6989233',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13059726/',
            'official_website'  => 'https://www.burgerkingjapan.co.jp/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger', 'fast food']);
        $resto->fillCoordinatesFromString('35.660876, 139.698979');

        $resto = Restaurant::create(array(
            'name'              => 'Wendy\'s first kitchen',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Burger',
            'lunch_price'       => 1000,
            'points'            => 'Various stuff, but mostly famous for burgers. Interior slightly better than average similar chain (better than BK). Price slightly more expensive.',
            'experience'        => 'Good burgers. Other dishes also available. To me the burgers are similar to what you can find at Burger King, but more expensive. ',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Wendy\'s+First+Kitchen+Shibuya+Centergai/@35.6601241,139.6969061,17z/data=!4m12!1m6!3m5!1s0x60188ca84a78fb55:0x538d580460ef2bb7!2sWendy\'s+First+Kitchen+Shibuya+Centergai!8m2!3d35.6601241!4d139.6990948!3m4!1s0x60188ca84a78fb55:0x538d580460ef2bb7!8m2!3d35.6601241!4d139.6990948',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13203394/',
            'official_website'  => 'https://wendys-firstkitchen.co.jp/shop/map.php?shopid=14',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger', 'fast food']);
        $resto->fillCoordinatesFromString('35.660390, 139.699116');

        $resto = Restaurant::create(array(
            'name'              => 'Taco Bell',
            'location'          => 'Shibuya',
            'type'              => 'Tacos',
            'lunch_price'       => 900,
            'points'            => 'Mexican style fast food, mostly buritos and tacos. Possibility to choose beef porc or chicken for the meat.',
            'experience'        => 'Quite good, but it could be cheaper.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Taco+Bell+Shibuya+Dogenzaka/@35.6599248,139.6954865,17z/data=!4m12!1m6!3m5!1s0x60188ca9be0b957f:0xa34dd8d7530b5518!2sTaco+Bell+Shibuya+Dogenzaka!8m2!3d35.6599248!4d139.6976752!3m4!1s0x60188ca9be0b957f:0xa34dd8d7530b5518!8m2!3d35.6599248!4d139.6976752',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13180395/',
            'official_website'  => 'http://tacobell.co.jp/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['mexican', 'fast food']);
        $resto->fillCoordinatesFromString('35.660199, 139.697664');

        $resto = Restaurant::create(array(
            'name'              => 'Kebab Cafe',
            'location'          => 'Shibuya',
            'type'              => 'Turk',
            'lunch_price'       => 900,
            'points'            => 'Popular turkish dishes such as kebab, possibility to eat inside on table. Dishes come on a plate, and with soup and tea.',
            'experience'        => 'Very good food in decent quantity, despite a rather cheap price. The place itself is not very confortable (narrow space).',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Kebab+Cafe/@35.660247,139.6970756,21z/data=!4m5!3m4!1s0x60188ca99be93651:0x461c229b06865bd9!8m2!3d35.6603228!4d139.6971273',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13120406/',
            'official_website'  => null,
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['turk']);
        $resto->fillCoordinatesFromString('35.660339, 139.697128');

        $resto = Restaurant::create(array(
            'name'              => 'Coco Ichi',
            'location'          => 'Shibuya, Cerulean Tower',
            'type'              => 'Japanese curry',
            'lunch_price'       => 900,
            'points'            => 'Highly customizable japanese curry. Very delicious. Counter only.',
            'experience'        => 'Once you find the right combination for you, it can be really delicious. I personally have "chicken katsu (deepfried) curry + nasu (eggplants) + cheese, ichi-kara (spicy level 1)". English menu is available.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/CoCo%E5%A3%B1%E7%95%AA%E5%B1%8B+%E6%B8%8B%E8%B0%B7%E5%8C%BA%E6%A1%9C%E4%B8%98%E7%94%BA%E5%BA%97/@35.6569245,139.6982403,17z/data=!4m12!1m6!3m5!1s0x60188b579e0464dd:0x5838a075cb9595d6!2zQ29Db-WjseeVquWxiyDmuIvosLfljLrmoZzkuJjnlLrlupc!8m2!3d35.6569245!4d139.700429!3m4!1s0x60188b579e0464dd:0x5838a075cb9595d6!8m2!3d35.6569245!4d139.700429',
            'tabelog_link'      => null,
            'official_website'  => 'http://tenpo.ichibanya.co.jp/search/shop/pc/detail.php?SCODE=2678',
            'score_lunch'       => 4,
            'score_place'       => 1,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.657260, 139.700408');

        $resto = Restaurant::create(array(
            'name'              => 'Coco Ichi',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Japanese curry',
            'lunch_price'       => 900,
            'points'            => 'Highly customizable japanese curry. Very delicious. Counter and tables.',
            'experience'        => 'Once you find the right combination for you, it can be really delicious. I personally have "chicken katsu (deepfried) curry + nasu (eggplants) + cheese, ichi-kara (spicy level 1)". English menu is available.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Curry+House+CoCo+Ichibanya+Shibuya-ku+Udagawach%C5%8D/@35.6598584,139.6987342,17.75z/data=!4m12!1m6!3m5!1s0x60188ca847c5a6d3:0xf060181e5db80755!2sCurry+House+CoCo+Ichibanya+Shibuya-ku+Udagawach%C5%8D!8m2!3d35.6605355!4d139.6994646!3m4!1s0x0:0xf060181e5db80755!8m2!3d35.6605352!4d139.6994649',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13059710/',
            'official_website'  => 'http://tenpo.ichibanya.co.jp/search/shop/pc/detail.php?SCODE=2098',
            'score_lunch'       => 4,
            'score_place'       => 1,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.660804, 139.699485');

        $resto = Restaurant::create(array(
            'name'              => 'Jonathan\'s',
            'location'          => 'Shibuya, Cerulean',
            'type'              => 'Family restaurant',
            'lunch_price'       => 900,
            'points'            => 'wide choice of dishes, also deserts, comfortable',
            'experience'        => 'good',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Jonathan\'s/@35.6555207,139.6999556,18.75z/data=!4m12!1m6!3m5!1s0x60188b5ba1d0dd99:0xdad1c25747467595!2sJonathan\'s!8m2!3d35.654701!4d139.705094!3m4!1s0x60188b5745d1a4dd:0xb23984f5a40be1ec!8m2!3d35.6557358!4d139.700456',
            'tabelog_link'      => '',
            'official_website'  => 'http://www.skylark.co.jp/jonathan/index.html',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['western']);
        $resto->fillCoordinatesFromString('35.655863, 139.700487');

        $resto = Restaurant::create(array(
            'name'              => 'Standing pizza bar Cona',
            'location'          => 'not far from ramen street',
            'type'              => 'Pizzeria',
            'lunch_price'       => 500,
            'points'            => 'Standing at the counter (no chairs). Very cheap, light pizza.',
            'experience'        => 'Decent pizza for a super cheap 500 yen comes with a small salad and a drink. Pizza is a bit small so better be not too much hungry.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/CONA+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6589355,139.6984178,18z/data=!4m5!3m4!1s0x60188b562b7be10f:0x5deeb948e867ca8!8m2!3d35.6586609!4d139.6986567',
            'tabelog_link'      => '',
            'official_website'  => 'http://www.candy-box.jp/shop/cona/',
            'score_lunch'       => 1,
            'score_place'       => 0,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['pizza']);
        $resto->fillCoordinatesFromString('35.658785, 139.698675');

        $resto = Restaurant::create(array(
            'name'              => 'Ayung Teras',
            'location'          => 'Shibuya',
            'type'              => 'Indonesian',
            'points'            => 'nice table, nie restaurant, decent food and drinks',
            'experience'        => 'decent. had a nice evening there dining with 4 people',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Ayung+Teras/@35.6552693,139.7003887,19.75z/data=!4m5!3m4!1s0x60188b574c9611e9:0x46aa4f4ee0d6c8e1!8m2!3d35.6550962!4d139.7003701',
            'tabelog_link'      => '',
            'official_website'  => 'http://www.ayungteras.com/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['indonesian']);
        $resto->fillCoordinatesFromString('35.655166, 139.700365');

        $resto = Restaurant::create(array(
            'name'              => 'M-SPO cafe dining',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'pasta',
            'lunch_price'       => 500,
            'points'            => 'super cheap pizza or pasta, wide and dark sports bar (no windows) with sports video playing on many walls.',
            'experience'        => 'I don\'t really like this dark, no windows bar atmosphere, playing cheesy music, and I don\'t enjoy watching sports on TV. The pizza marguerita was pretty much tasteless. Other than that service fast, table and room spacious, and super cheap...',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/M-SPO+cafe+dining/@35.6591098,139.6984993,17z/data=!4m5!3m4!1s0x60188ca910dcc451:0x66845d8439f43f38!8m2!3d35.6617719!4d139.6980353',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['pizza', 'pasta']);
        $resto->fillCoordinatesFromString('35.662026, 139.698027');

        $resto = Restaurant::create(array(
            'name'              => 'Go Go Curry',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'japanese curry',
            'lunch_price'       => 850,
            'points'            => 'chain restaurant a bit similar to cocoichi. customizable japanese curry, counter and small tables, basement floor',
            'experience'        => 'Good alternative to cocoichi. Less customizable, less options, but less expensive as well. Not really spicy, but good japanese curry. I enjoyed my chiken katsu with cheese. Basement is not very glamorous, but for a lunch it\'s OK. Loyalty System: Point card. 5 times: free toping. 10 times: free curry.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Go+Go+Curry+Shibuya-Udagawach%C5%8D/@35.6591408,139.6988359,17.25z/data=!4m5!3m4!1s0x60188ca915cae327:0x1ff30025a0e03069!8m2!3d35.6618738!4d139.6977677',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.662120, 139.697780');

        $resto = Restaurant::create(array(
            'name'              => 'Nice Pizza/Pasta restaurant',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'pizza/pasta/salad',
            'lunch_price'       => 1000,
            'points'            => '9th floor with nice large bay windows, spacious room with large tables. Real pizza oven. 1000yen menu set with a drink, bowl of soup, large salad, and large pizza. music playing, boom boom type, a bit loud but OK if you are alone.',
            'experience'        => 'loved the room. fast service. Taste was bland (marguerita). The deco is all fine except for that reindeer\'s head sprouting out of a column for no apparent reason',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['pizza', 'pasta']);
        $resto->fillCoordinatesFromString('35.661657, 139.697616');

        $resto = Restaurant::create(array(
            'name'              => 'Matsuya',
            'location'          => 'down the street from the office , with music shop, amp and drums, Floor: 1F, Windows: yes',
            'type'              => 'Japanese fast food',
            'lunch_price'       => 600,
            'points'            => 'Fast and good. Has counter and tables. Choice of curry or gyudon dishes.',
            'experience'        => 'I got a hamburger curry, it was really cheap and really good. Came fast too. Perfect for a quick meal, but not very glamorous.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Matsuya/@35.6590574,139.6984172,17z/data=!4m5!3m4!1s0x60188b59d831819b:0x71cfd0feb05de6ba!8m2!3d35.6571851!4d139.7015798',
            'tabelog_link'      => '',
            'official_website'  => 'https://www.matsuyafoods.co.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese', 'fast food', 'japanese curry']);
        $resto->fillCoordinatesFromString('35.657202, 139.701579');

        $resto = Restaurant::create(array(
            'name'              => 'Mazesoba Shichi',
            'location'          => 'Shibuya',
            'type'              => 'Mazesoba',
            'lunch_price'       => 800,
            'points'            => 'Special ramen without soup.',
            'experience'        => 'Mind blowingly delicious. Meat is awesome, smokey taste, and in large quantity. The shoyu base sauce is delicious. Large counter. Japanese reggai music, not too loud. Extremely small stools. Avoid the ramen, way less delicious than the mazesoba. So you know that restaurant created by the gods in an expirement to feed mere mortals divine food? Shichi? Well it seems like, as in any gods stories, one of them was evil and planted a not-so-divine dish in the menu. I unveiled it and am here to warn you: if, for any unimaginable reasons, you still havent been there, when go, do NOT order the ramen. Stick with the mazu soba. And about the place itself, it is narrow, stools are uncomfortable, it is cold in winter... but it is all forgiven after tasting their food.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%81%BE%E3%81%9C%E3%81%9D%E3%81%B0%E4%B8%83/@35.6582661,139.6984915,17.5z/data=!4m5!3m4!1s0x60188b57612e0f6b:0x129cb60f35f3f7ca!8m2!3d35.6566223!4d139.7010507',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13169375/',
            'official_website'  => 'https://twitter.com/men_daikokuya',
            'score_lunch'       => 4,
            'score_place'       => 0,
            'score_food'        => 3,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['maze soba', 'abura soba', 'ramen']);
        $resto->fillCoordinatesFromString('35.656633, 139.701051');

        $resto = Restaurant::create(array(
            'name'              => 'Cona',
            'location'          => 'Shibuya',
            'type'              => 'Pizzeria',
            'lunch_price'       => 500,
            'points'            => 'Cheap and light pizza, counter or tables available. 500 for just the pizza, +300yen for salad bar and drink bar. Pasta lunch also available.',
            'experience'        => 'Decent pizza 30cm, but very light (very thin crust and topping), better not be hungry. Counter is small, not very confortable. When I went there it was also too cold.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/CONA%E6%B8%8B%E8%B0%B7%E6%A1%9C%E3%83%B6%E4%B8%98%E5%BA%97/@35.6582228,139.6984793,17.5z/data=!4m5!3m4!1s0x60188b59e139125b:0x1ce5c308e856061b!8m2!3d35.6562354!4d139.7015805',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13133814/',
            'official_website'  => 'https://twitter.com/conasakura',
            'score_lunch'       => 2,
            'score_place'       => 0,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['pizza']);
        $resto->fillCoordinatesFromString('35.656251, 139.701582');

        $resto = Restaurant::create(array(
            'name'              => 'Kin no torikara',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Fried chicken to go',
            'lunch_price'       => 400,
            'points'            => 'Outside and no tables. Cheap, fast, to have on the go. Fried chicken or fries in a paper bag. Various sauce availble (including lemon juice).',
            'experience'        => 'Simple, good, I use it if I still need some more food if my lunch was too light. Nowhere to sit down around there, so have to walk around while eating or just stand there.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Kin-No-Torikara/@35.6589156,139.699263,17.21z/data=!4m5!3m4!1s0x60188ca849e8e51b:0x43472eed3e78c550!8m2!3d35.6603157!4d139.6992248',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13129686/',
            'official_website'  => 'http://www.kinnotorikara.jp/',
            'score_lunch'       => 2,
            'score_place'       => 0,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['karaage']);
        $resto->fillCoordinatesFromString('35.660329, 139.699224');

        $resto = Restaurant::create(array(
            'name'              => 'Mangako thai chicken rice',
            'location'          => 'Shibuya',
            'type'              => 'Thai',
            'lunch_price'       => 800,
            'points'            => 'supposedly famous chicken rice',
            'experience'        => 'I\'ve been told there were a thai restaurant chain in Shibuya serving chicken rice, and EVEN in Thailand, there were a line. So I went there, and arrived shortly in front a restaurant exposing dead chicken on its facade. No line. I go inside, and its small and crappy, with shitty music on the top. I order the famous chicken rice, for an outstanding 800yen. It comes quickly, with a soup (hot water) and some sauce (like sweet chinese shoyu with a touch of vinegar). The amount is ridiculously small (note that you could double it for 400yen). What\'s more, the chicken is actually half chicken, half chicken skin. I eat, nothing special at all. Rice is not really good. Chicken is nice and soft and tasteless, for the few bites I had on it. The sauce was good. Oh, and surprisingly enough, it also came with a mini-bucket full of pakuchi... this stuff is usually expensive here. And that was it, EXTREMELY disapointed. I felt robbed. Of my money. My time. My hopes. And still hungry.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E0%B8%A3%E0%B9%89%E0%B8%B2%E0%B8%99%E0%B8%82%E0%B9%89%E0%B8%B2%E0%B8%A7%E0%B8%A1%E0%B8%B1%E0%B8%99%E0%B9%84%E0%B8%81%E0%B9%88/@35.6584854,139.6999814,17z/data=!4m5!3m4!1s0x60188b5b85ab031f:0x35b195078881d9ca!8m2!3d35.6558927!4d139.7055917',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 0,
            'score_place'       => 0,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['thai']);
        $resto->fillCoordinatesFromString('35.655904, 139.705592');

        $resto = Restaurant::create(array(
            'name'              => 'Tokumasa curry',
            'location'          => 'Shibuya, 3Chome',
            'type'              => 'Japanese curry',
            'lunch_price'       => 800,
            'points'            => 'Nice atmosphere. Elvis Presley music.',
            'experience'        => 'Well balanced curry.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E4%B8%8A%E7%AD%89%E3%82%AB%E3%83%AC%E3%83%BC+%E6%B8%8B%E8%B0%B7%E6%9C%AC%E5%BA%97/@35.6590581,139.6985088,17z/data=!4m5!3m4!1s0x60188b599da6b5c3:0x89922071e4316be1!8m2!3d35.6571333!4d139.7038007',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13160341/',
            'official_website'  => 'http://www.tokumasa.net/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.657147, 139.703800');

        $resto = Restaurant::create(array(
            'name'              => 'Ramen and Tsukemen',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 800,
            'points'            => 'tonkostsu ramen, soupless ramen, curry, gyoza... and mini versions available.',
            'experience'        => 'Tried the white miso ramne, and it was pretty bland, not worth the 800yen. I liked the atmosphere, very local, kinda cosy, with the TV on and the grand mothers as the staff.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen']);
        $resto->fillCoordinatesFromString('35.656451, 139.700907');

        /* Place closed - confirmed 2017-12-26
        $resto = Restaurant::create(array(
            'name'              => 'Cafde. SALICA',
            'location'          => '2nd floor in front of Samrat',
            'type'              => 'cafe restaurant, sandwiches burgers desserts...',
            'lunch_price'       => 1100,
            'points'            => 'large space and tables, some with sofa, music OK and not too lound, a diverse menu',
            'experience'        => 'Once again I\'ve been blessed with a totally unexpected discovery of divinish food! I was going to Shibuya dowtown, wher ethe girls are pretty and the rayds are mighty, when suddenly an irresistible craving of premium burger got into me. Keeping my head cool, I thought "awwright no problemo, I can go to that restaurant I spot last month, "meat bank", that was advertising and appetizing burger. And so there I went. Confident. Looking at the menu before the entrance of the rather new establishment, I realized brutally that there were NO BURGER at all. What insolence. The world was crumbling all around me, I started feeling deeply cold and hungry and despaired and angry. I wandered around, crossing the street looking at other restaurants, which signs were blurred by my teary eyes. This is when I spotted a burger. Right there on a menu sign in the street. Some kind of cafe on the 2nd floor. My feeet directly driven by my empty stomach, I climbed the stairs. Nice surpise, the restaurant is spacious, empty of customers. Relaxing place. I order the avocado cheese burger, at a 1000 yen price tag with a drink. And... long story short, that ended up being one, if not the, but most likely top 6, best burger I ever had. And it looked gorgous too. And real delicious, at every levels, bread, lettuce, tomato, onions, sauce, french mustard, MEAT, cheese, avocado... all premium quality. Came served on a wooden palette too, for a little fanciness. So be advised, if you crave for premium burgers, do try this place! ',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => 'http://www.cafde-salica.com',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['burger']);]
        */

        $resto = Restaurant::create(array(
            'name'              => 'Mediterranean Pizzeria Domani',
            'location'          => 'Shibuyq',
            'type'              => 'italian',
            'lunch_price'       => 900,
            'points'            => 'Interior OK. Pizza OK but really nothing special... 900 yen for pizza + salad. The salad (some lattuce) is small but the dressing is delicious. Tables and counter, not small.',
            'experience'        => 'The place is a bit dark, but feels cosy. It is not small and could receive small groups. The food is alright.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E5%9C%B0%E4%B8%AD%E6%B5%B7%E6%96%99%E7%90%86+1966+%E3%83%89%E3%83%9E%E3%83%BC%E3%83%8B/@35.6587138,139.6988096,17.25z/data=!4m5!3m4!1s0x60188b59edc647e5:0xf23c321cc6ca5496!8m2!3d35.6564774!4d139.7025501',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13007058/',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['pasta', 'pizza', 'italian']);
        $resto->fillCoordinatesFromString('35.656491, 139.702546');

        $resto = Restaurant::create(array(
            'name'              => 'Meat Bank',
            'location'          => 'Shibuya',
            'type'              => 'burger',
            'lunch_price'       => 1200,
            'points'            => 'Nice interior, but lound music. Sofa and nice chairs. Wide windows bay. Nice staff. Premium burgers. Good. Lunch set 1200 yen avocado bacon cheese burger with drink and soup.',
            'experience'        => 'I liked the interior, and the comfort. But I found the music too loud. Not much of a problem when you are alone, but a bit tiring if you want to talk. The choice of burgers is good, and the burgers themselves quite delicious. Not cheap, but not so much overpriced compared with other premium burgers restaurants around there. I paid 1200 yen for a bacon avocado cheese burger with a soup and a drink. Id say, nice place if you want to eat burger in a comfy place.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/DORAEMON+MEAT+BANK/@35.6593003,139.6982389,17z/data=!4m12!1m6!3m5!1s0x60188ca9136921fb:0x5d7020b9461a5699!2sDORAEMON+MEAT+BANK!8m2!3d35.6616167!4d139.697614!3m4!1s0x60188ca9136921fb:0x5d7020b9461a5699!8m2!3d35.6616167!4d139.697614',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13207625/',
            'official_website'  => 'http://www.draemon.net/groupshop/shop13.html',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['burger', 'grill', 'western']);
        $resto->fillCoordinatesFromString('35.661641, 139.697613');

        $resto = Restaurant::create(array(
            'name'              => 'Asuka',
            'location'          => 'Shibuya, south',
            'type'              => 'Ramen',
            'lunch_price'       => 780,
            'points'            => 'Counter only. Narrow. Served fast. Very good tantan men.',
            'experience'        => 'We eat looking at the open kitchen. Staff was OK but not too polite / busy. Tantan men was maybe the best I had so far. Strong taste, hot but not too much (to me). And rather cheap (780 yen).',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Asuka/@35.6589642,139.6979569,17z/data=!4m5!3m4!1s0x60188b59d7c24f37:0x1329d7c983500d71!8m2!3d35.657214!4d139.70139',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13001715/',
            'official_website'  => null,
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen']);
        $resto->fillCoordinatesFromString('35.657230, 139.701387');

        $resto = Restaurant::create(array(
            'name'              => 'Samrat',
            'location'          => 'Shibuya',
            'type'              => 'Indian curry',
            'lunch_price'       => 900,
            'points'            => 'Sucked',
            'experience'        => 'Worst indian curry ever. When I first discovered indian curry near my office in setagaya-daita, it blew my mind so hard I ate it almost everyday. I just could not imagine eating anythnig else. Then years passed, I realized not all restaurants were equals, but still indian curry was always rhytming with delicious, greatly enjoyable meal. Then I worked in aoyama icchome and realized that the world can be a cold and heartless place. The indian food there was bland. Bland AF. Then I finally escaped what I thought was the darkest pit of restaurant and companies\' hell. To Shibuya! Surely one of the most amazing place to stroll for restaurants, and other things... But like in any movies and relationships, after all looked so perfect, something ugly, something world breaking happens and it all goes to shit. I ended up to Samrat shibuya today, and it sucked like I never thought it could suck. New low for indian curry, which now just joined the rank of my depressed thoughts about the world. Everything was bad, the interior was horrid, the nan was horribly oily, and the curry was doubtlessly the worst I ever had (and i asked for an easy one, the keema). Next time please somebody reconcile me with curry cause I dont feel I have the courage to face another one by myself.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/SAMRAT/@35.6593532,139.6980944,17z/data=!4m8!1m2!2m1!1ssamrat!3m4!1s0x60188ca9130c3be3:0x3ab72d1dff12516c!8m2!3d35.6617765!4d139.6978047',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13003359/',
            'official_website'  => 'http://www.samrat.co.jp/',
            'score_lunch'       => 0,
            'score_place'       => 0,
            'score_food'        => 0,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['indian', 'curry']);
        $resto->fillCoordinatesFromString('35.661795, 139.697803');

        $resto = Restaurant::create(array(
            'name'              => 'Mos Burger',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Burger',
            'lunch_price'       => 1000,
            'points'            => 'Japanese fast food burger.',
            'experience'        => 'Slow service. Overpriced. Impossible to eat by hands. I eat it with fork and knife at home, and taste is good. But possibly the worst burger chain in Japan.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/MOS+Burger+Shibuya-D%C5%8Dgenzaka/@35.6587417,139.6984195,17.25z/data=!4m8!1m2!2m1!1sMOSS+burger!3m4!1s0x0:0xdca4d2aadd809f58!8m2!3d35.6592059!4d139.6978207',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13059877/',
            'official_website'  => 'https://ssl.mos.jp/shop/detail/02144/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','fast food']);
        $resto->fillCoordinatesFromString('35.659218, 139.697821');

        $resto = Restaurant::create(array(
            'name'              => 'Kura',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Decent italian. They have decent gorgonzola pizza. Good choice of pasta, pizza and salad, decent interior.',
            'experience'        => 'Interior was alright, I liked the choice on the menu, and got a decent gorgonzola pizza. I did not make me full, for 1000 yen cost, though.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/KURA/@35.6586533,139.6988234,17.5z/data=!4m5!3m4!1s0x60188ca7f0a849d9:0x635d6250b7712dd7!8m2!3d35.6605941!4d139.7019922',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13040314/',
            'official_website'  => 'http://www.kura-jp.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian', 'pizza', 'pasta']);
        $resto->fillCoordinatesFromString('35.660607, 139.701990');

        $resto = Restaurant::create(array(
            'name'              => 'Khao San',
            'location'          => 'towards omotesando, 2nd floor',
            'type'              => 'Thai',
            'lunch_price'       => 1000,
            'points'            => 'Decent thai. Nice room, with windows bay.They have a gapao + green curry set, with good soup and small salad, for 980yen. Music. Open drink bar with tea and detox water.',
            'experience'        => 'Nice waitress, enjoyed the room and the set. Was good, but not especially delicious. Still well balanced set for a fair price.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%90%E3%83%B3%E3%82%B3%E3%82%AF%E5%B1%8B%E5%8F%B0+%E3%82%AB%E3%82%AA%E3%82%B5%E3%83%B3%E6%B8%8B%E8%B0%B7%E5%AE%AE%E7%9B%8A%E5%9D%82%E5%BA%97/@35.6589811,139.7005548,17.25z/data=!4m5!3m4!1s0x0:0xf9a71c346b83d71b!8m2!3d35.6602592!4d139.7055203',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13209312/',
            'official_website'  => 'http://www.spiceroad.co.jp/Shop_ShibuyaMiyamasu.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['thai', 'gapao']);
        $resto->fillCoordinatesFromString('35.660250, 139.705638');

        $resto = Restaurant::create(array(
            'name'              => 'On the corner (No.8 Bear Pond)',
            'location'          => 'symetrically opposite from Tower Record',
            'type'              => 'Burgers and stuff',
            'lunch_price'       => 1000,
            'points'            => '1000 yen lunches and premium 1100+ yen home made burgers. Possible topping customization includes jalapegno and gorgonzola. Big room. Windows. American music a bit loud.',
            'experience'        => 'Room OK. I got the jalapegno burger for 1300 yen. Came with a drink, and a mini soup served in an expresso cup. The burger was quite good, but not worth 1300 yen, in my opinion.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/No.8+by+bear+pond/@35.6593056,139.698986,17.25z/data=!4m5!3m4!1s0x60188ca791353ac5:0xc780d154660bca9f!8m2!3d35.661541!4d139.7030316',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13113702/',
            'official_website'  => 'http://www.bear-pond.com/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['burger']);
        $resto->fillCoordinatesFromString('35.661556, 139.703030');

        $resto = Restaurant::create(array(
            'name'              => 'Fujisoba',
            'location'          => 'in front of big bic camera',
            'type'              => 'soba udon katsudon',
            'lunch_price'       => 750,
            'points'            => 'Decent sets. Fast food style. Narrow counter only. Buy at machine, get your set on a plate and find somewhere to sit.',
            'experience'        => 'Fast and cheap, and decent. I tried the zaru soba + katsudon for 750 yen. Made me full OK. Definitely not delicious, but decent.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13103625/dtlmap/',
            'official_website'  => 'http://fujisoba.co.jp/',
            'score_lunch'       => 1,
            'score_place'       => 0,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['soba', 'katsudon', 'japanese', 'fast food']);
        $resto->fillCoordinatesFromString('35.660197, 139.702519');

        $resto = Restaurant::create(array(
            'name'              => 'Pizza Slice',
            'location'          => 'Shibuya, Sarugakucho',
            'type'              => 'pizza',
            'lunch_price'       => 1000,
            'points'            => 'big, heigh celing room with big windows bay. 500 yen american/newyork style pizza slices. Served fast. Just slices on a paper plate, no knife and fork. Free toppings include spicy stuff (but no oil), and oregan. Big tables avaialbe in addition of small and counter.',
            'experience'        => 'Pizza were decent. I liked the free oregan herbs available as topping on the tables. A bit overpriced though. Room is cool. When I was there, there were 29 girls for 3 guys.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Pizza+Slice/@35.6541438,139.6986611,16z/data=!4m8!1m2!2m1!1spizza+slice!3m4!1s0x60188b5b150ddc43:0x6774f31fd3f468c1!8m2!3d35.6535237!4d139.7051418',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130303/13164019/',
            'official_website'  => 'http://www.pizzaslice.co/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['pizza']);
        $resto->fillCoordinatesFromString('35.653555, 139.705145');

        $resto = Restaurant::create(array(
            'name'              => 'iBeer',
            'location'          => 'Hikarie building 7F',
            'type'              => 'italian',
            'lunch_price'       => 1000,
            'points'            => 'Open space restaurant in nice Hikarie 7F. Busy floor, but nice setting. A bit expensive, but one of the cheapest restaurant in the building.',
            'experience'        => '1162 yen Pasta Set with Salad + bread + organic tomatoe avocado pasta. Nice salad, hot bread, and very, very good pasta. I would go as far as to call the pasta "exquisite". I also liked the setting, tables and chairs.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/iBeer+Le+Sun+Palm/@35.659252,139.6999228,17z/data=!3m1!5s0x60188b58f894f891:0xa230d50aab3d69e4!4m8!1m2!2m1!1siBeer!3m4!1s0x0:0xe0dfc9c31b013e93!8m2!3d35.659052!4d139.7037091',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13169825/',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['pasta','pasta','italian']);
        $resto->fillCoordinatesFromString('35.659067, 139.703709');

        $resto = Restaurant::create(array(
            'name'              => 'Avatar',
            'location'          => 'D side in front of shibuya language shool',
            'type'              => 'indian curry',
            'lunch_price'       => 850,
            'points'            => 'nice little indian restaurant. Not a chain. Relatively nice staff. No cheesy TV clips. Decent price curry sets. No keema. Free nan and rice.',
            'experience'        => 'Enjoyed the place, but the chiken curry I ordered was bland, tasteless.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%82%A2%E3%83%B4%E3%82%A1%E3%82%BF%E3%83%BC%E3%83%AB%EF%BC%88%EF%BC%A1%EF%BC%B6%EF%BC%A1%EF%BC%B4%EF%BC%A1%EF%BC%B2%EF%BC%89/@35.6583902,139.6980654,16.75z/data=!4m5!3m4!1s0x60188b5a14212195:0xaddbce0d1c82d4a7!8m2!3d35.654973!4d139.7023979',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 0,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['indian curry']);
        $resto->fillCoordinatesFromString('35.655285, 139.702352');

        $resto = Restaurant::create(array(
            'name'              => 'Guzman y Gomez',
            'location'          => 'past tower record',
            'type'              => 'mexican',
            'lunch_price'       => 1300,
            'points'            => 'Good mexican fastfood cuisine. American chain. Expensive.',
            'experience'        => 'First and foremost, this place is insanely overpriced. I got 3 kedasilla for 1070 yen. It felt like a small snack. Definitely insufficient for a lunch. I left hungry and had to go eat something cheaper elsewhere. Taste-wise, it was good, and I could choose the meat amongst 8 different choices, for each of my 3 small kedasilla. The interior, and ordering system, is like a cheap fastfood. When I ate there the doors were kept opened despite the cold and I had to keep my jacket on. The music was very cheesy.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Guzman+y+Gomez+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.662334,139.7004488,19z/data=!4m12!1m6!3m5!1s0x60188ca88131ea95:0x111613650b9602bd!2zR3V6bWFuIHkgR29tZXog5riL6LC35bqX!8m2!3d35.662334!4d139.700996!3m4!1s0x60188ca88131ea95:0x111613650b9602bd!8m2!3d35.662334!4d139.700996',
            'tabelog_link'      => '',
            'official_website'  => 'http://gyg.jp/',
            'score_lunch'       => 2,
            'score_place'       => 0,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['mexican']);
        $resto->fillCoordinatesFromString('35.662400, 139.700988');

        $resto = Restaurant::create(array(
            'name'              => 'Il Fiume',
            'location'          => 'far',
            'type'              => 'italian',
            'lunch_price'       => 980,
            'points'            => 'Nice place, nice interior, even has a terrace, in a calm street. Lunch has salad buffet, with decent variety of salads. Dish can be chosen from the menu. Japanese-italian style. No pizza.',
            'experience'        => 'Enjoyed the place. Salads were plenty, and good. I tried some pasta, 980yen, but it was... just decent. But it made me want to come back and try another dish.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%82%A4%E3%83%AB%E3%83%95%E3%83%A5%E3%83%BC%E3%83%A1/@35.655656,139.70675,21z/data=!4m5!3m4!1s0x60188b5c7914049f:0xdda113f8492b26d9!8m2!3d35.6556367!4d139.7066753',
            'tabelog_link'      => '',
            'official_website'  => 'https://il-fiume.biz/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 2
        ));
        $resto->attachTags(['italian', 'salad buffet', 'pasta']);
        $resto->fillCoordinatesFromString('35.655657, 139.706674');

        $resto = Restaurant::create(array(
            'name'              => 'Verde',
            'location'          => 'Shibuya, 3Chome',
            'type'              => 'curry+gapoa+pizza',
            'lunch_price'       => 980,
            'points'            => 'Dark place on a 4th floor. Menu offers mostly indian curry sets, but also thai and pizza. Place doesnt look busy and there is a good amount of tables, so might be easy for large groups.',
            'experience'        => 'I tried the pizza+salad for 980yen. They have a real pizza oven to the crust was tasty from the wood fire. But other than that, it was quite plain. A lot of cheese on it though, so it made me well full. Salad was like the salad you get in a curry restaurant. The drink as well. I did not like how dark was the place. And the price was not cheap. So I won\'t be back. Fun note: the elevator to get there is extremely slow.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/VERDE+%E3%83%B4%E3%82%A7%E3%83%AB%E3%83%87+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6590104,139.6992206,17.25z/data=!4m5!3m4!1s0x60188b599e9b7543:0xf6253420bcfbe3f5!8m2!3d35.6572417!4d139.7036924',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['indian', 'curry', 'pizza']);
        $resto->fillCoordinatesFromString('35.657515, 139.703662');

        $resto = Restaurant::create(array(
            'name'              => 'Gapao Shokudō',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Thai',
            'lunch_price'       => 980,
            'points'            => 'gapao',
            'experience'        => 'very small tables, for most of them. Gapao was OK, came with good soup and small salad, from 980yen.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Gapao+Shokud%C5%8D/@35.6578467,139.6987192,17.25z/data=!4m5!3m4!1s0x0:0xfdfd8b7f19684676!8m2!3d35.6555029!4d139.701378',
            'tabelog_link'      => '',
            'official_website'  => 'https://shibuya-gapaoshokudo.owst.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['thai']);
        $resto->fillCoordinatesFromString('35.655802, 139.701326');

        $resto = Restaurant::create(array(
            'name'              => 'Blacows',
            'location'          => 'Ebisu',
            'type'              => 'burger',
            'lunch_price'       => 2000,
            'points'            => 'expensive wagyu beef burgers, nice place',
            'experience'        => 'very good burger. Customizable. Expensive.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/BLACOWS/@35.652128,139.7005291,16z/data=!4m12!1m6!3m5!1s0x60188b43edf2af03:0xd017aeed6fd54147!2sBLACOWS!8m2!3d35.649399!4d139.7070914!3m4!1s0x60188b43edf2af03:0xd017aeed6fd54147!8m2!3d35.649399!4d139.7070914',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130302/13096437/',
            'official_website'  => 'http://www.kuroge-wagyu.com/bc/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['burger', 'western']);
        $resto->fillCoordinatesFromString('35.649922, 139.707031');

        $resto = Restaurant::create(array(
            'name'              => 'Salasa',
            'location'          => 'Shibuya - Udagawacho',
            'type'              => 'izakaya',
            'lunch_price'       => 1050,
            'points'            => 'Various Japanese teishoku (lunch sets). Izakaya style. Small. Smallest tables ever. TV broadcasting news chanel. Slow service. Very good teishoku (lunch set)',
            'experience'        => 'The chef cooks your lunch from scratch apparently, so it takes up to 20 minutes of wait time. I was surprised because, in Japan, the service is usually very fast. But it was worth the wait as it was quite delicious. I ordered the cheese/porc fried rolls for 1050yen, and it came with pickles, miso soup, rice, and cabage. It was very good and I was fully satisfied.',
            'visited'           => true,
            'visit_date'        => '2017-11-28 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E7%AB%8B%E3%81%A1%E5%91%91%E3%81%BF%E5%B1%8B+%E3%82%B5%E3%83%A9%E3%82%B5/@35.65963,139.69801,17.08z/data=!4m12!1m6!3m5!1s0x60188ca9a573367d:0x4f79108df0bc13c6!2z56uL44Gh5ZGR44G_5bGLIOOCteODqeOCtQ!8m2!3d35.6605229!4d139.6981356!3m4!1s0x60188ca9a573367d:0x4f79108df0bc13c6!8m2!3d35.6605229!4d139.6981356',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13122160/',
            'official_website'  => '',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese', 'izakaya']);
        $resto->fillCoordinatesFromString('35.660837, 139.698107');

        $resto = Restaurant::create(array(
            'name'              => 'JS Burgers Cafe',
            'location'          => 'Shibuya',
            'type'              => 'burger',
            'lunch_price'       => 1500,
            'points'            => 'Very nice place (confortable, various tables, big windows bay, enjoyable music). Expensive. Premium burgers. Salad bar at lunch.',
            'experience'        => 'Very nice experience, nejoyed the place and the music. Loved the salad bar, with lots of choices, and good. Burger came fast. I tried the JS burger. The ingredient were undeniably good, but globally, it was lacking taste as there were almost no sauce. Still, I ended up full and satisfied.',
            'visited'           => true,
            'visit_date'        => '2017-11-29 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/J.S.+BURGERS+CAFE+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6602266,139.6982899,17z/data=!4m12!1m6!3m5!1s0x60188ca8f1e3e571:0x3c325c2063be4a6a!2zSi5TLiBCVVJHRVJTIENBRkUg5riL6LC35bqX!8m2!3d35.6621966!4d139.6996954!3m4!1s0x60188ca8f1e3e571:0x3c325c2063be4a6a!8m2!3d35.6621966!4d139.6996954',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13200176/',
            'official_website'  => 'http://burgers.journal-standard.jp/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 3,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['burger', 'salad buffet', 'western']);
        $resto->fillCoordinatesFromString('35.662428, 139.699706');

        $resto = Restaurant::create(array(
            'name'              => 'Pizza Salvatore Cuomo',
            'location'          => 'Daikanyama',
            'type'              => 'italian',
            'lunch_price'       => 1280,
            'points'            => 'Usually for delivery, but also has a nice interior with tables and counter, as well as a terrace (with heaters). 100yen pasta lunch, or 1280 yen pizza lunch. Lunch set includes a salad, a mini soup, and a drink. Decent but overpriced.',
            'experience'        => 'I was suprized at how nice the interior was. Did not like the music though (some sort of modern hip-hop, kind of loud). I tried the gorgonzola+mozarealla pizza. It was decent, but a bit small. 1280 yen felt overpriced.',
            'visited'           => true,
            'visit_date'        => '2017-11-30 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/PIZZA+SALVATORE+CUOMO+%E4%BB%A3%E5%AE%98%E5%B1%B1/@35.6573119,139.6995099,17z/data=!4m12!1m6!3m5!1s0x60188b50dc1a00db:0xbe0c23fe923749ec!2zUElaWkEgU0FMVkFUT1JFIENVT01PIOS7o-WumOWxsQ!8m2!3d35.653729!4d139.6992417!3m4!1s0x60188b50dc1a00db:0xbe0c23fe923749ec!8m2!3d35.653729!4d139.6992417',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13022224/',
            'official_website'  => 'http://www.salvatore.jp/restaurant/daikanyama/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian', 'pizza']);
        $resto->fillCoordinatesFromString('35.654004, 139.699252');

        $resto = Restaurant::create(array(
            'name'              => 'Uogashi Nihonichi',
            'location'          => 'Shibuya',
            'type'              => 'Sushi',
            'lunch_price'       => 1200,
            'points'            => 'Rather cheap and decent sushi. Standing at the counter. Avocado sushi available.',
            'experience'        => 'Standing is not so bad. It is a rather cheap and good sushi bar. The cheapest sushi are 176 for 2 pieces, and include torosamon (some fat saumon). Mid-range sushi are 216 for 2 pieces. Sushi are made in front of you on demand.',
            'visited'           => true,
            'visit_date'        => '2017-12-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Uogashi+Nihonichi/@35.6587563,139.6981308,20.25z/data=!4m5!3m4!1s0x60188ca9d762bf87:0x50712f246ece5137!8m2!3d35.6589335!4d139.6982036',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13052346/',
            'official_website'  => 'http://www.uogashi-nihonichi.com/shibuyacentergai/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['sushi']);
        $resto->fillCoordinatesFromString('35.658972, 139.698201');

        $resto = Restaurant::create(array(
            'name'              => 'Lotteria',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'burger',
            'lunch_price'       => 900,
            'points'            => 'Good alternative to McDo. Fast, unexpensive, and decent burgers.',
            'experience'        => 'Good alternative to McDo. Similar quality and experience, similar price, with alternative choices and taste.',
            'visited'           => true,
            'visit_date'        => '2017-12-07 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Lotteria+Shibuya+Dogenzaka+shop/@35.6585933,139.6982641,17.75z/data=!4m5!3m4!1s0x60188ca9d6e7a5ed:0xb00d1f2aa3db840a!8m2!3d35.6590528!4d139.6983417',
            'tabelog_link'      => '',
            'official_website'  => 'https://www.lotteria.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger']);
        $resto->fillCoordinatesFromString('35.659356, 139.698361');

        $resto = Restaurant::create(array(
            'name'              => 'TGIF',
            'location'          => 'Shibuya',
            'type'              => 'American',
            'lunch_price'       => 900,
            'points'            => 'Decent casual restaurant with a big room and decent interior. Serve variety of dishes such as burgers, tacos, or pasta. Decent prices. Decent taste. Amount of food is slightly below what I would like and expect.',
            'experience'        => 'I liked the room, and the windows bay. The service was a bit slow. The food was good (I tried the tacos), but I was still a bit hungry after the meal. The place was mostly crowded with (possibly noisy) groups of high schoolers.',
            'visited'           => true,
            'visit_date'        => '2017-12-07 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Restaurant+%26+American+Bar+T.G.I.+FRIDAY\'S+%E6%B8%8B%E8%B0%B7%E7%A5%9E%E5%8D%97%E5%BA%97/@35.6605356,139.6986126,17.25z/data=!4m8!1m2!2m1!1sTGIF!3m4!1s0x0:0xdb4e1a1660b476ea!8m2!3d35.6624829!4d139.700059',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13014179/',
            'official_website'  => 'http://www.tgifridays.co.jp/find-us/shibuya/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['western']);
        $resto->fillCoordinatesFromString('35.662745, 139.700018');

        $resto = Restaurant::create(array(
            'name'              => 'Ankara',
            'location'          => 'Shibuya',
            'type'              => 'Turk',
            'lunch_price'       => 1000,
            'points'            => 'Decent Turk restaurant. B1F, quite dark place, but decent interior. Big enough to allow groups.',
            'experience'        => 'Lunch set was dish + bread/nan + salad + soup + tea, all decent. Not as good as Kebab Cafe though (but more more confortable).',
            'visited'           => true,
            'visit_date'        => '2017-12-13 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%83%88%E3%83%AB%E3%82%B3%E6%96%99%E7%90%86%E3%83%AC%E3%82%B9%E3%83%88%E3%83%A9%E3%83%B3+ANKARA+%E5%AE%AE%E7%9B%8A%E5%9D%82%E5%BA%97+%E3%82%A2%E3%83%8A%E3%83%88%E3%83%AA%E3%82%A2%E6%B8%8B%E8%B0%B7/@35.6595385,139.7003228,17.25z/data=!4m5!3m4!1s0x0:0xa07aae74f4d375d9!8m2!3d35.6597551!4d139.7046362',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13089864/',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['turk']);
        $resto->fillCoordinatesFromString('35.660031, 139.704625');

        $resto = Restaurant::create(array(
            'name'              => 'Ajigen',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 850,
            'points'            => 'Decent miso ramen. Spicy "dragon" miso ramen available. Decent gyoza also available. No table, counter only. Staff very friendly to foreigners. English and Chinese menus available.',
            'experience'        => 'I got a regular miso ramen + gyoza and rice set for less than 1000 yen. That was totally decent. The counter was not too narrow, but the place was quite hot. The miso was OK but a bit light to my taste.',
            'visited'           => true,
            'visit_date'        => '2017-12-14 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E5%91%B3%E6%BA%90+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.659598,139.6990656,18.5z/data=!4m12!1m6!3m5!1s0x60188ca9caa47377:0xd473864447fcdf9!2z5ZGz5rqQIOa4i-iwt-W6lw!8m2!3d35.6599879!4d139.6987729!3m4!1s0x60188ca9caa47377:0xd473864447fcdf9!8m2!3d35.6599879!4d139.6987729',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13001724/',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','miso']);
        $resto->fillCoordinatesFromString('35.660128, 139.698765');

        $resto = Restaurant::create(array(
            'name'              => 'Pancho',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Pasta',
            'lunch_price'       => 780,
            'points'            => 'Totally decent pasta. Spagethi with 2 bases: meat sauce or napolitan, starting 780yen and customisable. Basement. Fast service. Tables. Big quantity.',
            'experience'        => 'I was happy with the quality for the price! And you can order up to 500g of pasta, with no additional charge. This is huge. Quick service. Several toppings on the tables, like free parmesan cheese. Interior is basment and poorly lighted, but decent. Tables are confortable enough.',
            'visited'           => true,
            'visit_date'        => '2017-12-15 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Pancho+Shibuya/@35.6591649,139.6969986,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b57d6416803:0xce5bf0b8015be959!8m2!3d35.6591606!4d139.6991873',
            'tabelog_link'      => 'https://tabelog.com/en/tokyo/A1303/A130301/13100208/',
            'official_website'  => 'http://naporitanpancho.com/shibuya',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['italian', 'pasta']);
        $resto->fillCoordinatesFromString('35.659453, 139.699219');

        $resto = Restaurant::create(array(
            'name'              => 'Murugi',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Curry',
            'lunch_price'       => 1050,
            'points'            => 'Good (japanese) curry. Cosy interior. A bit overpriced. Small tables, no counter.',
            'experience'        => 'The interior felt cosy, even if not very confortable, because the chairs and tables are very small. There is no counter, and even just one person has to occupy a 4 persons small table, so the place is full easily. The curry was good. A bit spicy, and I liked how the pieces of chicken were kind of melted in the curry. I was still a bit hungry after that though, and the price was relatively high. For lunch the choice is limited to a couple of dish including the chicken curry.',
            'visited'           => true,
            'visit_date'        => '2017-12-18 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Murugi/@35.6599674,139.6976882,16z/data=!4m8!1m2!2m1!1scurry!3m4!1s0x60188ca9f3ff4391:0x86579b54bd13670e!8m2!3d35.6589719!4d139.6965652',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13001732/',
            'official_website'  => 'http://www.murugi.net/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.659436, 139.696572');

        $resto = Restaurant::create(array(
            'name'              => 'Texmex Factory',
            'location'          => 'Shibuya',
            'type'              => 'Mexican',
            'lunch_price'       => 1280,
            'points'            => 'Unlimited "make your own tacos" food, buffet style, until 3pm. Unique price of 1280 yen, that includes drink. Good food, but cold. Big and nice interior with tables.',
            'experience'        => 'I liked the interior, spacious, with large tables. The music was a bit lound. I was impressed with the amount and variety of the food and sauces you can get. It tasted good too. The only complaint I would have is that the food is cold. If you get the meat right after a new batch has arrived, it will be warm, but otherwise it\'s all cold, which is kind of sad especially in winter. Nevertheless, I left this restaurant full and happy.',
            'visited'           => true,
            'visit_date'        => '2017-12-19 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/TEXMEX+FACTORY+%E6%B8%8B%E8%B0%B7%E7%A5%9E%E5%8D%97%E5%BA%97/@35.6596109,139.6989321,17z/data=!4m5!3m4!1s0x0:0x1e337a713464cab2!8m2!3d35.6624352!4d139.7000211',
            'tabelog_link'      => 'https://tabelog.com/en/tokyo/A1303/A130301/13199134/',
            'official_website'  => 'https://www.texmexfactory2016.com/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['mexican', 'tacos', 'all you can eat']);
        $resto->fillCoordinatesFromString('35.662736, 139.700080');

        $resto = Restaurant::create(array(
            'name'              => 'Paku Mori',
            'location'          => 'Shibuya',
            'type'              => 'Curry',
            'lunch_price'       => 980,
            'points'            => 'Friendly, small, cosy, with no tables (counter only). Choice of customizable japanese curry. Tastes good, but the quantity for the price is not great.',
            'experience'        => 'At first I did not feel so great, going to the basement of that building, but in the end I liked the friendly interior. The curry I got was very good, but I would have liked more for the price (980yen for an eggplants curry). Not really spicy-hot, but you can add spices at will from what is available on the counter.',
            'visited'           => true,
            'visit_date'        => '2017-12-20 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Curry+Park+forest+Shibuya/@35.6586233,139.6963805,17z/data=!4m12!1m6!3m5!1s0x60188ca9fe8bb37f:0x6f3e71a6afb91408!2sCurry+Park+forest+Shibuya!8m2!3d35.658109!4d139.6964878!3m4!1s0x60188ca9fe8bb37f:0x6f3e71a6afb91408!8m2!3d35.658109!4d139.6964878',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13001811/',
            'official_website'  => 'https://www.pakumori.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.658418, 139.696488');

        $resto = Restaurant::create(array(
            'name'              => 'Shakey\'s',
            'location'          => 'Shibuya',
            'type'              => 'Pizza',
            'lunch_price'       => 980,
            'points'            => 'Cheap all-you-can-eat pizza, pasta and curry.',
            'experience'        => 'The food is mediocre but this is OK because it is all-you-can-eat for less than a thousand yen, and there is a decent variety of pizza, pasta, and curry. You pay upfront and can leave when you want. The place is usually full of students. The interior is decent.',
            'visited'           => true,
            'visit_date'        => '2017-12-21 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Shakey\'s/@35.6591226,139.6983597,17z/data=!4m12!1m6!3m5!1s0x60188ca90a37c15f:0x15f45e1c33e21be7!2sShakey\'s!8m2!3d35.6609792!4d139.6978445!3m4!1s0x60188ca90a37c15f:0x15f45e1c33e21be7!8m2!3d35.6609792!4d139.6978445',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13012585/',
            'official_website'  => 'http://shakeys.jp/store/SHIBUYAUDAGAWA/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 0,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['all you can eat', 'pizza', 'pasta', 'japanese curry']);
        $resto->fillCoordinatesFromString('35.661210, 139.697813');

        $resto = Restaurant::create(array(
            'name'              => 'Kevelos',
            'location'          => 'Harajuku',
            'type'              => 'Pizza',
            'lunch_price'       => 1200,
            'points'            => 'Decent italian style pizza, but not cheap. Nice place with windows bay and some tables outside, in a small quiet street.',
            'experience'        => 'I liked the place. I ordered a marguerita pizza and it was decent, but costed 1200 yen with a small salad and some ice tea. I sat at the counter and could watch the guy making pizza in front of me, using a big wood fire oven.',
            'visited'           => true,
            'visit_date'        => '2017-12-25 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/PIZZA+KEVELOS+%E3%82%B1%E3%83%99%E3%83%AD%E3%82%B9+%E6%98%8E%E6%B2%BB%E7%A5%9E%E5%AE%AE%E5%89%8D%EF%BC%8F%E5%8E%9F%E5%AE%BF/@35.6671604,139.7048253,17z/data=!4m8!1m2!2m1!1spizza!3m4!1s0x0:0x98f5b7919f2719d6!8m2!3d35.6663673!4d139.7048575',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1306/A130601/13171079/',
            'official_website'  => 'http://kevelos.favy.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['pizza']);
        $resto->fillCoordinatesFromString('35.666659, 139.704900');

        $resto = Restaurant::create(array(
            'name'              => 'La Soffita',
            'location'          => 'Shibuya',
            'type'              => 'Pizza',
            'lunch_price'       => 1000,
            'points'            => 'Nice place, second floor with terrace. Pasta or pizza. Very good food. Very decent price. Warning: the prices get about 500 yen more expensive during weekends and holidays.',
            'experience'        => 'I was impressed by the big terrace. Inside is just decent. And then I was very surprised at how delicious their marguerita pizza was, and how big (32cm). The salad too. And that, with a coffee, for only 1000 yen? I never had such a great treat from an italian restaurant in Shibuya. They also have spicy oil if you ask.',
            'visited'           => true,
            'visit_date'        => '2017-12-27 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%83%A9%E3%83%BB%E3%82%BD%E3%83%95%E3%82%A3%E3%83%83%E3%82%BF/@35.659359,139.6979001,18z/data=!4m5!3m4!1s0x60188ca854f6a1ef:0x10dc7b929c86a181!8m2!3d35.6612092!4d139.699032',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13007485/',
            'official_website'  => 'https://www.lasoffitta.jp/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['pizza', 'pasta', 'italian']);
        $resto->fillCoordinatesFromString('35.661357, 139.699022');

        $resto = Restaurant::create(array(
            'name'              => 'Weekend Garage',
            'location'          => 'Shibuya',
            'type'              => 'Western',
            'lunch_price'       => 1080,
            'points'            => 'Nice and spacious place. Windows bay, high ceiling, with a large terrace as well. Enough space to go with a group. Menu has various western dishes such as burgers, hamburg, pasta, sals, deserts...',
            'experience'        => 'I liked the spacious room, and the view on the giant chimney outside. The price was right to me, 1180 for a premium cheese burger with fries, salad, soup and drink. The food was not especially delicious, but definitely good.',
            'visited'           => true,
            'visit_date'        => '2018-01-09 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/WGT+weekend+garage+tokyo/@35.6566471,139.6996475,17z/data=!4m5!3m4!1s0x60188b5b4d795555:0xc87a6fea4655753c!8m2!3d35.6527391!4d139.7057962',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13158522/',
            'official_website'  => '',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['western', 'burger', 'pasta', 'salad', 'hamburg']);
        $resto->fillCoordinatesFromString('35.653051, 139.705849');

        $resto = Restaurant::create(array(
            'name'              => 'Hacienda del Cielo',
            'location'          => 'Daikanyama',
            'type'              => 'Mexican',
            'lunch_price'       => 1200,
            'points'            => 'Nice and spacious place. Windows bay, high ceiling, with a large terrace, including a part that is covered as well. It\'s on the 9th floor, with a nice view (including Tokyo Tower). Variety of mexican food dishes from 1000 yen to more. Lunch menu includes a drink, a salad, and a small glass of sangria.',
            'experience'        => 'I liked the spacious room. The price was right to me, the food was very good and in decent quantity.',
            'visited'           => true,
            'visit_date'        => '2018-01-10 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Hacienda+del+Cielo/@35.6532243,139.6965174,16z/data=!4m5!3m4!1s0x0:0x911db2c2dfc8b756!8m2!3d35.6505182!4d139.7026198',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130303/13122348/',
            'official_website'  => 'http://modern-mexicano.jp/hacienda/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 2
        ));
        $resto->attachTags(['mexican']);
        $resto->fillCoordinatesFromString('35.650481,139.702594');

        $resto = Restaurant::create(array(
            'name'              => 'Pomme No Ki',
            'location'          => 'Shibuya',
            'type'              => 'Omurice',
            'lunch_price'       => 1000,
            'points'            => 'Omurice restaurant. Nothing fancy, cheap interior, almost no windows, cheesy music. Decent choice of omurice.',
            'experience'        => 'I did not like it much. But that place is alright if you want to eat an omurice. Lunch is about 1000 yen for one omurice with a small soup.',
            'visited'           => true,
            'visit_date'        => '2018-01-18 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%83%9D%E3%83%A0%E3%81%AE%E6%A8%B9+%E6%B8%8B%E8%B0%B7%E3%82%B9%E3%83%9A%E3%82%A4%E3%83%B3%E5%9D%82%E5%BA%97/@35.6589659,139.6987752,18z/data=!4m12!1m6!3m5!1s0x60188ca9ab7ca623:0x8f04136488fd0772!2z44Od44Og44Gu5qi5IOa4i-iwt-OCueODmuOCpOODs-WdguW6lw!8m2!3d35.6609578!4d139.6986733!3m4!1s0x60188ca9ab7ca623:0x8f04136488fd0772!8m2!3d35.6609578!4d139.6986733',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13103027/',
            'official_website'  => 'http://www.pomunoki.com/shop/kanto/tokyo/shop-0424.html',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['omurice']);
        $resto->fillCoordinatesFromString('35.660971, 139.698678');

        $resto = Restaurant::create(array(
            'name'              => 'Thai food pub Conrow',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Thai',
            'lunch_price'       => 800,
            'points'            => 'Underground, but cosy place. Food is good, and prices are decent.',
            'experience'        => 'I was expecting worse from the entrance, but once inside I was pleasingly surprised with the atmosphere of the place. I had a really good grilled chicken with yellow curry for only 800 yen (coming with a small, but good, salad). The chicken was without fat, very tender, nicely grilled (with burnt skin), and in very satisfactory quantity.',
            'visited'           => true,
            'visit_date'        => '2018-01-19 14:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/THAI+FOOD+PUB+CONROW/@35.6579282,139.6979337,18z/data=!4m12!1m6!3m5!1s0x60188ca9ab7ca623:0x8f04136488fd0772!2z44Od44Og44Gu5qi5IOa4i-iwt-OCueODmuOCpOODs-WdguW6lw!8m2!3d35.6609578!4d139.6986733!3m4!1s0x60188b55fb24e09f:0xbca949f57da95c3c!8m2!3d35.6572434!4d139.6958077',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13128650/',
            'official_website'  => null,
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['thai', 'gapao', 'green curry']);
        $resto->fillCoordinatesFromString('35.657250, 139.695807');

        $resto = Restaurant::create(array(
            'name'              => 'Acceso',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 990,
            'points'            => 'Italian bar-restaurant. Not real Italian. Lunch is mainly pasta, with nice course for 990 yen (drink bar, salad, bread, pasta and dessert).',
            'experience'        => 'The area with windows bay is for smoking allowed tables. The non-smoking area is in a big, but dark windowsless room. Large tables. Cheesy music. Vertical spot-lights on tables. The lunch set was surprisingly good for the price. Except maybe for the dessert, a tiramisu, which was absolutely tasteless, the pasta, salad and piece of bread were very fine.',
            'visited'           => true,
            'visit_date'        => '2018-01-23 14:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%82%A2%E3%83%81%E3%82%A7%E3%83%BC%E3%82%BE/@35.6585457,139.7004673,18z/data=!4m5!3m4!1s0x60188b59019f5c19:0x28da56b90bf422c9!8m2!3d35.6586583!4d139.7039616',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13120958/',
            'official_website'  => 'https://r.gnavi.co.jp/g851577/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['pasta']);
        $resto->fillCoordinatesFromString('35.658784, 139.704118');

        $resto = Restaurant::create(array(
            'name'              => 'Goo Italiano',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1200,
            'points'            => 'Italian restaurant with a nice atmosphere. Pasta, lasagna, meat... variety of pretty good lunch sets.',
            'experience'        => 'I really liked the place. It was quite busy at lunch time. There are some long tables, some counter tables, and some smaller tables as well. Variety of pasta and set with meat available. The food was very good. The price felt fine to me, given how I enjoyed the experience: 1200 yen for pasta + salad + bread + drink.',
            'visited'           => true,
            'visit_date'        => '2018-01-25 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/goo+ITALIANO+%E3%82%B0%E3%83%BC%E3%82%A4%E3%82%BF%E3%83%AA%E3%82%A2%E3%83%BC%E3%83%8E+%E6%B8%8B%E8%B0%B7/@35.659092,139.7004154,18z/data=!4m5!3m4!1s0x0:0xfdfae9faaf21546a!8m2!3d35.660879!4d139.704723',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13133728/',
            'official_website'  => 'http://www.take-5.co.jp/goo-italiano/shibuya/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian', 'pasta']);
        $resto->fillCoordinatesFromString('35.660891, 139.704722');

        $resto = Restaurant::create(array(
            'name'              => 'Ramen center gai',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => '',
            'experience'        => '',
            'visited'           => true,
            'visit_date'        => '2018-01-26 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen']);
        $resto->fillCoordinatesFromString('');

        $resto = Restaurant::create(array(
            'name'              => 'Hagare',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 900,
            'points'            => 'Nice room, nice music, nice food, decent prices. Italian food, but no pizza.',
            'experience'        => 'The room and music is very nice, but for some reason they use very small stools instead of chairs... which make it like a not so good place for a date. I had some lasgna, quite good, and a salad, bread and coffee, fine as well, for 980yen.',
            'visited'           => true,
            'visit_date'        => '2018-01-29 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/HAGARE%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6588789,139.7004375,18z/data=!4m5!3m4!1s0x60188b58045b55f1:0xafc420dda6788d31!8m2!3d35.66057!4d139.704088',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13209378/',
            'official_website'  => 'https://www.kaldi.co.jp/sp/cafe/hagare_shibuya/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['Italian']);
        $resto->fillCoordinatesFromString('italian','pasta');

        $resto = Restaurant::create(array(
            'name'              => 'Concombre',
            'location'          => 'Shibuya',
            'type'              => 'French',
            'lunch_price'       => 1300,
            'points'            => 'A nice little French restaurant, bistro style. Lunches are between 1000 and 2000 yen and come as plate with bread, salad, soup, dessert, and free refilled coffee. Food is good. Restaurant is decent for dates because of the nice French food, but no so great because of room and the proximity with other tables.',
            'experience'        => 'I enjoyed eating "tomate farcie", since it is quite rare to find. It was quite good. I paid 1300 yen for the lunch, which is above what I like to pay, but I think it was worth it. Seems like a lot of people going there between 12 and 13. Even after 13, it took quite a while for me to get served (about 20mn). Interesting note: you canbring your own wine if you pay 500 yen fee per bottle.',
            'visited'           => true,
            'visit_date'        => '2018-01-31 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Concombre/@35.6590569,139.7008431,18z/data=!4m5!3m4!1s0x60188b586fce186d:0xfac0c76926a651a1!8m2!3d35.660071!4d139.7031221',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13004708/',
            'official_website'  => 'http://www.lcn-g.com/concombre/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['french']);
        $resto->fillCoordinatesFromString('35.660079,139.703123');

        $resto = Restaurant::create(array(
            'name'              => 'House Pinocchio',
            'location'          => 'Shibuya, Cerulean',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Nice little restaurant. Pasta lunch for 1000 yen coming with salad and coffee. Counter and tables.',
            'experience'        => 'I enjoyed eating there. The waitress was very nice. The food was good. Price OK, but food amount not big.',
            'visited'           => true,
            'visit_date'        => '2018-02-02 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/HOUSE+Pinocchio/@35.6573518,139.6999394,18z/data=!4m5!3m4!1s0x60188b56e4646293:0x73626089e8d76f06!8m2!3d35.655811!4d139.698432',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13213606/',
            'official_website'  => 'https://www.facebook.com/pg/housepino/about/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian', 'pasta']);
        $resto->fillCoordinatesFromString('35.655867,139.698478');        

        $resto = Restaurant::create(array(
            'name'              => 'Fujisoba',
            'location'          => 'Shibuya',
            'type'              => 'Soba',
            'lunch_price'       => 750,
            'points'            => 'Kinda of japanese fast food, serving katsudon and soba dishes and sets. Place is not so bad. Food is not so good. Prices are cheap. Service is fast.',
            'experience'        => 'Place is better than some other establishments of the same chain. They have tables in addition of counter. Katsudon is decent, but soba and rice were below average in my opinion.',
            'visited'           => true,
            'visit_date'        => '2018-01-26 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Fuji+Soba/@35.660332,139.6975356,17z/data=!4m5!3m4!1s0x60188ca967b100e3:0x683e6960657664e6!8m2!3d35.6614936!4d139.6967719',
            'tabelog_link'      => '',
            'official_website'  => 'http://fujisoba.co.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['soba','katsudon']);
        $resto->fillCoordinatesFromString('35.661508,139.696770');

        $resto = Restaurant::create(array(
            'name'              => 'Ti Rolande',
            'location'          => 'Shibuya, Seibu B2F',
            'type'              => 'French',
            'lunch_price'       => 1300,
            'points'            => 'Delicious French galettes, quiches, and croque-monsieur. Quiet place (not a lot of people in Seibu B2F), comfortable. Food above my usual lunch budget, but worth it.',
            'experience'        => 'Lunch set are around 1300 yen for a mini salad and soup plus a dish. I tried galette complette and quiche, and it was delicious. They use very good ingredients, such as French ham and cheese, which is real nice.',
            'visited'           => true,
            'visit_date'        => '2018-02-07 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%83%86%E3%82%A3%E3%83%BB%E3%83%AD%E3%83%A9%E3%83%B3%E3%83%89+%E3%82%AB%E3%83%95%E3%82%A7+%E8%A5%BF%E6%AD%A6%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6591348,139.6991112,18z/data=!3m1!5s0x60188ca8177055e1:0xf009a238452b6721!4m8!1m2!2m1!1sTi+Rolande!3m4!1s0x0:0x344364f4c78281ba!8m2!3d35.6603571!4d139.700345',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13148254/',
            'official_website'  => 'http://ti-rolande.com/cafe/index.html',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['french','crepe','quiche','croque-monsieur','galette']);
        $resto->fillCoordinatesFromString('35.660365,139.700346');
        
        $resto = Restaurant::create(array(
            'name'              => 'Miami Patio',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Decent pasta in a decent place for a decent price.',
            'experience'        => 'Lunch there is not expensive, mostly under 1000yen. I tried some pasta, and it was very decent. The place is OK I guess, but I was sitting nearby some kind of machine or ventialltion that was making a ridiculously loud noise...',
            'visited'           => true,
            'visit_date'        => '2018-02-08 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Miami+patio/@35.6584554,139.7011902,18z/data=!4m8!1m2!2m1!1smiami+patio+!3m4!1s0x60188b584d44a14b:0x37734e7aa0f3fccd!8m2!3d35.6585169!4d139.7032196',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13134099/',
            'official_website'  => 'https://www.hotpepper.jp/strJ000707121/yoyaku/?vos=othpporgzzzzx00000001',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['italian','pasta']);
        $resto->fillCoordinatesFromString('35.658462,139.703349');


        $resto = Restaurant::create(array(
            'name'              => 'Ichiban',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 750,
            'points'            => 'Cheap homely ramen restaurant, serving wide variety of dishes in addition of ramen.',
            'experience'        => 'Grandma cooking. The food taste like in a japanese home. Not bad. Tables and counter available. Menu is in Japanese only.',
            'visited'           => true,
            'visit_date'        => '2018-02-13 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','miso']);
        $resto->fillCoordinatesFromString('35.660452,139.704341');

        $resto = Restaurant::create(array(
            'name'              => 'Adenia',
            'location'          => 'Shibuya',
            'type'              => 'Western',
            'lunch_price'       => 1000,
            'points'            => 'Very good.',
            'experience'        => 'Very good.',
            'visited'           => true,
            'visit_date'        => '2018-02-15 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Brasserie+%E3%82%A2%E3%83%87%E3%83%8B%E3%82%A2/@35.6568269,139.6996218,17z/data=!4m5!3m4!1s0x60188b5120875043:0x5c8bb3d4b989d422!8m2!3d35.653414!4d139.6988064',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['western','burger']);
        $resto->fillCoordinatesFromString('35.653435,139.698804');

        $resto = Restaurant::create(array(
            'name'              => 'Pota Pasta',
            'location'          => 'Shibuya',
            'type'              => 'Pasta',
            'lunch_price'       => 1000,
            'points'            => 'Decent pasta. Cheap price. Fast service. Narrow counter only.',
            'experience'        => '600 for decent size pasta dish. Nice. Choice between thick or thin spagethi. The counter is really narrow but in front of a window. Pasta come fast. Unfortunately, my tomato mozzarella pasta kind of lacked taste. Still, decent, and a good deal.',
            'visited'           => true,
            'visit_date'        => '2018-02-20 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/POTA+PASTA+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6585489,139.6984537,18z/data=!4m5!3m4!1s0x60188b57d475e281:0x267d9086f80cb4dc!8m2!3d35.6587047!4d139.6989325',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13193965/',
            'official_website'  => 'http://potapasta.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['pasta']);
        $resto->fillCoordinatesFromString('35.658718,139.698934');

        $resto = Restaurant::create(array(
            'name'              => 'Curry House Chili Tiri',
            'location'          => 'Shibuya',
            'type'              => 'Curry',
            'lunch_price'       => 950,
            'points'            => 'Tasty spicy Japanese style curry, using indian spices. Small place with just one counter. Often has a line of people waiting. Very fast serving. "Take out" also possible',
            'experience'        => 'I really really hate to wait in line outside... but once I did and did not regret it. I found the curry really delicious. And served very fast. I had the chicken massala for 950 yen, and it was quite spicy but not too much, and very tasty. Great balance. Also the pieces of chicken were very good. This is fast food style so better go alone.',
            'visited'           => true,
            'visit_date'        => '2018-02-21 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Curry+House+Chili+Chili/@35.656491,139.7041869,17z/data=!4m5!3m4!1s0x60188b5cb91448e9:0xe262216c91168a28!8m2!3d35.6541155!4d139.707545',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13002119/dtlrvwlst/B63241250/',
            'official_website'  => 'http://xn--7cka6jb.com/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.654137, 139.707527');

        $resto = Restaurant::create(array(
            'name'              => 'Tomboy',
            'location'          => 'Shibuya',
            'type'              => 'Curry',
            'lunch_price'       => 950,
            'points'            => 'Unexpensive and nice oriental food restaurant.',
            'experience'        => 'I liked the space and decor inside. Quite big. There are tables, counters, and also a tatami section. Good variety of lunch sets, for a very good price. For 1000 yen I could get salad, drink, 3 different kinds of curry, tandori chicken, and nan! The food was not especially delicious, but decent. With the exception of the spicy mutton curry, that was really delicious.',
            'visited'           => true,
            'visit_date'        => '2018-02-22 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/TOMBOY+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6586411,139.6980321,18z/data=!4m5!3m4!1s0x0:0xad6c1e648b6d070e!8m2!3d35.659088!4d139.69888',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13104579/',
            'official_website'  => 'http://www.tomboy106.com/shibuya106/access.html',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 1
        ));
        $resto->attachTags(['indian curry','gapao']);
        $resto->fillCoordinatesFromString('35.659122, 139.698909');
        
        $resto = Restaurant::create(array(
            'name'              => 'Chelsea Cafe',
            'location'          => 'Shibuya, Mark City',
            'type'              => 'Cafe',
            'lunch_price'       => 1200,
            'points'            => 'Unexpensive and nice oriental food restaurant.',
            'experience'        => 'I liked the space and decor inside. Quite big. There are tables, counters, and also a tatami section. Good variety of lunch sets, for a very good price. For 1000 yen I could get salad, drink, 3 different kinds of curry, tandori chicken, and nan! The food was not especially delicious, but decent. With the exception of the spicy mutton curry, that was really delicious.',
            'visited'           => true,
            'visit_date'        => '2018-02-23 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Chelsea+Cafe+Shibuya+Mark+City/@35.656849,139.6985482,18z/data=!3m1!5s0x60188b5631f5f4af:0xf2c804907c6b11f9!4m5!3m4!1s0x0:0x2af5c93390ed06d0!8m2!3d35.657695!4d139.697082',
            'tabelog_link'      => '',
            'official_website'  => 'http://www.dreamcorp.co.jp/cc/index.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['cafe','burger','sandwich']);
        $resto->fillCoordinatesFromString('35.657693, 139.697090');

        $resto = Restaurant::create(array(
            'name'              => 'Napoli',
            'location'          => 'Shibuya, 1Chome',
            'type'              => 'Italian',
            'lunch_price'       => 500,
            'points'            => 'Super cheap (at lunch time) good italian restaurant, with a nice interior (tables and counter).',
            'experience'        => 'The place is B1F but I liked it, nice ambiance, nice decoration. Pizza are 500 yen, made in a real oven in front of you. Maybe not the greatest pizza arround, but the quality is very good for the price (beats lots of most expensive restaurants). The lunch set also includes a small salad and soup.',
            'visited'           => true,
            'visit_date'        => '2018-02-26 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/PIZZERIA+BAR+NAPOLI+%E6%B8%8B%E8%B0%B7%E5%AE%AE%E7%9B%8A%E5%9D%82%E5%BA%97/@35.6599485,139.7035384,17z/data=!4m12!1m6!3m5!1s0x0:0x9e9b701b33f10831!2sTerra+burgers!8m2!3d35.660019!4d139.7065421!3m4!1s0x60188b557775a767:0x3b86406957ab553b!8m2!3d35.6610288!4d139.7060101',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13133770/',
            'official_website'  => 'https://r.gnavi.co.jp/eg632n6g0000/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pizza']);
        $resto->fillCoordinatesFromString('35.661034, 139.706101');


        $resto = Restaurant::create(array(
            'name'              => 'Curry Kailas',
            'location'          => 'Shibuya',
            'type'              => 'Curry',
            'lunch_price'       => 900,
            'points'            => 'Good japanese curry. Narrow counter only.',
            'experience'        => 'Small, narrow, but authetic place. Curry is good, filling, and cheap. Variety of toppings and customization available.',
            'visited'           => true,
            'visit_date'        => '2018-02-27 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%82%AB%E3%83%AA%E3%83%BC+%E3%82%AB%E3%82%A4%E3%83%A9%E3%82%B9/@35.6571556,139.6964024,19z/data=!4m5!3m4!1s0x0:0xc721eae685dbb1db!8m2!3d35.6571872!4d139.6963139',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13003347/',
            'official_website'  => 'http://www.shibuyadogenzaka.com/kairas/main.html',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.657186, 139.696305');

        ////////////////////////////// to complete below !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        $resto = Restaurant::create(array(
            'name'              => 'Whoopi Goldburger',
            'location'          => 'Shibuya',
            'type'              => 'Burger',
            'lunch_price'       => 1200,
            'points'            => 'Small and narrow counter. Unique and tasty home-made burgers.',
            'experience'        => 'The garage punk atmosphere is cool. The burgers are a bit expensive, but they do come with a drink, more french fries than usual, and are truly unique. Most of the taste comes from the topping, spices they put on the patty. Delicious spices mix, but quite salty. Fresh vegetables are used. My personal disapointement came from the fact that the patty had some hard bits in it, a bit like in a tsukune, and I don\'t like this. ',
            'visited'           => true,
            'visit_date'        => '2018-02-28 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Whoopi+goldburger/@35.6595163,139.7029215,18z/data=!4m5!3m4!1s0x60188b588e8beb35:0x28bc4f730f3a2180!8m2!3d35.6604164!4d139.7043645',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13121586/',
            'official_website'  => 'https://www.yelp.com/biz/%E3%82%A6%E3%83%BC%E3%83%94%E3%83%BC%E3%82%B4%E3%83%BC%E3%83%AB%E3%83%89%E3%83%90%E3%83%BC%E3%82%AC%E3%83%BC-%E6%B8%8B%E8%B0%B7%E5%8C%BA-2',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger']);
        $resto->fillCoordinatesFromString('35.6604551,139.6999866,16');

        $resto = Restaurant::create(array(
            'name'              => 'Aoyama Gapao Cafeteria',
            'location'          => 'Shibuya',
            'type'              => 'Thai',
            'lunch_price'       => 950,
            'points'            => 'Very good food with lunch set under 1000 yen. Great gapao. Nice and cosy interior. Also sells bentos.',
            'experience'        => 'I liked the place. Tables, isolated big tables, and counter available. I ordered a gapao, and it was delicious, coming with a very nice set (even including a Frisk candy).',
            'visited'           => true,
            'visit_date'        => '2018-03-01 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Aoyama+Gapao+cafeteria/@35.6605176,139.7072851,20z/data=!4m5!3m4!1s0x60188b5f23844e4d:0xa1cb2866057c2e9e!8m2!3d35.6605892!4d139.707427',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13041239/',
            'official_website'  => 'http://www.gapao-shokudo.com/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 3,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['thai','gapao']);
        $resto->fillCoordinatesFromString('35.660584, 139.707431');

        $resto = Restaurant::create(array(
            'name'              => 'Napolimania',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1200,
            'points'            => 'Nice place. Decent pizza, but felt a bit overpriced.',
            'experience'        => 'The place was nice and the music as well (rare thing). I had a marguerita pizza, and it was decent, but neither tasty nor big. For a price of 1200 yen, I was disapointed.',
            'visited'           => true,
            'visit_date'        => '2018-03-02 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/NAPOLIMANIA/@35.6597848,139.7115779,16z/data=!4m5!3m4!1s0x60188b5f21d207d7:0x2dc310dd7861f428!8m2!3d35.6604508!4d139.7076778',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13125763/',
            'official_website'  => 'http://www.napolimania.co.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pizza']);
        $resto->fillCoordinatesFromString('35.660443, 139.707686');

        $resto = Restaurant::create(array(
            'name'              => 'Usagi',
            'location'          => 'Shibuya',
            'type'              => 'Western Japanese',
            'lunch_price'       => 950,
            'points'            => 'Nice cosy place serving diffrent dishes every day. Japanese\'s western style food.',
            'experience'        => 'I had a avocado roastbeef on rice, coming with a soup and small salad, and it was very delicious. I also liked the interior, no windows, but very cosy.',
            'visited'           => true,
            'visit_date'        => '2018-03-06 14:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Usagi/@35.6599439,139.7052533,17z/data=!4m5!3m4!1s0x60188b5f4aaa6483:0xf826d8fa1bed3181!8m2!3d35.6609308!4d139.7064869',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13005386/',
            'official_website'  => '',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['western']);
        $resto->fillCoordinatesFromString('35.660917, 139.706510');

        $resto = Restaurant::create(array(
            'name'              => 'Moyan',
            'location'          => 'Shibuya',
            'type'              => 'Curry',
            'lunch_price'       => 1080,
            'points'            => 'All you can eat Japanese curry and salad buffet. Very good. 1080 yen. Very nice interior.',
            'experience'        => 'I immediately liked the inside of this restaurant very much. The buffet offerd a wide variety of curry (regular or spicy), rice, potatoes, chicken, salads and vegetables. No time limit, for a rather cheap price. Tables and counters with mangas available.',
            'visited'           => true,
            'visit_date'        => '2018-03-07 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%82%82%E3%81%86%E3%82%84%E3%82%93%E3%82%AB%E3%83%AC%E3%83%BC+246/@35.6607497,139.7074511,17z/data=!4m5!3m4!1s0x60188b58ca441c0b:0x70a4014716ce7539!8m2!3d35.6606752!4d139.7063233',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13090209/',
            'official_website'  => 'http://www.moyan.jp/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry','all you can eat']);
        $resto->fillCoordinatesFromString('35.6608075,139.7061413,21');

        $resto = Restaurant::create(array(
            'name'              => 'Yousyu Syonin',
            'location'          => 'Shibuya',
            'type'              => 'Chinese',
            'lunch_price'       => 920,
            'points'            => 'Good chinese restaurant with large choice of noodles and dumplings.',
            'experience'        => 'The interior is nothing fancy but spacious enough. I liked the choice offered by the menu in term of noodles and dumplings. I had some "eggplants mabu ramen" for 920yen and it was delicious. But as it is the case most of the time with noodles/ramen, I was still feeling a bit hungry after that.',
            'visited'           => true,
            'visit_date'        => '2018-03-08 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Yousyu-Syonin/@35.6593334,139.6996758,18z/data=!4m5!3m4!1s0x60188ca9654e3b75:0xcc1c755c2ecff434!8m2!3d35.6610877!4d139.696838?hl=en',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13198283/',
            'official_website'  => 'http://www.yousyusyonin.com/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['chinese','ramen','gyoza','dumpling','noodle']);
        $resto->fillCoordinatesFromString('35.661099, 139.696842');

        $resto = Restaurant::create(array(
            'name'              => 'Kua \'Aina',
            'location'          => 'Shibuya',
            'type'              => 'Burger',
            'lunch_price'       => 1500,
            'points'            => 'Nice burger chain. Wide choice of customizable, big, delicious burgers, for a price. Counter and tables availablein a spacious room with windows.',
            'experience'        => 'The inside is nice. Decor is nice, but it is organized as a fast food chain. However, the burgers are slow to come (like, 10 minutes). I paid about 1500 yen for a avocado mozarella burger set. Came with some good french fries and a drink. In conclusion, real good burger, but too expensive for me as a regular lunch.',
            'visited'           => true,
            'visit_date'        => '2018-03-09 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Kua+\'Aina,+%E3%83%A1%E3%82%BE%E3%83%B3%E3%83%BB%E3%83%89%E3%83%BB%E3%83%A6%E3%83%BC+1+Chome-10-4+Shibuya,+Shibuya-ku,+T%C5%8Dky%C5%8D-to+150-0002/@35.6590469,139.705265,18z/data=!4m2!3m1!1s0x60188b58be2d69f5:0xf3b420cd3cba980c',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13001358/',
            'official_website'  => 'http://www.kua-aina.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','sandwich']);
        $resto->fillCoordinatesFromString('35.6605744,139.7053451,22');

        $resto = Restaurant::create(array(
            'name'              => 'Ichiran',
            'location'          => 'Shibuya, Spain-zaka',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'Very good and customizable ramen. Very popular, so expect wait time.',
            'experience'        => 'Inside is like a windowless factory. you eat on a narrow counter with wooden panels on the side to isolate you from the persons next to you. Those panels can be folded if you are with friends. Ramen are easily customizable thanks to a paper available in multiple languages. If you know what you like, you can get very delicious ramen there. However, despite its 900 yen price, the base ramen is small and very few ingredients. Adding stuff makes the final price very expensive for a ramen.',
            'visited'           => true,
            'visit_date'        => '2018-03-16 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Ichiran/@35.6590403,139.7011174,17z/data=!4m5!3m4!1s0x60188ca9ab71057d:0x3cf8b41707c84c2b!8m2!3d35.6609782!4d139.6986629',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13166058/',
            'official_website'  => 'https://en.ichiran.com/shop/tokyo/shibuya-spain/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles']);
        $resto->fillCoordinatesFromString('35.6610036,139.6899592,15');

        $resto = Restaurant::create(array(
            'name'              => 'Hakata tenjin',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 750,
            'points'            => 'Ramen restaurant opened on the street. Cheap prices, good ramen, and variety of free toppings. Counter and tables available.',
            'experience'        => 'I like the fact that it is opened on the street. When it is cold, they have a plastic curtain to keep the inside warm. The ramen are really good for the price, and taste can easily be adjusted with available toppings. Speciality is tonkotsu but miso is also available. No machine, and English menu available.',
            'visited'           => true,
            'visit_date'        => '2018-03-19 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Hakata+tenjin+shibuya+minamiguchi/@35.6579276,139.6989165,19z/data=!3m1!4b1!4m5!3m4!1s0x60188b57b9d066b3:0xe8bbac7199ca83b!8m2!3d35.6579276!4d139.699465',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13004828/',
            'official_website'  => '',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','tonkotsu','miso']);
        $resto->fillCoordinatesFromString('35.657915, 139.699472');

        $resto = Restaurant::create(array(
            'name'              => 'Tinun',
            'location'          => 'Shibuya',
            'type'              => 'Thai',
            'lunch_price'       => 800 ,
            'points'            => 'Place is small, counter only, and not very comfortable. Food is very good and not expensive.',
            'experience'        => 'When I got there after 1pm, they did not have gapao available anymore. But I could get a porc gapao (instead of chicken) for 200yen more. It was still below 1000yen for the dish, and it was tasty, very good.',
            'visited'           => true,
            'visit_date'        => '2018-03-20 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%83%86%E3%82%A3%E3%83%BC%E3%83%8C%E3%83%B3%E3%83%BB%E3%83%80%E3%82%A4%E3%83%8B%E3%83%B3%E3%82%B0+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6580891,139.6983244,17.5z/data=!4m8!1m2!2m1!1z44OG44Kj44O844OM44Oz!3m4!1s0x0:0xd0db487936a61f3d!8m2!3d35.6579648!4d139.6993697',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13004646/',
            'official_website'  => 'http://www.spiceroad.co.jp/Shop_FC_Shibuya.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['thai','gapao']);
        $resto->fillCoordinatesFromString('35.657993, 139.699343');


        $resto = Restaurant::create(array(
            'name'              => 'Hokkaido Ramen',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'Standard ramen restaurant with just a counter. Slightly more confortable, less narrow than usual. Hokkaido specialities. Decent taste. Not cheap. Friendly staff and English menu available.',
            'experience'        => 'The counter is slightly larger than usual. I tried some shoyu ginger ramen for 900 yen. Decent, but I felt like for this price I should have got something better.',
            'visited'           => true,
            'visit_date'        => '2018-03-22 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles']);
        $resto->fillCoordinatesFromString('35.658978, 139.696993');

        $resto = Restaurant::create(array(
            'name'              => 'Nataraj',
            'location'          => 'Shibuya',
            'type'              => 'Indian curry',
            'lunch_price'       => 1080,
            'points'            => 'Vegetarian indian curry. Spacious and clean room with large tables. Lunch on menu or all-you-can-eat 1080yen buffet with 4 types of curry, 2 types of nan, rice, salad, and dessert.',
            'experience'        => 'I was very pleased with the place, bright, spacious, and clean. The buffet was good for the price. Not very spicy, which is not a problem to me, but not very tasty either, which is unfortunate.',
            'visited'           => true,
            'visit_date'        => '2018-03-26 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Nataraj/@35.6637637,139.6992895,15z/data=!4m5!3m4!1s0x0:0x81f4524b4461190c!8m2!3d35.6611253!4d139.7010487',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13187248/',
            'official_website'  => 'http://nataraj2.sakura.ne.jp/cafe3/language.html',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['indian curry','all you can eat']);
        $resto->fillCoordinatesFromString('35.661124, 139.701007');

        $resto = Restaurant::create(array(
            'name'              => 'Los barbados',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Exotic',
            'lunch_price'       => 900,
            'points'            => 'Very small cosy restaurant, with just a counter and maybe 7 seats. Cooks about 5 different dishes, mostly oriental curry and vegetarian.',
            'experience'        => 'I liked the ambiance of the place. It is very small but not uncomfortable. The dishes are about 900 yen for a decent amount and a salad/appetizer. I really enjoyed my chicken with lemon and wine sauce (the only non-vegetarian dish), it was very good.',
            'visited'           => true,
            'visit_date'        => '2018-03-29 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Los+barbados/@35.6603113,139.7021973,17z/data=!4m5!3m4!1s0x60188cac120307a9:0xbdcff87b735db17a!8m2!3d35.6627575!4d139.6952733',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13111493/',
            'official_website'  => 'http://www7b.biglobe.ne.jp/~los-barbados/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags([''curry', 'vegetarian'']);
        $resto->fillCoordinatesFromString('35.662753, 139.695291');

        $resto = Restaurant::create(array(
            'name'              => 'Thai Garden',
            'location'          => 'Shibuya',
            'type'              => 'Thai',
            'lunch_price'       => 950,
            'points'            => 'Very nice interior. Good lunch sets for a decent price.',
            'experience'        => 'Inside is quite nice, with multiple sections, different tables sizes and counter, and feels clean too. For 950yen I got a good gapao (not the best ever, not very spicy, but still delicious), a nie soup and a small salad, and even a desert.',
            'visited'           => true,
            'visit_date'        => '2018-03-30 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E6%B8%8B%E8%B0%B7+%E3%82%BF%E3%82%A4%E6%96%99%E7%90%86+%E5%80%8B%E5%AE%A4+%E9%A3%9F%E4%BA%8B+%E3%82%BF%E3%82%A4%E3%82%AC%E3%83%BC%E3%83%87%E3%83%B3/@35.6583444,139.7020995,17z/data=!4m5!3m4!1s0x0:0x4b86853a09b84b59!8m2!3d35.660515!4d139.699487',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13122178/',
            'official_website'  => 'http://thaigardenspice.com/thai-garden-shibuya/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['thai','gapao','green curry']);
        $resto->fillCoordinatesFromString('35.660501, 139.699428');

        $resto = Restaurant::create(array(
            'name'              => '',
            'location'          => 'Shibuya',
            'type'              => 'Vietnamese',
            'lunch_price'       => 950,
            'points'            => 'Good Vietnamese dishes, customizable sets, decent prices and decent place.',
            'experience'        => 'There were a nice space with tables, but unfortunaltey single people are placed on a less nice area, on a counter. It is still confortable though, and I appreciated the meal, very good. For 950yen, I got to choose a combination of Pho, a side dish, and some jelly for dessert.',
            'visited'           => true,
            'visit_date'        => '2018-04-03 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Hanoi+no+Hoi-san+%E3%83%8F%E3%83%8E%E3%82%A4%E3%81%AE%E3%83%9B%E3%82%A4%E3%81%95%E3%82%93/@35.6579689,139.7011462,18z/data=!4m5!3m4!1s0x60188b575cf41757:0x7eb6060afb8be20!8m2!3d35.6561919!4d139.7008321',
            'tabelog_link'      => '',
            'official_website'  => 'http://www.hoi-san.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['vietnamese']);
        $resto->fillCoordinatesFromString('35.656229, 139.700786');

        $resto = Restaurant::create(array(
            'name'              => 'C.C. Curry',
            'location'          => 'Shibuya',
            'type'              => 'Curry',
            'lunch_price'       => 600,
            'points'            => 'Fast, cheap customizable Japanese curry. Clean place. Tables and large counters.',
            'experience'        => 'Inside was better than a Cocoichi. More spacious, less busy, clean, overall more confortable. Prices are cheap, you can get a decent amount of food for 600yen. Dishes are customizable in a way very similar to Cocoichi. Salads also available. Curry is served almost immediately, no wait time. However, the curry is not very tasty and not very spicy. Quite bland, and the amount of curry itself in the dish is little.',
            'visited'           => true,
            'visit_date'        => '2018-04-04 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry', 'filling']);
        $resto->fillCoordinatesFromString('');
        

/*
        $resto = Restaurant::create(array(
            'name'              => '',
            'location'          => '',
            'type'              => '',
            'lunch_price'       => 950,
            'points'            => '',
            'experience'        => '',
            'visited'           => true,
            'visit_date'        => '2018-02-27 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['']);
        $resto->fillCoordinatesFromString('');
*/

// https://www.menya-cocoro.com/

        // https://hubjapan.io/articles/10-best-ramen-restaurants-in-shibuya

        // https://www.youtube.com/watch?v=S-Xm7s9eGxU
        // https://goo.gl/TbjN44

// nathan recommwend ramen place behind office behind mormons church

        // fill coordinates
        $this->fillNullCoordinatesFromGoogleMapsLink();
        
    }

    /**
    * Get all restaurants with null coordinates and
    * try to fill them with the ones found in the Google Maps link
    */
    private function fillNullCoordinatesFromGoogleMapsLink() : void
    {
        $result = Restaurant::whereNull('lat')->get();
        foreach ($result as $resto) {
            $resto->fillCoordinatesFromGoogleLink();
        }
    }

}
