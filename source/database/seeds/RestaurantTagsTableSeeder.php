<?php

use Illuminate\Database\Seeder;

use App\RestaurantTag;

/**
* Type column is mostly for color in the UI, showing tags about countries or features in different colors.
* The higher the number, the most important that category/type might be for the users (can be used in ORDER BY)
* The weight  column is to be able to give more importance to some tags (can be used in ORDER BY)
*/
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

        // Fill

        /******************************************/
        /*             COUNTRIES                  */
        /******************************************/
        RestaurantTag::create(array(
            'label'              => 'japanese',
            'type'               => '80',
            'weight'             => '300'
        ));

        RestaurantTag::create(array(
            'label'              => 'western',
            'type'               => '80',
            'weight'             => '300'
        ));

        RestaurantTag::create(array(
            'label'              => 'italian',
            'type'               => '80',
            'weight'             => '300'
        ));

        RestaurantTag::create(array(
            'label'              => 'indian',
            'type'               => '80',
            'weight'             => '300'
        ));

        RestaurantTag::create(array(
            'label'              => 'thai',
            'type'               => '80',
            'weight'             => '300'
        ));

        RestaurantTag::create(array(
            'label'              => 'chinese',
            'type'               => '80',
            'weight'             => '300'
        ));   

        RestaurantTag::create(array(
            'label'              => 'french',
            'type'               => '80',
            'weight'             => '100'
        ));        

        RestaurantTag::create(array(
            'label'              => 'american',
            'type'               => '80',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'turk',
            'type'               => '80',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'mexican',
            'type'               => '80',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'spanish',
            'type'               => '80',
            'weight'             => '100'
        ));
        RestaurantTag::create(array(
            'label'              => 'vietnamese',
            'type'               => '80',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'indonesian',
            'type'               => '80',
            'weight'             => '100'
        ));
        
        /******************************************/
        /*             TYPE                       */
        /******************************************/
        RestaurantTag::create(array(
            'label'              => 'fast food',
            'type'               => '40',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'cafe',
            'type'               => '40',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'teishoku',
            'type'               => '40',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'izakaya',
            'type'               => '40',
            'weight'             => '100'
        ));

        /******************************************/
        /*             FEATURES                   */
        /******************************************/        
        RestaurantTag::create(array(
            'label'              => 'filling',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'small servings',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'visa',
            'type'               => '60',
            'weight'             => '100'
        ));
        RestaurantTag::create(array(
            'label'              => 'all you can eat',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'all you can drink',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'authentic',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'vegetarian friendly',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'vegan friendly',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'menu with calories',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'menu with allergies',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'customisable dishes',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'buffet',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'smoking allowed',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'no smoking',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'smoking area',
            'type'               => '60',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'open air space',
            'type'               => '60',
            'weight'             => '100'
        ));

        /******************************************/
        /*             DISHES                     */
        /******************************************/ 
        
        RestaurantTag::create(array(
            'label'              => 'ramen',
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
            'label'              => 'japanese curry',
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
            'label'              => 'hamburg',
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
            'label'              => 'gyoza',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'dumpling',
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
            'label'              => 'tsukemen',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'tantanmen',
            'type'               => '100',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'wantanmen',
            'type'               => '100',
            'weight'             => '100'
        ));

    }
}
