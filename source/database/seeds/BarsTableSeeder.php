<?php

use Illuminate\Database\Seeder;
use App\Bar;

class BarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bars')->delete();

        $bar = Bar::create(array(
            'name'              => 'Tatsu Ichi',
            'location'          => 'Shibuya, Center Gai',
            'type'              => 'Standing Bar',
            'points'            => 'A standing bar where it is easy to mingle with people. Quite cheap, no table charge.',
            'experience'        => 'Situated at the end of the main street "center gai", there is always a lot of foreigners there. Friendly Japanese staff. TV playing sports. Music. All kind of drinks and food also available. Payment must be done upon order at the table. Beer is 300yen. Can be kind of loud and smoky. Very easy to mingle with people. There is a live webcam you can access from their website, if you want to check the crowd.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/TASUICHI/@35.6599464,139.6966029,17z/data=!4m5!3m4!1s0x60188ca970e7aaab:0x8b912b8ebfe8bafe!8m2!3d35.6609271!4d139.6970106',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13020555/',
            'official_website'  => 'http://www.tasuichi.co.jp/',
            'score'             => 2,
            'score_place'       => 1,
            'score_mingling'    => 3,
            'score_price'       => 2
        ));
        $bar->attachTags(['standing','no table charge']);
        $bar->fillCoordinatesFromString('35.660987, 139.697056');

        $bar = Bar::create(array(
            'name'              => 'Coins bar',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Bar',
            'points'            => 'Everthing is 300 yen. No table charge.',
            'experience'        => 'B1F. Can be loud and smoky. Quite dark. Possible to mingle, especially if drinking at the main, big table, but people are not supposed to leave their seat and walk around. Complimentary small bowl of popcorn is given. Sometimes, there is a popular DJ event.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Coins+Bar/@35.6620286,139.6950236,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9415924a3:0xb1f01b371af5fcc4!8m2!3d35.6620243!4d139.6972123',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13036298/',
            'official_website'  => 'https://www.hotpepper.jp/strJ000011428/',
            'score'             => 1,
            'score_place'       => 1,
            'score_mingling'    => 2,
            'score_price'       => 2
        ));
        $bar->attachTags(['no table charge']);
        $bar->fillCoordinatesFromString('35.662074, 139.697344');

        $bar = Bar::create(array(
            'name'              => 'Hub',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'British Pub',
            'points'            => 'Big chain of British pubs. No table charge.',
            'experience'        => 'B1F. One section for non smokers. Happy hours from 5pm to 7pm. Quite easy to walk around and mingle. TV shows some sports.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/HUB%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97/@35.6586675,139.6963393,17.25z/data=!4m5!3m4!1s0x60188ca9dbd8f065:0x74414b744e7f5174!8m2!3d35.6588625!4d139.6978816',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13185353/',
            'official_website'  => 'https://www.pub-hub.com/index.php/shop/detail/96',
            'score'             => 1,
            'score_place'       => 1,
            'score_mingling'    => 2,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge','pub']);
        $bar->fillCoordinatesFromString('35.658931, 139.697853');

        $bar = Bar::create(array(
            'name'              => 'Hub',
            'location'          => 'Shibuya, Center Gai',
            'type'              => 'British Pub',
            'points'            => 'Big chain of British pubs. No table charge.',
            'experience'        => 'B1F. Happy hours from 5pm to 7pm. Quite easy to walk around and mingle. TV shows some sports. Popular and well situated, the place is quite crowded, usually with a lot of foreigners.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/HUB/@35.6598773,139.6977413,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca823a4c10f:0xd9a1a25ed54e0e6!8m2!3d35.659873!4d139.69993',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13185646/',
            'official_website'  => 'https://www.pub-hub.com/index.php/shop/detail/90',
            'score'             => 1,
            'score_place'       => 1,
            'score_mingling'    => 2,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge','pub']);
        $bar->fillCoordinatesFromString('35.659906, 139.699935');

        $bar = Bar::create(array(
            'name'              => 'Hub',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'British Pub',
            'points'            => 'Big chain of British pubs. No table charge.',
            'experience'        => 'B1F. One section for non smokers. Happy hours from 5pm to 7pm. Quite easy to walk around and mingle. TV shows some sports. Popular, the place is fairly crowded, usually with a lot of foreigners.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/HUB+Shibuya/@35.6624703,139.6965823,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9207054cd:0x912d5501fd059fc1!8m2!3d35.662466!4d139.698771',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13002059/',
            'official_website'  => 'https://www.pub-hub.com/index.php/shop/detail/2',
            'score'             => 2,
            'score_place'       => 1,
            'score_mingling'    => 2,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge', 'non smoking area','pub']);
        $bar->fillCoordinatesFromString('35.662435, 139.698766');
        
        $bar = Bar::create(array(
            'name'              => 'Hub',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'British Pub',
            'points'            => 'Big chain of British pubs. No table charge.',
            'experience'        => 'B1F. Happy hours from 5pm to 7pm. Quite easy to walk around and mingle. TV shows some sports. Popular, the place is fairly crowded, usually with a lot of foreigners.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/HUB+Shibuya/@35.6624703,139.6965823,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9207054cd:0x912d5501fd059fc1!8m2!3d35.662466!4d139.698771',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13042468/',
            'official_website'  => 'https://www.pub-hub.com/index.php/shop/detail/109',
            'score'             => 1,
            'score_place'       => 1,
            'score_mingling'    => 2,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge','pub']);
        $bar->fillCoordinatesFromString('35.660660, 139.698791');

        $bar = Bar::create(array(
            'name'              => 'Hobgoblin',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'British Pub',
            'points'            => 'Small chain of British pubs. No table charge.',
            'experience'        => '3F. Nice pub, feeling better, more local, than the Hub, will be less crowded and not as easy to mingle.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Hobgoblin+Shibuya+British+Pub/@35.6583003,139.6974463,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b57c704df9d:0x45950055d352b895!8m2!3d35.658296!4d139.699635',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13015858/',
            'official_website'  => 'http://hobgoblin.jp/%E2%80%8B%E3%83%9B%E3%83%96%E3%82%B4%E3%83%96%E3%83%AA%E3%83%B3%E6%B8%8B%E8%B0%B7/',
            'score'             => 1,
            'score_place'       => 2,
            'score_mingling'    => 1,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge','pub']);
        $bar->fillCoordinatesFromString('35.658331, 139.699608');

        $bar = Bar::create(array(
            'name'              => 'The Dubliners',
            'location'          => 'Shibuya, Dogenzaka',
            'type'              => 'Irish Pub',
            'points'            => 'Nice pub with an open air section.',
            'experience'        => '2F. I really like that they have a covered terrace, opened all seasons. The place can be quite crowded, with lots of foreigners.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/THE+DUBLINERS\'+IRISH+PUB+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6592592,139.6957692,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca9c4cfffff:0x7cf08f7b4c365416!8m2!3d35.6592549!4d139.6979579',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13025772/',
            'official_website'  => 'http://www.dubliners.jp/shop.html',
            'score'             => 2,
            'score_place'       => 2,
            'score_mingling'    => 1,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge','pub']);
        $bar->fillCoordinatesFromString('35.659216, 139.697948');

        $bar = Bar::create(array(
            'name'              => 'Pres',
            'location'          => 'Shibuya, Center Gai',
            'type'              => 'Jazz Bar',
            'points'            => 'Small, dark, peaceful bar with jazz music.',
            'experience'        => 'B1F. The place is very dark, with a big U-shape counter only. Nice bartender making nice cocktails. The crowd is mostly mature Japanese. There is a few hundreds yen for table charge.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Pres+Jazz+Bar/@35.6602446,139.6967952,17.75z/data=!4m5!3m4!1s0x60188ca9a73db223:0x94724485461cf449!8m2!3d35.6608245!4d139.6979092',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13171801/',
            'official_website'  => 'http://tokyojazzsite.com/directory/listing/pres-jazz-bar',
            'score'             => 1,
            'score_place'       => 1,
            'score_mingling'    => 1,
            'score_price'       => 0
        ));
        $bar->attachTags(['jazz']);
        $bar->fillCoordinatesFromString('35.660836, 139.697885');

        $bar = Bar::create(array(
            'name'              => 'BAR',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Bar',
            'points'            => 'No table charge. Could not figure out the name of the place.',
            'experience'        => '3F. The place is actually quite nice, rather spacious, dark but clean and with tables next to the windows, and a large U-shape counter. On a Friday night, nobody was there around 9pm, and when I tried again around 11pm, a bunch of friends, young people, were playing a soccer video game on a PS4.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%80%92150-0031+T%C5%8Dky%C5%8D-to,+Shibuya-ku,+Sakuragaokach%C5%8D,+17%E2%88%9212+%E6%B8%8B%E8%B0%B7%E3%82%B8%E3%83%A7%E3%83%B3%E3%82%BD%E3%83%B3%E3%83%93%E3%83%AB3F/@35.6567914,139.699856,17.25z/data=!4m5!3m4!1s0x60188b57588ff7e3:0xd18c54432a6f217e!8m2!3d35.6561446!4d139.7013286',
            'tabelog_link'      => '',
            'official_website'  => '',
            'score'             => 1,
            'score_place'       => 1,
            'score_mingling'    => 1,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge']);
        $bar->fillCoordinatesFromString('35.656169, 139.701352');

        $bar = Bar::create(array(
            'name'              => 'The Wine',
            'location'          => 'Shibuya',
            'type'              => 'Restaurant',
            'points'            => 'Very cheap, and good wine. 500 yen table charge.',
            'experience'        => 'B1F. This is a nice little restaurant. Nice staff as well. It is OK to go there and just drink. The wine is good and very cheap, starting 180 yen a glass, or 800 yen for a bootle! But there is a 500 yen table charge (a fee each person must pay).',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/The+Wine%EF%BC%88%E3%82%B6+%E3%83%AF%E3%82%A4%E3%83%B3%EF%BC%89+%E6%B8%8B%E8%B0%B7%E5%BA%97/@35.6563044,139.6986873,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b5440ad5c93:0xa2dd6ed95050aea3!8m2!3d35.6563001!4d139.700876',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13225241/',
            'official_website'  => 'https://gf5j603.gorp.jp/',
            'score'             => 2,
            'score_place'       => 2,
            'score_mingling'    => 0,
            'score_price'       => 1
        ));
        $bar->attachTags(['no smoking']);
        $bar->fillCoordinatesFromString('35.656348, 139.700879');

        /*
        $bar = Bar::create(array(
            'name'              => 'xxxx',
            'location'          => 'Shibuya',
            'type'              => 'Bar',
            'points'            => 'xxxxxxxx',
            'experience'        => 'xxxxxxxx',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'xxxxxxxxx',
            'tabelog_link'      => 'xxxxxxxxx',
            'official_website'  => 'xxxxxxxxx',
            'score'             => 1,
            'score_place'       => 1,
            'score_mingling'    => 1,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge');
        $bar->fillCoordinatesFromString('xxxxxxxxxxx');
        */
    }

    /*

    
Jazz bar
Shisha place the 3 ones
the 2 pubs near office
the irish pub
the dubliners
abbot choice

https://fr.favy-jp.com/topics/121

sake stand

MUSIC BAR ROCKAHOLIC 渋谷(ロックバー)

https://theculturetrip.com/asia/japan/articles/the-10-best-nightclubs-and-bars-in-tokyo-for-cheap-drinks/

https://www.google.com/maps/place/NIGHTFLY/@35.6628603,139.6941868,18z/data=!4m8!1m2!2m1!1sudagawacho+shibuya+bar!3m4!1s0x60188cac6b7b7ef9:0xd9f8a5101f317a8c!8m2!3d35.6628603!4d139.6948375


https://www.google.com/maps/place/%EF%BC%AD%EF%BC%A9%EF%BC%AC%EF%BC%AC%EF%BC%A9%EF%BC%A2%EF%BC%A1%EF%BC%B2/@35.6609738,139.6965086,20z/data=!4m8!1m2!2m1!1sudagawacho+shibuya+bar!3m4!1s0x60188ca97b2e66d3:0xe80b9e858422743b!8m2!3d35.6609912!4d139.6967015

http://tokyojazzsite.com/directory/categories/shibuya

    */
}
