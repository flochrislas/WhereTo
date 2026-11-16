<?php

namespace Database\Seeders;

use App\Models\KaraokeTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        /*             SYSTEMS */
        /******************************************/
        KaraokeTag::create([
            'label' => 'DAM',
            'type' => '200',
            'weight' => '200',
        ]);
        // System: JOYSOUND , but that is also the name of a place
        KaraokeTag::create([
            'label' => 'JOYSOUND',
            'type' => '200',
            'weight' => '200',
        ]);

        /******************************************/
        /*             TYPE */
        /******************************************/
        KaraokeTag::create([
            'label' => 'private rooms',
            'type' => '100',
            'weight' => '100',
        ]);

        KaraokeTag::create([
            'label' => 'public bar',
            'type' => '100',
            'weight' => '100',
        ]);

        /******************************************/
        /*             FRANCHISE */
        /******************************************/
        KaraokeTag::create([
            'label' => 'karaokekan',
            'type' => '50',
            'weight' => '50',
        ]);

        KaraokeTag::create([
            'label' => 'big echo',
            'type' => '50',
            'weight' => '50',
        ]);

        KaraokeTag::create([
            'label' => 'adores',
            'type' => '50',
            'weight' => '50',
        ]);

        KaraokeTag::create([
            'label' => 'joysound',
            'type' => '50',
            'weight' => '50',
        ]);

        KaraokeTag::create([
            'label' => 'shidax',
            'type' => '50',
            'weight' => '50',
        ]);

        KaraokeTag::create([
            'label' => 'round 1',
            'type' => '50',
            'weight' => '50',
        ]);

        KaraokeTag::create([
            'label' => 'karaoke no tetsujin',
            'type' => '50',
            'weight' => '50',
        ]);

        KaraokeTag::create([
            'label' => 'king of system',
            'type' => '50',
            'weight' => '50',
        ]);

        KaraokeTag::create([
            'label' => 'uta hiroba',
            'type' => '50',
            'weight' => '50',
        ]);

    }
}
