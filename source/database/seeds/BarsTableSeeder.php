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
        $bar->attachTags(['jazz','music']);
        $bar->fillCoordinatesFromString('35.660836, 139.697885');

        $bar = Bar::create(array(
            'name'              => 'Start bar',
            'location'          => 'Shibuya, Sakuragaokacho',
            'type'              => 'Bar',
            'points'            => 'No table charge.',
            'experience'        => '3F. The place is quite nice, rather spacious, dark but clean and with tables next to the windows, and a large U-shape counter. Friendly bartender. On a Friday night, nobody was there around 9pm, and when I tried again around 11pm, a bunch of friends, young people, were playing a soccer video game on a PS4.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Start+Bar/@35.65644,139.6987829,16.75z/data=!4m5!3m4!1s0x60188b575886aaab:0xcae17a169b9e55a6!8m2!3d35.65615!4d139.701309',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13188833/',
            'official_website'  => 'http://start-bar.com/',
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
            'type'              => 'Izakaya',
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
        $bar->attachTags(['no smoking','izakaya']);
        $bar->fillCoordinatesFromString('35.656348, 139.700879');

        $bar = Bar::create(array(
            'name'              => 'Rock no cocoro',
            'location'          => 'Shibuya, Udagawacho',
            'type'              => 'Bar',
            'points'            => 'Lound rock music. Quite narrow, but pretty nice atmosphere.',
            'experience'        => 'Quite loud, it is difficult to have a conversation. Can be smoky too. If not too crowded, it is possible to play darts. If you enjoy the music, atmosphere is quite nice, and the crowd usually as well. Prices are alright (like 600 yen for a glass of wine).',
            'visited'           => true,
            'visit_date'        => '2017-07-07 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%83%AD%E3%83%83%E3%82%AF%E3%81%AE%E3%81%93%E3%81%93%E3%82%8D+rock+no+cocoro/@35.6624971,139.6948316,17z/data=!3m1!4b1!4m5!3m4!1s0x60188ca94e710061:0xaaa1b5c22d19ddd3!8m2!3d35.6624928!4d139.6970203',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13096097/',
            'official_website'  => 'http://www.rocknococoro.com/home.html',
            'score'             => 1,
            'score_place'       => 1,
            'score_mingling'    => 2,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge','rock','music']);
        $bar->fillCoordinatesFromString('35.662463, 139.696944');

        $bar = Bar::create(array(
            'name'              => 'JBS',
            'location'          => 'Shibuya',
            'type'              => 'Music Bar',
            'points'            => 'Small music bar. No smoking. Scary bartender. Great music, not too loud. No table charge. Cheap drinks. No food.',
            'experience'        => 'This place is very unique. First thing you notice, is the very nice room, all wood, and thousands and thousands of vynils. Then you notice the bartender is an old Japanese man who looks extremely angry and unfriendly. You will not feel welcomed. If you ask him if there is a menu, or if you can smoke, or he has Japanese sake, he will just get angry at you. It is seriously unbelievable, especially in Japan. But, if you can ignore this, then you can have a great time. Just ask for a drink based on what bottles you see at the bar. It will be 500 yen for a whiskey or a simple cocktail like a vodka tonic, or a bottled beer. There is no charge. No food. The music, and the accoustic, is really great. The volume was perfect to me, loud enough to enjoy the music, but without hurting your ears, and there is no problem having a conversation without speaking loudly. Actually, try not being loud, that would make the owner even angrier.',
            'visited'           => true,
            'visit_date'        => '2019-03-20 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Jazz+Blues+Soul+Bar/@35.6570326,139.6945271,17z/data=!3m1!4b1!4m5!3m4!1s0x60188b560d4a3e8d:0x38273669398e2e25!8m2!3d35.6570283!4d139.6967158',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13123950/',
            'official_website'  => 'https://eyescream.jp/fashion/1608/',
            'score'             => 2,
            'score_place'       => 2,
            'score_mingling'    => 1,
            'score_price'       => 1
        ));
        $bar->attachTags(['no table charge','music','no smoking']);
        $bar->fillCoordinatesFromString('35.657019, 139.696739');


        $bar = Bar::create(array(
            'name'              => 'Tokyo Wine Club Raku',
            'location'          => 'Shibuya',
            'type'              => 'Izakaya',
            'points'            => 'Nice and lively place. Good to enjoy some unexpensive wine. 500 yen otoshi.',
            'experience'        => 'The place was quite crowded, but with a very nice and lively atmosphere. Nice staff. Terrace with a few tables available outside too. Wine is not expensive, starting at 2800 yen a bottle, with lots of options. You will have to accept a small salad that costs 500 yen in order to stay. They offer a lots of small dishes to accompany wine, as well.',
            'visited'           => true,
            'visit_date'        => '2019-03-20 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/%E6%9D%B1%E4%BA%AC%E3%83%AF%E3%82%A4%E3%83%B3%E5%80%B6%E6%A5%BD%E9%83%A8+%E6%A5%BD/@35.6577027,139.6965384,17.75z/data=!4m5!3m4!1s0x0:0x5bcac7d78f4ec076!8m2!3d35.6573443!4d139.6977125',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13109931/',
            'official_website'  => 'https://tokyowinecluberaku.gorp.jp/',
            'score'             => 2,
            'score_place'       => 2,
            'score_mingling'    => 0,
            'score_price'       => 1
        ));
        $bar->attachTags(['izakaya','wine']);
        $bar->fillCoordinatesFromString('35.657538, 139.697765');

        $bar = Bar::create(array(
            'name'              => 'Tavern Bach',
            'location'          => 'Shibuya',
            'type'              => 'Izakaya',
            'points'            => 'Feels like a very cheap izakaya, but actually not cheap.',
            'experience'        => 'Inside looks terrible. No decoration nor atmosphere whatsoever. It is just a big room with simple tables next to each other, with stools to seat. It is extremely bare. It feels cheap, but it is not. Prices are the same than a regular, nice izakaya. It gets crowded easily for some reasons though, so it can become noisy and smoky. There are tablets for placing orders but it is in Japanese only, without pictures, and the interface is crappy and not intuitive. It is possible to order from an English paper menu, despite the staff does not understand English. It takes longer but it is doable. The choice of food is not great, and the quality is not great either... I cannot see any reason to go there.',
            'visited'           => true,
            'visit_date'        => '2019-03-20 22:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/Tavern+Bach/@35.6571329,139.6967588,18.5z/data=!4m5!3m4!1s0x60188b566b2f02bf:0x122b10ce3fa1163a!8m2!3d35.6570919!4d139.6973728',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13182198/',
            'official_website'  => 'https://www.hotpepper.jp/strJ001127934/yoyaku/?vos=othpporgzzzzx00000001',
            'score'             => 0,
            'score_place'       => 0,
            'score_mingling'    => 0,
            'score_price'       => 1
        ));
        $bar->attachTags(['izakaya']);
        $bar->fillCoordinatesFromString('35.657093, 139.697368');

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
        $bar->attachTags(['no table charge']);
        $bar->fillCoordinatesFromString('xxxxxxxxxxx');
        */
    }

    /*

JBS Shibuya
https://favy-jp.com/shops/106930

Music Bar 45
Rockaholic

Hakuri Tabai Hanbey Shibuya (薄利多賣半兵ヱ 渋谷道玄坂店)
that maybe is the place i went with tapia

Shisha place the 3 ones
the 2 pubs near office
abbot choice

https://fr.favy-jp.com/topics/121

sake stand

MUSIC BAR ROCKAHOLIC 渋谷(ロックバー)

https://theculturetrip.com/asia/japan/articles/the-10-best-nightclubs-and-bars-in-tokyo-for-cheap-drinks/
Aoyama Tunnel, Shibuya
Oath, Shibuya
loud music

https://www.google.com/maps/place/NIGHTFLY/@35.6628603,139.6941868,18z/data=!4m8!1m2!2m1!1sudagawacho+shibuya+bar!3m4!1s0x60188cac6b7b7ef9:0xd9f8a5101f317a8c!8m2!3d35.6628603!4d139.6948375

Milli bar
https://www.google.com/maps/place/%EF%BC%AD%EF%BC%A9%EF%BC%AC%EF%BC%AC%EF%BC%A9%EF%BC%A2%EF%BC%A1%EF%BC%B2/@35.6609738,139.6965086,20z/data=!4m8!1m2!2m1!1sudagawacho+shibuya+bar!3m4!1s0x60188ca97b2e66d3:0xe80b9e858422743b!8m2!3d35.6609912!4d139.6967015

http://tokyojazzsite.com/directory/categories/shibuya

Club de Beluga : 
Anticipation (feat Thomas Siffing)

    */
}
