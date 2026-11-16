<?php

namespace Database\Seeders;

use App\Models\RestaurantType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        RestaurantType::create([
            'label' => 'ramen',
        ]);
    }
}
