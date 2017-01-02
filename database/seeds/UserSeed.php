<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	$user = new User();
       	$user->name = 'Mauricio Ashimine';
       	$user->email = 'admin@admin.com';
       	$user->password = bcrypt('secret');
       	$user->save();
    }
}
