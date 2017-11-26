<?php

use Illuminate\Database\Seeder;
use App\User;

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
        
        User::create(array(
            'name'     => 'Florent',
            'email'    => 'florent.redares@gmail.com',
            'password' => Hash::make('password'),
        ));
        
        User::create(array(
            'name'     => 'User2',
            'email'    => 'user2@test.rt',
            'password' => Hash::make('password'),
        ));
    }
}
