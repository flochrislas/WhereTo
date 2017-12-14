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
            'label'              => 'vegetarian friendly'
        ));

        RestaurantTags::create(array(
            'label'              => 'vegan friendly'
        ));

        RestaurantTags::create(array(
            'label'              => 'menu with calories'
        ));

        RestaurantTags::create(array(
            'label'              => 'menu with allergies'
        ));

        RestaurantTags::create(array(
            'label'              => 'customisable dishes'
        ));

        RestaurantTags::create(array(
            'label'              => 'italian'
        ));

        RestaurantTags::create(array(
            'label'              => 'french'
        ));

        RestaurantTags::create(array(
            'label'              => 'mexican'
        ));

        RestaurantTags::create(array(
            'label'              => 'indian'
        ));

        RestaurantTags::create(array(
            'label'              => 'vietnamese'
        ));

        RestaurantTags::create(array(
            'label'              => 'turk'
        ));

        RestaurantTags::create(array(
            'label'              => 'indonesian'
        ));

        RestaurantTags::create(array(
            'label'              => 'american'
        ));

        RestaurantTags::create(array(
            'label'              => 'japanese'
        ));

        RestaurantTags::create(array(
            'label'              => 'thai'
        ));

        RestaurantTags::create(array(
            'label'              => 'japanese curry'
        ));

        RestaurantTags::create(array(
            'label'              => 'sushi'
        ));

        RestaurantTags::create(array(
            'label'              => 'izakaya'
        ));

        RestaurantTags::create(array(
            'label'              => 'all you can eat'
        ));

        RestaurantTags::create(array(
            'label'              => 'all you can drink'
        ));

        RestaurantTags::create(array(
            'label'              => 'ramen'
        ));

        RestaurantTags::create(array(
            'label'              => 'tonkotsu'
        ));

        RestaurantTags::create(array(
            'label'              => 'miso'
        ));

        RestaurantTags::create(array(
            'label'              => 'sukiyaki'
        ));

        RestaurantTags::create(array(
            'label'              => 'shabushabu'
        ));

        RestaurantTags::create(array(
            'label'              => 'burger'
        ));

    }
}
