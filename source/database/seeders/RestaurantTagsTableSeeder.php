<?php

namespace Database\Seeders;

use App\Models\RestaurantTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        /*             COUNTRIES */
        /******************************************/
        RestaurantTag::create([
            'label' => 'japanese',
            'type' => '80',
            'weight' => '300',
        ]);

        RestaurantTag::create([
            'label' => 'western',
            'type' => '80',
            'weight' => '300',
        ]);

        RestaurantTag::create([
            'label' => 'italian',
            'type' => '80',
            'weight' => '250',
        ]);

        RestaurantTag::create([
            'label' => 'indian',
            'type' => '80',
            'weight' => '250',
        ]);

        RestaurantTag::create([
            'label' => 'thai',
            'type' => '80',
            'weight' => '250',
        ]);

        RestaurantTag::create([
            'label' => 'chinese',
            'type' => '80',
            'weight' => '250',
        ]);

        RestaurantTag::create([
            'label' => 'french',
            'type' => '80',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'american',
            'type' => '80',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'turk',
            'type' => '80',
            'weight' => '80',
        ]);

        RestaurantTag::create([
            'label' => 'mexican',
            'type' => '80',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'spanish',
            'type' => '80',
            'weight' => '80',
        ]);
        RestaurantTag::create([
            'label' => 'vietnamese',
            'type' => '80',
            'weight' => '50',
        ]);

        RestaurantTag::create([
            'label' => 'indonesian',
            'type' => '80',
            'weight' => '50',
        ]);

        RestaurantTag::create([
            'label' => 'african',
            'type' => '80',
            'weight' => '50',
        ]);

        /******************************************/
        /*             TYPE */
        /******************************************/
        RestaurantTag::create([
            'label' => 'fast food',
            'type' => '40',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'cafe',
            'type' => '40',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'bar',
            'type' => '40',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'teishoku',
            'type' => '40',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'izakaya',
            'type' => '40',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'bakery',
            'type' => '40',
            'weight' => '100',
        ]);

        /******************************************/
        /*             FEATURES */
        /******************************************/
        RestaurantTag::create([
            'label' => 'filling',
            'type' => '60',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'small servings',
            'type' => '60',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'visa',
            'type' => '60',
            'weight' => '100',
        ]);
        RestaurantTag::create([
            'label' => 'to-go',
            'type' => '60',
            'weight' => '100',
        ]);
        RestaurantTag::create([
            'label' => 'all you can eat',
            'type' => '60',
            'weight' => '80',
        ]);

        RestaurantTag::create([
            'label' => 'all you can drink',
            'type' => '60',
            'weight' => '80',
        ]);

        RestaurantTag::create([
            'label' => 'authentic',
            'type' => '60',
            'weight' => '80',
        ]);

        RestaurantTag::create([
            'label' => 'vegetarian friendly',
            'type' => '60',
            'weight' => '80',
        ]);

        RestaurantTag::create([
            'label' => 'menu with calories',
            'type' => '60',
            'weight' => '80',
        ]);

        RestaurantTag::create([
            'label' => 'menu with allergies',
            'type' => '60',
            'weight' => '80',
        ]);

        RestaurantTag::create([
            'label' => 'English menu',
            'type' => '60',
            'weight' => '90',
        ]);

        RestaurantTag::create([
            'label' => 'customisable dishes',
            'type' => '60',
            'weight' => '15',
        ]);

        RestaurantTag::create([
            'label' => 'buffet',
            'type' => '60',
            'weight' => '20',
        ]);

        RestaurantTag::create([
            'label' => 'smoking allowed',
            'type' => '60',
            'weight' => '50',
        ]);

        RestaurantTag::create([
            'label' => 'no smoking',
            'type' => '60',
            'weight' => '50',
        ]);

        RestaurantTag::create([
            'label' => 'smoking area',
            'type' => '60',
            'weight' => '50',
        ]);

        RestaurantTag::create([
            'label' => 'open air space',
            'type' => '60',
            'weight' => '50',
        ]);

        /******************************************/
        /*             DISHES */
        /******************************************/
        RestaurantTag::create([
            'label' => 'japanese curry',
            'type' => '100',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'ramen',
            'type' => '100',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'burger',
            'type' => '100',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'pizza',
            'type' => '100',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'pasta',
            'type' => '100',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'curry',
            'type' => '100',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'sushi',
            'type' => '100',
            'weight' => '100',
        ]);

        RestaurantTag::create([
            'label' => 'gapao',
            'type' => '100',
            'weight' => '50',
        ]);

        RestaurantTag::create([
            'label' => 'omurice',
            'type' => '100',
            'weight' => '45',
        ]);

        RestaurantTag::create([
            'label' => 'tenpura',
            'type' => '100',
            'weight' => '45',
        ]);

        RestaurantTag::create([
            'label' => 'soba',
            'type' => '100',
            'weight' => '40',
        ]);

        RestaurantTag::create([
            'label' => 'noodles',
            'type' => '100',
            'weight' => '40',
        ]);

        RestaurantTag::create([
            'label' => 'okonomiyaki',
            'type' => '100',
            'weight' => '30',
        ]);

        RestaurantTag::create([
            'label' => 'hamburg',
            'type' => '100',
            'weight' => '30',
        ]);

        RestaurantTag::create([
            'label' => 'grill',
            'type' => '100',
            'weight' => '22',
        ]);

        RestaurantTag::create([
            'label' => 'karaage',
            'type' => '100',
            'weight' => '30',
        ]);

        RestaurantTag::create([
            'label' => 'green curry',
            'type' => '100',
            'weight' => '30',
        ]);

        RestaurantTag::create([
            'label' => 'crepe',
            'type' => '100',
            'weight' => '30',
        ]);

        RestaurantTag::create([
            'label' => 'galette',
            'type' => '100',
            'weight' => '30',
        ]);

        RestaurantTag::create([
            'label' => 'quiche',
            'type' => '100',
            'weight' => '30',
        ]);

        RestaurantTag::create([
            'label' => 'croque-monsieur',
            'type' => '100',
            'weight' => '20',
        ]);

        RestaurantTag::create([
            'label' => 'sandwich',
            'type' => '100',
            'weight' => '30',
        ]);

        RestaurantTag::create([
            'label' => 'gyoza',
            'type' => '100',
            'weight' => '20',
        ]);

        RestaurantTag::create([
            'label' => 'dumpling',
            'type' => '100',
            'weight' => '20',
        ]);

        RestaurantTag::create([
            'label' => 'katsu',
            'type' => '100',
            'weight' => '20',
        ]);

        RestaurantTag::create([
            'label' => 'yakitori',
            'type' => '100',
            'weight' => '11',
        ]);

        RestaurantTag::create([
            'label' => 'kushiage',
            'type' => '100',
            'weight' => '8',
        ]);

        RestaurantTag::create([
            'label' => 'yakiniku',
            'type' => '100',
            'weight' => '6',
        ]);

        RestaurantTag::create([
            'label' => 'sukiyaki',
            'type' => '100',
            'weight' => '5',
        ]);

        RestaurantTag::create([
            'label' => 'shabushabu',
            'type' => '100',
            'weight' => '5',
        ]);

        RestaurantTag::create([
            'label' => 'taco rice',
            'type' => '100',
            'weight' => '5',
        ]);

        RestaurantTag::create([
            'label' => 'teppanyaki',
            'type' => '100',
            'weight' => '5',
        ]);

        RestaurantTag::create([
            'label' => 'meat',
            'type' => '100',
            'weight' => '4',
        ]);

        RestaurantTag::create([
            'label' => 'desert',
            'type' => '100',
            'weight' => '4',
        ]);

        RestaurantTag::create([
            'label' => 'hotdog',
            'type' => '100',
            'weight' => '4',
        ]);

        RestaurantTag::create([
            'label' => 'falafel',
            'type' => '100',
            'weight' => '4',
        ]);

        RestaurantTag::create([
            'label' => 'kebab',
            'type' => '100',
            'weight' => '4',
        ]);

        RestaurantTag::create([
            'label' => 'burrito',
            'type' => '100',
            'weight' => '4',
        ]);

        RestaurantTag::create([
            'label' => 'tonkotsu',
            'type' => '100',
            'weight' => '0',
        ]);

        RestaurantTag::create([
            'label' => 'miso',
            'type' => '100',
            'weight' => '0',
        ]);

        RestaurantTag::create([
            'label' => 'organic',
            'type' => '100',
            'weight' => '0',
        ]);

    }
}
