<?php

namespace Database\Seeders;
use App\Models\RoleUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RoleUser::create([
            'user_id' => 1,
            'role_id' => 1,
            'team_id' => 1,
            'status' => 'active',
            'active' => 1,
            'working_id' => 'ABC123',
        ]);
    }
}
