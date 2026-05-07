<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            Permission::query()->each(function ($permission) use ($superAdmin) {
                PermissionRole::updateOrCreate([
                    'role_id' => $superAdmin->id,
                    'permission_id' => $permission->id,
                ]);
            });
        }

        $projectManager = Role::where('name', 'Project Manager')->first();
        if ($projectManager) {
            Permission::whereIn('model', [
                'PROJECT',
                'PRODUCT',
                'QUOTATION',
                'CONTRACT',
                'ORDER',
                'COMMISSION',
                'BILLING',
            ])->each(function ($permission) use ($projectManager) {
                PermissionRole::updateOrCreate([
                    'role_id' => $projectManager->id,
                    'permission_id' => $permission->id,
                ]);
            });
        }
    }
}
