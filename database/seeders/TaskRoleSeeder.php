<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Defines which roles may access the To-do list and the task "type" each role
 * is responsible for. The Task component reads roles.type to scope visibility,
 * so this mapping is data-driven (no hard-coded role names in code).
 *
 *   type = finance | admin | operasional   (empty = no todo access)
 */
class TaskRoleSeeder extends Seeder
{
    public function run()
    {
        $map = [
            'Super Admin'         => 'admin',        // managers see everything anyway
            'Admin'               => 'admin',
            'Accounting'          => 'finance',
            'Commercial Manager'  => 'finance',
            'Operational Manager' => 'operasional',
            'Project Manager'     => 'operasional',
            'Agent'               => 'operasional',
        ];

        foreach ($map as $name => $type) {
            Role::where('name', $name)->update(['type' => $type]);
        }
    }
}
