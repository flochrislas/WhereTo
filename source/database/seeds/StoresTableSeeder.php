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
          'name'              => 'Hakata Furyu',
          'location'          => 'Shibuya',
          'type'              => 'Ramen',
          'points'            => 'Tonkostsu miso available, up to 2 free kaidama, choice soft/hard for the noodles',
          'experience'        => 'Very good ramen, strong taste, good price, not very confortable (narrow counter, crappy pop music...)',
          'google_maps_link'  => 'https://www.google.co.jp/maps/place/Hakata+Furyu/@35.6585187,139.6983918,21z/data=!4m5!3m4!1s0x60188b5629325fa5:0x59eb6bd6fac9b856!8m2!3d35.6585287!4d139.6984651',
          'official_website'  => 'https://fu-ryu.net/',
          'score'             => 3
        ));
        $store->attachTags(['ramen','noodles','miso']);
        $store->fillCoordinatesFromString('35.658545, 139.698468');

    }
}
