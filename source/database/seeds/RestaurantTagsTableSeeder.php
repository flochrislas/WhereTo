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
            'weight'             => '250'
        ));

        RestaurantTag::create(array(
            'label'              => 'indian',
            'type'               => '80',
            'weight'             => '250'
        ));

        RestaurantTag::create(array(
            'label'              => 'thai',
            'type'               => '80',
            'weight'             => '250'
        ));

        RestaurantTag::create(array(
            'label'              => 'chinese',
            'type'               => '80',
            'weight'             => '250'
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
            'weight'             => '80'
        ));

        RestaurantTag::create(array(
            'label'              => 'mexican',
            'type'               => '80',
            'weight'             => '100'
        ));

        RestaurantTag::create(array(
            'label'              => 'spanish',
            'type'               => '80',
            'weight'             => '80'
        ));
        RestaurantTag::create(array(
            'label'              => 'vietnamese',
            'type'               => '80',
            'weight'             => '50'
        ));

        RestaurantTag::create(array(
            'label'              => 'indonesian',
            'type'               => '80',
            'weight'             => '50'
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
            'label'              => 'bar',
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

        RestaurantTag::create(array(
            'label'              => 'bakery',
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
            'label'              => 'to-go',
            'type'               => '60',
            'weight'             => '100'
        ));
        RestaurantTag::create(array(
            'label'              => 'all you can eat',
            'type'               => '60',
            'weight'             => '80'
        ));

        RestaurantTag::create(array(
            'label'              => 'all you can drink',
            'type'               => '60',
            'weight'             => '80'
        ));

        RestaurantTag::create(array(
            'label'              => 'authentic',
            'type'               => '60',
            'weight'             => '80'
        ));

        RestaurantTag::create(array(
            'label'              => 'vegetarian friendly',
            'type'               => '60',
            'weight'             => '80'
        ));

        RestaurantTag::create(array(
            'label'              => 'vegan friendly',
            'type'               => '60',
            'weight'             => '80'
        ));

        RestaurantTag::create(array(
            'label'              => 'menu with calories',
            'type'               => '60',
            'weight'             => '80'
        ));

        RestaurantTag::create(array(
            'label'              => 'menu with allergies',
            'type'               => '60',
            'weight'             => '80'
        ));

        RestaurantTag::create(array(
            'label'              => 'English menu',
            'type'               => '60',
            'weight'             => '90'
        ));

        RestaurantTag::create(array(
            'label'              => 'customisable dishes',
            'type'               => '60',
            'weight'             => '15'
        ));

        RestaurantTag::create(array(
            'label'              => 'buffet',
            'type'               => '60',
            'weight'             => '20'
        ));

        RestaurantTag::create(array(
            'label'              => 'smoking allowed',
            'type'               => '60',
            'weight'             => '50'
        ));

        RestaurantTag::create(array(
            'label'              => 'no smoking',
            'type'               => '60',
            'weight'             => '50'
        ));

        RestaurantTag::create(array(
            'label'              => 'smoking area',
            'type'               => '60',
            'weight'             => '50'
        ));

        RestaurantTag::create(array(
            'label'              => 'open air space',
            'type'               => '60',
            'weight'             => '50'
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
            'weight'             => '50'
        ));

        RestaurantTag::create(array(
            'label'              => 'omurice',
            'type'               => '100',
            'weight'             => '45'
        ));

        RestaurantTag::create(array(
            'label'              => 'tenpura',
            'type'               => '100',
            'weight'             => '45'
        ));

        RestaurantTag::create(array(
            'label'              => 'soba',
            'type'               => '100',
            'weight'             => '40'
        ));
        
        RestaurantTag::create(array(
            'label'              => 'noodles',
            'type'               => '100',
            'weight'             => '40'
        ));

        RestaurantTag::create(array(
            'label'              => 'okonomiyaki',
            'type'               => '100',
            'weight'             => '30'
        ));

        RestaurantTag::create(array(
            'label'              => 'hamburg',
            'type'               => '100',
            'weight'             => '30'
        ));

        RestaurantTag::create(array(
            'label'              => 'grill',
            'type'               => '100',
            'weight'             => '22'
        ));

        RestaurantTag::create(array(
            'label'              => 'karaage',
            'type'               => '100',
            'weight'             => '30'
        ));

        RestaurantTag::create(array(
            'label'              => 'green curry',
            'type'               => '100',
            'weight'             => '30'
        ));

        RestaurantTag::create(array(
            'label'              => 'crepe',
            'type'               => '100',
            'weight'             => '30'
        ));

        RestaurantTag::create(array(
            'label'              => 'galette',
            'type'               => '100',
            'weight'             => '30'
        ));

        RestaurantTag::create(array(
            'label'              => 'quiche',
            'type'               => '100',
            'weight'             => '30'
        ));

        RestaurantTag::create(array(
            'label'              => 'croque-monsieur',
            'type'               => '100',
            'weight'             => '20'
        ));

        RestaurantTag::create(array(
            'label'              => 'sandwich',
            'type'               => '100',
            'weight'             => '30'
        ));

        RestaurantTag::create(array(
            'label'              => 'gyoza',
            'type'               => '100',
            'weight'             => '20'
        ));

        RestaurantTag::create(array(
            'label'              => 'dumpling',
            'type'               => '100',
            'weight'             => '20'
        ));

        RestaurantTag::create(array(
            'label'              => 'katsu',
            'type'               => '100',
            'weight'             => '20'
        ));

        RestaurantTag::create(array(
            'label'              => 'yakitori',
            'type'               => '100',
            'weight'             => '11'
        ));

        RestaurantTag::create(array(
            'label'              => 'kushiage',
            'type'               => '100',
            'weight'             => '8'
        ));

        RestaurantTag::create(array(
            'label'              => 'yakiniku',
            'type'               => '100',
            'weight'             => '6'
        ));

        RestaurantTag::create(array(
            'label'              => 'sukiyaki',
            'type'               => '100',
            'weight'             => '5'
        ));

        RestaurantTag::create(array(
            'label'              => 'shabushabu',
            'type'               => '100',
            'weight'             => '5'
        ));

        RestaurantTag::create(array(
            'label'              => 'taco rice',
            'type'               => '100',
            'weight'             => '5'
        ));

        RestaurantTag::create(array(
            'label'              => 'teppanyaki',
            'type'               => '100',
            'weight'             => '5'
        ));

        RestaurantTag::create(array(
            'label'              => 'meat',
            'type'               => '100',
            'weight'             => '4'
        ));

        RestaurantTag::create(array(
            'label'              => 'desert',
            'type'               => '100',
            'weight'             => '4'
        ));

        RestaurantTag::create(array(
            'label'              => 'hotdog',
            'type'               => '100',
            'weight'             => '4'
        ));

        RestaurantTag::create(array(
            'label'              => 'falafel',
            'type'               => '100',
            'weight'             => '4'
        ));

        RestaurantTag::create(array(
            'label'              => 'tonkotsu',
            'type'               => '100',
            'weight'             => '0'
        ));

        RestaurantTag::create(array(
            'label'              => 'miso',
            'type'               => '100',
            'weight'             => '0'
        ));

        RestaurantTag::create(array(
            'label'              => 'organic',
            'type'               => '100',
            'weight'             => '0'
        ));

    }
}
