<?php

use Illuminate\Database\Seeder;

use App\RestaurantTag;

class RestaurantTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('restaurant_tags')->delete();

        RestaurantTags::create(array(
            'label'              => 'ramen'
        ));
    }
}
