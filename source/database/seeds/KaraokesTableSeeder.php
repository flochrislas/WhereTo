<?php

use Illuminate\Database\Seeder;
use App\Karaoke;

class KaraokesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('karaokes')->delete();

        $karaoke = Karaoke::create(array(
            'name'              => 'Karaoke-kan',
            'area'              => 'Shibuya',
            'location'          => 'Center Gai',
            'points'            => 'Biggest karaoke chain in Tokyo, but not the cheapest.',
            'experience'        => 'Situated in the main street "center gai", the place always look quite busy. Most Karaokekan offers maracas and tambourines to play with for free, as well as some costumes. Of course, lots of drinks and food are also available on the menu, with all-you-can-drink options.',
            'visited'           => false,
            'visit_date'        => null,
            'google_maps_link'  => 'https://www.google.com/maps/place/Karaokekan/@35.6582333,139.6973818,17z/data=!4m5!3m4!1s0x60188ca9a62ec5c7:0xe9b5b3ca3ef50590!8m2!3d35.660417!4d139.698798',
            'official_website'  => 'http://karaokekan.jp/index.html',
            'score'             => 1
        ));
        $karaoke->attachTags(['karaokekan','private rooms']);
        $karaoke->fillCoordinatesFromString('35.660434, 139.698699');

        $karaoke = Karaoke::create(array(
            'name'              => 'Joysound',
            'area'              => 'Shibuya',
            'location'          => 'Sation\'s south exit',
            'points'            => 'One of the main systems maker',
            'experience'        => 'Good karaoke with popular system. If the main building is full, they will take you to some other subsidiary place nearby.',
            'visited'           => false,
            'visit_date'        => null,
            'google_maps_link'  => 'https://www.google.com/maps/place/JOYSOUND/@35.6590358,139.6999694,18z/data=!4m8!1m2!2m1!1sjoysound!3m4!1s0x60188b57c0c20b7b:0x2daf0687b41f13ca!8m2!3d35.6583445!4d139.6998317',
            'official_website'  => 'https://shop.joysound.com/shop/joysound-shibuyaminamiguchi/',
            'score'             => 1
        ));
        $karaoke->attachTags(['joysound','private rooms']);
        $karaoke->fillCoordinatesFromString('35.658400, 139.699813');


        $karaoke = Karaoke::create(array(
            'name'              => 'Joysound',
            'area'              => 'Shibuya',
            'location'          => 'Dogenzaka',
            'points'            => 'One of the main systems maker',
            'experience'        => 'Good karaoke with popular system.',
            'visited'           => false,
            'visit_date'        => null,
            'google_maps_link'  => 'https://www.google.com/maps/place/%E3%82%AB%E3%83%A9%E3%82%AA%E3%82%B1+JOYSOUND%E6%B8%8B%E8%B0%B7%E9%81%93%E7%8E%84%E5%9D%82%E5%BA%97%EF%BC%88%E3%82%B8%E3%83%A7%E3%82%A4%E3%82%B5%E3%82%A6%E3%83%B3%E3%83%89%EF%BC%89/@35.6578705,139.6981214,18z/data=!4m8!1m2!2m1!1sjoysound!3m4!1s0x60188b57b4dc951b:0x11d4e65747f637eb!8m2!3d35.6576613!4d139.6988554',
            'official_website'  => 'https://shop.joysound.com/shop/joysound-shibuyadougenzaka/',
            'score'             => 1
        ));
        $karaoke->attachTags(['joysound','private rooms']);
        $karaoke->fillCoordinatesFromString('35.657687, 139.698939');

        https://www.google.com/maps/place/JOYSOUND/@35.6590358,139.6999694,18z/data=!4m8!1m2!2m1!1sjoysound!3m4!1s0x60188b57c0c20b7b:0x2daf0687b41f13ca!8m2!3d35.6583445!4d139.6998317



    }
}
