<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        User::create([
            'name' => 'Florent',
            'email' => 'florent.redares@gmail.com',
            'level' => '1',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'User2',
            'email' => 'user2@test.rt',
            'level' => '1',
            'password' => Hash::make('password'),
        ]);
    }
}
