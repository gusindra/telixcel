<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
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
        $admin = User::where('email', 'admin@telixcel.com')->first();
        $superAdmin = Role::where('name', 'Super Admin')->first();

        if ($admin && $superAdmin && $admin->current_team_id) {
            RoleUser::updateOrCreate([
                'user_id' => $admin->id,
                'role_id' => $superAdmin->id,
                'team_id' => $admin->current_team_id,
            ], [
                'status' => 'active',
                'active' => 1,
                'working_id' => 'ABC123',
            ]);
        }

        $projectUser = User::where('email', 'user@telixcel.com')->first();
        $projectManager = Role::where('name', 'Project Manager')->first();

        if ($projectUser && $projectManager && $projectUser->current_team_id) {
            RoleUser::updateOrCreate([
                'user_id' => $projectUser->id,
                'role_id' => $projectManager->id,
                'team_id' => $projectUser->current_team_id,
            ], [
                'status' => 'active',
                'active' => 1,
                'working_id' => 'PM123',
            ]);
        }

        $teamAdminUser = User::where('email', 'teamadmin@telixcel.com')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        if ($teamAdminUser && $adminRole && $teamAdminUser->current_team_id) {
            RoleUser::updateOrCreate([
                'user_id' => $teamAdminUser->id,
                'role_id' => $adminRole->id,
                'team_id' => $teamAdminUser->current_team_id,
            ], [
                'status' => 'active',
                'active' => 1,
                'working_id' => 'ADM456',
            ]);
        }
    }
}
