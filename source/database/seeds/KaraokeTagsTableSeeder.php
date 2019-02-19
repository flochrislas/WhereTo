<?php

use Illuminate\Database\Seeder;
use App\KaraokeTag;

class KaraokeTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear
        DB::table('karaoke_tags')->delete();

        // Fill

        /******************************************/
        /*             SYSTEMS                    */
        /******************************************/
        KaraokeTag::create(array(
            'label'              => 'DAM',
            'type'               => '200',
            'weight'             => '200'
        ));
        // System: JOYSOUND , but that is also the name of a place
        KaraokeTag::create(array(
            'label'              => 'JOYSOUND',
            'type'               => '200',
            'weight'             => '200'
        ));

        /******************************************/
        /*             TYPE                       */
        /******************************************/
        KaraokeTag::create(array(
            'label'              => 'private rooms',
            'type'               => '100',
            'weight'             => '100'
        ));

        KaraokeTag::create(array(
            'label'              => 'public bar',
            'type'               => '100',
            'weight'             => '100'
        ));

        /******************************************/
        /*             FRANCHISE                  */
        /******************************************/
        KaraokeTag::create(array(
            'label'              => 'karaokekan',
            'type'               => '50',
            'weight'             => '50'
        ));

        KaraokeTag::create(array(
            'label'              => 'big echo',
            'type'               => '50',
            'weight'             => '50'
        ));

        KaraokeTag::create(array(
            'label'              => 'adores',
            'type'               => '50',
            'weight'             => '50'
        ));

        KaraokeTag::create(array(
            'label'              => 'joysound',
            'type'               => '50',
            'weight'             => '50'
        ));

        KaraokeTag::create(array(
            'label'              => 'shidax',
            'type'               => '50',
            'weight'             => '50'
        ));

        KaraokeTag::create(array(
            'label'              => 'round 1',
            'type'               => '50',
            'weight'             => '50'
        ));

        KaraokeTag::create(array(
            'label'              => 'karaoke no tetsujin',
            'type'               => '50',
            'weight'             => '50'
        ));

        KaraokeTag::create(array(
            'label'              => 'king of system',
            'type'               => '50',
            'weight'             => '50'
        ));

        KaraokeTag::create(array(
            'label'              => 'uta hiroba',
            'type'               => '50',
            'weight'             => '50'
        ));


    }
}
