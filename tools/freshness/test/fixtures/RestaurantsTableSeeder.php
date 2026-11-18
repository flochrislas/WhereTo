<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Test fixture for the freshness tool: same shape as the real RestaurantsTableSeeder.
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
            'name'              => Str::random(10),
        ]);
        */

        $resto = Restaurant::create([
            'name' => 'Hakata Furyu',
            'location' => 'Shibuya',
            'type' => 'Ramen',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/0',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.658545, 139.698468');

        $resto = Restaurant::create([
            'name' => 'Kindenmaru',
            'business_status' => 'CLOSED_PERMANENTLY',
            'google_place_id' => 'ChIJclosedKindenmaru',
            'location' => 'Shibuya',
            'type' => 'Ramen',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/1',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.655166, 139.700365');

        $resto = Restaurant::create([
            'name' => 'Go Go Curry',
            'location' => 'Shibuya',
            'type' => 'Japanese curry',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/2',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.657333, 139.703654');

        /*
        // This place has been replaced by the FIX Lounge
        $resto = Restaurant::create(array(
            'name'              => 'M-SPO cafe dining',
            'score_date'        => 0
        ));
        $resto->attachTags(['pizza', 'pasta']);
        $resto->fillCoordinatesFromString('35.662026, 139.698027');
        */

        $resto = Restaurant::create([
            'name' => 'Mc Donald\'s',
            'location' => 'Shibuya',
            'type' => 'Burger',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/3',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.659000, 139.700000');

        $resto = Restaurant::create([
            'name' => 'Mc Donald\'s',
            'location' => 'Shibuya',
            'type' => 'Burger',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/4',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.661000, 139.698000');

        $resto = Restaurant::create([
            'name' => 'Il Ritrovo',
            'google_place_id' => 'ChIJknownRitrovo',
            'location' => 'Shibuya',
            'type' => 'Italian',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/5',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.660100, 139.697100');

        // TODO: complete this
        $resto = Restaurant::create([
            'name' => 'Bistro Shibuya',
            'location' => 'Shibuya',
            'type' => 'French',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/6',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.656000, 139.699000');

        $resto = Restaurant::create([
            'name' => 'Sushi Zanmai',
            'location' => 'Shibuya',
            'type' => 'Sushi',
            'lunch_price' => 900,
            'points' => 'Spans several lines, like a few real ones:
          <ul>
              <li>// not a comment, /* nor this</li>
          </ul>
          It\'s fine.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/7',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.659500, 139.699500');

        $resto = Restaurant::create([
            'name' => 'Pancho',
            'location' => 'Shibuya',
            'type' => 'Pasta',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.660500, 139.698500');

        $resto = Restaurant::create([
            'name' => 'Krung Siam',
            'location' => 'Shibuya',
            'type' => 'Thai',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/9',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.6578,139.7012,17');

        $resto = Restaurant::create([
            'name' => 'Tengu Sakaba',
            'business_status' => 'CLOSED_TEMPORARILY',
            'location' => 'Shibuya',
            'type' => 'Izakaya',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/10',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.658800, 139.697800');

        $resto = Restaurant::create([
            'name' => 'Usagi',
            'location' => 'Shibuya',
            'type' => 'Cafe',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/11',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.662000, 139.700500');

        $resto = Restaurant::create([
            'name' => 'Usagi',
            'location' => 'Shibuya',
            'type' => 'Cafe',
            'lunch_price' => 900,
            'points' => 'A line with \'quotes\', commas, and => arrows inside.',
            'visited' => true,
            'visit_date' => '2017-09-05 13:00:00',
            'google_maps_link' => 'https://www.google.com/maps/place/11',
            'tabelog_link' => null,
            'score_lunch' => 3,
            'score_food' => 2,
        ]);
        $resto->attachTags(['ramen', 'japanese']);
        $resto->fillCoordinatesFromString('35.662000, 139.700500');

        /*
        ///AAA///
        $resto = Restaurant::create(array(
          'name'              => 'xxxx',
        ));
        $resto->fillCoordinatesFromString('xxxx');
        */
    }
}
