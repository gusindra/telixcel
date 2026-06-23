<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Models\Company;
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
        $admin = User::updateOrCreate(['email' => 'admin@telixcel.com'], [
            'name' => 'Admin Telixcel',
            'password' => Hash::make('12345678'),
        ]);

        $inHouseTeam = Team::updateOrCreate(['slug' => 'telixcel'], [
            'name' => 'Telixcel',
            'user_id' => $admin->id,
            'personal_team' => false,
        ]);

        $admin->forceFill(['current_team_id' => $inHouseTeam->id])->save();

        TeamUser::updateOrCreate(
            ['team_id' => $inHouseTeam->id, 'user_id' => $admin->id],
            ['role' => 'superadmin', 'status' => null]
        );

        $projectUser = User::updateOrCreate(['email' => 'user@telixcel.com'], [
            'name' => 'User Telixcel',
            'password' => Hash::make('12345678'),
        ]);

        $projectTeam = Team::updateOrCreate(['slug' => 'project-manager'], [
            'name' => 'Project Manager Team',
            'user_id' => $projectUser->id,
            'personal_team' => false,
        ]);

        $projectUser->forceFill(['current_team_id' => $projectTeam->id])->save();

        TeamUser::updateOrCreate(
            ['team_id' => $projectTeam->id, 'user_id' => $projectUser->id],
            ['role' => 'admin', 'status' => null]
        );

        Company::updateOrCreate(['code' => 'TLX'], [
            'name' => 'Telixcel Project',
            'tax_id' => '-',
            'post_code' => '00000',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta',
            'address' => 'Jakarta',
            'logo' => '',
            'person_in_charge' => 'Admin Telixcel',
            'user_id' => 0,
        ]);

        User::updateOrCreate(['email' => 'user1@telixcel.com'], [
            'name' => 'User Telixcel',
            'password' => Hash::make('12345678'),
            'current_team_id' => null
        ]);

        User::updateOrCreate(['email' => 'user4@telixcel.com'], [
            'name' => 'User 4',
            'password' => Hash::make('12345678'),
            'current_team_id' => $projectTeam->id
        ]);

        User::updateOrCreate(['email' => 'usera@telixcel.com'], [
            'name' => 'User A',
            'password' => Hash::make('12345678'),
            'current_team_id' => $projectTeam->id
        ]);

        User::updateOrCreate(['email' => 'userb@telixcel.com'], [
            'name' => 'User B',
            'password' => Hash::make('12345678'),
            'current_team_id' => $projectTeam->id
        ]);

        User::updateOrCreate(['email' => 'userc@telixcel.com'], [
            'name' => 'User C',
            'password' => Hash::make('12345678'),
            'current_team_id' => $projectTeam->id
        ]);

        // Team Admin — Telixcel team
        $teamAdmin = User::updateOrCreate(['email' => 'teamadmin@telixcel.com'], [
            'name' => 'Team Admin',
            'password' => Hash::make('12345678'),
            'current_team_id' => $inHouseTeam->id,
        ]);

        TeamUser::updateOrCreate(
            ['team_id' => $inHouseTeam->id, 'user_id' => $teamAdmin->id],
            ['role' => 'admin', 'status' => null]
        );

    }
}
