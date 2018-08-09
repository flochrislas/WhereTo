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
        $resto->attachTags(['burger','fast food','to-go','visa']);
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
        $resto->attachTags(['burger','fast food','to-go','visa']);
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
            'points'            => 'Popular turkish dishes such as kebab, possibility to eat inside on table. Dishes come on a plate, and with soup and tea.',
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
            'name'              => 'Coco Ichi',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Japanese curry',
            'lunch_price'       => 900,
            'points'            => 'Highly customizable Japanese curry. Very delicious. Counter only.',
            'experience'        => 'Once you find the right combination for you, it can be really delicious. I personally have "chicken katsu (deepfried) curry + nasu (eggplants) + cheese, ichi-kara (spicy level 1)". English menu is available.',
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
        $resto->attachTags(['japanese curry','to-go','visa','English menu','customisable dishes','no smoking']);
        $resto->fillCoordinatesFromString('35.657260, 139.700408');

        $resto = Restaurant::create(array(
            'name'              => 'Coco Ichi',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Japanese curry',
            'lunch_price'       => 900,
            'points'            => 'Highly customizable Japanese curry. Very delicious. Counter and tables.',
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
        $resto->attachTags(['japanese curry','to-go','visa','English menu','customisable dishes','no smoking']);
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
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Japanese curry',
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
            'points'            => 'Supposedly famous chicken rice',
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

        $resto = Restaurant::create(array(
            'name'              => 'Meat Bank',
            'location'          => 'Shibuya',
            'type'              => 'Burger',
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
        $resto->attachTags(['curry', 'vegetarian']);
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
            'score_lunch'       => 3,
            'score_place'       => 2,
            'score_food'        => 2,
            'score_price'       => 1,
            'score_date'        => 0
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
        $resto->attachTags(['burger','american','to-go','visa']);
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
            'experience'        => 'There seems to be always a line of people sitting, waiting their turn to be able to enter the place. THe place is quite big, with tables and counters. You can grab items from the convey belt running everywhere, but there is a big menu with hundreds of choices and paper where you just have to put dish number and amount to place an order. You can get a dish of 2 sushi for as cheap as 100 yen. Tastes decent. There is also a fair amount of vegan sushi and dishes. I was surprised at how good the avocado tempura was, and quite filling for 250 yen. The amount of fish on each sushi is quite nice.',
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
        $resto->attachTags(['sushi', 'vegan friendly', 'vegetarian friendly']);
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
            'google_maps_link'  => 'https://www.google.co.jp/maps/place/%E3%83%9F%E3%83%84%E3%83%95%E3%82%B8%E3%83%80%E3%82%A4%E3%83%8B%E3%83%B3%E3%82%B0+%7C+%E6%B8%8B%E8%B0%B7%E9%A7%85+%E5%B1%85%E9%85%92%E5%B1%8B+%E5%92%8C%E9%A3%9F+%7C+%E3%81%97%E3%82%83%E3%81%B6%E3%81%97%E3%82%83%E3%81%B6+%E5%A5%B3%E5%AD%90%E4%BC%9A+%E5%AE%B4%E4%BC%9A+%E8%AA%95%E7%94%9F%E6%97%A5%E4%BC%9A+%E8%A5%BF%E5%8F%A3/@35.6581514,139.6991014,17z/data=!4m5!3m4!1s0x0:0xbb0d1f999517a9d6!8m2!3d35.6573233!4d139.697712',
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
            'tabelog_link'      => '',
            'official_website'  => '',
            'score_lunch'       => 2,
            'score_place'       => 1,
            'score_food'        => 1,
            'score_price'       => 0,
            'score_date'        => 0
        ));
        $resto->attachTags(['french','cafe']);
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
            'experience'        => 'One big counter. Foreigner friendly (friendly staff and English menus). Decent choice of ramen, tsukemen, tantanmen, and hiyashi ramen in summer. I tried shoyu tsukemen with egg, large portion for 950, and it was very good. THe piece of meat was kinda smoked, quite delicious. One thing I would reproach them though is that when I went in mid-July, their door stayed opened and it was too hot inside.',
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
        $resto->attachTags(['burger','curry','cafe','western');
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
            'points'            => 'Counter with great view on Shibuya\'s crossing. Nice cafe serving pasta and deserts with fresh fruits.',
            'experience'        => '7th floor right in front of the crossing. THe view is very nice> The pasta were actually delicious. For lunch, you can add +400yen to your 1200yen pasta dish inn order to get a mini fresh fruits parfait. The food comes rather slowly, but it is easy to be patient while enjoying the view. Each time I went the place was rather empty, which is another good point. The mini parfait I had was not especially great, lacking sweetness to my westerner taste.',
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
        $resto->attachTags(['cafe','pasta','desert']);
        $resto->fillCoordinatesFromString('35.659735, 139.700788');




        /*
        $resto = Restaurant::create(array(
            'name'              => '',
            'location'          => '',
            'type'              => '',
            'lunch_price'       => 950,
            'points'            => '',
            'experience'        => '',
            'visited'           => true,
            'visit_date'        => '2018-08-07 13:00:00',
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

        // roppongi place with jorge and yamaguchi
        // https://www.google.com/maps/place/%E3%80%92106-0031+T%C5%8Dky%C5%8D-to,+Minato-ku,+Nishiazabu,+1+Chome%E2%88%928,+%EF%BC%A6%EF%BC%A5%EF%BC%B2%EF%BC%AD%EF%BD%89%EF%BC%AE%EF%BC%B4%EF%BC%B8%EF%BC%AF/@35.6598723,139.726296,18z/data=!4m5!3m4!1s0x60188b7a7ad6966d:0xc96057139678e33c!8m2!3d35.6608104!4d139.7248288

        // best sushi place from inuisan : http://www.uogashizushi.co.jp/shop/o-shop/tokyoeki/

        // good ramne up yoyogi koen west side : https://tabelog.com/tokyo/A1318/A131810/13004221/dtlmenu/

        // https://www.menya-cocoro.com/

        // https://hubjapan.io/articles/10-best-ramen-restaurants-in-shibuya

        // https://www.youtube.com/watch?v=S-Xm7s9eGxU
        // https://goo.gl/TbjN44

        // king of spices , or spice king, towards ebisu or somethin

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
