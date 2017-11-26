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
     * Run the database seeds.
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
        
        Restaurant::create(array(
            'name'     			=> 'Ramen 1 (yellow sign)',
            'location'   		=> 'Shibuya - Ramen Street',
            'coord'   		    => '9876546542x6987545y',
            'type'    	        => 'ramen',
            'lunch_price'       => 800,
            'points'            => 'Tonkostsu miso available, up to 2 free kaidama (noodles refill), choice soft/hard for noodles',
            'experience'   	 	=> 'Very good ramen, strong taste, good price, not very confortable (narrow counter, crappy pop music...)',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

    }
}
