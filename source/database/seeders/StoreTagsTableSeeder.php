<?php

namespace Database\Seeders;

use App\Models\StoreTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear
        DB::table('store_tags')->delete();

        // Fill

        /******************************************/
        /*               goods */
        /******************************************/
        StoreTag::create([
            'label' => 'electronics',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'clothes',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'fashion',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'sports',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'men',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'women',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'shoes',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'sneakers',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'CD/DVD',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'video games',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'toys',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'music instruments',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'music gear',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'guitars',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'outdoor',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'food',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'drinks',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'gifts',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'luxury',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'mall',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'airsoft',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => '100yen',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'housewares',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'snacks',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'phones',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'computers',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'headphones',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'cameras',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'jewelry',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'watches',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'glasses',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'music',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'records',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'vinyl',
            'type' => '80',
            'weight' => '300',
        ]);

        StoreTag::create([
            'label' => 'stationery',
            'type' => '80',
            'weight' => '300',
        ]);

        // check meaning of mall vs dept store .

    }
}
