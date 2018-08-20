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

        $resto = Bar::create(array(
            'name'              => 'Tatsu Ichi',
            'location'          => 'Shibuya, center gai',
            'type'              => 'Bar',
            'points'            => 'A standing bar wher eit is easy to minglr with people. On the cheap side.',
            'experience'        => 'Situated at the end of the main street "center gai", ther eis always a lot of foreigners there. Friendly Japanese staff. TV playing sports. Music. All kind of drinks and food also available. Payment must be done upon order at the table. Beer is 300yen.',
            'visited'           => true,
            'visit_date'        => '2017-07-07 20:00:00',
            'google_maps_link'  => 'https://www.google.com/maps/place/TASUICHI/@35.6599464,139.6966029,17z/data=!4m5!3m4!1s0x60188ca970e7aaab:0x8b912b8ebfe8bafe!8m2!3d35.6609271!4d139.6970106',
            'tabelog_link'      => 'https://tabelog.com/tokyo/A1303/A130301/13020555/',
            'official_website'  => 'http://www.tasuichi.co.jp/',
            'score'             => 2,
            'score_place'       => 1,
            'score_mingling'    => 3,
            'score_price'       => 2
        ));
        $resto->attachTags(['standing']);
        $resto->fillCoordinatesFromString('35.660987, 139.697056');
    }
}
