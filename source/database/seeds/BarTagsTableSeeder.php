<?php

use Illuminate\Database\Seeder;
use App\BarTag;

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
        /*             XXXXXXXXX                  */
        /******************************************/
        BarTag::create(array(
            'label'              => 'standing',
            'type'               => '100',
            'weight'             => '100'
        ));

        BarTag::create(array(
            'label'              => 'craft beers',
            'type'               => '100',
            'weight'             => '100'
        ));

        BarTag::create(array(
            'label'              => 'shisha',
            'type'               => '100',
            'weight'             => '100'
        ));

        BarTag::create(array(
            'label'              => 'no table charge',
            'type'               => '100',
            'weight'             => '100'
        ));

        BarTag::create(array(
            'label'              => 'non smoking area',
            'type'               => '100',
            'weight'             => '100'
        ));
    }
}
