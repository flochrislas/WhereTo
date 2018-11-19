<?php

use Illuminate\Database\Seeder;

use App\StoreTag;

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
        /*               goods                    */
        /******************************************/
        StoreTag::create(array(
            'label'              => 'electronics',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'clothes',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'fashion',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'sports',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'men',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'women',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'shoes',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'sneakers',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'CD/DVD',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'video games',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'music instruments',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'guitars',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'food',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'drinks',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'gifts',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'luxury',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'mall',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'airsoft',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => '100yen',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'housewares',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'snacks',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'phones',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'computers',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'headphones',
            'type'               => '80',
            'weight'             => '300'
        ));

        StoreTag::create(array(
            'label'              => 'cameras',
            'type'               => '80',
            'weight'             => '300'
        ));

        // check meaning of mall vs dept store

      }
}
