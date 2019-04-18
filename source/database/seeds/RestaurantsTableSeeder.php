<?php

use Illuminate\Database\Seeder;
use App\Restaurant;

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
            'type'              => 'Burger',
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
        $resto->attachTags(['burger','fast food','to-go','visa','western','american']);
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
        $resto->attachTags(['burger','fast food','to-go','visa','western','american']);
        $resto->fillCoordinatesFromString('35.660390, 139.699116');

        $resto = Restaurant::create(array(
            'name'              => 'Taco Bell',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Mexican',
            'lunch_price'       => 900,
            'points'            => 'Mexican style fast food, mostly buritos and tacos. Possibility to choose beef porc or chicken for the meat.',
            'experience'        => 'Quite good, but it could be cheaper. Interior is just decent for a fast-food, but music is quite shitty.',
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
        $resto->attachTags(['mexican','fast food','to-go','visa']);
        $resto->fillCoordinatesFromString('35.660199, 139.697664');

        $resto = Restaurant::create(array(
            'name'              => 'Kebab Cafe',
            'location'          => 'Shibuya',
            'type'              => 'Turk',
            'lunch_price'       => 900,
            'points'            => 'Popular Turkish dishes such as kebab, possibility to eat inside on table. Dishes come on a plate, and with soup and tea.',
            'experience'        => 'Very good food in decent quantity, despite a rather cheap price. The place itself is not very confortable (narrow space).',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Kebab+Cafe/@35.660247,139.6970756,21z/data=!4m5!3m4!1s0x60188ca99be93651:0x461c229b06865bd9!8m2!3d35.6603228!4d139.6971273',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13120406/',
            'official_website'  => 'https://kebabcafe.jimdo.com/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['turk']);
        $resto->fillCoordinatesFromString('35.660339, 139.697128');

        $resto = Restaurant::create(array(
            'name'              => 'Coco Ichibanya',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Japanese curry',
            'lunch_price'       => 900,
            'points'            => 'Highly customizable Japanese curry. Very delicious. Counter only. Kinda smaller than most coco ichi.',
            'experience'        => 'Better known as just "Coco Ichi". Once you find the right combination for you, it can be really delicious. I personally have "chicken katsu (deepfried) curry + nasu (eggplants) + cheese, ichi-kara (spicy level 1)". English menu is available. It is easy to order a small, or a large serving, depending on how hungry you are. Coco Ichi is very present in Tokyo, so if you have the opportunity, I would recommend trying one that is not in Shibuya, as it will be more spacious and less crowded.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/CoCo%E5%A3%B1%E7%95%AA%E5%B1%8B+%E6%B8%8B%E8%B0%B7%E5%8C%BA%E6%A1%9C%E4%B8%98%E7%94%BA%E5%BA%97/@35.6569245,139.6982403,17z/data=!4m12!1m6!3m5!1s0x60188b579e0464dd:0x5838a075cb9595d6!2zQ29Db-WjseeVquWxiyDmuIvosLfljLrmoZzkuJjnlLrlupc!8m2!3d35.6569245!4d139.700429!3m4!1s0x60188b579e0464dd:0x5838a075cb9595d6!8m2!3d35.6569245!4d139.700429',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13161408/',
            'official_website'  => 'http://tenpo.ichibanya.co.jp/search/shop/pc/detail.php?SCODE=2678',
            'score_lunch'       => 4,
            'score_place'       => 1,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry','to-go','visa','English menu','customisable dishes','no smoking','filling','vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.657260, 139.700408');

        $resto = Restaurant::create(array(
            'name'              => 'Coco Ichibanya',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Japanese curry',
            'lunch_price'       => 900,
            'points'            => 'Highly customizable Japanese curry. Very delicious. Counter and tables. Kinda smaller than most coco ichi.',
            'experience'        => 'Better known as just "Coco Ichi". Once you find the right combination for you, it can be really delicious. I personally have "chicken katsu (deepfried) curry + nasu (eggplants) + cheese, ichi-kara (spicy level 1)". English menu is available. It is easy to order a small, or a large serving, depending on how hungry you are. Coco Ichi is very present in Tokyo, so if you have the opportunity, I would recommend trying one that is not in Shibuya, as it will be more spacious and less crowded.',
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
        $resto->attachTags(['japanese curry','to-go','visa','English menu','customisable dishes','no smoking','filling','vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.660804, 139.699485');

        $resto = Restaurant::create(array(
            'name'              => 'Jonathan\'s',
            'location'          => 'Shibuya',
            'type'              => 'Family restaurant',
            'lunch_price'       => 900,
            'points'            => 'Family restaurant with a wide choice of dishes, Japanese and western styles, also deserts.',
            'experience'        => 'Quite comfortable place. Food is very decent. Drink bar (unlimited soft drinks) available for cheap. Kids menu available.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Jonathan\'s/@35.6555207,139.6999556,18.75z/data=!4m12!1m6!3m5!1s0x60188b5ba1d0dd99:0xdad1c25747467595!2sJonathan\'s!8m2!3d35.654701!4d139.705094!3m4!1s0x60188b5745d1a4dd:0xb23984f5a40be1ec!8m2!3d35.6557358!4d139.700456',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13097970/',
            'official_website'  => 'http://www.skylark.co.jp/jonathan/index.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['western','visa']);
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
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13059175/',
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
            'points'            => 'Nice table, nie restaurant, decent food and drinks',
            'experience'        => 'Decent. I had a nice evening there dining with 4 people',
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
            'type'              => 'Pasta',
            'lunch_price'       => 500,
            'points'            => 'Super cheap pizza or pasta, wide and dark sports bar (no windows) with sports video playing on many walls.',
            'experience'        => 'I don\'t really like this dark, no windows bar atmosphere, playing cheesy music, and I don\'t enjoy watching sports on TV. The pizza marguerita was pretty much tasteless. Other than that service fast, table and room spacious, and super cheap...',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/M-SPO+cafe+dining/@35.6591098,139.6984993,17z/data=!4m5!3m4!1s0x60188ca910dcc451:0x66845d8439f43f38!8m2!3d35.6617719!4d139.6980353',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13039686/',
            'official_website'  => 'https://m-spo.jp/',
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
            'location'          => 'Shibuya',
            'type'              => 'Japanese curry',
            'lunch_price'       => 850,
            'points'            => 'Chain restaurant a bit similar to cocoichi. Customizable Japanese curry, counter and small tables, basement floor.',
            'experience'        => 'Good alternative to cocoichi. Less customizable, less options, but less expensive as well. Not really spicy, but good japanese curry. I enjoyed my chiken katsu with cheese. Basement is not very glamorous, but for a lunch it\'s OK. Loyalty System: Point card. 5 times: free toping. 10 times: free curry.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Go+Go+Curry+Shibuya+Keisatusho-mae+Stadium/@35.6573056,139.7014373,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b599edad969:0xd51cbf70012022f!8m2!3d35.6573056!4d139.7036313',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13127257/',
            'official_website'  => 'http://www.gogocurry.com/shop/119/index.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry']);
        $resto->fillCoordinatesFromString('35.657333, 139.703654');

        $resto = Restaurant::create(array(
            'name'              => 'Nice Pizza/Pasta restaurant',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => '9th floor with nice large bay windows, spacious room with large tables. Real pizza oven. 1000yen menu set with a drink, bowl of soup, large salad, and large pizza. music playing, boom boom type, a bit loud but OK if you are alone.',
            'experience'        => 'loved the room. fast service. Taste was bland (marguerita). The deco is all fine except for that reindeer\'s head sprouting out of a column for no apparent reasons.',
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
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Japanese fast-food',
            'lunch_price'       => 600,
            'points'            => 'Fast and good. Has counter and tables. Choice of curry or gyudon dishes.',
            'experience'        => 'I got a hamburger curry, it was really cheap and really good. Came fast too. Perfect for a quick meal, but not very glamorous.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Matsuya/@35.6590574,139.6984172,17z/data=!4m5!3m4!1s0x60188b59d831819b:0x71cfd0feb05de6ba!8m2!3d35.6571851!4d139.7015798',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13125238/',
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
            'points'            => 'Divine mazesoba/aburasoba (soupless ramen).',
            'experience'        => 'Mind blowingly delicious. Meat is awesome, smokey taste, and in large quantity. The shoyu base sauce is delicious. Large counter. Japanese reggai music, not too loud. Extremely small stools. Inside is cold in winter. Avoid the ramen, way less delicious than the mazesoba. That restaurant has most likely been created by the gods in an expirement to feed mere mortals divine food.',
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
        $resto->attachTags(['maze soba', 'abura soba', 'ramen','noodles']);
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
        $resto->attachTags(['karaage','to-go']);
        $resto->fillCoordinatesFromString('35.660329, 139.699224');

        $resto = Restaurant::create(array(
            'name'              => 'Khao Man Gai',
            'location'          => 'Shibuya',
            'type'              => 'Thai',
            'lunch_price'       => 800,
            'points'            => 'Supposedly famous (Thai) chicken rice',
            'experience'        => 'I\'ve been told there were a thai restaurant in Shibuya serving chicken rice, and that it was a popular chain from Thailand or something. So I went there, and arrived shortly in front a restaurant exposing dead chicken on its facade. No line. I go inside, and its small and crappy, with shitty music on the top. I order the famous chicken rice, for an outstanding 800yen. It comes quickly, with a soup (hot water) and some sauce (like sweet chinese shoyu with a touch of vinegar). The amount is ridiculously small (note that you could double it for 400yen). What\'s more, the chicken is actually half chicken, half chicken skin. I eat, nothing special at all. Rice is not really good. Chicken is nice and soft and tasteless, for the few bites I had on it. The sauce was good. Oh, and surprisingly enough, it also came with a mini-bucket full of pakuchi... this stuff is usually expensive here. And that was it, EXTREMELY disapointed. I felt robbed. Of my money. My time. My hopes. And still hungry.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E0%B8%A3%E0%B9%89%E0%B8%B2%E0%B8%99%E0%B8%82%E0%B9%89%E0%B8%B2%E0%B8%A7%E0%B8%A1%E0%B8%B1%E0%B8%99%E0%B9%84%E0%B8%81%E0%B9%88/@35.6584854,139.6999814,17z/data=!4m5!3m4!1s0x60188b5b85ab031f:0x35b195078881d9ca!8m2!3d35.6558927!4d139.7055917',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13168980/',
            'official_website'  => 'http://raan-kaithong-pratunam.jp/',
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
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Ramen',
            'lunch_price'       => 800,
            'points'            => 'Tonkostsu ramen, soupless ramen, curry, gyoza... and mini versions available.',
            'experience'        => 'Tried the white miso ramen, and it was pretty bland, not worth the 800yen. I liked the atmosphere, very local, kinda cosy, with the TV on and the grand-mothers as the staff.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%A9%E3%83%BC%E3%83%A1%E3%83%B3%E3%83%BB%E3%81%A4%E3%81%91%E9%BA%BA+%E6%A1%9C%E3%82%93%E5%9D%8A/@35.6581587,139.6991789,17z/data=!4m5!3m4!1s0x60188b575ce19af3:0xec3fd4ba6e393d06!8m2!3d35.6564414!4d139.700917',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13135184/',
            'official_website'  => 'https://ramendb.supleks.jp/s/23232.html',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles']);
        $resto->fillCoordinatesFromString('35.656451, 139.700907');

        /* Place closed - confirmed 2017-12-26
        $resto = Restaurant::create(array(
            'name'              => 'Cafde. SALICA',
            'location'          => '2nd floor in front of Samrat',
            'type'              => 'cafe restaurant, sandwiches burgers desserts...',
            'lunch_price'       => 1100,
            'points'            => 'large space and tables, some with sofa, music OK and not too loud, a diverse menu',
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

        /*
        // Closed due to block demolition
        $resto = Restaurant::create(array(
            'name'              => 'Mediterranean Pizzeria Domani',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
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
        */

        $resto = Restaurant::create(array(
            'name'              => 'Meat Bank',
            'location'          => 'Shibuya',
            'type'              => 'Burger',
            'lunch_price'       => 1200,
            'points'            => 'Nice interior, but loud music. Sofa and nice chairs. Wide windows bay. Nice staff. Premium burgers. Good. Lunch set 1200 yen avocado bacon cheese burger with drink and soup.',
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
        $resto->attachTags(['burger','grill','western','american']);
        $resto->fillCoordinatesFromString('35.661641, 139.697613');

        // This place has been shut down, building demolished for new building
        /*
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
        */

        // Building has been demolished
        /*
        $resto = Restaurant::create(array(
            'name'              => 'Samrat',
            'location'          => 'Shibuya',
            'type'              => 'Indian curry',
            'lunch_price'       => 900,
            'points'            => 'Do not go there.',
            'experience'        => 'Worst indian curry in existence. When I first discovered indian curry, around shimokitazawa, it blew my mind so hard I ate it almost everyday. I just could not imagine eating anything else. Then years passed, I realized not all restaurants were equals, but still indian curry was always rhytming with delicious, greatly enjoyable meal. Then I went around aoyama and realized that the world can be a cold and heartless place. The indian food there was bland. Bland AF. Then I moved on to leave what I thought was the darkest pit of restaurant\'s hell. To Shibuya! Surely one of the most amazing place to stroll for restaurants, and other things... But like in any movies and relationships, after all looked so perfect, something ugly, something world breaking happens and it all goes to shit. I ended up in Samrat shibuya today, and it sucked like I never thought it could suck. New low for indian curry, and a new addition to the collection of my most depressing experience in the world. Everything was bad, the interior was horrid, the nan was horribly oily, and the curry was doubtlessly the worst I ever had (and I asked for an easy one, the keema), the service, the atmosphere, the dirtiness... While eating I was just thinking at how fast I wanted to leave, and never come back. Ever.',
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
        $resto->attachTags(['indian','curry']);
        $resto->fillCoordinatesFromString('35.661795, 139.697803');
        */

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
        $resto->attachTags(['burger','fast food','to-go','visa']);
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
            'location'          => 'Shibuya, Miyamasuzaka',
            'type'              => 'Thai',
            'lunch_price'       => 1000,
            'points'            => 'Decent thai. Nice room, with windows bay. They have a gapao + green curry set, with good soup and small salad, for 980yen. Music. Open drink bar with tea and detox water.',
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
            'location'          => 'Shibuya',
            'type'              => 'American',
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
        $resto->attachTags(['burger','western','american']);
        $resto->fillCoordinatesFromString('35.661556, 139.703030');

        $resto = Restaurant::create(array(
            'name'              => 'Fujisoba',
            'location'          => 'Shibuya',
            'type'              => 'Japanese fast-food',
            'lunch_price'       => 750,
            'points'            => 'Decent sets. Fast food style. Narrow counter only. Buy at machine, get your set on a plate and find somewhere to sit.',
            'experience'        => 'Fast and cheap, and decent. I tried the zaru soba + katsudon for 750 yen. Made me full OK. Definitely not delicious, but decent. There are many Fujisoba joints, this one was the one in front of the big Bic Camera on the Hikarie\'s side of Shibuya.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Fuji+Soba/@35.6601748,139.7003162,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b580c9bf44f:0xf0aaf9378e534638!8m2!3d35.6601748!4d139.7025102',
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
            'location'          => 'Shibuya, Hikarie 7F',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Open space restaurant in nice Hikarie 7F. Busy floor, but nice setting. A bit expensive, but one of the cheapest restaurant in the building.',
            'experience'        => '1162 yen Pasta Set with Salad + bread + organic tomato avocado pasta. Nice salad, hot bread, and very, very good pasta. I would go as far as to call the pasta "exquisite". I also liked the setting, tables and chairs.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/iBeer+Le+Sun+Palm/@35.659252,139.6999228,17z/data=!3m1!5s0x60188b58f894f891:0xa230d50aab3d69e4!4m8!1m2!2m1!1siBeer!3m4!1s0x0:0xe0dfc9c31b013e93!8m2!3d35.659052!4d139.7037091',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13169825/',
            'official_website'  => 'https://www.facebook.com/%E3%82%A2%E3%82%A4%E3%83%93%E3%82%A2%E3%83%BC%E3%83%AB%E3%82%B5%E3%83%B3%E3%83%91%E3%83%BC%E3%83%A0-588128561304666/',
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
            'type'              => 'Indian curry',
            'lunch_price'       => 850,
            'points'            => 'Nice little indian restaurant. Not a chain. Relatively nice staff. No cheesy TV clips. Decent price curry sets. No keema. Free nan and rice.',
            'experience'        => 'Enjoyed the place, but the chiken curry I ordered was bland, tasteless.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%82%A2%E3%83%B4%E3%82%A1%E3%82%BF%E3%83%BC%E3%83%AB%EF%BC%88%EF%BC%A1%EF%BC%B6%EF%BC%A1%EF%BC%B4%EF%BC%A1%EF%BC%B2%EF%BC%89/@35.6583902,139.6980654,16.75z/data=!4m5!3m4!1s0x60188b5a14212195:0xaddbce0d1c82d4a7!8m2!3d35.654973!4d139.7023979',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13061017/',
            'official_website'  => 'https://retty.me/area/PRE13/ARE8/SUB803/100000448097/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 0,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['indian','curry']);
        $resto->fillCoordinatesFromString('35.655285, 139.702352');

        $resto = Restaurant::create(array(
            'name'              => 'Guzman y Gomez',
            'location'          => 'past tower record',
            'type'              => 'Mexican',
            'lunch_price'       => 1300,
            'points'            => 'Good mexican fastfood cuisine. American chain. Expensive.',
            'experience'        => 'First and foremost, this place is insanely overpriced. I got 3 kedasilla for 1070 yen. It felt like a small snack. Definitely insufficient for a lunch. I left hungry and had to go eat something cheaper elsewhere. Taste-wise, it was good, and I could choose the meat amongst 8 different choices, for each of my 3 small kedasilla. The interior, and ordering system, is like a cheap fastfood. When I ate there the doors were kept opened despite the cold and I had to keep my jacket on. The music was very cheesy.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Guzman+y+Gomez+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.662334,139.7004488,19z/data=!4m12!1m6!3m5!1s0x60188ca88131ea95:0x111613650b9602bd!2zR3V6bWFuIHkgR29tZXog5riL6LC35bqX!8m2!3d35.662334!4d139.700996!3m4!1s0x60188ca88131ea95:0x111613650b9602bd!8m2!3d35.662334!4d139.700996',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13213810/',
            'official_website'  => 'http://gyg.jp/',
            'score_lunch'       => 2,
            'score_place'       => 0,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['mexican','to-go','visa']);
        $resto->fillCoordinatesFromString('35.662400, 139.700988');

        $resto = Restaurant::create(array(
            'name'              => 'Il Fiume',
            'location'          => 'far',
            'type'              => 'Italian',
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
            'type'              => 'Indian',
            'lunch_price'       => 980,
            'points'            => 'Dark place on a 4th floor. Menu offers mostly indian curry sets, but also thai and pizza. The place does not look busy and there is a good amount of tables, so might be easy for large groups.',
            'experience'        => 'I tried the pizza+salad for 980yen. They have a real pizza oven to the crust was tasty from the wood fire. But other than that, it was quite plain. A lot of cheese on it though, so it made me well full. Salad was like the salad you get in a curry restaurant. The drink as well. I did not like how dark was the place. And the price was not cheap. So I won\'t be back. Fun note: the elevator to get there is really extremely slow.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/VERDE+%E3%83%B4%E3%82%A7%E3%83%AB%E3%83%87+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6590104,139.6992206,17.25z/data=!4m5!3m4!1s0x60188b599e9b7543:0xf6253420bcfbe3f5!8m2!3d35.6572417!4d139.7036924',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13144985/',
            'official_website'  => 'https://www.hotpepper.jp/strJ001044855/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['indian', 'curry', 'pizza']);
        $resto->fillCoordinatesFromString('35.657515, 139.703662');

        $resto = Restaurant::create(array(
            'name'              => 'Gapao Shokudō',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Thai',
            'lunch_price'       => 980,
            'points'            => 'Decent Thai restaurant.',
            'experience'        => 'Very small tables, for most of them. Gapao was OK, came with good soup and small salad, from 980yen (which is not a really good deal).',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Gapao+Shokud%C5%8D/@35.6578467,139.6987192,17.25z/data=!4m5!3m4!1s0x0:0xfdfd8b7f19684676!8m2!3d35.6555029!4d139.701378',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13174303/',
            'official_website'  => 'https://shibuya-gapaoshokudo.owst.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['thai']);
        $resto->fillCoordinatesFromString('35.655802, 139.701326');

        $resto = Restaurant::create(array(
            'name'              => 'Blacows',
            'location'          => 'Ebisu',
            'type'              => 'Burger',
            'lunch_price'       => 2000,
            'points'            => 'Expensive wagyu beef burgers, nice place.',
            'experience'        => 'Very good burgers. Customizable. Expensive. The interior is cool and eating there is a nice experience.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/BLACOWS/@35.652128,139.7005291,16z/data=!4m12!1m6!3m5!1s0x60188b43edf2af03:0xd017aeed6fd54147!2sBLACOWS!8m2!3d35.649399!4d139.7070914!3m4!1s0x60188b43edf2af03:0xd017aeed6fd54147!8m2!3d35.649399!4d139.7070914',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130302/13096437/',
            'official_website'  => 'http://www.kuroge-wagyu.com/bc/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['burger', 'western', 'customisable dishes']);
        $resto->fillCoordinatesFromString('35.649922, 139.707031');

        $resto = Restaurant::create(array(
            'name'              => 'Salasa',
            'location'          => 'Shibuya - Udagawacho',
            'type'              => 'Izakaya',
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
            'type'              => 'Burger',
            'lunch_price'       => 1500,
            'points'            => 'Very nice place (confortable, various tables, big windows bay, enjoyable music). Expensive. Premium burgers. Salad bar at lunch.',
            'experience'        => 'Very nice experience, enjoyed the place and the music. Loved the salad bar, with lots of choices, and good. Burger came fast. I tried the JS burger. The ingredient were undeniably good, but globally, it was lacking taste as there were almost no sauce. Still, I ended up full and satisfied.',
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
        $resto->attachTags(['burger','salad buffet','western']);
        $resto->fillCoordinatesFromString('35.662428, 139.699706');

        $resto = Restaurant::create(array(
            'name'              => 'Pizza Salvatore Cuomo',
            'location'          => 'Daikanyama',
            'type'              => 'Italian',
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
            'type'              => 'Burger',
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
        $resto->attachTags(['burger','to-go','visa']);
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
        $resto->attachTags(['western','american','mexican']);
        $resto->fillCoordinatesFromString('35.662745, 139.700018');

        $resto = Restaurant::create(array(
            'name'              => 'Ankara',
            'location'          => 'Shibuya',
            'type'              => 'Turk',
            'lunch_price'       => 1000,
            'points'            => 'Decent Turkish restaurant. B1F, quite dark place, but decent interior. Big enough to allow groups.',
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
            'points'            => 'Good pasta. Spaghetti with 2 bases: meat sauce or napolitan, starting 780yen and (very) customisable. Basement. Fast service. Tables and counter. Small, average, or large servings available.',
            'experience'        => 'I was happy with the quality for the price! And you can order up to 500g of pasta, with no additional charge. This is huge. Quick service. Several toppings on the tables, like free parmesan cheese. Interior is basment, might feel a tad gloomy, but decent. Staff is nice. Tables are confortable enough. In summer, the air conditioning there can be quite strong (cold).',
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
        $resto->attachTags(['pasta','filling','no smoking']);
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
            'experience'        => 'I liked the interior, spacious, with large tables. The music was a bit loud. I was impressed with the amount and variety of the food and sauces you can get. It tasted good too. The only complaint I would have is that the food is cold. If you get the meat right after a new batch has arrived, it will be warm, but otherwise it\'s all cold, which is kind of sad especially in winter. Nevertheless, I left this restaurant full and happy.',
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
            'score_lunch'       => 4,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
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
            'name'              => 'Hagare',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 900,
            'points'            => 'Nice room, nice music, nice food, decent prices. Italian food, but no pizza.',
            'experience'        => 'The room and music is very nice, but for some reason they use very small stools instead of chairs... which make it like a not so good place for a date. I had some lasgna, quite good, and a salad, bread and coffee, fine as well, for 980yen. On a second occasion, I tried the mozzarela, raw ham and toasted bread for 1200 yen, and it was actually delicious (yes, even the bread).',
            'visited'           => true,
            'visit_date'        => '2018-01-29 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/HAGARE%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6600991,139.7023562,17z/data=!4m5!3m4!1s0x60188b58045b55f1:0xafc420dda6788d31!8m2!3d35.66057!4d139.704088',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13209378/',
            'official_website'  => 'https://www.kaldi.co.jp/sp/cafe/hagare_shibuya/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pasta']);
        $resto->fillCoordinatesFromString('35.660612, 139.704054');

        $resto = Restaurant::create(array(
            'name'              => 'Concombre',
            'location'          => 'Shibuya',
            'type'              => 'French',
            'lunch_price'       => 1300,
            'points'            => 'A nice little French restaurant, bistro style. Lunches are between 1000 and 2000 yen and come as plate with bread, salad, soup, dessert, and free refilled coffee. Food is good. Restaurant is decent for dates because of the nice French food, but no so great because of room and the proximity with other tables.',
            'experience'        => 'I enjoyed eating "tomate farcie", since it is quite rare to find. It was quite good. I paid 1300 yen for the lunch, which is above what I like to pay, but I think it was worth it. Seems like a lot of people going there between 12 and 13. Even after 13, it took quite a while for me to get served (about 20mn). Interesting note: you can bring your own wine if you pay 500 yen fee per bottle.',
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
            'type'              => 'Japanese fast-food',
            'lunch_price'       => 750,
            'points'            => 'Kinda of japanese fast-food, serving katsudon and soba dishes and sets. Place is not so bad. Food is not so good. Prices are cheap. Service is fast.',
            'experience'        => 'Place is better than some other establishments of the same chain. They have tables in addition of counter. Katsudon is decent, but soba and rice were below average in my opinion.',
            'visited'           => true,
            'visit_date'        => '2018-01-26 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Fuji+Soba/@35.660332,139.6975356,17z/data=!4m5!3m4!1s0x60188ca967b100e3:0x683e6960657664e6!8m2!3d35.6614936!4d139.6967719',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13060781/',
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
            'points'            => 'Nice restaurant with various European style food (and home made burgers).',
            'experience'        => 'Interior is nice and makes for a comfortable lunch. Tables only, 2 floors. They have quite fancy, and good burgers. Each time you go they give you one special coin (one per person). With 2 coins you can get a free coffee and desert.',
            'visited'           => true,
            'visit_date'        => '2018-02-15 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Brasserie+%E3%82%A2%E3%83%87%E3%83%8B%E3%82%A2/@35.6568269,139.6996218,17z/data=!4m5!3m4!1s0x60188b5120875043:0x5c8bb3d4b989d422!8m2!3d35.653414!4d139.6988064',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13139147/',
            'official_website'  => 'http://www.adenia.jp/',
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
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Pasta',
            'lunch_price'       => 600,
            'points'            => 'Decent pasta. Cheap price. Fast service. Narrow counter only.',
            'experience'        => '600yen for decent size pasta dish. Nice. Choice between thick or thin spagethi. The counter is really narrow but in front of a window. Food comes fast. Unfortunately, my tomato mozzarella pasta kind of lacked taste. Still, decent, and a good deal. Another time I got small serving of arabiata for 480yen, and it was quite good.',
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
            'type'              => 'Indian',
            'lunch_price'       => 950,
            'points'            => 'Unexpensive and nice oriental food restaurant. Great lunch sets.',
            'experience'        => 'I liked the space and decor inside. Quite big. There are tables, counters, and also a tatami section. Good variety of lunch sets, for a very good price. For 1000 yen I could get salad, drink, 3 different kinds of curry, tandori chicken, and nan! The food was not especially delicious, but decent. With the exception of the spicy mutton curry, that was really delicious. Asecond time I tried the Thai, gapao set. Very nice set with spring rolls, soup, dessert, and quite a good gapao, for 950yen.',
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
        $resto->attachTags(['indian','curry','gapao','thai','smoking area']);
        $resto->fillCoordinatesFromString('35.659122, 139.698909');

        $resto = Restaurant::create(array(
            'name'              => 'Chelsea Cafe',
            'location'          => 'Shibuya, Mark City',
            'type'              => 'Cafe',
            'lunch_price'       => 1200,
            'points'            => 'Nice cafe with large windows bay and terrace.',
            'experience'        => 'The place looks nice but it can feels a bit cramped since it\'s not very spacious. I tried the main burger. I was decent but felt overpriced.',
            'visited'           => true,
            'visit_date'        => '2018-02-23 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Chelsea+Cafe+Shibuya+Mark+City/@35.656849,139.6985482,18z/data=!3m1!5s0x60188b5631f5f4af:0xf2c804907c6b11f9!4m5!3m4!1s0x0:0x2af5c93390ed06d0!8m2!3d35.657695!4d139.697082',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13161333/',
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
            'points'            => 'Good Japanese curry. Narrow counter only.',
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

        $resto = Restaurant::create(array(
            'name'              => 'Whoopi Goldburger',
            'location'          => 'Shibuya',
            'type'              => 'Burger',
            'lunch_price'       => 1200,
            'points'            => 'Small and narrow counter. Unique and tasty home-made burgers.',
            'experience'        => 'The garage punk atmosphere is cool. The burgers are a bit expensive, but they do come with a drink, more French fries than usual, and are truly unique. Most of the taste comes from the topping, spices they put on the patty. Delicious spices mix, but quite salty. Fresh vegetables are used. My personal disapointement came from the fact that the patty had some hard bits in it, a bit like in a tsukune, and I don\'t like this. ',
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
        $resto->attachTags(['burger','sandwich','to-go','visa']);
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
        $resto->attachTags(['indian','curry','all you can eat']);
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
        $resto->attachTags(['curry','vegetarian friendly']);
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
            'name'              => 'Hanoi no Hoi San',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Vietnamese',
            'lunch_price'       => 950,
            'points'            => 'Good Vietnamese dishes, customizable sets, decent prices and decent place.',
            'experience'        => 'There were a nice space with tables, but unfortunaltey single people are placed on a less nice area, on a counter. It is still confortable though, and I appreciated the meal, very good. For 950yen, I got to choose a combination of Pho, a side dish, and some jelly for dessert.',
            'visited'           => true,
            'visit_date'        => '2018-04-03 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Hanoi+no+Hoi-san+%E3%83%8F%E3%83%8E%E3%82%A4%E3%81%AE%E3%83%9B%E3%82%A4%E3%81%95%E3%82%93/@35.6579689,139.7011462,18z/data=!4m5!3m4!1s0x60188b575cf41757:0x7eb6060afb8be20!8m2!3d35.6561919!4d139.7008321',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13126583/',
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
            'name'              => 'C.C. Curry Shop',
            'location'          => 'Shibuya',
            'type'              => 'Curry',
            'lunch_price'       => 600,
            'points'            => 'Fast, cheap customizable Japanese curry. Clean place. Tables and large counters.',
            'experience'        => 'Inside was better than a Cocoichi. More spacious, less busy, clean, overall more confortable. Prices are cheap, you can get a decent amount of food for 600yen. Dishes are customizable in a way very similar to Cocoichi. Salads also available. Curry is served almost immediately, no wait time. However, the curry is not very tasty and not very spicy. Quite bland, and the amount of curry itself in the dish is little.',
            'visited'           => true,
            'visit_date'        => '2018-04-04 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%AB%E3%83%AC%E3%83%BC+%E3%82%B7%E3%83%A7%E3%83%83%E3%83%97+%EF%BC%A3%26%EF%BC%A3+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6580951,139.7004052,18z/data=!4m8!1m2!2m1!1sC.C.+curry+shop!3m4!1s0x0:0x253b72297e04c7b5!8m2!3d35.6586559!4d139.6990921',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13049762/',
            'official_website'  => 'http://www.curry-cc.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry', 'filling']);
        $resto->fillCoordinatesFromString('35.658675, 139.699079');

        $resto = Restaurant::create(array(
            'name'              => 'Ramen Shibuhide',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 950,
            'points'            => 'A decent ramen place. Tonkotsu ramen. Counter and tables. English available.',
            'experience'        => 'Inside was not any kind of cool, but confortable enough. For 980yen I got a delicious roasted pork ramen of a copious amount. I was surprised at the amount of meat I got, and I liked the soup and the egg and vegetables put there by default (no need to fiddles with toppings, it is all there). If you are really hungry you can order additional rice dishes (and white rice is free!).',
            'visited'           => true,
            'visit_date'        => '2018-04-05 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%89%E3%81%82%E3%82%81%E3%82%93+%E6%B8%8B%E8%8B%B1/@35.6588138,139.7014914,18z/data=!4m5!3m4!1s0x60188b57da17d377:0xfb94787ddaad4bef!8m2!3d35.6589781!4d139.699479',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13073252/',
            'official_website'  => 'http://www.shibuyaism.com/entry/2016/11/19/130000',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen', 'filling']);
        $resto->fillCoordinatesFromString('35.658967, 139.699456');

        $resto = Restaurant::create(array(
            'name'              => 'Tapas & Tapas',
            'location'          => 'Shibuya, icchome',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Decent pasta, decent place, smoking allowed. Not really a good deal, especially for non-smoker.',
            'experience'        => 'Despite being in B1F, the place is relatively nice. The problem if you do not smoke, is that there is no real separation between smoking and non-smoking. The smoke is well vented, but still the smell is not great... Ohter than that, the staff was nice, I liked my pasta, but it was kind of overpriced in my eyes: 1000 yen for a decent-but-not-copious plate of pasta and free drink bar.',
            'visited'           => true,
            'visit_date'        => '2018-04-09 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Tapas+%26+Tapas/@35.6584517,139.7023856,18z/data=!4m5!3m4!1s0x60188b586f4eb4d3:0x7228eafa6c2fdef4!8m2!3d35.659914!4d139.703201',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13013728/',
            'official_website'  => 'http://www.tapas-tapas.com/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['pasta', 'smoking allowed', 'visa']);
        $resto->fillCoordinatesFromString('35.659822, 139.703296');

        $resto = Restaurant::create(array(
            'name'              => 'Tharros',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Nice and big interior. Great salad buffet during lunch time. Mediocre pasta dish.',
            'experience'        => 'The interior is very nice, and quite spacious. I was pleased. The buffet was great, including carrots, lentils, eggs, fried potatoes, onions, bread, and a delicious baslsamic sauce dressing. I tried the mozarealla and tomato sauce spagethi dish, but it was quite mediocre, especially with regards to the tomato sauce. But, for 1000yen, it made me full and the buffet makes it worth it.',
            'visited'           => true,
            'visit_date'        => '2018-05-21 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Tharros/@35.6577442,139.7007574,18z/data=!4m5!3m4!1s0x60188b57b83f1c37:0x895372195d90c946!8m2!3d35.6580471!4d139.6995289',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13046432/',
            'official_website'  => 'http://www.tharros.jp/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pasta','buffet']);
        $resto->fillCoordinatesFromString('35.658051, 139.699532');

        $resto = Restaurant::create(array(
            'name'              => 'Sgrosso',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Decent lunch menu of western food for a decent price. If the weather is good enough the place opens to open air, which is nice, especially since there is almost no car passing in this street. Table and counter available.',
            'experience'        => 'I enjoyed sitting on a table outside for lunch in a nice sprint\'s day. For 1000yen, I got a plate with different salads/entry, a coffee, and a marguerita pizza. The entry plate was very good, then the pizza looked like a japanese frozen pizza with some additional cheeze on the top... but it was not bad and quite filling. Tables outside have stools for sitting, which is not very confortable, but the nice feeling you get from eating outisde in a peaceful street should outweight this minor inconvenience.',
            'visited'           => true,
            'visit_date'        => '2018-05-23 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Trattoria+Sgrosso/@35.6571908,139.7011768,18z/data=!4m5!3m4!1s0x60188b57b4dc9449:0xf5146fd0f442e229!8m2!3d35.6576812!4d139.6988916',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13019705/',
            'official_website'  => 'https://sgroa996300.gorp.jp/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pasta','pizza','open air space','smoking allowed','visa']);
        $resto->fillCoordinatesFromString('35.657738, 139.698912');

        $resto = Restaurant::create(array(
            'name'              => 'Fatburger',
            'location'          => 'Shibuya, 109men',
            'type'              => 'Burger',
            'lunch_price'       => 1200,
            'points'            => 'Decent burgers. A bit overpriced. American franchise. 7th floor of the 109men, above the Shibuya\'s crossing, no windows but nice interior. Seems not too crowded.',
            'experience'        => 'Clean and nice place, and not too crowded. I got a cheese burger set for 1200 yen, drink bar and fries. The burger was good, but not very big. It felt a bit overpriced (they also have 800yen milkshakes...).',
            'visited'           => true,
            'visit_date'        => '2018-05-23 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Fatburger/@35.6597313,139.7008906,18z/data=!4m12!1m6!3m5!1s0x60188b57fda41cc3:0x535ec43fca150b42!2sFatburger!8m2!3d35.6597313!4d139.7008906!3m4!1s0x60188b57fda41cc3:0x535ec43fca150b42!8m2!3d35.6597313!4d139.7008906',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13221401/',
            'official_website'  => 'https://www.fatburger.tokyo/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','western','american','to-go','visa']);
        $resto->fillCoordinatesFromString('35.659714, 139.700917');

        $resto = Restaurant::create(array(
            'name'              => 'Village Vanguard Diner',
            'location'          => 'Harajuku',
            'type'              => 'Burger',
            'lunch_price'       => 2000,
            'points'            => 'Delicious burgers. Expensive. Long wait. Nice interior that can open to outside with also a terrace.',
            'experience'        => 'I liked the interior, quite nice and opened to outside, even on the second floor. Jazz music playing. I ordered a avocaado cheese burger, that was 1600yen, and a set potatoes + drink for an additional 400. Despite the restaurant not being busy, it took about 25 minutes sfor the burger to come, and an additional 5 minutes for the potatoes to join the feast. It was all delicious, even the coffee. I especially enjoyed the super thick patty, and the burnt onions.',
            'visited'           => true,
            'visit_date'        => '2018-05-27 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/VILLAGE+VANGUARD+DINER+Harajuku/@35.6623121,139.7067824,17z/data=!4m5!3m4!1s0x60188ca4217dba71:0x3c3f676a9f378948!8m2!3d35.6664394!4d139.7050229',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1306/A130601/13209290/',
            'official_website'  => 'https://www.village-v.co.jp/shop/detail/706',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','filling','open air space']);
        $resto->fillCoordinatesFromString('35.666436, 139.705007');

        $resto = Restaurant::create(array(
            'name'              => 'Lowline',
            'location'          => 'Shibuya',
            'type'              => 'Hamburg',
            'lunch_price'       => 900,
            'points'            => 'Peaceful underground place with confortable tables. A bit dark. Filling lunch sets, mostly hamburg and rice.',
            'experience'        => 'I don\'t like basements much, but this one was clean, with soft guitar music and large tables. I took a hamburg with cheese and salad, rice and fries. Good, very filling, for just 900 yen. Smoking is allowed inside.',
            'visited'           => true,
            'visit_date'        => '2018-05-29 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Lowline%EF%BC%88%E3%83%AD%E3%83%BC%E3%83%A9%E3%82%A4%E3%83%B3%EF%BC%89/@35.6565666,139.6957154,15z/data=!4m5!3m4!1s0x0:0x9e5818f94b0e5ec2!8m2!3d35.6565666!4d139.6957154',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13211084/',
            'official_website'  => 'https://lowline.gorp.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['hamburg', 'filling']);
        $resto->fillCoordinatesFromString('35.656526, 139.695628');

        $resto = Restaurant::create(array(
            'name'              => 'Cento Anni',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1080,
            'points'            => 'Decent Japanese Italian restaurant.',
            'experience'        => 'Room and tables big enough. Music is low but people talking all around might be noisy. Food is decent. 1080 gets you a lunch set with a nice salad and a dish from the daily menu. Service was fast.',
            'visited'           => true,
            'visit_date'        => '2018-05-30 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/CENTO+ANNI/@35.6564274,139.6982254,17z/data=!4m5!3m4!1s0x60188b56ef9ff36b:0xc74dc285a316e1ee!8m2!3d35.65573!4d139.698043',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13018952/',
            'official_website'  => 'https://www.centoanni.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['italian']);
        $resto->fillCoordinatesFromString('35.655749, 139.698027');

        $resto = Restaurant::create(array(
            'name'              => 'Katsu midori sushi',
            'location'          => 'Shibuya',
            'type'              => 'Sushi',
            'lunch_price'       => 1000,
            'points'            => 'Big and popular (read busy) sushi restaurant. Lot of choices (hundreds) with not only sushi, but meat and different dishes as well. Reasonably priced. Decent taste.',
            'experience'        => 'There seems to be always a line of people sitting, waiting their turn to be able to enter the place. THe place is quite big, with tables and counters. You can grab items from the convey belt running everywhere, but there is a big menu with hundreds of choices and paper where you just have to put dish number and amount to place an order. You can get a dish of 2 sushi for as cheap as 100 yen. Tastes decent. There is also a fair amount of vegetarian sushi and dishes. I was surprised at how good the avocado tenpura was, and quite filling for 250 yen. The amount of fish on each sushi is quite nice.',
            'visited'           => true,
            'visit_date'        => '2018-05-31 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Katsu+Midori+Seibu+Shibuya/@35.6578812,139.7035082,16z/data=!4m5!3m4!1s0x0:0xa9b1e2d5e31cd607!8m2!3d35.6602348!4d139.7005149',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13135066/',
            'official_website'  => 'http://katumidori.co.jp/shibuya/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['sushi', 'vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.660271, 139.700488');

        $resto = Restaurant::create(array(
            'name'              => 'Nagi Ramen',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 870,
            'points'            => 'Small ramen shop serving ANCHOVY based soup ramen and tsukemen.',
            'experience'        => 'I purchased a ticket for tsukemen from outside, not knowing what I was getting. I could smell anchovy right after I opened the door. I really hate anchovy... but I took it upon myuself to try. The tsukemen came, and... ell, the quality of the soup was quite great. I could eat everything. If you don\'t hate anchovies, this ramen will most likely be great.',
            'visited'           => true,
            'visit_date'        => '2018-06-04 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Ramen+Nagi+Shibuya-East+side/@35.6584537,139.70283,17z/data=!4m8!1m2!2m1!1snagi!3m4!1s0x0:0x22c79272cc719e9b!8m2!3d35.6579201!4d139.7046503',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13137167/',
            'official_website'  => 'http://www.n-nagi.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','tsukemen']);
        $resto->fillCoordinatesFromString('35.657917, 139.704644');

        $resto = Restaurant::create(array(
            'name'              => 'Taiyou no tomato men',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 1000,
            'points'            => 'Tomato base ramen.',
            'experience'        => 'I got the tomato ramen with chicken and cheese, with some cheese yakigyoza on the side. Delicious. Nice interior.',
            'visited'           => true,
            'visit_date'        => '2018-06-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E5%A4%AA%E9%99%BD%E3%81%AE%E3%83%88%E3%83%9E%E3%83%88%E9%BA%BA+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6580421,139.6958017,17z/data=!4m5!3m4!1s0x60188ca9e5bbb441:0x5767b4819e27b97d!8m2!3d35.6587744!4d139.6970945',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13216348/',
            'official_website'  => 'http://taiyo-tomato.com/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen']);
        $resto->fillCoordinatesFromString('35.658763, 139.697123');

        $resto = Restaurant::create(array(
            'name'              => 'Mistu-Fuji Dining',
            'location'          => 'Shibuya',
            'type'              => 'Izakaya',
            'lunch_price'       => 850,
            'points'            => 'Clean Japanese isakaya restaurant, casually opened for lunch. 100 yen discount on everything after 13:30.',
            'experience'        => 'Tables. Some music playing, a TV is on as well. Free refill for rice. I had a avocado and mashed tuna on rice, with some shiso leaves and spices, it was absolutely delicious. Some pickles and miso soup on the side. I had this after 13:30 so it cost me only 750yen, which is awesome.',
            'visited'           => true,
            'visit_date'        => '2018-06-27 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%83%9F%E3%83%84%E3%83%95%E3%82%B8%E3%83%80%E3%82%A4%E3%83%8B%E3%83%B3%E3%82%B0/@35.6575325,139.6966552,17z/data=!4m5!3m4!1s0x60188b56408ddc07:0xbb0d1f999517a9d6!8m2!3d35.6573233!4d139.697712',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13176101/',
            'official_website'  => 'https://mitsufuji.owst.jp/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['teishoku','japanese','izakaya','no smoking']);
        $resto->fillCoordinatesFromString('35.657316, 139.697756');

        $resto = Restaurant::create(array(
            'name'              => 'Gusto',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Family',
            'lunch_price'       => 850,
            'points'            => 'Chain of family restaurant with wide choice of dishes, sides and desserts. Reasonably priced.',
            'experience'        => 'The place has a smoking area, and a non smoking area. Tables and counters in front of windows bay. Not especially good, but decent for the price.',
            'visited'           => true,
            'visit_date'        => '2018-06-09 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Gusto+Shibuya+Dogenzaka/@35.6574217,139.6963248,17z/data=!4m8!1m2!2m1!1sgasto!3m4!1s0x0:0xce74765a3efb9ab2!8m2!3d35.6576678!4d139.6959901',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13115272/',
            'official_website'  => 'https://www.skylark.co.jp/gusto/index.html',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['teishoku','western','menu with calories','all you can drink','smoking area','visa']);
        $resto->fillCoordinatesFromString('35.657678, 139.696085');

        $resto = Restaurant::create(array(
            'name'              => 'Belle Mare Cafe',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 900,
            'points'            => 'Confortable place to have an italian lunch for a decent price.',
            'experience'        => 'The place was quite confortable. Italian pasta and pizza available, ranging from 850 to 1000 yen for a set including a soup and a drink. I tried some pasta. It was decent. Soup was good.',
            'visited'           => true,
            'visit_date'        => '2018-06-11 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Belle+Mare+Cafe+Shibuya/@35.658043,139.7009337,17z/data=!4m5!3m4!1s0x60188b5735f711d1:0x54f167e7ea87968c!8m2!3d35.6558748!4d139.7001743',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13118461/',
            'official_website'  => 'https://caffe.belmare.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pasta','pizza']);
        $resto->fillCoordinatesFromString('35.655717, 139.700077');

        $resto = Restaurant::create(array(
            'name'              => 'Hayashi',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 1100,
            'points'            => 'Very popular ramen, with a line of people waiting outside at all time.',
            'experience'        => 'I finally tried it one day I saw the line was only of 3 people waiting. 1100 yen to get the full version of their ramen. Other choices are the same ramen with less meat, and no egg. One counter. Rather confortable. No customization possible for the one ramen you can order. I admit the quality is OK, and it is not a bad ramen, maybe well balanced, but I could not find anything special, and would not even call it delicious. Moreover, a downer for me, is that the soup has a fishy taste, which I don\'t appreciate much. Definitely not worth the wait time.',
            'visited'           => true,
            'visit_date'        => '2018-06-12 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Hayashi+Ramen/@35.6580377,139.6987824,17z/data=!4m5!3m4!1s0x0:0x2b49bf8aea2ce949!8m2!3d35.6572967!4d139.6980153',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13003367/',
            'official_website'  => 'https://ramendb.supleks.jp/s/177.html',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen']);
        $resto->fillCoordinatesFromString('35.657297, 139.697979');

        $resto = Restaurant::create(array(
            'name'              => 'Mushi-sho tsutsumi gyoza',
            'location'          => 'Shibuya',
            'type'              => 'Chinese',
            'lunch_price'       => 870,
            'points'            => 'Quite good dumplings. Chinese fast food. Underground (B1F). Counter and tables. Mostly gyoza and chicken.',
            'experience'        => 'For a decent price, you can quickly get some pretty good Chinese lunch sets. The gyoza were surprisingly good. My chicken eggplants with vinegar sauce was good. You can choose rice or noodles. All sets below 900 yen. Going underground there is not very welcoming, but once inside, it\'s pretty fine by this type of fast-food restaurants standards..',
            'visited'           => true,
            'visit_date'        => '2018-06-13 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%82%80%E3%81%97%E7%84%BC+%E5%8C%85%E9%A4%83%E5%AD%90/@35.6584497,139.6997785,18z/data=!4m5!3m4!1s0x60188ca83b417dc5:0x2e7e5c9247ac10aa!8m2!3d35.6599774!4d139.6996927',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13171735/',
            'official_website'  => 'https://r.gnavi.co.jp/4t0rbhp00000/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['chinese','dumplings']);
        $resto->fillCoordinatesFromString('35.659986, 139.699698');

        $resto = Restaurant::create(array(
            'name'              => 'Sushi go round',
            'location'          => 'Shibuya',
            'type'              => 'Sushi',
            'lunch_price'       => 1000,
            'points'            => 'Modern sushi-go-round restaurant where you use an individual tablet to place sushi order. Counter only.',
            'experience'        => 'Decent sushi. Saumon was not great, but tuna was better (and bigger pieces) for the same price (from 180yen for 2 sushi). Ordering on the tablet is very convenient and can be done in English. Your order will come in front of you on a train. A regular convey belt also can provide with random items you are free to pick.',
            'visited'           => true,
            'visit_date'        => '2018-06-14 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Kyodaru/@35.65751,139.6992125,18z/data=!4m8!1m2!2m1!1sKyodaru!3m4!1s0x0:0xe9af8173ad3afb74!8m2!3d35.6586245!4d139.6990317',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13199827/',
            'official_website'  => 'https://www.kyotaru.co.jp/shopsearch/shop/17981.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['sushi']);
        $resto->fillCoordinatesFromString('35.658616, 139.698996');

        $resto = Restaurant::create(array(
            'name'              => 'Segafredo Zanetti',
            'location'          => 'Shibuya',
            'type'              => 'Cafe',
            'lunch_price'       => 1000,
            'points'            => 'Large, kinda chilling, cafe. 3 floors, including one for smokers. Tables and counter in front of windows bay.',
            'experience'        => 'You can have a nice view of the street in front of the Mega Donkey. Food is kind of over-priced, which is always the case in this sort of establishment. They have snadwiches, panini, pizza, cakes... It is Italian oriented. Most options feature some seafood. I tried a cheese ham panini for 450 yen, and it was decent, but far from great. Better not be too hungry...',
            'visited'           => true,
            'visit_date'        => '2018-06-14 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Segafredo+Zanetti/@35.6588205,139.6987653,18z/data=!4m8!1m2!2m1!1sSegafredo!3m4!1s0x0:0x3b179d38a9bcfa26!8m2!3d35.6601481!4d139.6974589',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13004656/',
            'official_website'  => 'http://www.segafredo.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe', 'small servings', 'italian','smoking area','open air space']);
        $resto->fillCoordinatesFromString('35.660203, 139.697491');

        $resto = Restaurant::create(array(
            'name'              => 'Katsuya',
            'location'          => 'Shibuya',
            'type'              => 'Katsu',
            'lunch_price'       => 850,
            'points'            => 'Cheap and fast katsudon and tonkatsu dishes. Japanese fast-food style. Counters and ticket machine with English available.',
            'experience'        => 'Not especially delicious, but a decent for the price. Another good point: the meat used for the tonkatsu didn\'t have any big fat part.',
            'visited'           => true,
            'visit_date'        => '2018-06-18 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Katsuya/@35.6579076,139.6995951,18z/data=!4m8!1m2!2m1!1z44GL44Gk44KE!3m4!1s0x0:0xb2096da56c69f1d3!8m2!3d35.6587629!4d139.6982981',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13030860/',
            'official_website'  => 'http://www.arclandservice.co.jp/katsuya/shop/731.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['katsudon','tonkatsu','filling']);
        $resto->fillCoordinatesFromString('35.658768, 139.698308');

        $resto = Restaurant::create(array(
            'name'              => 'Toridon (?)',
            'location'          => 'Shibuya, 3Chome',
            'type'              => 'Katsu',
            'lunch_price'       => 850,
            'points'            => 'Nice katsudon set. Not especially delicious, but big.',
            'experience'        => 'For 980 yen I got a huge bowl with 2 big tonkatsu, lots of rice (omori) and a scrambled eggs, with miso soup and pickles on the side. Not especially yasty, but decent, and you can add variety of toppings as there are several available on the counter. Small counter and few tables, but not too bad.',
            'visited'           => true,
            'visit_date'        => '2018-06-19 13:00:00',
            'google_maps_link'  => '',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['katsudon','tonkatsu','filling']);
        $resto->fillCoordinatesFromString('35.657251, 139.703753');

        $resto = Restaurant::create(array(
            'name'              => 'Madrid',
            'location'          => 'Shibuya',
            'type'              => 'Spanish',
            'lunch_price'       => 950,
            'points'            => 'Spanish-like food (does not really feel authentic), such as paela, porc, choriso and raw ham... counter and tables available.',
            'experience'        => '2 TV were on inside, showing some video, but without the sound. Some american country music were playing. I ordered a raw ham sandwich with some salad and fried potatoes, for 790yen. Lunch came with a drink as well. The price was kind of decent, but I was left very unimpressed by the food. The place opens to outside when weather is OK.',
            'visited'           => true,
            'visit_date'        => '2018-06-27 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%9E%E3%83%89%E3%83%AA%E3%83%BC%E3%83%89/@35.6576975,139.697818,18z/data=!3m1!4b1!4m5!3m4!1s0x60188b57b4efcf87:0xf605f14ef5ecd0dd!8m2!3d35.6576953!4d139.6989123',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13021158/',
            'official_website'  => 'http://www.madrid-tokyo.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['spanish']);
        $resto->fillCoordinatesFromString('35.657700, 139.698925');

        $resto = Restaurant::create(array(
            'name'              => 'Roccoman',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Gyoza',
            'lunch_price'       => 680,
            'points'            => 'Decent gyoza lunch. Light lunch 680yen. Bigger lunch 980yen.',
            'experience'        => 'I liked the room with 2 big windows bays. Unfortunately, it is quite noisy: the TV is on with the sound, and the room\'s speakers also blast some music, and of course, other customers talking all around (it is an open space without separations). Food was decent but nothing exceptional.',
            'visited'           => true,
            'visit_date'        => '2018-06-22 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/ROCCOMAN%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6570053,139.6988651,18z/data=!4m5!3m4!1s0x0:0x488b65e43f2b1f56!8m2!3d35.6582562!4d139.6962446',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13216233/',
            'official_website'  => 'http://roccoman-japanese-izakaya-restaurant.business.site/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['gyoza']);
        $resto->fillCoordinatesFromString('35.658131, 139.696500');

        $resto = Restaurant::create(array(
            'name'              => 'Kindenmaru Ramen',
            'location'          => 'Shibuya, Center Gai',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'Very decent ramen and cold soupless ramen in summer.',
            'experience'        => 'I went at the end of June, and they had cold dish options. I tried a cold soupless ramen (900yen) and it was delicious (2 kind of meat, both very good, sesami based sauce very good as well, good mix of spices, and an boiled egg). Like in most ramen restaurant, you can also order some cheap yaki gyoza on the side. The place is small with a counter and a couple of tables. Miso ramen available.',
            'visited'           => true,
            'visit_date'        => '2018-06-26 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Ramen+Kindenmaru+Shibuya+Honten/@35.6588502,139.7002597,18z/data=!4m8!1m2!2m1!1z6YeR5Lyd5Li4!3m4!1s0x0:0x3908e6679571286b!8m2!3d35.6602372!4d139.6994072',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13020600/',
            'official_website'  => 'https://www.hotpepper.jp/strJ000104478/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','miso']);
        $resto->fillCoordinatesFromString('35.660200, 139.699393');

        $resto = Restaurant::create(array(
            'name'              => 'Sushi Zanmai',
            'location'          => 'Shibuya',
            'type'              => 'Sushi',
            'lunch_price'       => 1200,
            'points'            => 'Quite good sushi chain restaurant. Counters and tables available. No conveyor belt. Menu sets and a la carte. English OK.',
            'experience'        => 'Rather spacious sushi restaurant, with sushi prices starting as low as 98yen. Despite that rather low price and the cheap sushi not looking so great, there are, in fact, quite delicious. I could eat their 98yen saumon and their 158yen red tuna all day. Better than the suhi-go-round places I have tried so far.',
            'visited'           => true,
            'visit_date'        => '2018-06-27 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Sushi+Zanmai/@35.6596218,139.6988228,18z/data=!4m8!1m2!2m1!1sSushi+Zanmai!3m4!1s0x0:0xa33efc1bafc81a32!8m2!3d35.661116!4d139.6965721',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13123962/',
            'official_website'  => 'http://www.kiyomura.co.jp/shops/detail/79',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['sushi']);
        $resto->fillCoordinatesFromString('35.661094, 139.696603');

        $resto = Restaurant::create(array(
            'name'              => 'Brasserie Viron',
            'location'          => 'Shibuya',
            'type'              => 'French',
            'lunch_price'       => 1200,
            'points'            => 'French bakery and cafe (brasserie). Decent for a price.',
            'experience'        => 'Prices feel a bit expensive. Quality is not that great. I had a croque-monsieur once for lunch in their 2nd floor\'s brasserie, the interior was not great and neither was the food. I could feel the cheese was decent for Japan, but bread and ham were kind of disapointing. The bakery seems decent though, and it is not like there are many competitor in the neighbourhood or even Tokyo anyway.',
            'visited'           => true,
            'visit_date'        => '2018-06-27 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Brasserie+VIRON/@35.6576443,139.7017993,17z/data=!4m5!3m4!1s0x60188ca970aa2465:0x1aa8c50c49653ca!8m2!3d35.6608958!4d139.6968211',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13004627/',
            'official_website'  => 'https://xn--w8jte276hx5vdnebu3f.com/brasserie-viron-shibuya/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['french','cafe','bakery','sandwiches','croque-monsieur']);
        $resto->fillCoordinatesFromString('35.660864, 139.696779');

        $resto = Restaurant::create(array(
            'name'              => 'Ringer Hut',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Noodles',
            'lunch_price'       => 1200,
            'points'            => 'Healthy fast food serving mostly noodles based dishes (cold and hot) for a cheap price.',
            'experience'        => 'Food was good and cheap. I ordered soupless noodles with lots of vegetables and 5 yaki gyoza for 780yen. Place was Japanese fast-food style, with a machine to place your order at the entrance, and a big counter with no tables. Confortable enough, I enjoyed my lunch there. I also liked the yuzu kosho (green spicy citrus paste) they give with the gyoza.',
            'visited'           => true,
            'visit_date'        => '2018-06-28 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Ringer+Hut+Shibuya+Dogenzaka/@35.6582075,139.7004023,17z/data=!4m5!3m4!1s0x60188ca9fc10a2d1:0xfc55b1b8b4eb0545!8m2!3d35.6584124!4d139.6967009',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13121214/',
            'official_website'  => 'https://www.ringerhut.jp/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['noodles','fast food','gyoza']);
        $resto->fillCoordinatesFromString('35.658397, 139.696745');

        $resto = Restaurant::create(array(
            'name'              => 'Smokehouse',
            'location'          => 'Harajuku',
            'type'              => 'Meat',
            'lunch_price'       => 1800,
            'points'            => 'BBQ Meat. Nice place. Bar counter and tables. Outside tables available as well on 2nd floor terrace.',
            'experience'        => 'Decent. Places where you can eat western style BBQ meat are quite rare, so this place is noteworthy. It is not especially delicious nor cheap though. I had a Bama Burger for 1800yen. It came with a fair amount of fries, good patty, very good bacon, but kind of disapointing cheese, sauce and veggie. Good, but overall lacking in taste.',
            'visited'           => true,
            'visit_date'        => '2018-06-29 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/SMOKEHOUSE/@35.6606598,139.7076495,16z/data=!4m5!3m4!1s0x0:0x22f2979e65ea0099!8m2!3d35.6655325!4d139.7048171',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13161143/',
            'official_website'  => 'https://www.tysons.jp/smokehouse/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['grill','burger']);
        $resto->fillCoordinatesFromString('35.665622, 139.704725');

        $resto = Restaurant::create(array(
            'name'              => 'Excelsior Cafe',
            'location'          => 'Shibuya, Mark City',
            'type'              => 'Cafe',
            'lunch_price'       => 800,
            'points'            => 'Decent cafe with decent price and good croissant, sandwiches etc...',
            'experience'        => 'Kind of big room, with a well separated smoking area. Prices are not expensive when compared to other similar cafe. Food can be quite good. Decent choice of small sandwiches.',
            'visited'           => true,
            'visit_date'        => '2018-06-27 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Excelsior+Caff%C3%A9/@35.6580707,139.6969034,18z/data=!3m1!5s0x60188b5631f5f4af:0xf2c804907c6b11f9!4m8!1m2!2m1!1sExcelsior+mark+city!3m4!1s0x60188b560f5d27e3:0x966996575c83f0c8!8m2!3d35.6581487!4d139.6979397',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13049311/',
            'official_website'  => 'http://www.excelsiorcaffe.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe','sandwich','to-go']);
        $resto->fillCoordinatesFromString('35.658116, 139.698004');

        $resto = Restaurant::create(array(
            'name'              => 'Tsukemen Tetsu',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Ramen',
            'lunch_price'       => 800,
            'points'            => 'Hakata tonkotsu ramen with a speciality in tsukemen. Good ramen restaurant.',
            'experience'        => 'More spacious than a regular ramen restaurant, there are counters and tables. They serve tsukemen, ramen, mazesoba, and, in summer cold noodles dishes (cold mazesoba or tsukemen). Soup is mostly tonkotsu, Hakata style. It is possible to order extra meat. I had a good lunch there. I tried the summer dish which was very unique, mazesoba with tofu.',
            'visited'           => true,
            'visit_date'        => '2018-07-03 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Tsukemen+TETSU+Shibuya/@35.6584637,139.6986022,18z/data=!4m8!1m2!2m1!1sTsukemen+TETSU!3m4!1s0x60188ca9b85587d5:0xd34260f7c2bb4f06!8m2!3d35.6594969!4d139.6977895',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13173206/',
            'official_website'  => 'http://www.tetsu102.com/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['tsukemen','ramen','noodles']);
        $resto->fillCoordinatesFromString('35.659501, 139.697777');

        $resto = Restaurant::create(array(
            'name'              => 'Shinbu Sakiya',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Ramen',
            'lunch_price'       => 950,
            'points'            => 'Delicious miso ramen.',
            'experience'        => 'I usually always add some toppings on my ramen to make iot tasty enough for me (pepper, spice, etc...), but here I did not have to. Not even a little bit. The miso soup was just delicious. The meat was OK but far from great, the egg was good, noodles as well, and one big surprise I had with with the bamboo shoot. That is right, the bamboo shoot you almost always find in ramen and most likely don\'t pay any attention to. Well her those were huge pieces, and tasty! A first for me. The size of the serving is rather small for the price but worth it. The interior has nothing special: 2 floors, counter and tables.',
            'visited'           => true,
            'visit_date'        => '2018-07-06 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E7%9C%9F%E6%AD%A6%E5%92%B2%E5%BC%A5+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6576872,139.6983625,18z/data=!4m5!3m4!1s0x60188b5604786a35:0x98e575d2a803fca7!8m2!3d35.6578594!4d139.6966083',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13109578/',
            'official_website'  => 'http://samurai-noodle.jp/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','miso']);
        $resto->fillCoordinatesFromString('35.657856, 139.696602');

        $resto = Restaurant::create(array(
            'name'              => 'Tengu Sakaba',
            'location'          => 'Shibuya, Center Gai',
            'type'              => 'Izakaya',
            'lunch_price'       => 550,
            'points'            => 'Very cheap teishoku (japanese lunch set). Smoking allowed and not very well compartimented. Cheap izakaya underground.',
            'experience'        => 'Underground room. Smoking is allowed and not well compartimented, which is a pretty bad point. Lunch are very cheap and very decent (curry, noodles etc... with free rice and soup). Counters and tables.',
            'visited'           => true,
            'visit_date'        => '2018-07-10 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Tengu/@35.6589598,139.6991177,18z/data=!4m8!1m2!2m1!1sTengu!3m4!1s0x60188ca84143d6e5:0xa9f6c7a32dda8899!8m2!3d35.6602794!4d139.6992027',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13102355/',
            'official_website'  => 'https://www.teng.co.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['izakaya','japanese','teishoku','japanese curry']);
        $resto->fillCoordinatesFromString('35.660303, 139.699212');

        $resto = Restaurant::create(array(
            'name'              => 'Usagi',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 950,
            'points'            => 'Good ramen restaurant.',
            'experience'        => 'One big counter. Foreigner friendly (friendly staff and English menus). Decent choice of ramen, tsukemen, tantanmen, and hiyashi (cold) ramen in summer. I tried shoyu tsukemen with egg, large portion for 950, and it was very good. The piece of meat was kinda smoked, quite delicious. One thing I would reproach them though is that when I went in mid-July, their door stayed opened and it was too hot inside. In winter. I tried the Special Ramen, which is their regular one with all toppings. It was a bit salty, but quite good. Not tasty nor filling enough for me though, and the cost was high (1200 yen).',
            'visited'           => true,
            'visit_date'        => '2018-07-11 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Usagi/@35.6570234,139.6973772,17z/data=!4m5!3m4!1s0x60188b550b2d38ff:0x1c8f91e37a97e72c!8m2!3d35.6557027!4d139.6934934',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13041115/',
            'official_website'  => 'https://ramen-restaurant-786.business.site/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','tsukemen','noodles','tantanmen']);
        $resto->fillCoordinatesFromString('35.655730, 139.693535');

        $resto = Restaurant::create(array(
            'name'              => 'Moriva Coffee',
            'location'          => 'Shibuya',
            'type'              => 'Cafe',
            'lunch_price'       => 650,
            'points'            => 'A western style Japanese cafe with decent menus but not the greatest interior.',
            'experience'        => 'I had a avocado chicken sandwich for 450 yen and it was good. Inside is lighted with spotlights (not good) and with very small tables and counters. A smoking area is isolated from the rest.',
            'visited'           => true,
            'visit_date'        => '2018-07-11 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Moriva+Coffee/@35.6568561,139.6982953,17z/data=!4m5!3m4!1s0x60188b55bc6bfb63:0xe39fbae7a5da3266!8m2!3d35.6558885!4d139.6945402',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13116477/',
            'official_website'  => 'http://www.morivacoffee.com/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe','sandwich','small servings','smoking area','to-go']);
        $resto->fillCoordinatesFromString('35.655810, 139.694601');

        $resto = Restaurant::create(array(
            'name'              => 'Manmosu',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'Small ramen restaurant with narrow counters. Very decent tsukemen with "original" noodles. ',
            'experience'        => 'Small ramen shop offering tsukemen and ramen. 2 types of noodles, classic or original. My tsukemen was good, depite the soup being very slightly fishy (I do not like fishy taste). Thick and tasty soup, decent meat and egg, and original noodles were thick but good. Counter is quite narrow but somehow OK. Tsukemen can take 10mn to get ready, which is a bit long by Japanese standards.',
            'visited'           => true,
            'visit_date'        => '2018-07-12 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E9%81%93%E7%8E%84%E5%9D%82%E3%83%9E%E3%83%B3%E3%83%A2%E3%82%B9/@35.6576175,139.6974783,18z/data=!4m5!3m4!1s0x60188b560699c71d:0x72a0db46aaddecd5!8m2!3d35.6577081!4d139.6965118',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13122700/',
            'official_website'  => 'http://www.shibuyaism.com/entry/8017699',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','tsukemen','noodles']);
        $resto->fillCoordinatesFromString('35.657712, 139.696492');

        $resto = Restaurant::create(array(
            'name'              => 'Ramen kokuu',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 950,
            'points'            => 'Good, light taste shoyu  wantanmen (ramen with Chinese dumplings). More confortable than average ramen place.',
            'experience'        => 'The place has a single, but large, counter, which is more confortable that the usual ramen place. I tried the shoyu ramen with Chinese dumpling and extra meat. Soup was light, taste OK, the meat was above average, salty, but still with a bit too much fat for me, and dumplings, 5 pieces, were very good. For 1200yen, that was the most expensive dish on the menu. I would have liked more noodles. This was good but felt a bit overpriced.',
            'visited'           => true,
            'visit_date'        => '2018-07-18 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Ramen+Kokuu/@35.6566129,139.6989133,17z/data=!4m5!3m4!1s0x60188b572fb6f3df:0x90e19dcc4e740613!8m2!3d35.6550656!4d139.6993425',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13149719/',
            'official_website'  => 'https://ramendb.supleks.jp/s/62837.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['noodles','ramen','wantanmen']);
        $resto->fillCoordinatesFromString('35.655086, 139.699391');

        $resto = Restaurant::create(array(
            'name'              => 'Hooters',
            'location'          => 'Shibuya',
            'type'              => 'Western',
            'lunch_price'       => 2000,
            'points'            => 'American and Mexican overpriced food served by sexy waitresses.',
            'experience'        => 'Situated on the 4th floor above the Uniclo store, the place is quite big, but no windows, and poorly lighted by spotlights. Tables and counters, musics, TV playing soundless in every corners. Of course the main attraction are the sexy waitresses. As for the food, I tried the burger, that cost me a whoping 2000 yen for jsut a patty and a slice of cheese, with no drinks. Took a long time to come, and was not great. By far the most overpriced burger I ever had. The menu also offers some mexican stuff and some meat.',
            'visited'           => true,
            'visit_date'        => '2018-07-19 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/HOOTERS+SHIBUYA/@35.658202,139.6998254,17z/data=!4m5!3m4!1s0x0:0x21cd103482770807!8m2!3d35.659527!4d139.698318',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13167506/',
            'official_website'  => 'http://www.hooters.co.jp/shop/shibuya/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['western','american','burger','mexican']);
        $resto->fillCoordinatesFromString('35.659407, 139.698295');

        $resto = Restaurant::create(array(
            'name'              => 'Mandala',
            'location'          => 'Shibuya',
            'type'              => 'Indian',
            'lunch_price'       => 850,
            'points'            => 'Indian restaurant serving curry, but also gapao.',
            'experience'        => 'Interior kind of mediocre, but somehow I liked the music (instrumental wind bells of some kind). I tried the chicken curry with nan for 850yen, the cheapest dish. It was decent for Shibuya\'s low standards (when it comes to Indian curry), mostly because the nan was nice. But I would not recommend it.',
            'visited'           => true,
            'visit_date'        => '2018-07-23 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Grill%26Dining+Bar+Mandala+%E3%83%9E%E3%83%B3%E3%83%80%E3%83%A9%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6579949,139.6978242,16.75z/data=!4m5!3m4!1s0x60188b576738a74d:0xd2561a1948685da9!8m2!3d35.6564863!4d139.7009733',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13007278/',
            'official_website'  => 'https://www.hotpepper.jp/strJ000001223/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['indian','curry','gapao']);
        $resto->fillCoordinatesFromString('35.656507, 139.700950');

        $resto = Restaurant::create(array(
            'name'              => 'Ramen King',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 700,
            'points'            => 'Cheap and noisy but decent ramen restaurant.',
            'experience'        => 'The place is white bright and kind of noisy (workers make noise cooking). I tried the hiyashi-chuka set, cold noodles dish available in summer. For 850 yen it came with a bowl of rice, 3 yaki gyoza (dumplings), and the main dish. Portions are big, which make for a good ratio quantity/cost. Taste and quality very decent. Gyoza were very good. But the competition for ramen is very fierce in Shibuya, and I would not recommend this place over some others with more tasty dishes (unless you are on a tight budget, because this one is amongst the cheapest option, with decent ramen starting at 700 yen).',
            'visited'           => true,
            'visit_date'        => '2018-07-24 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Ramen+King+Koraku+Honpo/@35.6581915,139.6993192,18z/data=!4m8!1m2!2m1!1z44Op44O844Oh44Oz546L!3m4!1s0x0:0x5b48247ed1d2f05d!8m2!3d35.658663!4d139.698564',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13022566/',
            'official_website'  => '',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','gyoza']);
        $resto->fillCoordinatesFromString('35.658670, 139.698520');

        $resto = Restaurant::create(array(
            'name'              => 'Demode Queen',
            'location'          => 'Shibubya, Udagawamachi',
            'type'              => 'Grill',
            'lunch_price'       => 1300,
            'points'            => 'Nice place. Meat and burgers. Sofa avaiable next to large windows. Decent prices (cheaper than competitors).',
            'experience'        => 'Inside had chill peacefull music, large windows and some nice deco... liked it. I tried the avocado cheese burger for 1280yen, and it was big and good. In summer they also have a beer garden (food and drinks in open air) on their rooftop (4F).',
            'visited'           => true,
            'visit_date'        => '2018-07-30 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Demode+Queen+Udagawa+Machiten/@35.6600688,139.6977111,17z/data=!4m12!1m6!3m5!1s0x60188ca96097f991:0xb1ca20672648b7f7!2sDemode+Queen+Udagawa+Machiten!8m2!3d35.661457!4d139.69648!3m4!1s0x60188ca96097f991:0xb1ca20672648b7f7!8m2!3d35.661457!4d139.69648',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13173808/',
            'official_website'  => 'https://kiwa-group.co.jp/demodeqeen/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['western','grill','filling','burger','English menu','smoking area']);
        $resto->fillCoordinatesFromString('35.661514, 139.696473');

        $resto = Restaurant::create(array(
            'name'              => 'Sushi Zanmai',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Sushi',
            'lunch_price'       => 1400,
            'points'            => 'Good sushi restaurant.',
            'experience'        => 'Big chain that is famous for offering good sushi. Not as cheap as the suhi-go-round you can find a bit everywhere, but not as pricey as the more elite places. This one as slightly less choice than the one situated at the end of center gai, but the interior is nice and provide with a good experience (and they have the stuff I like anyway, salmon and tuna).',
            'visited'           => true,
            'visit_date'        => '2018-07-31 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%80%8A%E3%81%99%E3%81%97%E3%81%96%E3%82%93%E3%81%BE%E3%81%84+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E3%82%BB%E3%83%B3%E3%82%BF%E3%83%BC%E3%83%93%E3%83%AB%E5%BA%97%E3%80%8B%E6%B8%8B%E8%B0%B7+%E4%BA%BA%E6%B0%97+%E3%81%8A%E3%81%99%E3%81%99%E3%82%81%E5%AF%BF%E5%8F%B8%E5%BA%97/@35.658474,139.6970407,18z/data=!4m8!1m2!2m1!1sSushi+Zanmai!3m4!1s0x0:0xeaf569ce81b2beff!8m2!3d35.6592587!4d139.69793',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13213452/',
            'official_website'  => 'http://www.kiyomura.co.jp/shops/detail/79',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['sushi']);
        $resto->fillCoordinatesFromString('35.659256, 139.697918');

        $resto = Restaurant::create(array(
            'name'              => 'Choco Cro',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Cafe',
            'lunch_price'       => 680,
            'points'            => 'Big chain. Cafe with a isolated smoking area. Offers good sandwiches and chocolate croissants, along with a variety of others things (ice creams, pancakes, cakes...)',
            'experience'        => 'Interior was decently confortable (but kinda dark, most light come from spotlights. I like their chocolate croissants (200yen) and their ham cheese sandwich (400yen). You can get a lunch set with sandwich + croissant + drink for 680 yen.',
            'visited'           => true,
            'visit_date'        => '2018-07-31 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Saint+Marc+Cafe+Shibuya+Dogenzaka+shop/@35.6586484,139.6984354,18z/data=!4m5!3m4!1s0x60188ca9c4bf68f1:0xdead5af68ae1e923!8m2!3d35.6592608!4d139.6979151',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13168224/',
            'official_website'  => 'http://www.saint-marc-hd.com/saintmarccafe/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe','sandwich','small servings','smoking area','to-go','English menu','visa']);
        $resto->fillCoordinatesFromString('35.659256, 139.697918');

        $resto = Restaurant::create(array(
            'name'              => 'Habana Cafe',
            'location'          => 'Shibuya',
            'type'              => 'Cuban',
            'lunch_price'       => 900,
            'points'            => 'Cuban style. Nice restaurant and bar. Advertising an Havana style. Very good food.',
            'experience'        => 'I first went in thinking it was an overpriced serving some kind of glorified panini. I was wrong. The serving are very decent and what they call a half-size is actually enough for one person. They serve cuban sandwiches, tacos and buritos. Cuban sandwiches ressemble big panini. I tried the summer special "avocado cheese habano", and I was really surprised at how good it was. It was brilliant. For 900yen you can get a half sandwich (which is good for one person) and a drink. For 1600 yen you can get the full size, and save some money if you are with someone else. Nice place, nice service, good food. Recommendable.',
            'visited'           => true,
            'visit_date'        => '2018-08-02 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Cafe+Habana+TOKYO/@35.6564029,139.700515,16z/data=!4m5!3m4!1s0x60188b5b18195555:0x62ba3026ced0ac79!8m2!3d35.6533025!4d139.7045202',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130303/13196354/',
            'official_website'  => 'http://cafehabana.jp/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['mexican','sandwiches','to-go','no smoking','English menu','cafe']);
        $resto->fillCoordinatesFromString('35.653342, 139.704487');

        $resto = Restaurant::create(array(
            'name'              => 'Asian Spice King',
            'location'          => 'Shibuya, Sarugakucho',
            'type'              => 'Indian Curry',
            'lunch_price'       => 950,
            'points'            => 'Decent indian curry restaurant with above-than-average interior, but, beware: smoking is allowed.',
            'experience'        => 'Looking at the menu, it was a bit weird, as the price of the lunch set changes depending on what curry you choose (vegetables, chicken, keeema, mutton...). If you want keema, make sure to ask for the B set... and you will get a bowl of curry, a small salad, and a big piece of nan for 950 yen. It is not cheap, but the portion of curry was one of the biggest I have ever got, and quality/taste was very decent. Another good surprise was that the interior was quite nice. Large and confortable counter as well as different tables. Good enough for date. Could not escape the shitty Indian pop music selection though. So, I was quite happy with my lunch, until toward the end, when I started smelling some cigarette smoke... then I realized that the place allows smoking... which is kind of a deal breaker for me. If you are unlucky, you could end up next to someone who smokes and ruins you lunch, and I don\'t like to take such risk. For 700 yen you can get a bento.',
            'visited'           => true,
            'visit_date'        => '2018-08-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Asian+Spice+King+%E6%B8%8B%E8%B0%B7%E3%83%BB%E4%BB%A3%E5%AE%98%E5%B1%B1/@35.6578217,139.6995226,16z/data=!4m12!1m6!3m5!1s0x60188b5b3d9f92cf:0x5944d1ca4d80a6a5!2zQXNpYW4gU3BpY2UgS2luZyDmuIvosLfjg7vku6PlrpjlsbE!8m2!3d35.6532537!4d139.7047046!3m4!1s0x60188b5b3d9f92cf:0x5944d1ca4d80a6a5!8m2!3d35.6532537!4d139.7047046',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13159709/',
            'official_website'  => 'http://masalahut.net/asian.html',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['indian','curry','smoking allowed','to-go','English menu','vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.653319, 139.704687');

        $resto = Restaurant::create(array(
            'name'              => 'Port of Call',
            'location'          => 'Shibuya',
            'type'              => 'Western',
            'lunch_price'       => 1000,
            'points'            => 'The casual cafe atmosphere is great. The food is great. The price is very reasonable.',
            'experience'        => 'The cafe/restaurant is situated behind a cloth store, but they have a sign outside. Go in to see a nice space with a large windows bay directly in front of the JR railway (the ground shake a bit when a train passes by). Inside is very relaxing and confortable. For 1000 yen or less you can get a lunch set (burger, quesadilla, curry... and/or nice desserts) that comes with soup, salad, and drink. I tried their weekly burger, that was sweet mustard chicken burger, and it was, kind of surprinsingly, absolutely delicious. Brilliant. I had a great lunch experience, but just avoid going there if you are in a hurry, as the food can take a while to be ready.',
            'visited'           => true,
            'visit_date'        => '2018-08-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Cafe%26Store+PORT+OF+CALL+DAIKANYAMA/@35.6564679,139.699952,17z/data=!4m8!1m2!2m1!1sport+of+call!3m4!1s0x0:0x881c208777d428e6!8m2!3d35.6538136!4d139.7048025',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13165417/',
            'official_website'  => 'https://www.instagram.com/port_of_call_daikanyama/',
            'score_lunch'       => 4,
            'score_place'       => 2,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['burger','curry','cafe','western']);
        $resto->fillCoordinatesFromString('35.653809, 139.704789');

        $resto = Restaurant::create(array(
            'name'              => 'Plaloma',
            'location'          => 'Shibuya',
            'type'              => 'Thai',
            'lunch_price'       => 1000,
            'points'            => 'Nice Thai restaurant with quite diverse menu even for lunch.',
            'experience'        => 'The interior is nice and confortable. The menu offers more different dishes for lunch than usual Thai restaurants. I got the gapao, and it was very decent. It came with a good soup, a small salad, a small dessert, and a drink, for 1000 yen. It all came fast, nice service. Recommendable place.',
            'visited'           => true,
            'visit_date'        => '2018-08-08 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%BF%E3%82%A4%E3%83%80%E3%82%A4%E3%83%8B%E3%83%B3%E3%82%B0+%E3%83%97%E3%83%A9%E3%83%BC%E3%83%AD%E3%83%BC%E3%83%9E%E3%83%BC/@35.6579362,139.7003489,17z/data=!4m5!3m4!1s0x60188b56ee4506ff:0x6ed7b8b394f04f37!8m2!3d35.6557042!4d139.6979642',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13219402/',
            'official_website'  => 'http://plaloma.sub.jp/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['thai','green curry','gapao']);
        $resto->fillCoordinatesFromString('35.655726, 139.697950');

        $resto = Restaurant::create(array(
            'name'              => 'Shibuya Parlor',
            'location'          => 'Shibuya crossing',
            'type'              => 'Cafe',
            'lunch_price'       => 1200,
            'points'            => 'Counter with great view on Shibuya crossing. Nice cafe serving pasta and deserts with fresh fruits.',
            'experience'        => '7th floor right in front of the crossing. The view is very nice. The pasta were actually delicious. For lunch, you can add +400yen to your 1200yen pasta dish in order to get a mini fresh fruits parfait. The food comes rather slowly, but it is easy to be patient while enjoying the view. Each time I went the place was rather empty, which is another good point. The mini parfait I had was not especially great, lacking sweetness to my westerner taste.',
            'visited'           => true,
            'visit_date'        => '2018-08-09 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/SHIBUYA+PARLOR/@35.6597365,139.6986217,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b6d7d3f546f:0x922824b7ce3b34ef!8m2!3d35.6597322!4d139.7008104',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13220995/',
            'official_website'  => 'https://www.shibuya109.jp/shop/SPL/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['cafe','pasta','desert','no smoking']);
        $resto->fillCoordinatesFromString('35.659735, 139.700788');

        // This place has been shut down for new building construction
        /*
        $resto = Restaurant::create(array(
            'name'              => 'Mud',
            'location'          => 'Shibuya',
            'type'              => 'Cafe',
            'lunch_price'       => 980,
            'points'            => 'Dark and peaceful, "american style" cafe',
            'experience'        => 'Terrace at the front with tables where you can smoke. Inside is quite dark, with tables and sofas. Menu consists of 980yen plates such as hotdog, sandwich or rice, with different options. Overall it was decent, but I found that 980yen was expensive for a hotdog, even a large one with some salad and fries. You can drink free water, but to get something else, it is an additional 300yen (!).',
            'visited'           => true,
            'visit_date'        => '2018-08-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/MUD+SPOT+Shibuya+1/@35.6576928,139.7010057,17z/data=!4m12!1m6!3m5!1s0x60188b59c29de9a9:0xe66787f0217e3dd6!2sMUD+SPOT+Shibuya+1!8m2!3d35.656893!4d139.7021631!3m4!1s0x60188b59c29de9a9:0xe66787f0217e3dd6!8m2!3d35.656893!4d139.7021631',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13137346/',
            'official_website'  => 'http://www.mudcoffee.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe','sandwich','hotdog','open air space','smoking area']);
        $resto->fillCoordinatesFromString('35.656930, 139.702169');
        */

        $resto = Restaurant::create(array(
            'name'              => 'Sathaban Wijai Ahan Thai',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Thai',
            'lunch_price'       => 1080,
            'points'            => 'Very nice, small Thai restaurant. Food and service above average. Narrow counter and small tables.',
            'experience'        => 'So the place is small, and easily packed, but somehow the experience eating there is still very good. Clean, bright, with nice and fast service. Wide range of options for lunch, but I went with the gapao, a classic, in order to be able to compare with other Thai easily. For 1080yen, I got my gapao with very good and spicy soup, a very nice spring roll, and a pitcher of cold tea. Quality was very good, above average I would say. I also appreciated they somehow got the ambient temperature right (it was August abd inside was not too cold nor too warm).',
            'visited'           => true,
            'visit_date'        => '2018-08-15 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Sathaban+Wijai+Ahan+Thai/@35.657509,139.7005488,18z/data=!4m5!3m4!1s0x60188b5742498edb:0x85a7c326245fe97!8m2!3d35.6561766!4d139.7005042',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13180281/',
            'official_website'  => 'http://www.sscy.co.jp/research/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['thai','gapao','green curry','no smoking']);
        $resto->fillCoordinatesFromString('35.656122, 139.700537');

        $resto = Restaurant::create(array(
            'name'              => 'Fields',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Bar restaurant',
            'lunch_price'       => 830,
            'points'            => 'Bar restaurant with a windows bay above the Sakuragaokacho\'s crossing. Unexpensive good lunch plates. Smoking allowed.',
            'experience'        => 'I went there late and it was almost empty, so nobody was smoking. Otherwise that could have been a big problem... I sat at the counter in front of the windows bay, it was very nice. Some music was playing, and some TV were on with a soccer game. I ordred a katsu curry plate, and it was really very good. The katsu was great, the curry was tasty, the rice was in abundance and some lettuce and tomatoes were on the side. Plus a fried egg on the rice. For 830 yen (it was the most expensive dish there). I enjoyed it much. Other items on the menu includes popular Japanese style taco-rice, napolitan pasta, and othjer combinations, starting 500 yen. Add 100yen for a salad or a drink.',
            'visited'           => true,
            'visit_date'        => '2018-08-16 14:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Fields/@35.6571737,139.7014883,17z/data=!4m5!3m4!1s0x60188b59e1618d81:0x4734f154b5bf8f44!8m2!3d35.656485!4d139.701617',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13031693/',
            'official_website'  => 'http://www.e-fields.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['pasta','katsu','japanese curry','smoking allowed']);
        $resto->fillCoordinatesFromString('35.656484, 139.701585');

        $resto = Restaurant::create(array(
            'name'              => 'Pancho',
            'location'          => 'Shibuya, 3Chome',
            'type'              => 'Pasta',
            'lunch_price'       => 780,
            'points'            => 'Good pasta. Spaghetti with 2 bases: meat sauce or napolitan, starting 780yen and (very) customisable. Basement. Fast service. Tables and counter. Small, average, or large servings available.',
            'experience'        => 'I was happy with the quality for the price! And you can order up to 500g of pasta, with no additional charge. This is huge. Quick service. Several toppings on the tables, like free parmesan cheese. Interior is basment, might feel a tad gloomy, but decent. Staff is nice. Tables are confortable enough. In summer, the air conditioning there can be quite strong (cold).',
            'visited'           => true,
            'visit_date'        => '2018-08-17 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Pancho+Shibuya+South/@35.6571943,139.7015754,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b599dbb0af9:0x3c9573aaa5fbc04e!8m2!3d35.65719!4d139.7037641',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13206176/',
            'official_website'  => 'http://naporitanpancho.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['pasta','filling','no smoking']);
        $resto->fillCoordinatesFromString('35.657222, 139.703782');

        $resto = Restaurant::create(array(
            'name'              => 'Daylight Kitchen',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Organic',
            'lunch_price'       => 1500,
            'points'            => 'Healthy and quite exquisite lunch sets.',
            'experience'        => 'Inside is spacious and nice, and there is also a large terrace with some green trees to cut you from the road. Very nice. 2 main lunch sets: meat or vegetables. I tried the ratatouille, it was served with salad, rice, soup, and few other small things, all very, very good. 1500 yen did not feel overpriced for this kind of lunch in Shibuya. I had a very pleasant experience.',
            'visited'           => true,
            'visit_date'        => '2018-08-20 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Daylight+Kitchen/@35.6577381,139.6991146,17z/data=!4m5!3m4!1s0x60188b573fb4e74b:0x9f37c90b12a546cd!8m2!3d35.6558944!4d139.6998334',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13113772/',
            'official_website'  => 'http://www.daylightkitchen.jp/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['open air space','no smoking','cafe','vegetarian friendly','organic']);
        $resto->fillCoordinatesFromString('35.655948, 139.699819');

        $resto = Restaurant::create(array(
            'name'              => 'Bel Mare',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Nice small Italian restaurant.',
            'experience'        => 'Small but confortable, with decent size tables. White tableclothes and classical music playing in the background. Mostly pasta in the menu for lunch. I tried some tomato based salami pasta, and it was absolutely delicious. 1000 yen for the set with a drink and a salad. You can add 100 yen to make the portion bigger (it becomes quite big, be sure to be very hungry).',
            'visited'           => true,
            'visit_date'        => '2018-08-21 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/bel+mare/@35.6567873,139.6985703,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b576570fcd1:0xe2fc27305983b3fc!8m2!3d35.656783!4d139.700759',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13010645/',
            'official_website'  => 'https://belmare.jp/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pasta','no smoking']);
        $resto->fillCoordinatesFromString('35.656787, 139.700843');

        $resto = Restaurant::create(array(
            'name'              => 'Bel Mare',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Nice small Italian restaurant.',
            'experience'        => 'Small but confortable, with decent size tables. White tableclothes and classical music playing in the background. Mostly pasta in the menu for lunch. I tried some tomato based salami pasta, and it was absolutely delicious. 1000 yen for the set with a drink and a salad. You can add 100 yen to make the portion bigger (it becomes quite big, be sure to be very hungry).',
            'visited'           => true,
            'visit_date'        => '2018-08-21 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/bel+mare/@35.6567873,139.6985703,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b576570fcd1:0xe2fc27305983b3fc!8m2!3d35.656783!4d139.700759',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13010645/',
            'official_website'  => 'https://belmare.jp/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pasta','no smoking']);
        $resto->fillCoordinatesFromString('35.656787, 139.700843');

        $resto = Restaurant::create(array(
            'name'              => 'La cocorico',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Nice and spacious Italian restaurant.',
            'experience'        => 'The place, on the 5th floor of "the gems" building, was nice and spacious. Wooden interior. Lunch menu sets are about 1000 yen. A bit more if you order a larger portion. For 1000 yen, I got a very nice small salad with some roast beef and smoked saumon, a mini soup, a drink, a small dessert, and the main dish was a 18cm pizza marguerita. 28cm size was available for an additionnal 500 yen, but I got satisfied with the smaller one, given the other items coming with the set. The pizza itself was actually quite good, above the average in Shibuya.',
            'visited'           => true,
            'visit_date'        => '2018-08-22 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/La+Cocorico/@35.6549871,139.7019954,16z/data=!4m5!3m4!1s0x60188b5bc716543f:0xd8b70bfaabe81b24!8m2!3d35.6557281!4d139.7045167',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13147320/',
            'official_website'  => 'http://www.la-cocorico.jp/shibuya',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pizza','pasta','no smoking']);
        $resto->fillCoordinatesFromString('35.655713, 139.704509');

        $resto = Restaurant::create(array(
            'name'              => 'Tora uma',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Izakaya',
            'lunch_price'       => 800,
            'points'            => 'Small izakaya. Clean place. Offers some Japanese dishes for lunch.',
            'experience'        => 'Inside is small but nice. A counter and a few tables. Some music and a TV on. I enjoyed the peaceful atmosphere. Two men are taking care of preparing the food and they cook pretty well. The karaage were freshly made on demand and looked very good. I ordred the taco rice. It came with a miso soup, some Japanese pickles, and a salad. For 800 yen. It was very good. Menu is all in Japanese, no English. Lunch is limited to 4 or 5 choices of food. Nobody were smoking during the time I had my lunch, but ash trays on the tables indicates that might be allowed.',
            'visited'           => true,
            'visit_date'        => '2018-08-23 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E8%99%8E%E3%81%86%E3%81%BE/@35.6575261,139.6993365,17z/data=!4m5!3m4!1s0x60188b575c34987d:0xe2ff1b8bc763cb1c!8m2!3d35.6562359!4d139.7011389',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13173914/',
            'official_website'  => 'https://www.facebook.com/%E8%99%8E%E3%81%86%E3%81%BE-1595613027332000/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['izakaya','teishoku','karaage','taco rice']);
        $resto->fillCoordinatesFromString('35.656264, 139.701139');

        $resto = Restaurant::create(array(
            'name'              => 'Saray',
            'location'          => 'Shibuya',
            'type'              => 'Turk',
            'lunch_price'       => 1000,
            'points'            => 'Small, cosy Turkish restaurant.',
            'experience'        => 'The plae cwas empty when I went afer 1pm. Staff was friendly. Room was cosy. For 1000 yen I picked the mixed kebab plate, that came with a soup and a drink. The soup was quite very good, the main dish was good without being great.',
            'visited'           => true,
            'visit_date'        => '2018-08-24 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/SARAY/@35.6582947,139.6996006,17z/data=!4m5!3m4!1s0x60188b57b190ffaf:0xacbf832dc03a859!8m2!3d35.6575082!4d139.6991652',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13004608/',
            'official_website'  => 'http://www.saray.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['turk']);
        $resto->fillCoordinatesFromString('35.657500, 139.699144');

        $resto = Restaurant::create(array(
            'name'              => 'Gochiso soba sora',
            'location'          => 'Shibuya, 109men',
            'type'              => 'Soba',
            'lunch_price'       => 1000,
            'points'            => 'Small tenpura and soba restaurant in 109men\'s 7th floor.',
            'experience'        => 'This place is kind of hidden behind a big gyoza restaurant on the 7th floor. Small but empty, the place is bright and look clean, but somehow the floor was very sticky. You order from a choice of soba and/or tenpura dishes at the machine (English available). I chose the most expensive, soba + tenpura, for 1380 yen. The food was very decent (way better than cheap chains such as fuji soba), good fresh soba and nice tenpura. But somehow the experience is not that great, maybe due to the fact that the place lacks personality, and has no windows, no view.',
            'visited'           => true,
            'visit_date'        => '2018-08-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Magnet+by+Shibuya+109/@35.659286,139.6999489,18z/data=!3m1!5s0x60188ca8027011b9:0xaf0e3821734cad50!4m13!1m7!3m6!1s0x60188b57fd9f7bb3:0xa314cb9bc59b96a7!2s7F,+1+Chome-23-10+Jinnan,+Shibuya-ku,+T%C5%8Dky%C5%8D-to+150-0041,+Japan!3b1!8m2!3d35.6597275!4d139.7009065!3m4!1s0x60188b57fda0b3bb:0x6c103e05d0fa17a7!8m2!3d35.6597801!4d139.7009394?hl=en',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13221831/',
            'official_website'  => 'https://www.shibuya109.jp/shop/GSS/?msnId=MAGNET',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['soba','tenpura','no smoking']);
        $resto->fillCoordinatesFromString('35.659702, 139.700843');

        $resto = Restaurant::create(array(
            'name'              => 'Cinagro',
            'location'          => 'Shibuya, Seibu',
            'type'              => 'Organic',
            'lunch_price'       => 1480,
            'points'            => 'Really great all-you-can-eat salad buffet, and wide choice of dishes.',
            'experience'        => 'While it is not especially exciting going to the second basement of the Seibu mall, the restaurants there are nice and clean and stand by Seibu\'s relatively nice  standards. Sept Anges is located in the middle of the floor, and has an all-you-can-eat salad buffet opened at lunch time. Lots of different things, including deepfried eggplants and potatoes, bread, raw vegetables, and many different oils, vinegars and spices you can uses. All was surpringly very tasty. Inluding in the lunch price is a choice of main dish. Many are available (A to K, English menu available). I picked some ariabata spaghetti, it was relatively small in amount, but delicious.',
            'visited'           => true,
            'visit_date'        => '2018-08-29 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Cinagro+Shibuya/@35.6590155,139.699435,18z/data=!3m1!5s0x60188ca8177055e1:0xf009a238452b6721!4m5!3m4!1s0x60188ca81772ab83:0x59c10e0dfc85cb41!8m2!3d35.6602337!4d139.7002987',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13150447/',
            'official_website'  => 'https://www.cinagro.jp/shop-list/#shibuya',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['all-you-can-eat','pasta','pizza','italian','vegetarian friendly','organic','cafe','no smoking','english menu']);
        $resto->fillCoordinatesFromString('35.660236, 139.700387');

        $resto = Restaurant::create(array(
            'name'              => 'Oshima ramen',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Ramen',
            'lunch_price'       => 750,
            'points'            => 'Quite nice and big ramen restaurant. Very good taste. Numerous choice of ramen types, and side dishes. Prices below average.',
            'experience'        => 'I was very pleasantly surprised at how nice and big th place was. No windows, but nicely decorated, with a good atmosphere. Numerous choice of ramen, miso, shio, original, cold (in summer), with additional gyoza or chanhan for the most hungry customers. I ordered their miso ramen, for 750 yen (cheap!) with some yaki gyoza (fried dumplings) for an additional 180 yen for 6 pieces (cheap!). The ramen was very much like I love it: very tasty miso. Good egg, good meat, even the corn was nice. The dumplings were good but nothing special. I ended up very satisfied, and would place this establishment amongst the top 5 of Shibuya for ramen.',
            'visited'           => true,
            'visit_date'        => '2018-08-30 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E5%A4%A7%E5%B3%B6%E3%83%A9%E3%83%BC%E3%83%A1%E3%83%B3+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6578435,139.6990391,18z/data=!4m5!3m4!1s0x60188b57b06633cb:0xd42bd797b8d6f64f!8m2!3d35.6575667!4d139.6993341',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13029771/',
            'official_website'  => 'http://www.shibuyaism.com/entry/2016/11/27/143928',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','miso']);
        $resto->fillCoordinatesFromString('35.657633, 139.699313');

        $resto = Restaurant::create(array(
            'name'              => 'Boul\'ange',
            'location'          => 'Shibuya, 1Chome',
            'type'              => 'Bakery',
            'lunch_price'       => 1100,
            'points'            => 'French bakery. Second floor has counters and tables for customers to eat.',
            'experience'        => 'Quite a lot of choice, especially for sweet stuff. They also so have baguette sandwiches, and some kind of burger and hotdog. I tried a ham, cheese and lettuce sandwich a kind of apple pie, for a total of 850 yen. It was good for Japan, despite the bread being kind of chewy. Maybe not so great for French people.',
            'visited'           => true,
            'visit_date'        => '2018-09-05 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/BOUL\'+ANGE+(%E3%83%96%E3%83%BC%E3%83%AB%E3%82%A2%E3%83%B3%E3%82%B8%E3%83%A5)+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6598152,139.7005843,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b586cf68f65:0xd6b4dc95e1d0afa0!8m2!3d35.6598109!4d139.702773',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13209052/',
            'official_website'  => 'http://boulange.baycrews.co.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['bakery','french','sandwich']);
        $resto->fillCoordinatesFromString('35.659753, 139.702670');

        $resto = Restaurant::create(array(
            'name'              => 'Antica Forneria, by Elio',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Italian',
            'lunch_price'       => 1600,
            'points'            => 'Decent Italian restaurant in Stream building 2F.',
            'experience'        => 'Rather small place is divided in 2 parts: opened front (small tables and rather nice) and back (bigger tables but it is a room without windows...). Lunch menu offers mostly pasta or pizza of the day. For 1600yen you will get a pizza or pasta, with a small salad and a drink. That does feel a bit overpriced. As for the food itself, I ordered a cheese and salamy pizza, and it was good. The crust was very nice. The cheese surpringly good. The salami was "real". The tomato base could be improved. If you ask, you can get some (actually) spicy oil to add on your dish. Good, but in the end the experience didn\'t feel worth the price, especially if you are placed in the room in the back.',
            'visited'           => true,
            'visit_date'        => '2018-09-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/SHIBUYA+STREAM/@35.6589064,139.7020128,17z/data=!4m5!3m4!1s0x60188b31a00165ed:0x387c407b91e2ad68!8m2!3d35.6571797!4d139.7030919',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1308/A130803/13131847/',
            'official_website'  => 'https://shibuyastream.jp/shop/shopdetail.php?id=14',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['italian','pasta','pizza']);
        $resto->fillCoordinatesFromString('35.657365, 139.702853');

        $resto = Restaurant::create(array(
            'name'              => 'The Great Burger',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Burger',
            'lunch_price'       => 1600,
            'points'            => 'Nice but expensive burger joint.',
            'experience'        => 'On the first floor of the Stream building, the place is nice. If the weather allows it, you can eat outside, which is pleasant except for the possible small waves of sewer smell (depends on the wind I guess). Inside is small but nice american style decor. I ordered the cheese burger for 1300 yen plus a drink for an additional 200... kind of expensive given the relatively small size of the servings. The bread was nicely grilled, I loved that. Fair amount of fresh lettuce and tomato. Good cheese. The patty was disapointed in size and thickness. A nice experience overall, but still not really worth the price in my opinion.',
            'visited'           => true,
            'visit_date'        => '2018-09-18 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/THE+GREAT+BURGER+STAND/@35.6574643,139.7007862,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b99189933f3:0xa6827d5fa076bc23!8m2!3d35.65746!4d139.7029749',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225844/',
            'official_website'  => 'https://shibuyastream.jp/shop/shopdetail.php?id=2',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 3,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['burger','open air space','to-go']);
        $resto->fillCoordinatesFromString('35.657511, 139.702939');

        $resto = Restaurant::create(array(
            'name'              => 'CityShop Pizza',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Pizza',
            'lunch_price'       => 2000,
            'points'            => 'Fancy customizable "healthy" pizza restaurant.',
            'experience'        => 'On the 2nd floor of the Stream building, you can eat inside or in front of the shop, which is open air but still inside of the building. The system is similar with Subway, but with pizza instead of sandwiches, and fancier ingredients. You can choose 3 main toppings, 2 small toppings, one type of crust and one type of cheese. Ingredients are classified into veggies, carbs, and protein. About 12 people are behind the counter preparing your pizza, the process took less time than I expected. The resulting "pizza" was also better than I expected. Very good crust, quite filling rich topping... it was very good. But the cost is way above my budget for lunch: about 2000 yen for your pizza with a drink.',
            'visited'           => true,
            'visit_date'        => '2018-09-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/CITYSHOP+PIZZA/@35.6572997,139.7008033,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b771f06b86b:0x227bc684cd0bec37!8m2!3d35.6572954!4d139.702992',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13224516/',
            'official_website'  => 'https://shibuyastream.jp/shop/shopdetail.php?id=17',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['pizza','customisable dishes','open air space','no smoking','visa','vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.657298, 139.702978');

        $resto = Restaurant::create(array(
            'name'              => 'Spice Lip',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Thai',
            'lunch_price'       => 1080,
            'points'            => 'Thai restaurant, 2nd floor of the Stream building.',
            'experience'        => 'The place is nice. If the weather is OK, one part is opened to the outside, which is nice despite the view is blocked by immediate neighbor building, except for the occasional sewers smell. I ordered the gapao lunch set for 1000yen. It comes with a small salad and a small soup but no drink (you would need an additional 200yen for a coffee). For this price I needed something amazingly delicious or a very large portion. I\'ve got none of this. Decent gapao, but small and not worth the price.',
            'visited'           => true,
            'visit_date'        => '2018-09-20 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Spice+Lip/@35.6570607,139.7013485,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ba070269fc9:0x9aab2a9c51d4482a!8m2!3d35.6570607!4d139.7035372',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225015/',
            'official_website'  => 'https://shibuyastream.jp/shop/shopdetail.php?id=11',
            'score_lunch'       => 1,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['thai','gapao','green curry']);
        $resto->fillCoordinatesFromString('35.657055, 139.703530');

        $resto = Restaurant::create(array(
            'name'              => 'Shoutaian',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Beef',
            'lunch_price'       => 1400,
            'points'            => 'Japanese style dishes using superior quality beef.',
            'experience'        => 'Inside is small and cosy. Kinda dark, no windows, but nice atmosphere and nice staff. Various dishes using beef in different (Japanese) ways. Prices start at 1400 for 200g and you can order up to 600g. I tried the ponzu hamburg 200g for 1400 yen. It came with a salad and, otoshi (assortment of appetizers), a small soup, and a bowl of rice. It was very good. High quality meat indeed. A bit over my budget though, and each dish takes quiet a while to prepare (more than other restaurants in Japan, expect something like 10 minute), so as a result, the place is easily packed.',
            'visited'           => true,
            'visit_date'        => '2018-09-25 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E8%82%89%E3%83%90%E3%83%AB+%E5%B0%86%E6%B3%B0%E5%BA%B5+shou%E2%80%90taian%EF%BC%88%E3%82%B7%E3%83%A7%E3%82%A6%E3%82%BF%E3%82%A4%E3%82%A2%E3%83%B3%EF%BC%89%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6557067,139.7016565,17z/data=!4m5!3m4!1s0x60188b5742300001:0x856288d00297419d!8m2!3d35.6559892!4d139.7004459',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13156252/',
            'official_website'  => 'http://shoutaian-shibuyasakuragaoka.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['meat']);
        $resto->fillCoordinatesFromString('35.655981, 139.700456');

        $resto = Restaurant::create(array(
            'name'              => 'OreRyu Shio Ramen',
            'location'          => 'Shibuya, Nanpeidaicho',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'Nice and spacious ramen restaurant. Very tasty.',
            'experience'        => 'The place is quite nice for a ramen restaurant, quite spacious with big tables. Smoking semms allowed outside in the covered terrace, but inside is non-smoking. Quiet a large selection of ramens and side dishes. I ordered the spicy miso ramene with 3 yaki gyoza (dumplings) for 900 yen. I found the ramen absolutely delicious, the way I like it, with a very strong miso taste. There is also various additional miso pastes and various toppings available for free on the tables.',
            'visited'           => true,
            'visit_date'        => '2018-09-26 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E4%BF%BA%E6%B5%81%E5%A1%A9%E3%82%89%E3%83%BC%E3%82%81%E3%82%93+%E5%8D%97%E5%B9%B3%E5%8F%B0%E5%BA%97/@35.6546116,139.6950221,16z/data=!4m5!3m4!1s0x60188b5471de9181:0x1a309e05b5f0deb!8m2!3d35.6531984!4d139.6944015',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13113820/',
            'official_website'  => 'https://oreryushio.co.jp/?page_id=140',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','miso','English menu','smoking area']);
        $resto->fillCoordinatesFromString('35.653149, 139.694405');

        $resto = Restaurant::create(array(
            'name'              => 'Kyoto Katsugyu',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Katsu',
            'lunch_price'       => 1200,
            'points'            => 'Nice deep fried beef cutlet restaurant.',
            'experience'        => 'Despite being in B1F (no windows), the place was nice and clean. Nice choice of beef cutlet dishes, with various sauces. Maybe not the best choice if you have high cholesterol, but pretty good stuff.',
            'visited'           => true,
            'visit_date'        => '2018-09-27 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E7%89%9B%E3%82%AB%E3%83%84%E5%B0%82%E9%96%80%E5%BA%97+%E4%BA%AC%E9%83%BD%E5%8B%9D%E7%89%9B+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6576026,139.6973341,17z/data=!4m8!1m2!2m1!1skyoto+katsugyu+shibuya!3m4!1s0x60188b5609c33713:0x9ec2bfd2013c81a7!8m2!3d35.657262!4d139.696203',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13192127/',
            'official_website'  => 'https://shibuya-kyotokatsugyu.business.site/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['katsu','meat','English menu','no smoking']);
        $resto->fillCoordinatesFromString('35.657257, 139.696205');        

        $resto = Restaurant::create(array(
            'name'              => 'Bar a vin CROISEE',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Cafe',
            'lunch_price'       => 780,
            'points'            => 'Nice cafe with lunch options in the Stream building (2F, river side).',
            'experience'        => 'I had a falafel to-go there. There are many places you can eat outside around the Stream building. A falafel is 780 yen to-go, and 1000 yen inside (with some fries). It was kind of my first falafel, so it is hard to judge, but I found it very good.',
            'visited'           => true,
            'visit_date'        => '2018-09-28 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/bar+%C3%A0+vin+CROIS%C3%89E(%EF%BD%B8%EF%BE%9B%EF%BE%9C%EF%BD%BE%EF%BE%9E)%E6%B8%8B%E8%B0%B7%E3%82%B9%E3%83%88%E3%83%AA%E3%83%BC%E3%83%A0/@35.6573286,139.7017623,16z/data=!4m5!3m4!1s0x60188bd7307763c5:0x204bb46fdc53f4f4!8m2!3d35.657139!4d139.7030203',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225113/',
            'official_website'  => 'https://shibuyastream.jp/shop/shopdetail.php?id=9',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['cafe','open air space','vegetarian friendly','falafel','to-go']);
        $resto->fillCoordinatesFromString('35.657345, 139.703107');

        $resto = Restaurant::create(array(
            'name'              => 'Teppan kitchen',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Teppan',
            'lunch_price'       => 1000,
            'points'            => 'Decent teppanyaki restaurant. Spacious and bright place in Stream 2F.',
            'experience'        => 'Teppanyaki places are usually a bit on the expensive side, but this one is decently priced for lunch. The place is clean and bright, with a big windows bay. Tables and counter available. Then counter faces the grill so you can see the staff cooking on the teppan. I tried the chicken set,that came with some kind of pickles, a miso soup, a bowl of rice, and cabbage, for 1000 yen. It was very good. Other sets include beef, or okonomiyaki.',
            'visited'           => true,
            'visit_date'        => '2018-10-07 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/TEPPAN+KITCHEN/@35.6577056,139.7018495,17z/data=!4m5!3m4!1s0x60188bbe03e87e7b:0xbf9402ac5fd4b835!8m2!3d35.6573787!4d139.7028044',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225921/',
            'official_website'  => 'https://shibuyastream.jp/shop/shopdetail.php?id=26',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['teppanyaki','meat','okonomiyaki','no smoking','English menu']);
        $resto->fillCoordinatesFromString('35.657040, 139.703559');

        $resto = Restaurant::create(array(
            'name'              => 'The Dubliners',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Pub',
            'lunch_price'       => 980,
            'points'            => 'Nice place, even for lunch, with a terrace on the second floor, but smoking is allowed.',
            'experience'        => 'This is really great if the weather is nice and you get to be on their terrace on the 2nd floor. The inside is also nice, but smoking being allowed, it could ruin the experience. The menu has salad, pizza (with vegetarian options), pasta... I got the pasta lunch for 980 yen and it was surprisingly good. It came with a small salad and 2 slices of bread.',
            'visited'           => true,
            'visit_date'        => '2018-10-02 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/THE+DUBLINERS%E2%80%99+CAFE%26PUB+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.65842,139.6973621,18z/data=!4m8!1m2!2m1!1sthe+dubliners!3m4!1s0x0:0x1508740828033de5!8m2!3d35.6592533!4d139.6979588',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13025772/',
            'official_website'  => 'http://www.dubliners.jp/shop.html',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['pub','pasta','pizza','vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.659236, 139.697944');


        $resto = Restaurant::create(array(
            'name'              => 'Public House',
            'location'          => 'Shibuya, New South Exit',
            'type'              => 'Cafe',
            'lunch_price'       => 980,
            'points'            => 'Big cafe and lounge with nice but inexpensive menu.',
            'experience'        => 'Big place, with huge windows bay. Nice and confortable, divided in 2 parts: smoking and non-smoking. Buffet in the morning until 10am, then lunch until 4pm. Pasta, meat and rice... nice dishes can vary depending on the week. I tried some pork with balsamic vinegar, avocado and rice, for 980 yen. It was very good, and came with a drink as well.',
            'visited'           => true,
            'visit_date'        => '2018-10-03 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/PUBLIC+HOUSE/@35.6574761,139.7009524,17z/data=!3m1!5s0x60188b59c00ca2a5:0x89a02b53c17668b6!4m8!1m2!2m1!1spublic+house!3m4!1s0x0:0xf0cced61df64ae4a!8m2!3d35.6557838!4d139.7040582',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13110570/',
            'official_website'  => 'http://www.cafecompany.co.jp/brands/publichouse/shibuya/index.html',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['cafe','smoking area','English menu']);
        $resto->fillCoordinatesFromString('35.655830, 139.704006');

        $resto = Restaurant::create(array(
            'name'              => 'Kamukura',
            'location'          => 'Shibuya, center gai',
            'type'              => 'Ramen',
            'lunch_price'       => 1000,
            'points'            => 'Disapointing big ramen restaurant on center gai',
            'experience'        => 'The place is quite big (and bright) for a ramen restaurant, but it is counter only. Comfortable enough. For 1000 yen I got a set: a ramen and bowl of minced meat on rice with an egg. The ramen was disapointing. The soup was light, with a nice taste, but was extremely hot. The noodles were OK but in a very small quantity. The meat was just bad... and appart from that it was just some cabbage in the soup. The side dish was OKayish and allowed me to fill my belly (something the ramen in itself definitely cannot do). The staff is friendly, the place is convenient and foreigner-friendly, but in the end, the food is too poor to be worth it. Better go to any of the dozen of other ramen restaurants in the erea.',
            'visited'           => true,
            'visit_date'        => '2018-10-04 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Dotonbori+Kamukura+Shibuya/@35.6599138,139.69812,18z/data=!4m8!1m2!2m1!1sKamukura!3m4!1s0x0:0xaf0a49f2a71b0951!8m2!3d35.6606774!4d139.698384',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13018962/',
            'official_website'  => 'http://www.kamukura.co.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles']);
        $resto->fillCoordinatesFromString('35.660569, 139.698298');

        $resto = Restaurant::create(array(
            'name'              => 'Wine no Ruisuke',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Grill',
            'lunch_price'       => 950,
            'points'            => 'Grilled chicken. Decent place in Stream building.',
            'experience'        => 'Decent place. I tried the chicken burger for 750 yen, with an extra 200 yen for French fries. It took forever to come, like 10 or 15 minutes. The fries were kind of cold, and the amount similar to a McDo S size. The burger was very good. The bread was tasty, and the chicken was not dry at all and quiet delicious.',
            'visited'           => true,
            'visit_date'        => '2018-10-09 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Wine+no+Ruisuke/@35.6580377,139.7013155,17z/data=!4m8!1m2!2m1!1swine+no+ruisuke!3m4!1s0x0:0x2d18362c1a6a58a2!8m2!3d35.6569699!4d139.703462',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225214/',
            'official_website'  => 'https://shibuyastream.jp/shop/shopdetail.php?id=27',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['meat','no smoking','small servings']);
        $resto->fillCoordinatesFromString('35.656970, 139.703485');

        $resto = Restaurant::create(array(
            'name'              => 'Maita',
            'location'          => 'Shibuya, 2Chome',
            'type'              => 'Indian',
            'lunch_price'       => 860,
            'points'            => 'Decent Indian curry place, but smoking is allowed...',
            'experience'        => 'Situated on the 9th floor, you need to take a small and slow elevator to go there, but once inside, the place was cleaner than I expected. Windows are there but it is still quite dark. The menu is good and inexpensive. In addition to all the curry sets you could expect, they have green curry and curry soup as well. I tried the keema curry set for 780 yen (curry, nan, rice and salad) and a coffee for 80 yen. It came fast. It was good. Also: the amount of curry is really satisfying. However, the place alows smoking, and ther is no separation whatsoever. It is very likely someone will be smoking at some point, and that could really damage the experience.',
            'visited'           => true,
            'visit_date'        => '2018-10-10 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%80%E3%82%A4%E3%83%8B%E3%83%B3%E3%82%B0+%E3%83%90%E3%83%AB+MAITA/@35.6588465,139.7004431,17z/data=!4m8!1m2!2m1!1smaita!3m4!1s0x60188bc46ef88d95:0xa4b5f5de82e9dedf!8m2!3d35.6595026!4d139.7039229',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13224295/',
            'official_website'  => 'https://www.hotpepper.jp/strJ001200494/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['indian','curry','smoking allowed','English menu']);
        $resto->fillCoordinatesFromString('35.659644, 139.703863');

        $resto = Restaurant::create(array(
            'name'              => 'Glorious Chain Cafe',
            'location'          => 'Shibuya, 1Chome',
            'type'              => 'Cafe',
            'lunch_price'       => 1080,
            'points'            => 'Decent cafe with a nice menu.',
            'experience'        => 'Spacious cafe, with windows bay, but on the ground floor. Kind of a nice place, but the music was definitely not to my taste. I really likied the menu, it was hard to make my pick. But since it was mostly around 1200 yen, I try the cheapest option: the taco rice for 1080 yen. With one drink. Decent amount, not bad, but I wish it had been tastier.',
            'visited'           => true,
            'visit_date'        => '2018-10-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Glorious+Chain+Cafe/@35.6613921,139.7004716,17z/data=!4m5!3m4!1s0x60188ca7b8821d0d:0x87f8747fc4c12b8f!8m2!3d35.6620808!4d139.7026281',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe','taco rice','burger','omurice']);
        $resto->fillCoordinatesFromString('35.661942, 139.702440');


$resto = Restaurant::create(array(
            'name'              => 'Curry Udon Senkichi',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Japanese Curry',
            'lunch_price'       => 700,
            'points'            => 'Japanese curry with udon or rice sets. Nice and cheap.',
            'experience'        => 'Nice little place with tables and counter. Nice choice of curry udon and curry rice, with choice of additional toppings. Prices are really low. I got a curry with omurice and roasted eggplants (700 yen), and cheese (+100 yen). This was good, but the curry could be tastier (it was quite soft).',
            'visited'           => true,
            'visit_date'        => '2018-10-12 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%AB%E3%83%AC%E3%83%BC%E3%81%86%E3%81%A9%E3%82%93%E5%8D%83%E5%90%89+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6574312,139.6956922,17z/data=!4m5!3m4!1s0x60188b55eff7b697:0xf907ca33814d6c60!8m2!3d35.6570825!4d139.6956815',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13004645/',
            'official_website'  => 'https://senkichi.co.jp/#s02',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['udon','curry','omurice','English menu','no smoking']);
        $resto->fillCoordinatesFromString('35.657078, 139.695693');

        $resto = Restaurant::create(array(
            'name'              => 'Shibuya Tonteki',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Meat',
            'lunch_price'       => 900,
            'points'            => 'Kind of a meat fast food.',
            'experience'        => 'Basically 2 types of meats, minced, or not. Grilled on a hot plate. Reday fast. Choice of the amount from 200 to 300g. 800 yen for the largest portion, +200 yen for rice and soup. I tried the minced "hamburg" meatball with Japanese style topping, and it was not especially delicious, but very decent. Adjusted to the price I would say... you can get better, but then expect to spend more than 1200 yen.',
            'visited'           => true,
            'visit_date'        => '2018-10-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E6%B8%8B%E8%B0%B7%E3%83%88%E3%83%B3%E3%83%86%E3%82%AD+%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82G%E3%82%B9%E3%82%AF%E3%82%A8%E3%82%A2%E3%83%BC%E5%BA%97/@35.6578453,139.6955151,17z/data=!4m5!3m4!1s0x60188caa0042e8d1:0xe8b86c105ee2ae0d!8m2!3d35.6578192!4d139.6962017',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13165183/',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['meat']);
        $resto->fillCoordinatesFromString('35.657812, 139.696217');

        $resto = Restaurant::create(array(
            'name'              => 'Ventuno Tokyo',
            'location'          => 'Shibuya, center gai',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Spacious and bright place with tables, good pizza, and buffet option for lunch.',
            'experience'        => 'Located on the basement floor, but spacious and well lighted, it is a confortable place with better chairs and tables than most places. You can order a simple dish of pasta (less than 100 yen) or pizza (from 1000 yen), and add some money to get salad and soup. A salad bar is available, and is pretty good. Soft drinks are free. I tried a marguerita pizza for 1000 yen and it was surprisingly good. One of the best in Shibuya! But the crust is very thin and I ended up still hungry.',
            'visited'           => true,
            'visit_date'        => '2018-10-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Ventuno+Tokyo/@35.6597973,139.7000143,17z/data=!4m5!3m4!1s0x0:0x14266b8498cc3095!8m2!3d35.660386!4d139.699687',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13032476/',
            'official_website'  => 'https://ventuno-tokyo.owst.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pizza','pasta','no smoking']);
        $resto->fillCoordinatesFromString('35.660185, 139.699564');

        $resto = Restaurant::create(array(
            'name'              => 'Shoya',
            'location'          => 'Shibuya, center gai',
            'type'              => 'Izakaya',
            'lunch_price'       => 800,
            'points'            => 'Izakaya offering cheap lunch sets around noon.',
            'experience'        => 'It is nice to be able to feel that "authentic" izakaya atmosphere during lunch time. Old Japanese pop songs, wooden interior but confortable seats. Food is not especially delicious, but good enough given the low price.',
            'visited'           => true,
            'visit_date'        => '2018-10-19 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E5%BA%84%E3%82%84+%E6%B8%8B%E8%B0%B7%E3%82%BB%E3%83%B3%E3%82%BF%E3%83%BC%E8%A1%97%E5%BA%97+%E3%81%AF%E3%81%AA%E3%82%8C/@35.6603131,139.7010658,17z/data=!4m5!3m4!1s0x60188ca849058759:0x4f84dc1d50badd3a!8m2!3d35.6604442!4d139.6992838',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13111596/',
            'official_website'  => 'http://search.daisyo.co.jp/shop.php?shop_cd=3356',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['izakaya','English menu','smoking area','teishoku']);
        $resto->fillCoordinatesFromString('35.660442, 139.699292');

        $resto = Restaurant::create(array(
            'name'              => 'Tare Katsu',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Katsu',
            'lunch_price'       => 1000,
            'points'            => 'Nice katsudon restaurant.',
            'experience'        => 'Small place with just a counter. Cosy. Good food. Decent price. 1000 yen for a curry rice with deep fried vegetables and 3 pieces of deep fried pork (katsu). The curry itself was not especially tasty, but some spices are available to add directly on tyhe counter. The vegetables and katsu were very good.',
            'visited'           => true,
            'visit_date'        => '2018-10-07 13:00:00',
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/Tare+Katsu+Shibuya/@35.6582008,139.6988727,17z/data=!4m5!3m4!1s0x0:0x6baaa4e98431cfdd!8m2!3d35.6580526!4d139.6992428',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13185513/',
            'official_website'  => 'http://tarekatsu.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['katsu','no smoking']);
        $resto->fillCoordinatesFromString('35.658049, 139.699230');

        $resto = Restaurant::create(array(
            'name'              => 'Guykatsu Motomura',
            'location'          => 'Shibuya, 2Chome',
            'type'              => 'Guykatsu',
            'lunch_price'       => 1400,
            'points'            => 'Decent deep fried beef.',
            'experience'        => 'Small cosy place on the basement floor. Counter and tables. Must pay in cash upfront, choosing within 4 or 5 different sets. I tried the cheapest 1400 yen set that includes 150g of deep fried beef, difference sauces, cabbage, potatoe salad, bowl of rice (1 free refill) and miso soup. The beef is kind of raw, so you can put your slices on a small individual hot plate in front of you. I enjoyed my meal, it was quite good. The beef is of good quality, fat and kind of melting in your mouth.',
            'visited'           => true,
            'visit_date'        => '2018-10-22 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Gyukatsu+Motomura/@35.6586979,139.7016605,17z/data=!4m5!3m4!1s0x60188b5978424ac1:0x52e9ab4fbb69301f!8m2!3d35.6596472!4d139.7041266',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13183447/',
            'official_website'  => 'https://www.gyukatsu-motomura.com/shop/shibuya-bunten',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['katsu','meat','English menu','no smoking']);
        $resto->fillCoordinatesFromString('35.659706, 139.704094');

        $resto = Restaurant::create(array(
            'name'              => 'Les deux magots',
            'location'          => 'Shibuya, Mark City',
            'type'              => 'Bakery',
            'lunch_price'       => 1200,
            'points'            => 'Bakery in the basement floor of the Mark City building, on the Shibuya station.',
            'experience'        => 'Prices are a but high. Food is good, but maybe not enough to justify the price. Decent ham and cheese in my 500 yen small sandwich. The place is for "take-out" mostly, but there are some small tables availables as well.',
            'visited'           => true,
            'visit_date'        => '2018-10-23 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%89%E3%82%A5+%E3%83%9E%E3%82%B4+%E3%83%91%E3%83%AA+%E6%9D%B1%E6%A8%AA%E3%81%AE%E3%82%8C%E3%82%93%E5%BA%97/@35.6583688,139.6993904,18z/data=!3m1!5s0x60188b5631c5f5a9:0xf2c804909bcb1998!4m8!1m2!2m1!1z44OJ44KlIOODnuOCtA!3m4!1s0x0:0x513f651f326913bf!8m2!3d35.6587177!4d139.699942',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13198180/',
            'official_website'  => 'http://www.saint-germain.co.jp/shop/list/shop-283/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['bakery','sandwiches']);
        $resto->fillCoordinatesFromString('35.658740, 139.699925');

        $resto = Restaurant::create(array(
            'name'              => 'Kuumba du falafel',
            'location'          => 'Shibuya, Shinsencho',
            'type'              => 'Falafel',
            'lunch_price'       => 1200,
            'points'            => 'Small place. Big falafels.',
            'experience'        => 'Small counter and a couple of tables. 1200 yen for a full (big) falafel sandwich, or 1580 for the plate. They have various intersting imported beverages as well. The full falafel sand (sand is Japanese short for sandwich) is quite big and made me full. It was delicious at the begining, but got less tasty as I was digging in, as ingredients are compartimented and the paste things are dry without topping. The only topping was a very good, but thick and quite spicy, salsa paste. Maybe the plat ewould have been better for me since I would have been able to mix things together more... but the price is dissuasive.',
            'visited'           => true,
            'visit_date'        => '2018-10-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Kuumba+du+Falafel/@35.6568766,139.6954035,16z/data=!4m5!3m4!1s0x6018f35536baff83:0x83492aabeddee150!8m2!3d35.6571469!4d139.6907686',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13102142/dtlrvwlst/',
            'official_website'  => 'http://kuumbainternational.com/kuumbashop/KUUMBA/LOCATION.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['falafel','vegetarian friendly','English menu','no smoking']);
        $resto->fillCoordinatesFromString('35.657146, 139.690795');

        $resto = Restaurant::create(array(
            'name'              => 'Dalian',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Chinese',
            'lunch_price'       => 980,
            'points'            => 'Nice and good Chinese restaurant. Stream bld. 2F.',
            'experience'        => 'I liked the place. nice decoration, nice huge windows bay, no cheesy music, even the light bulbs were cool. Prices were surpringly decent, and the menu was offering a nice variety of choices. I ordered the boiled dumpling set, with "sui gyoza", soup, salad, rice, deep fried roll... 980 yen, +100 yen because I upgraded my rice to chanhan. I was surprised again at how good everything was. Delicious. Great experience.',
            'visited'           => true,
            'visit_date'        => '2018-10-25 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/DALIAN+%E6%B8%8B%E8%B0%B7%E3%82%B9%E3%83%88%E3%83%AA%E3%83%BC%E3%83%A0%E5%BA%97/@35.6571287,139.7012817,17z/data=!3m1!4b1!4m5!3m4!1s0x60188bdde90de97f:0x43949fc179489284!8m2!3d35.6571287!4d139.7034704',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225920/',
            'official_website'  => 'https://dalian-chukagai.owst.jp/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['chinese','dumpling','gyoza','English menu', 'no smoking','noodles']);
        $resto->fillCoordinatesFromString('35.657082, 139.703472');

        $resto = Restaurant::create(array(
            'name'              => 'Crossroads bakery',
            'location'          => 'Shibuya, Hikarie',
            'type'              => 'Bakery',
            'lunch_price'       => 1200,
            'points'            => 'A small bakery in Hikarie B2F. "Take-out" only.',
            'experience'        => 'I wanted a baguette sandwich, ham and cheese, and at the time that was the only bakery out of the five there are at Hikarie B2F that sold some. The camembert cheese was fine, the ham was too scarce, and the bread was bad (very chewy). The price was 453 yen. The staff was nice and gave me a piece of cookie for free (but it did not taste great). This is just a bakery, no place to stay and eat, but it is easy enough to go to the 11th floor and eat there (and the the view is great).',
            'visited'           => true,
            'visit_date'        => '2018-10-25 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%AF%E3%83%AD%E3%82%B9%E3%83%AD%E3%83%BC%E3%83%89%E3%83%99%E3%83%BC%E3%82%AB%E3%83%AA%E3%83%BC/@35.6588406,139.7008418,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b5203378a55:0xfe07333f6e6fab9a!8m2!3d35.6588406!4d139.7030305',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13219145/',
            'official_website'  => 'http://crossroadbakery.com/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['bakery','sandwich','to-go']);
        $resto->fillCoordinatesFromString('35.659135, 139.703750');

        $resto = Restaurant::create(array(
            'name'              => 'Suju Indigo',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Soba',
            'lunch_price'       => 1000,
            'points'            => 'Nice spacious restaurant serving cold or hot thick soba dishes.',
            'experience'        => 'Nice and spacious, with huge windows bay, counters and tables. Smooth jazz music with powerful bass. Nice choice of soba dishes, hot or cold, ranging from 950 to 1250 yen. Add 100 yen to get a decent amount. Advertising as healthy, all dishes have a fair amount of sesami and dry see weed. I tried the hot spicy chicken soba. It was good. But maybe not as delicious nor filling as to justify the price.',
            'visited'           => true,
            'visit_date'        => '2018-11-05 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/search/suju+indigo/@35.6582902,139.7011871,17z',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225911/',
            'official_website'  => 'http://www.suju-masayuki.com/shops/indigo.php',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['soba','English menu','no smoking']);
        $resto->fillCoordinatesFromString('35.657502, 139.703012');

        $resto = Restaurant::create(array(
            'name'              => 'Freshness Burger',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Burger',
            'lunch_price'       => 1200,
            'points'            => 'Decent Japanese alternative to Burger King.',
            'experience'        => 'The place is not big, but has an isolated smoking area with tables. Non-smoking main area has counters with electric outlets and small tablets. The place also offers free wifi. As for the food, while maybe not as good as Burger King or Wendy\'s, it is a decent Japanese alternative.',
            'visited'           => true,
            'visit_date'        => '2018-11-14 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Freshness+Burger/@35.658181,139.6943459,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b5602c23785:0xfdb4e0887f638201!8m2!3d35.6581767!4d139.6965346',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13061452/',
            'official_website'  => 'http://www.freshnessburger.co.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','smoking area','English menu','fast food']);
        $resto->fillCoordinatesFromString('35.658172, 139.696548');

        $resto = Restaurant::create(array(
            'name'              => 'Jeff\'s Kitchen Curry',
            'location'          => 'Shibuya, Station',
            'type'              => 'Japanese Curry',
            'lunch_price'       => 900,
            'points'            => 'Japanese curry fast food',
            'experience'        => 'Japanese curry dishes with many options. Counter only. Pay at machine and get ticket. English available. Food comes fast. Amount and price average. Not really tasty. You can go there if you are at the station and lack time for eating... but ohterwise it is not worth it.',
            'visited'           => true,
            'visit_date'        => '2018-11-15 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%B7%E3%82%A7%E3%83%95%E3%82%BA%E3%82%AD%E3%83%83%E3%83%81%E3%83%B3+%E3%82%AB%E3%83%AC%E3%83%BC%E5%8E%A8%E6%88%BF+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6593021,139.7007633,19z/data=!4m8!1m2!2m1!1z44Kr44Os44O8!3m4!1s0x0:0xefbe66f5dd54c1c3!8m2!3d35.6593443!4d139.701161',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13133149/',
            'official_website'  => 'https://www.jefb.co.jp/shop/brand/65',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['japanese curry','fast food','no smoking']);
        $resto->fillCoordinatesFromString('35.659364, 139.701136');

        $resto = Restaurant::create(array(
            'name'              => 'Krung Siam',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Thai',
            'lunch_price'       => 1080,
            'points'            => 'Nice Thai restaurant.',
            'experience'        => 'Inside is nice and clean. Nice staff as well. I tried the gapao set for lunch, 1080 yen, coming with a good soup and nice spring roll. The gapoa itself was tasty and quiet spicy (a bit too much to my taste).',
            'visited'           => true,
            'visit_date'        => '2018-11-16 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%AF%E3%83%AB%E3%83%B3%E3%82%B5%E3%82%A4%E3%82%A2%E3%83%A0%E6%B8%8B%E8%B0%B7%E6%96%87%E5%8C%96%E6%9D%91%E9%80%9A%E3%82%8A%E5%BA%97/@35.6575273,139.6964592,16z/data=!4m5!3m4!1s0x60188caa31ea7b1b:0xd6ef47e97b2df1c7!8m2!3d35.6596107!4d139.6955365',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13211556/',
            'official_website'  => 'http://www.krungsiam.info/shopinfo_dougenzaka.html',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['thai','gapao','no smoking']);
        $resto->fillCoordinatesFromString('35.659615, 139.695524');

        $resto = Restaurant::create(array(
            'name'              => 'OreRyu Shio Ramen',
            'location'          => 'Shibuya, Maruyamacho',
            'type'              => 'Ramen',
            'lunch_price'       => 980,
            'points'            => 'Nice place, with nice ramen.',
            'experience'        => 'Very nice place for a ramen restaurant, with quite a lot of tables. Beattles playing in the background. Mostly shio (salt) ramen, but other kind available as well, and lots of toppings can be added, with free dressings to adjust the taste. Optional side dishes includes yaki gyoza (fried dumplings), and meat on rice. I tried a spicy miso ramen and it was very tasty... but too spicy for me!',
            'visited'           => true,
            'visit_date'        => '2018-11-20 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E4%BF%BA%E6%B5%81%E5%A1%A9%E3%82%89%E3%83%BC%E3%82%81%E3%82%93+%E5%86%86%E5%B1%B1%E7%94%BA%E5%BA%97/@35.6575779,139.6948019,18z/data=!3m1!4b1!4m5!3m4!1s0x60188b62765e4df3:0xf9fbabe2a8e8a2a2!8m2!3d35.6575757!4d139.6958962',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225761/',
            'official_website'  => 'https://oreryushio.co.jp/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','no smoking','English menu']);
        $resto->fillCoordinatesFromString('35.657603, 139.695906');

        $resto = Restaurant::create(array(
            'name'              => 'OreRyu Shio Ramen',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Ramen',
            'lunch_price'       => 980,
            'points'            => 'Nice place, with nice ramen.',
            'experience'        => 'Nice but small place with a couple of table available outside as well. Counter and tables. Beattles playing in the background. Mostly shio (salt) ramen, but other kind available as well, and lots of toppings can be added, with free dressings to adjust the taste. Optional side dishes includes yaki gyoza (fried dumplings), and meat on rice. I tried a creamy shio ramen. I was soft and tasty, very nice.',
            'visited'           => true,
            'visit_date'        => '2018-11-20 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Oreryu+Shio-ramen+Shibuya-sohonten/@35.6550606,139.6948019,17z/data=!4m8!1m2!2m1!1z5L-65rWB5aGp44KJ44O844KB44KT!3m4!1s0x0:0x81ef09e68309aa54!8m2!3d35.656127!4d139.6954858',
            'tabelog_link'      => 'sdfsdfsdfsdfsdfsdf',
            'official_website'  => 'https://oreryushio.co.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','no smoking','English menu']);
        $resto->fillCoordinatesFromString('35.656154, 139.695443');

        $resto = Restaurant::create(array(
            'name'              => 'Suzu Cafe',
            'location'          => 'Shibuya',
            'type'              => 'Lounge',
            'lunch_price'       => 1080,
            'points'            => 'Lounge with pasta lunch and free soft drinks.',
            'experience'        => 'Inside is spacious, high ceiling, windows bay, with soft music playing in the background. Sofa and low tables available. Few items for lunch sets, most likely to be some spagethi dishes. Softs drinks such as juice, tea or coffee are free. Lunch sets, coming with a small salad, are about 1000 yen. I tried smoked sausages with tomato sauce spagheti: it was good. Seats and tables are not accomodoting for tall people.',
            'visited'           => true,
            'visit_date'        => '2018-10-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/SUZU+CAFE+-GEMS+Shibuya-/@35.6568201,139.7024207,17z/data=!4m5!3m4!1s0x60188b5bc76c3217:0xf06939768d0b66af!8m2!3d35.655713!4d139.7045021',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13148366/',
            'official_website'  => 'http://www.completecircle.co.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['cafe','pasta','smoking area']);
        $resto->fillCoordinatesFromString('35.655705, 139.704505');

        $resto = Restaurant::create(array(
            'name'              => 'Mc Donald\'s',
            'location'          => 'Shibuya, 1chome',
            'type'              => 'Burger',
            'lunch_price'       => 900,
            'points'            => 'It is a McDonald\'s',
            'experience'        => 'This one is quite big. Menu is what you can expect. There are some Japanese-only burgers, and most of the standards burgers.',
            'visited'           => true,
            'visit_date'        => '2018-01-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/McDonald\'s/@35.6597564,139.7010101,18z/data=!4m8!1m2!2m1!1smcdonald\'s!3m4!1s0x0:0xfee69d3a0dd310d!8m2!3d35.6599117!4d139.7020237',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13090239/',
            'official_website'  => 'https://map.mcdonalds.co.jp/map/13208',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','fast food','western']);
        $resto->fillCoordinatesFromString('35.659932, 139.702090');


        $resto = Restaurant::create(array(
            'name'              => 'Mc Donald\'s',
            'location'          => 'Shibuya, Jinnan',
            'type'              => 'Burger',
            'lunch_price'       => 900,
            'points'            => 'It is a McDonald\'s',
            'experience'        => 'Menu is what you can expect. There are some Japanese-only burgers, and most of the standards burgers.',
            'visited'           => true,
            'visit_date'        => '2018-01-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/McDonald\'s/@35.6594286,139.7007248,18z/data=!4m8!1m2!2m1!1smcdonald\'s!3m4!1s0x0:0x792a5218fa6c4c7b!8m2!3d35.6601969!4d139.7009731',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13098129/',
            'official_website'  => 'https://map.mcdonalds.co.jp/map/13146',
            'score_lunch'       => 1,
            'score_place'       => 0,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','fast food','western']);
        $resto->fillCoordinatesFromString('35.660195, 139.700910');

        $resto = Restaurant::create(array(
            'name'              => 'Mad Burger',
            'location'          => 'Shibuya',
            'type'              => 'Burger',
            'lunch_price'       => 1500,
            'points'            => 'Nice burger restaurant.',
            'experience'        => 'The interior is nice and cosy. Counter and tables of different sizes. Lunch menu add a small salad or fried potatoes to your burger for free. I had a avocado cheese burger for 1300 yen. It was very good, and very well balanced. It\'s a bit on the expensive side though. Note that you can customize your burger with additional toppings as well.',
            'visited'           => true,
            'visit_date'        => '2018-10-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/MAD+BURGER+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.656543,139.7030618,16z/data=!4m5!3m4!1s0x60188b5c19ad8e35:0x2c3ef54dfa893455!8m2!3d35.6574322!4d139.7076859',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13190615/',
            'official_website'  => 'http://madburgers.jp/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','no smoking']);
        $resto->fillCoordinatesFromString('35.657448, 139.707671');

        $resto = Restaurant::create(array(
            'name'              => 'Kirin City',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Pub',
            'lunch_price'       => 750,
            'points'            => 'Nice, bright and spacious space for casual lunch.',
            'experience'        => 'With large windows bay, the place is bright, and quite spacious with many tables in addition to a small counter. The menu for lunch is mostly pasta or Japanese curry. I tried "napolitan" spaghetti and was surprised at how good it was. For 750 yen. You can also get a small salad for +150yen, or the "drink bar" with soft drinks and soup for +200yen. Of course beer is also available on tap.',
            'visited'           => true,
            'visit_date'        => '2018-11-29 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/KIRIN+City/@35.6570982,139.7003463,18z/data=!4m8!1m2!2m1!1skirin+city!3m4!1s0x60188b59dfbde383:0xf3e4dc1ae8e0f2a4!8m2!3d35.656689!4d139.701432',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13014525/',
            'official_website'  => 'https://kirincity138.gorp.jp/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['pasta','meat','vegetarian friendly','English menu','smoking area','japanese curry']);
        $resto->fillCoordinatesFromString('35.656753, 139.701437');

        $resto = Restaurant::create(array(
            'name'              => 'Reg-on diner',
            'location'          => 'Shibuya',
            'type'              => 'Burger',
            'lunch_price'       => 1400,
            'points'            => 'Very nice burger. Tabelog\'s highest ranking for burgers in Shibuya.',
            'experience'        => 'Quite far from the station, the place doesn\'t look like anything special, is kinda small and not especially confortable. Windows bay make it bright and nice enough though. Menu is home made, customizable burgers only. I tried an avocado cheese burger (coming with some fried potatoes) for 1450 yen, which is a tad expensive. It was one of the best burger I ever had. It came shaped perfectly, standing beautifully on its plate without the help of any kind of pick, then squizzed smoothly in my hand as I was putting it into the folded paper to eat it. All ingredients were very fresh, lettuce was crispy, tomato was tasty, avocado was perfectly ripped, the 100% additive free meat was perfect and seasoned with pepper while mayonaise is used between the bread and the lettuce. The cheese was very good as was the bread. Very well balanced, a pleasure to eat. Tabelog might be right on this one.',
            'visited'           => true,
            'visit_date'        => '2018-11-30 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Reg-On+Diner/@35.656169,139.7081992,16z/data=!4m5!3m4!1s0x60188b5d16601319:0xe4b8eafd4feb0244!8m2!3d35.6555588!4d139.7098836',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13054359/',
            'official_website'  => 'http://www.regondiner.com/',
            'score_lunch'       => 4,
            'score_place'       => 1,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','no smoking']);
        $resto->fillCoordinatesFromString('35.655578, 139.709898');

        $resto = Restaurant::create(array(
            'name'              => 'Double Tall',
            'location'          => 'Shibuya',
            'type'              => 'Cafe',
            'lunch_price'       => 950,
            'points'            => 'Nice casual cafe. Good but very filling.',
            'experience'        => 'CAsual cafe, with a small terrace, and a non-smoking 2nd floor. Smoking is allowed on 1st floor. Menu has taco rice, hamburg, curry... this kind of dish, for about 1000 yen. I tried the taco rice. It was very good, but rather small. Better not to be very hungry. I enjoyed the peaceful atmosphere, as the place was not crowded and not near any busy street or road.',
            'visited'           => true,
            'visit_date'        => '2018-12-07 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/DOUBLE+TALL+CAFE+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.656681,139.7037923,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b5bf0d75b65:0x59ba186368456417!8m2!3d35.656681!4d139.705981',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13023504/',
            'official_website'  => 'https://doubletall.com/cafe/shibuya/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe','taco rice','smoking area','open air space']);
        $resto->fillCoordinatesFromString('35.656674, 139.705983');

        $resto = Restaurant::create(array(
            'name'              => 'Menya Itadori',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 950,
            'points'            => 'Nice curry ramen and tsukemen.',
            'experience'        => 'The place is quite small with a counter several small tables. Curry ramen, curry tsukemen and curry rice is availble with various options. I tried the shabu curry tsukemen, for 1050yen. Quality was good, nice meat, good curry soup (not very spicy, but a little bit). Small in amount though. For an additional 150yen you can get a side dish of flavored rice, which is pretty good.',
            'visited'           => true,
            'visit_date'        => '2018-12-14 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E9%BA%BA%E5%B1%8B%E8%99%8E%E6%9D%96+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6581636,139.6982863,18z/data=!4m8!1m2!2m1!1z6bq65bGL6JmO5p2WIOa4i-iwtw!3m4!1s0x60188bdb44f04969:0x7fc2cf2e19f3707d!8m2!3d35.6749473!4d139.7645722',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13228232/',
            'official_website'  => 'http://menyaitadori.jp/shop_004.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','tsukemen']);
        $resto->fillCoordinatesFromString('35.658555, 139.698586');

        $resto = Restaurant::create(array(
            'name'              => 'R',
            'location'          => 'Shibuya',
            'type'              => 'Cafe bar',
            'lunch_price'       => 950,
            'points'            => 'Smoking allowed. Nice atmosphere. Small amount of food.',
            'experience'        => 'The place is small, but relaxing and with big windows bay. The music is nice (retro) and warm from huge speakers. Smoking is allowed, so I cannot recommend this place to non smokers. Food takes a long time to come, and the amount is quite small. I tried a tofu and meat sauce gratin, for 950 yen. It came with a small salad and a small soup. It was good, but left me still hungry. You can get a drink, such as a coffee, for an additional 150 yen.',
            'visited'           => true,
            'visit_date'        => '2018-12-19 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Cafe+%26+Bar+R+%EF%BD%9E%E3%82%A2%E3%83%BC%E3%83%AB%EF%BD%9E/@35.657325,139.7031873,17z/data=!4m5!3m4!1s0x60188b595c34bc51:0xeaad5ebf918258b6!8m2!3d35.657325!4d139.705376',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13131541/',
            'official_website'  => 'http://www.cafebar-r.com/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe','smoking allowed','small servings']);
        $resto->fillCoordinatesFromString('35.657415, 139.705371');

        $resto = Restaurant::create(array(
            'name'              => 'Kasugatei',
            'location'          => 'Shibuya',
            'type'              => 'Abura soba',
            'lunch_price'       => 980,
            'points'            => 'Decent soupless ramen (abura soba, maze soba).',
            'experience'        => 'Small and narrow place with just a counter. Easily packed. Nice cool atmosphere, with walls full of handwritings. Very good abura soba.',
            'visited'           => true,
            'visit_date'        => '2019-01-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E6%B2%B9%E3%81%9D%E3%81%B0+%E6%98%A5%E6%97%A5%E4%BA%AD+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6585896,139.6970284,17.03z/data=!4m8!1m2!2m1!1z5rK544Gd44Gw!3m4!1s0x0:0xbe7c2b17507771d6!8m2!3d35.6589385!4d139.6984066',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13109540/',
            'official_website'  => 'http://www.kasugatei.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['noodles','ramen']);
        $resto->fillCoordinatesFromString('35.658927, 139.698365');

        $resto = Restaurant::create(array(
            'name'              => 'Ichikakuya',
            'location'          => 'Shibuya',
            'type'              => 'Ramen',
            'lunch_price'       => 980,
            'points'            => 'Good strong heavy ramen.',
            'experience'        => 'I like rich and strong taste for my ramen, and this place got me covered. Very good, for a humble price. They have a lot of choice but their main is Yakohama ikei ramen, which is one with a rich, fat and tasty soup.',
            'visited'           => true,
            'visit_date'        => '2019-01-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Ichikakuya/@35.6587626,139.696484,17.5z/data=!4m5!3m4!1s0x60188ca9d9c63a9f:0xbadb93c8d7e0cbf7!8m2!3d35.6589671!4d139.6983001',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13170862/',
            'official_website'  => 'https://gardengroup.co.jp/brand/yokohamadou/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['noodles','ramen','miso']);
        $resto->fillCoordinatesFromString('35.659068, 139.698397');

        $resto = Restaurant::create(array(
            'name'              => 'Gabulicious',
            'location'          => 'Shibuya',
            'type'              => 'Cafe bar',
            'lunch_price'       => 800,
            'points'            => 'Delicious food for a humble price, in a cosy place.',
            'experience'        => '2 floors, nice cosy place. Very diverse menu with beef steak, curry, pasta, pizza, fish, salads, ham, cheese... I tried the keema curry for 780 yen and it was absolutely delicious.',
            'visited'           => true,
            'visit_date'        => '2019-01-18 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%AF%E3%82%A4%E3%83%B3%E9%85%92%E5%A0%B4+GabuLicious%EF%BC%88%E3%82%AC%E3%83%96%E3%83%AA%E3%82%B7%E3%83%A3%E3%82%B9%EF%BC%89%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6572429,139.6955888,17z/data=!4m5!3m4!1s0x60188b56047d36b1:0xa10736b847d6df29!8m2!3d35.6578248!4d139.6967153',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13153474/',
            'official_website'  => 'http://www.sld-inc.com/gabulicious_shibuya.html',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['curry','pasta','teishoku','meat','pizza']);
        $resto->fillCoordinatesFromString('35.657885, 139.696650');

        $resto = Restaurant::create(array(
            'name'              => 'Libero Carta',
            'location'          => 'Shibuya',
            'type'              => 'Bar cafe',
            'lunch_price'       => 1300,
            'points'            => 'Clean but loud small bar restaurant serving good sandwiches and burgers at lunch.',
            'experience'        => 'Basement floor, the inside was clean and nice, but rather uncomfortable and very loud. Music is playing rather loudly, and a movie is also playing on a big TV at the same time, same at about the same volume.  Tables for 2 are very, very small, and ther eare 2 larger tables for larger groups. The menu for lunch has sandwiches and burgers. I tried the cheese and ham toasted sandwich with a drink for 1300 yen. Despite the bread being kind of chewy, it was quite very good.',
            'visited'           => true,
            'visit_date'        => '2019-01-21 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/LIBERO+CARTA+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.660562,139.6977494,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca84934ab03:0x9f51a719b65960d5!8m2!3d35.660562!4d139.699297',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13214622',
            'official_website'  => 'https://www.hotpepper.jp/strJ001185821/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','sandwich','English menu']);
        $resto->fillCoordinatesFromString('35.660576, 139.699281');

        $resto = Restaurant::create(array(
            'name'              => 'Wine&Cafe Sai',
            'location'          => 'Shibuya',
            'type'              => 'Cafe bar',
            'lunch_price'       => 1000,
            'points'            => 'Cosy wine bar restaurant with smoking and non smoking tables. Food quality seems above average.',
            'experience'        => 'Despite having a counter and some tables with people smoking, and could not smell any smoke at all, in the non-smoking area. The place seems rather popular and is busy. The menu for lunch had napolitan pasta, hamburg steak, omurice, shogayaki, and chicken curry... I tried the napolitan pasta for 1000yen, and it took forever to come to my table, but it was the best napolitan pasta I ever had. Prices go from 1000 to 1300 and include a drink.',
            'visited'           => true,
            'visit_date'        => '2019-01-23 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Wine%26Cafe+Sai/@35.6579771,139.6976704,17z/data=!4m8!1m2!2m1!1sshibuya+sai!3m4!1s0x60188b57c88afa95:0x574cf43d3c5eafdd!8m2!3d35.6582123!4d139.6993619',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13163276/',
            'official_website'  => 'http://www.cafeonesai.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 3,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['pasta','hamburg','omurice','curry','smoking area','English menu','teishoku']);
        $resto->fillCoordinatesFromString('35.658232, 139.699348');

        $resto = Restaurant::create(array(
            'name'              => 'Baluccha',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Delicious food in a cosy place for a decent price.',
            'experience'        => 'On the 2nd floor, at first the entrance looks like your are climbing some residential stairs to some appartment. But inside is nice and cosy. A counter and some tables. Just a few options for lunch, including the pasta of the day, for 1000 yen. Coming with a drink, it was above average. Very good.',
            'visited'           => true,
            'visit_date'        => '2019-01-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E5%8C%97%E9%99%B8%C3%97%E3%82%A4%E3%82%BF%E3%83%AA%E3%82%A2%E3%83%B3+%E3%83%90%E3%83%AB%E3%83%83%E3%83%81%E3%83%A3/@35.6575774,139.6970158,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b57b10baaab:0xd5d3f7eb96a54719!8m2!3d35.6575774!4d139.6992045',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13206012/',
            'official_website'  => 'https://ja-jp.facebook.com/%E5%8C%97%E9%99%B8%E3%82%A4%E3%82%BF%E3%83%AA%E3%82%A2%E3%83%B3-%E3%83%90%E3%83%AB%E3%83%83%E3%83%81%E3%83%A3-%E9%81%93%E7%8E%84%E5%9D%82-1274143949367616/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pasta']);
        $resto->fillCoordinatesFromString('35.657577, 139.699157');

        $resto = Restaurant::create(array(
            'name'              => 'Tandoor',
            'location'          => 'Shibuya, Seibu',
            'type'              => 'Indian curry',
            'lunch_price'       => 1480,
            'points'            => 'Decent Indian curry at Seibu B2F.',
            'experience'        => 'Down Seibu second basement floor, this restaurant will not be crowded. One of the cheapest lunch set includes 2 types of curry, an infinite amount of rice and nan, a salad and a drink, for 1480 yen. Lots of curry types you can choose from. I picked chicken keema and mutton masala. The curry came in generous amounts, and tasted very decent. The nan was OK, not great. As usual, Indian music was playing, but this time, from a CD that was skipping like every 2 seconds. Funny, for about 5 seconds. Anyway, this place was not bad, but I am looking for tastier curry that this, and the price is above my personal lunch budget, so I doubt I will be back.',
            'visited'           => true,
            'visit_date'        => '2019-01-28 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%BF%E3%83%B3%E3%83%89%E3%82%A5%E3%83%BC%E3%83%AB%E8%A5%BF%E6%AD%A6%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6603096,139.6984438,17.25z/data=!3m1!5s0x60188ca8177055e1:0x8ba002957d0f0ca1!4m5!3m4!1s0x60188ca81a7404b7:0x3ad0d47a7549883e!8m2!3d35.660239!4d139.700462',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13183492/',
            'official_website'  => 'http://www.tandoor.co.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['indian','curry','English menu','no smoking']);
        $resto->fillCoordinatesFromString('35.660191, 139.700325');

        $resto = Restaurant::create(array(
            'name'              => 'Botanical bar Oniwa',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Bar',
            'lunch_price'       => 1200,
            'points'            => 'Bar that cooks curry for lunch.',
            'experience'        => 'On the way up to loves hotels, in a bar that is rather difficult to find. The bar decoration is mostly plastic plants, hence the name. 2 low tables with sofas and a large counter. Curry is 700 yen for a portion or 1000 yen for all you can eat, with rice. You will need to buy a drink as well. Cheapest being 500 yen (coffee or other non alcoholic beverages). The place is dark, rather comfortable and intimate. The curry, "chicken colagen", is absolutely delicious. But with the additional cost just for the drink, the overall cost is stil too much.',
            'visited'           => true,
            'visit_date'        => '2019-01-30 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Botanical+Bar+ONIWA/@35.6595252,139.696404,18z/data=!4m8!1m2!2m1!1sbotanical+bar!3m4!1s0x0:0xdbc6a0bae6d65a2!8m2!3d35.6601729!4d139.6965426',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13212879/',
            'official_website'  => 'https://www.oniwa.tokyo/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 1
        ));
        $resto->attachTags(['japanese curry','curry','bar']);
        $resto->fillCoordinatesFromString('35.660200, 139.696471');

        $resto = Restaurant::create(array(
            'name'              => 'It Balls',
            'location'          => 'Shibuya',
            'type'              => 'Burger',
            'lunch_price'       => 1300,
            'points'            => 'Good burgers.',
            'experience'        => 'The place is nice and cosy, if you don\'t mind the 4 TVs silently showing some Japanese chanels. A bunch of tables and a small counter. THe menu has mostly burgers, but also some alternatives such as pasta, salad, chicken or shrimps. Prices range from 1080 to about 1580. I tried the cheese burger for 1280 yen. Coming with a drink, some fries and pickles, the bread was nicely toasted, plenty of lettuce, fresh tomato, decent cheese and sauce. The patty was good, grilled with pepper, but the amount of meat relatively small. Overall, slightly above average in my opinion, but I wish it would have been a bit tastier.',
            'visited'           => true,
            'visit_date'        => '2019-02-04 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%EF%BC%91F,+3+Chome-6-19+Shibuya,+Shibuya-ku,+T%C5%8Dky%C5%8D-to+150-0002/@35.6572775,139.7033648,17.25z/data=!4m5!3m4!1s0x60188b596c02f923:0xd9d6190474d14d3d!8m2!3d35.6576834!4d139.7048312',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13230499/',
            'official_website'  => 'https://itballs.gorp.jp/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger','to-go','western','american','English menu']);
        $resto->fillCoordinatesFromString('35.657682, 139.704761');

        
        $resto = Restaurant::create(array(
            'name'              => 'Bokkokukaitentori QueRico',
            'location'          => 'Shibuya, near Stream',
            'type'              => 'Mexican',
            'lunch_price'       => 980,
            'points'            => 'Good mexican roasted chicken tapas.',
            'experience'        => 'I liked inside was very colorful and clean, with the right volume of mexican music. For lunch, the menu consists mainly of roasted chicken with something else on the side. You can have one third, one half, or the full chicken. Lunch set comes with a delicious tomato soup. I tried the roasted chicken with taco rice, with the half-chicken size, for 980 yen. The chicken was absolutely delicious. The taco rice was very good, but I was expecting a bit tastier coming from mexican cuisine (but I could have used some of the additional sauce that were on the table, maybe that was my mistake). I ended up very satisfied, especially given the fact that it made me full for under a thousand yen. The English menu does not really covers the lunch sets, but the waitress was nice and helpful.',
            'visited'           => true,
            'visit_date'        => '2019-02-06 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Bokkokukaitentori/@35.6574011,139.7016273,18z/data=!4m5!3m4!1s0x60188b6acb418b83:0x3bb77ae2c24819bc!8m2!3d35.657261!4d139.702609',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225362/',
            'official_website'  => 'http://querico.jp/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['mexican','taco rice','meat']);
        $resto->fillCoordinatesFromString('35.657699, 139.702552');

        $resto = Restaurant::create(array(
            'name'              => 'Hobishuka',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Chinese',
            'lunch_price'       => 790,
            'points'            => 'Cheap Chinese restaurant allowing smoking inside.',
            'experience'        => '4F of a relatively crappy building. Inside is somehow casual and relaxing. Windows bay. No music, and a TV at very low volume. Smoking is allowed, so I cannot recommend this place unless you would like to smoke. At the time I went however, the smoke did not incomodate me (people were smoking 3 tables away from me). The menu has pictures and numbers, and offers a very large choice of dishes (more than 30), meat with vegetables, dumplings, tantamen or cantonese rice. For 790 yen I got a plate of meat and vegetables, a small plate of spicy tofu (you can change it for a salad), a soup, a small desert, and a bowl of rice. Cheap, tastes average.',
            'visited'           => true,
            'visit_date'        => '2019-01-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E4%B8%AD%E8%8F%AF%E6%96%99%E7%90%86+%E9%B3%B3%E7%BE%8E%E9%85%92%E5%AE%B6/@35.6576666,139.6973208,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b57ba8cc315:0x882d084effa600ee!8m2!3d35.6576666!4d139.6995095',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13117990/',
            'official_website'  => 'https://gegz100.gorp.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['chinese','dumpling','gyoza','teishoku','smoking allowed']);
        $resto->fillCoordinatesFromString('35.657693, 139.699551');

        $resto = Restaurant::create(array(
            'name'              => 'Queens Steak & Burger',
            'location'          => 'Shibuya',
            'type'              => 'Steak house',
            'lunch_price'       => 1000,
            'points'            => 'Nice steak and burger small restaurant.',
            'experience'        => 'Inside is simple and peaceful. You can order something for 1000 yen, but the full lunch set is 1500 yen. I tried the burger lunch set for 1500yen. I got one good coffee, one delicious soup and very delicious salad. The amount of soup and salad was way above average. Then the burger came with fries. The fries were very... fried. Hard. The burger sauce was delicious, and it had fresh tomato and lettuce, with decent bread. The patty was relatively big, 180g, but, very sadly, overcooked. Ended full and satisfied nonetheless.',
            'visited'           => true,
            'visit_date'        => '2019-02-15 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Queens+Steak+%26+Burger+%E3%82%AF%E3%82%A4%E3%83%BC%E3%83%B3%E3%82%BA+%E3%82%B9%E3%83%86%E3%83%BC%E3%82%AD%26%E3%83%90%E3%83%BC%E3%82%AC%E3%83%BC/@35.6593041,139.7008074,17z/data=!4m5!3m4!1s0x60188b573d1510f9:0xfcf677176ecd9fbf!8m2!3d35.6557529!4d139.6995206',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13220190/',
            'official_website'  => 'http://queens-japan.info/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['meat','burger','English menu']);
        $resto->fillCoordinatesFromString('35.655720, 139.69960');

        $resto = Restaurant::create(array(
            'name'              => 'Spice Magic',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Exotic',
            'lunch_price'       => 720,
            'points'            => 'Very peaceful, comfortable. Very good gapao, curry, and buritos.',
            'experience'        => 'Inside the Mont-Bell (shop for outdoor clothes and gear) building. Nice place, with a windows bay, and very soft guitar and piano music, accompanied by birds chirping. This create a relaxing atmosphere. Seats are confortable. On the menu are some rice and curry or gapao dishes, and some buritos, cakes or ice cream as well. I tried the gapao for 780yen, and the spicy chicken burito for 400 yen. The gapao was not like the one you find in a Thai restaurant, but it was totally delicious. The burito was very good as well. It is very good quality for the price. Warning though: it is spicy in the sense of flavor. It is not spicy hot, at all. Since it is in a Mont-Bell store, there are some moutains related magazines and stuff available to read. This place is very quite, and it is a thing hard to come by in Shibuya.',
            'visited'           => true,
            'visit_date'        => '2019-02-18 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%B9%E3%83%91%E3%82%A4%E3%82%B9%E3%83%9E%E3%82%B8%E3%83%83%E3%82%AF+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6623943,139.6953583,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9386fd50f:0xc6e4e5616f800733!8m2!3d35.66239!4d139.697547',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13045782/',
            'official_website'  => 'http://store.montbell.jp/spicemagic/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['gapao','curry','English menu','no smoking']);
        $resto->fillCoordinatesFromString('35.662355, 139.697588');

        $resto = Restaurant::create(array(
            'name'              => 'Gh Ethnica',
            'location'          => 'Shibuya, Stream',
            'type'              => 'Ethnic',
            'lunch_price'       => 1000,
            'points'            => 'Good dish of curry and mixed plates.',
            'experience'        => 'Located along the Shibuya river, in the ground floor of the Stream building, the place is rather nice, with its big windows bay. It is comfortable if you get a table with chairs, but lots of tables have stools to sit around. For lunch, the menu offers 3 different mixed plates (serving of meat and vegetables). I tried the special curry set for 1000 yen. It came with a salad on the side, of decent amount. The dish itself was delicious, and kind of fancy. The chicken curry was good, the rice as well, and on the top was various fresh vegetables. Drink set is plus 200 yen.',
            'visited'           => true,
            'visit_date'        => '2019-02-20 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/GH+ETHNICA/@35.6573243,139.7010106,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b868edda8a9:0x8b52a817991e927!8m2!3d35.65732!4d139.7031993',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225123/',
            'official_website'  => 'http://www.ghghgh.jp/gh-ethnica/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['curry','no smoking','English menu']);
        $resto->fillCoordinatesFromString('35.657391, 139.703171');

        // bahama kitchen

        $resto = Restaurant::create(array(
            'name'              => 'Bahama kitchen',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Ethnic',
            'lunch_price'       => 1000,
            'points'            => 'Very cosy cafe-bar-restaurant offering curry, hot sandwiches, rice or salads for lunch.',
            'experience'        => '4th floor. The place is small, with maybe 5 tables and a counter, but with a very nice relaxing atmosphere, helped by some old school reggae music. There is also a veranda where you can chill out and smoke. The place seems to also sell some vinyls. They have curry, hot sandwiches, flavored rice and salads for lunch, for about 1000 yen. I tried the "rainbow curry", for 1000 yen, which came with a nice little salad on the side. The dish was very good, and I wished the amount was a bit bigger. For an additional 100 yen you can get a drink. Their coffee was quite good.',
            'visited'           => true,
            'visit_date'        => '2019-02-27 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/BAHAMA+KITCHEN+%2F+%E3%83%90%E3%83%8F%E3%83%9E%E3%82%AD%E3%83%83%E3%83%81%E3%83%B3/@35.6624397,139.695118,17z/data=!3m1!4b1!4m5!3m4!1s0x60188d6670611e41:0x8cb8ebfb58ffa1c5!8m2!3d35.6624354!4d139.6973067',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13229320/',
            'official_website'  => 'https://www.bahamakitchen.tokyo/',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['curry',]);
        $resto->fillCoordinatesFromString('35.662427, 139.697371');

        $resto = Restaurant::create(array(
            'name'              => 'Le Sun Palm',
            'location'          => 'Shibuya, Mark City',
            'type'              => 'Cafe Restaurant',
            'lunch_price'       => 1000,
            'points'            => 'Cafe restaurant.',
            'experience'        => 'Tried the avocado cheese gravy hamburg for a thousand yen, plus about 200 extra for a coffee and a salad. The whole thing was good, but no so tasty. A good example of "it looks better than it actually tastes". They have various sweet, cakes, curry, salads, sandwiches... The place in itself was nothing special, decent, and conveniently situated right below the station, at one of the entrance of Mark City.',
            'visited'           => true,
            'visit_date'        => '2019-03-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/LE+SUN+PALM/@35.6583188,139.6983185,18z/data=!3m1!5s0x60188b5631c5f5a9:0xf2c804904456f99b!4m5!3m4!1s0x60188b5634144fd7:0x2d7418c4a0b97b43!8m2!3d35.6579592!4d139.6985438',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13154333/',
            'official_website'  => 'http://www.s-markcity.co.jp/shop/detail.php?id=142',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['cafe','sandwich','hamburg','curry','no smoking']);
        $resto->fillCoordinatesFromString('35.658016, 139.698608');

        $resto = Restaurant::create(array(
            'name'              => 'Nepalico',
            'location'          => 'Shibuya',
            'type'              => 'Curry',
            'lunch_price'       => 875,
            'points'            => 'Nice little place for Nepalese cuisine.',
            'experience'        => 'I liked the cosy interior, gentle lights, and instrumental music. The lunch menu mostly consists of curry with rice. I tried keema curry for 875 yen. It comes with a salad, and a drink, and I could get free large portion. Food is good, but my curry was not especially tasty. Quantity is good for the price.',
            'visited'           => true,
            'visit_date'        => '2019-03-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%8D%E3%83%91%E3%83%AA%E3%82%B3+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6568359,139.6970088,17z/data=!4m5!3m4!1s0x60188b56e4633e11:0x614743e2231beb93!8m2!3d35.6558378!4d139.6983928',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13116418/',
            'official_website'  => 'http://www.nepalico.com/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['curry','no smoking']);
        $resto->fillCoordinatesFromString('35.655858, 139.698378');

        $resto = Restaurant::create(array(
            'name'              => 'Yasube',
            'location'          => 'Shibuya',
            'type'              => 'Tsukemen',
            'lunch_price'       => 880,
            'points'            => 'Good tsukemen (noodles separated from soup) place.',
            'experience'        => 'Decent place with one counter and seats very close to each other, but it is pretty standard in any noodle restaurant. Staff is nice, music is better than usual. The place is for tsukemen only. But it is good. I tried the miso tsukemen, large portion, for 880 yen, added an egg in the soup for 100 yen more. The soup, that has quite consistent toppings, was very good. The noodles very good. I was plenty satisified.',
            'visited'           => true,
            'visit_date'        => '2019-03-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Tsukemen+Yasubee/@35.6571377,139.7016121,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b599da15555:0xb75387d44f62076!8m2!3d35.6571334!4d139.7038008',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13001149/',
            'official_website'  => 'http://www.yasubee.com/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['noodles','tsukemen']);
        $resto->fillCoordinatesFromString('35.657175, 139.703850');

        $resto = Restaurant::create(array(
            'name'              => 'AzabuSabo',
            'location'          => 'Shibuya, Seibu',
            'type'              => 'Teishoku',
            'lunch_price'       => 1200,
            'points'            => 'Down Seibu B2F. Quiet, but not very good.',
            'experience'        => 'Never very exciting going down Seibu B2F, but some good places are there. This one is not one of them. Lunch prices are above 1000 yen, for noodles, or rice based dishes. I tried some chicken with rice, with a salad, a soup and some pickles, for 1200 yen. It was not very good. The place seems to offer variety of sweets, and maybe rely on this more than regular meals. For 1200 yen, mine was very disappointing.',
            'visited'           => true,
            'visit_date'        => '2019-03-08 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E9%BA%BB%E5%B8%83%E8%8C%B6%E6%88%BF+%E8%A5%BF%E6%AD%A6%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.660479,139.6988661,18z/data=!3m1!5s0x60188ca8177055e1:0xf009a238452b6721!4m5!3m4!1s0x60188ca810804057:0xfba0048bd9a7ab8d!8m2!3d35.6602633!4d139.7005452?hl=en',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13180111/',
            'official_website'  => 'http://www.amaya-company.co.jp/shop/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['no smoking','teishoku','ramen','noodles']);
        $resto->fillCoordinatesFromString('35.660260, 139.700536');

        $resto = Restaurant::create(array(
            'name'              => 'The Neworder Table',
            'location'          => 'Shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1180,
            'points'            => 'Decent Italianish place, a bit pricey, but delicious.',
            'experience'        => 'The place is under the Toho cinema, underground, so no windows. But it is spacious inside, and confortable enough. Menu mostly have Italian oriented dishes, like pasta, pizza, salads... I tried the eggplants arabiata spaghetti for 1180 yen. Quite pricey, and coming without drink nor salad or soup. But, I have to admit it was absolutely delicious.',
            'visited'           => true,
            'visit_date'        => '2019-03-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E6%B8%8B%E8%B0%B7+%E3%82%A4%E3%82%BF%E3%83%AA%E3%82%A2%E3%83%B3+%E3%83%A9%E3%83%B3%E3%83%81+The+Neworder+Table-%E3%83%8B%E3%83%A5%E3%83%BC%E3%82%AA%E3%83%BC%E3%83%80%E3%83%BC%E3%83%86%E3%83%BC%E3%83%96%E3%83%AB-/@35.6581668,139.6990518,18z/data=!4m5!3m4!1s0x60188ca82a823dc5:0xf7b5ad8899dd0e28!8m2!3d35.6591105!4d139.6989445',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13199399/',
            'official_website'  => 'https://thenewordertable.com/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['italian','pasta','pizza']);
        $resto->fillCoordinatesFromString('35.659108, 139.698960');

        $resto = Restaurant::create(array(
            'name'              => 'Kinniku shokudo',
            'location'          => 'Shibuya',
            'type'              => 'Protein',
            'lunch_price'       => 1100,
            'points'            => 'Nice place, focused on protein diet. Lots of meats and data on what you eat.',
            'experience'        => 'With a funny name, meaning "muscle canteen", this is a nice place, with large counter and tables. Energetic music. Fair amount of options in the menu, with detailed info about amounts of protein, fat, carbs, calories etc... In addition of the cloassic glass of water, we also get a small cup of strawberry protein drink. I tried the chicken salad with balsamic vinegar dressing, for 1100 yen. It was big and delicious. The chicken was amazing, not dry, and not fat. Note that you can get dishes consisting mostly of differents meats, but price will raise consistently, possibly above 2000 yen.',
            'visited'           => true,
            'visit_date'        => '2019-03-13 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E7%AD%8B%E8%82%89%E9%A3%9F%E5%A0%82+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6567511,139.696381,17z/data=!4m5!3m4!1s0x60188b1c7282a307:0x974fdd1c3cf0f798!8m2!3d35.6572698!4d139.6965419',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225444/',
            'official_website'  => 'http://kinnikushokudo.jp/',
            'score_lunch'       => 3,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['meat']);
        $resto->fillCoordinatesFromString('35.657248, 139.696506');

        $resto = Restaurant::create(array(
            'name'              => 'Kantipur',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Nepalese',
            'lunch_price'       => 1000,
            'points'            => 'Very decent place to get curry and Nepalese/Indian food.',
            'experience'        => 'The access to the restaurant is not obvious and far from being appealing, but once inside, it is surprisingly spacious and cosy. Mostly curry for lunch (including an vegetables one), with all-you-can-eat nan and rice. The amount of curry is generous. The price is about 1000 yen. But they have a super lunch set with 7 different foods (in addition to the wecome salad) you can order for 1480 yen. So I tried that, choose one curry that was included, as well as a drink. It was, globally, very nice. Now, about the curry in itself, I chose keema, and it was fine, but I wished it was a bit tastier. Also, those who like spicy/hot food might be disapointed. It is all very mild. The nan was not especially good either. But I was happy eating there, mostly for the non-curry part, and the nice affordable big lunch set.',
            'visited'           => true,
            'visit_date'        => '2019-03-15 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Kantipur/@35.6562803,139.6998904,17z/data=!4m5!3m4!1s0x60188b575fc324bd:0xc09c92ad59533e0d!8m2!3d35.65635!4d139.701194',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13003357/',
            'official_website'  => 'http://www.kantipur.co.jp/index.html',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['indian','curry','no smoking','vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.656342, 139.701420');

        $resto = Restaurant::create(array(
            'name'              => 'Atomasashiya',
            'location'          => 'Shibuya, Nanpeidaicho',
            'type'              => 'Ramen',
            'lunch_price'       => 900,
            'points'            => 'Quite unique and delicious ramen.',
            'experience'        => 'Away from the busy part of Shibuya, this place is peaceful ramen restaurant run by an old couple. There are some tables and a counter. THe place seems it can be quite busy. The old man can speak English and will help foreigners to pick a ramen  on the ticket machine. It takes quite a while for the dish to be ready, but it is worth it. The soup has a rich and delicious taste. Noodles are good, and I loved the meat. I ordered an egg to be added, and it was also very delicious. It is, however, not very filling. Still, one of the best ramen in Shibuya.',
            'visited'           => true,
            'visit_date'        => '2019-03-18 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Atomasashiya/@35.6552862,139.693382,16.75z/data=!4m5!3m4!1s0x60188b542b9fb427:0x7840d7d541b5d67!8m2!3d35.655413!4d139.695995',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13122253/',
            'official_website'  => 'https://www.masaemon.jp/entry/2014/05/12/tokyo-shibuya-ramen-artmasashiya',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles']);
        $resto->fillCoordinatesFromString('35.655411, 139.695988');

        $resto = Restaurant::create(array(
            'name'              => 'Tengu Sakaba',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Izakaya',
            'lunch_price'       => 560,
            'points'            => 'Very cheap lunch, nice place, decent taste.',
            'experience'        => 'The lunch sets are very cheap, less tha 600 yen, and you can ask for big portion of rice without additional fee. The place is spacious and nice. The lunch options are mainly rice with deep fried meat, katsu, and/or Japanese curry. I tried the chicken katsu curry for 560 yen, that came with a miso soup, and an additional cabbage salad for 100 yen. The curry was quite bland, but at least the chicken was decent.',
            'visited'           => true,
            'visit_date'        => '2019-03-20 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%86%E3%83%B3%E3%82%B0%E9%85%92%E5%A0%B4+%E6%B8%8B%E8%B0%B7%E8%A5%BF%E5%8F%A3%E6%A1%9C%E4%B8%98%E5%BA%97/@35.6560825,139.6993113,17z/data=!4m8!1m2!2m1!1z5aSp54uX6YWS5aC0!3m4!1s0x60188b59fced79a1:0x4f183c7889af75a2!8m2!3d35.6559867!4d139.7015374',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13150021/',
            'official_website'  => 'https://www.teng.co.jp/sakaba/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['izakaya','teishoku','katsu']);
        $resto->fillCoordinatesFromString('35.656076, 139.701500');

        $resto = Restaurant::create(array(
            'name'              => 'Kiraku',
            'location'          => 'Shibuya',
            'type'              => 'Wonton',
            'lunch_price'       => 950,
            'points'            => 'Delicious wontonmen: ramen with Chinese dumplings inside.',
            'experience'        => 'Small restaurant on 2 floors, no decoration or atmosphere, and you can be seated on the same table where some random person is eating. The place is very popular, so there is often some wait time. The food is quite great. I tried the moyashi wontonmen, a big ramen with Chinese dumplings inside, for 950 yen. The soup was extremely hot, I would say, more than usual ramen. But it tasted great. The Chinese dumplings were also very good.',
            'visited'           => true,
            'visit_date'        => '2019-03-22 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Kiraku/@35.6585435,139.6965551,18z/data=!4m5!3m4!1s0x60188ca9c413ffff:0x9dbdd7c1104a4596!8m2!3d35.6590386!4d139.6968685',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13001705/',
            'official_website'  => 'https://ramendb.supleks.jp/s/41.html',
            'score_lunch'       => 2,
            'score_place'       => 0,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen']);
        $resto->fillCoordinatesFromString('35.659075, 139.696878');

        $resto = Restaurant::create(array(
            'name'              => 'Gokigentori',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Yakitori',
            'lunch_price'       => 950,
            'points'            => 'Nice cosy place. Very good yakitori lunch set.',
            'experience'        => 'I liked the cosy atmosphere and the wooden interior. There is a windows bay, but it remains rather dark, maybe because they have a loft, which is nice and allows for more tables. One large counter, and a few big tables. Nice music trying to set a "bistro" mood. Nice staff. I tried the first yakitori-don lunch set for 900 yen. 3 pieces of different skewered chicken, on a bowl of rice, with a miso soup and a small salad. It was all very good. Warning: if you order a set with more than 3 yakitori, you will get neck, soft bone and liver, which is quite different from the first 3 regular chicken meat.',
            'visited'           => true,
            'visit_date'        => '2019-03-25 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Bistro+Gokigentori+Shibuyahonshaten/@35.6568055,139.6948655,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b566e8c7bc9:0x11c2c3313a50e4b5!8m2!3d35.6568055!4d139.6970542',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13151085/',
            'official_website'  => 'http://www.gokigendori.jp/shoplist/shibuya.html',
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 1
        ));
        $resto->attachTags(['yakitori','no smoking']);
        $resto->fillCoordinatesFromString('35.656778, 139.697045');

        $resto = Restaurant::create(array(
            'name'              => 'Kabe no ana',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Pasta',
            'lunch_price'       => 1350,
            'points'            => 'Good pasta. Bit expensive.',
            'experience'        => 'The place is decent without being especially nice. There is a counter in front of the windows bay, and tables. It specializes in pasta, mostly spaghetti, with a lot of choices sorted by base. I tried the tomato-cream based avocado and mozzarella spaghetti. It came fuming hot, with a small salad and a drink, for 1350 yen. A bit expensive, but it was very good, a bit above average.',
            'visited'           => true,
            'visit_date'        => '2019-03-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Kabe+no+Ana+Shibuya+Shop/@35.6595738,139.6955171,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9c04fec51:0x1d70d674d3a3a85e!8m2!3d35.6595738!4d139.6977058',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13003362/',
            'official_website'  => 'http://www.kabenoana.com/',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 2,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['pasta','no smoking','visa','vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.659573, 139.697730');

        $resto = Restaurant::create(array(
            'name'              => 'Gansozushi',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Sushi',
            'lunch_price'       => 1000,
            'points'            => 'Cheap sushi.',
            'experience'        => 'The place is small and bare. Very narrow counter and small stools. Way less crowded than Uobei, another cheap sushi restaurant next door. There is a suhi-go-round convey belt, but there will not be much circulating there if there are not many customers. So you will haev to ask the chief directly. He is nice, can understand English, and make sushi very quickly. Prices start at 90yen for 2 sushi, such as tuna or saumon. Very cheap. Taste is decent for the price... but I would not recommend this place unless you would like a quick sushi snack (the place is opened from lunch to late diner non stop).',
            'visited'           => true,
            'visit_date'        => '2019-04-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Sushi+go-round+Restaurant+Shibuya/@35.6596115,139.6965986,18z/data=!4m5!3m4!1s0x60188ca9bedc7287:0xeef88e73f9aeca1e!8m2!3d35.6598861!4d139.6977761',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13044773/',
            'official_website'  => 'http://www.gansozushi.com/shop/cat5/20091010post-10.html',
            'score_lunch'       => 1,
            'score_place'       => 0,
            'score_food'        => 1,
            'score_price'       => 2,
            'score_date'        => 0
        ));
        $resto->attachTags(['sushi']);
        $resto->fillCoordinatesFromString('35.659887, 139.697758');

        $resto = Restaurant::create(array(
            'name'              => 'Kirin City',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Pub',
            'lunch_price'       => 740,
            'points'            => 'Nice pub. Good pasta.',
            'experience'        => 'Nice and confortable place. Various choice of vegetables, meats, pasta etc... Prices start cheap, at 740yen for the Napolitan spaghetti dish, which is quite good. You can also get a small salad for +150yen. As it is a pub, beer is available on tap',
            'visited'           => true,
            'visit_date'        => '2019-04-01 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Kirin+City+shibuya+dogenzaka/@35.6595889,139.6971534,18z/data=!4m5!3m4!1s0x60188ca9bd988ef9:0xd049d78122b6495e!8m2!3d35.660052!4d139.6975961',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13055706/',
            'official_website'  => 'https://kirincity104.gorp.jp/',
            'score_lunch'       => 2,
            'score_place'       => 2,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['pasta','meat','vegetarian friendly','English menu']);
        $resto->fillCoordinatesFromString('35.660059, 139.697676');

        // TODO //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $resto = Restaurant::create(array(
            'name'              => 'Yinega',
            'location'          => 'Shibuya, 1Chome',
            'type'              => 'Ethnic',
            'lunch_price'       => 890,
            'points'            => 'xxxxx',
            'experience'        => 'xxxxx',
            'visited'           => true,
            'visit_date'        => '2019-04-03 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%A4%E3%82%A7%E3%83%8D%E3%82%AC/@35.660704,139.7029031,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b58967a3719:0x1d0da2d9f68202f5!8m2!3d35.6606997!4d139.7050918',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13162279/',
            'official_website'  => 'https://yinega.wixsite.com/yinega',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['african','curry','kebab','taco rice','vegetarian friendly']);
        $resto->fillCoordinatesFromString('35.660703, 139.705117');

        // TODO Rin ramen
        $resto = Restaurant::create(array(
            'name'              => 'Rin',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Ramen',
            'lunch_price'       => 870,
            'points'            => 'Delicious, and huge, ramen.',
            'experience'        => 'Small place, with a few tables and a small counter. Interior is very bare (if you eat at the counter, you will be facing a decrepit wall and some machine to mix flour or something). The music is surprisingly good (maybe it has to do with the fact the restaraunt is in a records stores area). It has numerous options for the soup of the ramen: shio (salt), shoyu (soy sauce), spicy miso, ponzu, and even curry and curry cheese. Japanese menu only. Cash only. Order at the machine (1000yen bills only, otherwise ask the staff for change). Then it takes a while for the chef to cook (more than the fast pace of the average ramen). Despite the unexpensive price, the ramen is absolutely huge. Maybe 3 times more than the average, so you better be hungry. Lots of noodles, but also an extremely generous amount of very good meat. I tried the spicy miso ramen, and it was really delicious, with a very rich flavor. Quite a unique taste. Easily one of my favorite in Shibuya. And it made me so full... which is rare for a ramen. Note: there is no paper tissue, so I recommend you have some on you.',
            'visited'           => true,
            'visit_date'        => '2019-04-08 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Rin+Shibuya/@35.6613949,139.6974323,17z/data=!4m5!3m4!1s0x60188caeb3709559:0x4296cbdad9b41c51!8m2!3d35.6630335!4d139.6971554',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13121071/',
            'official_website'  => 'https://www.shonanboy.net/shibuya-ramen-rin/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['ramen','noodles','miso','filling']);
        $resto->fillCoordinatesFromString('35.663035, 139.697177');

        // TODO Vandalism
        $resto = Restaurant::create(array(
            'name'              => 'Vandalism',
            'location'          => 'Shibuya',
            'type'              => 'Bar Cafe',
            'lunch_price'       => 950,
            'points'            => 'xxxxx',
            'experience'        => 'xxxxx',
            'visited'           => true,
            'visit_date'        => '2019-04-10 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/VANDALISM+%E6%B8%8B%E8%B0%B7/@35.6601789,139.6967235,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca83562a5b3:0x6c5c80f130fe8f1d!8m2!3d35.6601746!4d139.6989122',
            'tabelog_link'      => 'xxxxx',
            'official_website'  => 'http://vandalism.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['burger']);
        $resto->fillCoordinatesFromString('35.660237, 139.698954');

        // TODO Chao Thai
        $resto = Restaurant::create(array(
            'name'              => 'Chao Thai',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Thai',
            'lunch_price'       => 950,
            'points'            => 'xxxxx',
            'experience'        => 'xxxxx',
            'visited'           => true,
            'visit_date'        => '2019-04-12 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Chao+Thai+Shibuya+Dogenzaka+shop/@35.6588557,139.6958478,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9d9c63a9f:0x9fe65bb7c5478600!8m2!3d35.6588514!4d139.6980365',
            'tabelog_link'      => 'xxxxx',
            'official_website'  => 'http://www.chaothai.jp/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['thai','gapao']);
        $resto->fillCoordinatesFromString('35.659001, 139.697995');

        // TODO J.S. Curry
        $resto = Restaurant::create(array(
            'name'              => 'J.S. Curry',
            'location'          => 'Shibuya, 1Chome',
            'type'              => 'Japanese Curry',
            'lunch_price'       => 1047,
            'points'            => 'xxxxx',
            'experience'        => 'xxxxx',
            'visited'           => true,
            'visit_date'        => '2019-04-15 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/J.S.CURRY+Miyamasuzaka/@35.6599431,139.7013178,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b5866d5ca05:0xa16cc0d1f628d227!8m2!3d35.6599388!4d139.7035065',
            'tabelog_link'      => 'xxxxx',
            'official_website'  => 'https://www.baycrews.co.jp/brandlist/food/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['curry']);
        $resto->fillCoordinatesFromString('35.659834, 139.703527');

        // TODO Grandma George
        $resto = Restaurant::create(array(
            'name'              => 'Grandma\'s Georges',
            'location'          => 'Shibuya',
            'type'              => 'Cafe Restaurant',
            'lunch_price'       => 950,
            'points'            => 'Nice cafe but can be noisy.',
            'experience'        => 'I was surprised at how very nice inside is, despite being underground. Very nice interior, high ceiling, and smooth jazz songs. Lots of tables, fitting for groups. Crowd is mostly woman. Menu offers various healthy looking dishes for lunch. Very reasonably priced. I paid about 950yen for a coffee, a soup, a small salad, rice, and a dish of grilled chicken with vegetables. Second good surprise, the dish was absolutely delicious. Now, because the room is big and there is no partitioning, it can get noisy in there. Also, smoking is allowed... so there is a risk you can smell smoke during your lunch, even if it does not seem like a place that can get smoky.',
            'visited'           => true,
            'visit_date'        => '2019-04-17 13:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Grandma\'s+GEORGES/@35.6621366,139.6973616,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca8f02f712d:0x5b77b493d83ffa87!8m2!3d35.6621323!4d139.6995503',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13163682/',
            'official_website'  => 'https://r.gnavi.co.jp/1g6kpzkm0000/',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['xxxxx']);
        $resto->fillCoordinatesFromString('35.662126, 139.699524');

      
/*
// try french boistrot 139 near MontBell

// try HIDE OUT next to Daiso

// try this curry place : https://tabelog.com/tokyo/A1303/A130301/13171069/dtlmap/
// ramen kiraku https://fr.favy-jp.com/topics/2309

SHIBUichi BAKERY SHIBUichi BAKERY (Bread、Cafe、Burger)
3.39  15 reviews 
SHIBUichi BAKERY  - 料理写真:３種のチーズとはちみつのキューブ

crazy amount of food in hirakatashi
https://tabelog.com/osaka/A2707/A270702/27071015/
in shibuya
https://tabelog.com/tokyo/A1303/A130301/13059598/


SERGEY: Prospect 2018 , downfall 2004

*/


        // mcdo near stream and bic camera big one, 

        /*
        $resto = Restaurant::create(array(
            'name'              => 'xxxxx',
            'location'          => 'Shibuya',
            'type'              => 'xxxxx',
            'lunch_price'       => 950,
            'points'            => 'xxxxx',
            'experience'        => 'xxxxx',
            'visited'           => true,
            'visit_date'        => '2019-04-01 13:00:00',
            'google_maps_link'  => 'xxxxx',
            'tabelog_link'      => 'xxxxx',
            'official_website'  => 'xxxxx',
            'score_lunch'       => 1,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 1,
            'score_date'        => 0
        ));
        $resto->attachTags(['xxxxx']);
        $resto->fillCoordinatesFromString('xxxxx');
        */

        // NEGISHI

        // roppongi place with jorge and yamaguchi
        // https://www.google.com/maps/place/%E3%80%92106-0031+T%C5%8Dky%C5%8D-to,+Minato-ku,+Nishiazabu,+1+Chome%E2%88%928,+%EF%BC%A6%EF%BC%A5%EF%BC%B2%EF%BC%AD%EF%BD%89%EF%BC%AE%EF%BC%B4%EF%BC%B8%EF%BC%AF/@35.6598723,139.726296,18z/data=!4m5!3m4!1s0x60188b7a7ad6966d:0xc96057139678e33c!8m2!3d35.6608104!4d139.7248288

        // best sushi place from inuisan : http://www.uogashizushi.co.jp/shop/o-shop/tokyoeki/

        // good ramne up yoyogi koen west side : https://tabelog.com/tokyo/A1318/A131810/13004221/dtlmenu/

        // https://www.menya-cocoro.com/

        // https://hubjapan.io/articles/10-best-ramen-restaurants-in-shibuya

        // https://www.youtube.com/watch?v=S-Xm7s9eGxU
        // https://goo.gl/TbjN44

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
