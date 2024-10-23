<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = 'HuuMinh';
        $user->user_name = 'nhminh1';
        $user->email = 'nhminh1@gmail.com';
        $user->password = Hash::make('123456'); 
        $user->save();
    }
}
