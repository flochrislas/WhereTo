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

    }
}
