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
        // Clear
        DB::table('restaurant_tags')->delete();

        /******************************************/
        /*             COUNTRIES                  */
        /******************************************/
        RestaurantTag::create(array(
            'label'              => 'italian',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'french',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'mexican',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'indian',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'vietnamese',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'turk',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'indonesian',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'american',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'japanese',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'chinese',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'western',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'thai',
            'type'               => '100',
            'weight'             => '100'
        ));
        
        /******************************************/
        /*             TYPE                       */
        /******************************************/
        RestaurantTag::create(array(
            'label'              => 'izakaya',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'fast food',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'cafe',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'teishoku',
            'type'               => '100',
            'weight'             => '100'
        ));

        /******************************************/
        /*             FEATURES                   */
        /******************************************/        
        RestaurantTag::create(array(
            'label'              => 'all you can eat',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'all you can drink',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'authentic',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'vegetarian friendly',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'vegan friendly',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'menu with calories',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'menu with allergies',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'customisable dishes',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'buffet',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'smoking allowed',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'no smoking',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'smoking area',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'open air space',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'visa',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'filling',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'small servings',
            'type'               => '100',
            'weight'             => '100'
        ));

        /******************************************/
        /*             DISHES                     */
        /******************************************/ 
        RestaurantTag::create(array(
            'label'              => 'japanese curry',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'ramen',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'tonkotsu',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'miso',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'sukiyaki',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'shabushabu',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'burger',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'pizza',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'pasta',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'curry',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'sushi',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'gapao',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'omurice',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'tenpura',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'soba',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'yakiniku',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'okonomiyaki',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'hamburg',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'grill',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'karaage',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'green curry',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'crepe',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'galette',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'quiche',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'croque-monsieur',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'sandwich',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'noodles',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'gyoza',
            'type'               => '100',
            'weight'             => '100'
        ));


        RestaurantTag::create(array(
            'label'              => 'dumpling',
            'type'               => '100',
            'weight'             => '100'
        ));

    }
}
