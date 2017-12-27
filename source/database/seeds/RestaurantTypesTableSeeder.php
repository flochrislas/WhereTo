<?php

use Illuminate\Database\Seeder;

use App\RestaurantType;

class RestaurantTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('restaurant_types')->delete();

        RestaurantType::create(array(
            'label'              => 'ramen'
        ));
    }
}
