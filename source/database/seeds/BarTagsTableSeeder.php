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
        /*             COUNTRIES                  */
        /******************************************/
        BarTag::create(array(
            'label'              => 'standing',
            'type'               => '100',
            'weight'             => '100'
        ));
    }
}
