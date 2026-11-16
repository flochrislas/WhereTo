<?php

namespace Database\Seeders;

use App\Models\BarTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear
        DB::table('bar_tags')->delete();

        // Fill

        /******************************************/
        /*             XXXXXXXXX */
        /******************************************/
        BarTag::create([
            'label' => 'standing',
            'type' => '100',
            'weight' => '100',
        ]);

        BarTag::create([
            'label' => 'pub',
            'type' => '100',
            'weight' => '100',
        ]);

        BarTag::create([
            'label' => 'izakaya',
            'type' => '100',
            'weight' => '100',
        ]);

        BarTag::create([
            'label' => 'no table charge',
            'type' => '100',
            'weight' => '100',
        ]);

        BarTag::create([
            'label' => 'non smoking area',
            'type' => '100',
            'weight' => '100',
        ]);

        BarTag::create([
            'label' => 'no smoking',
            'type' => '100',
            'weight' => '100',
        ]);

        BarTag::create([
            'label' => 'craft beers',
            'type' => '100',
            'weight' => '100',
        ]);

        BarTag::create([
            'label' => 'shisha',
            'type' => '100',
            'weight' => '100',
        ]);

        BarTag::create([
            'label' => 'music',
            'type' => '100',
            'weight' => '100',
        ]);
        BarTag::create([
            'label' => 'rock',
            'type' => '100',
            'weight' => '100',
        ]);
        BarTag::create([
            'label' => 'jazz',
            'type' => '100',
            'weight' => '100',
        ]);
        BarTag::create([
            'label' => 'wine',
            'type' => '100',
            'weight' => '100',
        ]);
        BarTag::create([
            'label' => 'beer',
            'type' => '100',
            'weight' => '100',
        ]);

    }
}
