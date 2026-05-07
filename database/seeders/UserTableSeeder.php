<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin Telixcel',
            'email' => 'admin@telixcel.com',
            'password' => Hash::make('12345678'),
            'current_team_id' => 1
        ]);

        User::create([
            'name' => 'User Telixcel',
            'email' => 'user@telixcel.com',
            'password' => Hash::make('12345678'),
            'current_team_id' => 2
        ]);

        User::create([
            'name' => 'User Telixcel',
            'email' => 'user1@telixcel.com',
            'password' => Hash::make('12345678'),
            'current_team_id' => 0
        ]);

        User::create([
            'name' => 'User 4',
            'email' => 'user4@telixcel.com',
            'password' => Hash::make('12345678'),
            'current_team_id' => 2
        ]);

        User::create([
            'name' => 'User A',
            'email' => 'usera@telixcel.com',
            'password' => Hash::make('12345678'),
            'current_team_id' => 2
        ]);

        User::create([
            'name' => 'User B',
            'email' => 'userb@telixcel.com',
            'password' => Hash::make('12345678'),
            'current_team_id' => 2
        ]);

        User::create([
            'name' => 'User C',
            'email' => 'userc@telixcel.com',
            'password' => Hash::make('12345678'),
            'current_team_id' => 2
        ]);

    }
}
