<?php

use Illuminate\Database\Seeder;

class RestaurantTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('restaurantTypes')->delete();

        Restaurant::create(array(
            'label'              => 'ramen'
        ));
    }
}
