<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('Admin@4321'),
                'user_type' => 'admin',
                // LoginController only authenticates users with status = 1.
                'status' => 1,
                'user_id' => null
            ]
        );

        User::updateOrCreate(
            ['email' => 'bellway@gmail.com'],
            [
                'name' => 'User',
                'email' => 'bellway@gmail.com',
                'password' => Hash::make('Bellway@4321'),
                'user_type' => 'user',
                'status' => 1,
                'user_id' => null
            ]
        );
    }

}
