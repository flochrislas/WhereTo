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

        RestaurantTag::create(array(
            'label'              => 'italian'
        ));

        RestaurantTag::create(array(
            'label'              => 'french'
        ));

        RestaurantTag::create(array(
            'label'              => 'mexican'
        ));

        RestaurantTag::create(array(
            'label'              => 'indian'
        ));

        RestaurantTag::create(array(
            'label'              => 'vietnamese'
        ));

        RestaurantTag::create(array(
            'label'              => 'turk'
        ));

        RestaurantTag::create(array(
            'label'              => 'indonesian'
        ));

        RestaurantTag::create(array(
            'label'              => 'american'
        ));

        RestaurantTag::create(array(
            'label'              => 'japanese'
        ));

        RestaurantTag::create(array(
            'label'              => 'western'
        ));

        RestaurantTag::create(array(
            'label'              => 'thai'
        ));        

        RestaurantTag::create(array(
            'label'              => 'izakaya'
        ));

        RestaurantTag::create(array(
            'label'              => 'fast food'
        ));

        RestaurantTag::create(array(
            'label'              => 'all you can eat'
        ));

        RestaurantTag::create(array(
            'label'              => 'all you can drink'
        ));

        RestaurantTag::create(array(
            'label'              => 'authentic'
        ));

        RestaurantTag::create(array(
            'label'              => 'vegetarian friendly'
        ));

        RestaurantTag::create(array(
            'label'              => 'vegan friendly'
        ));

        RestaurantTag::create(array(
            'label'              => 'menu with calories'
        ));

        RestaurantTag::create(array(
            'label'              => 'menu with allergies'
        ));

        RestaurantTag::create(array(
            'label'              => 'customisable dishes'
        ));

        RestaurantTag::create(array(
            'label'              => 'japanese curry'
        ));

        RestaurantTag::create(array(
            'label'              => 'ramen'
        ));

        RestaurantTag::create(array(
            'label'              => 'tonkotsu'
        ));

        RestaurantTag::create(array(
            'label'              => 'miso'
        ));

        RestaurantTag::create(array(
            'label'              => 'sukiyaki'
        ));

        RestaurantTag::create(array(
            'label'              => 'shabushabu'
        ));

        RestaurantTag::create(array(
            'label'              => 'burger'
        ));

        RestaurantTag::create(array(
            'label'              => 'pizza'
        ));

        RestaurantTag::create(array(
            'label'              => 'pasta'
        ));

        RestaurantTag::create(array(
            'label'              => 'curry'
        ));

        RestaurantTag::create(array(
            'label'              => 'sushi'
        ));

        RestaurantTag::create(array(
            'label'              => 'gapao'
        ));

        RestaurantTag::create(array(
            'label'              => 'omurice'
        ));

        RestaurantTag::create(array(
            'label'              => 'tenpura'
        ));

        RestaurantTag::create(array(
            'label'              => 'soba'
        ));

        RestaurantTag::create(array(
            'label'              => 'yakiniku'
        ));

        RestaurantTag::create(array(
            'label'              => 'okonomiyaki'
        ));

        RestaurantTag::create(array(
            'label'              => 'hamburg'
        ));

        RestaurantTag::create(array(
            'label'              => 'grill'
        ));

        RestaurantTag::create(array(
            'label'              => 'karaage'
        ));

        RestaurantTag::create(array(
            'label'              => 'green curry'
        ));

        RestaurantTag::create(array(
            'label'              => 'crepe'
        ));

        RestaurantTag::create(array(
            'label'              => 'galette'
        ));

        RestaurantTag::create(array(
            'label'              => 'quiche'
        ));

        RestaurantTag::create(array(
            'label'              => 'croque-monsieur'
        ));

    }
}
