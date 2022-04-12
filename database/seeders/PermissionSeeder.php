<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            'CHAT',
            'CUSTOMER',
            'CAMPAIGN',
            'ORDER',
            'PRODUCT',
            'TICKET',
            'BILLING',
            'AFTERSALES',
            'TEMPLATE',
            'TEAM',
            'PROJECT',
            'SETTING'
        );
        $functions = array(
            'VIEW',
            'CREATE',
            'UPDATE',
            'DELETE',
            'AUDIT'
        );
        foreach ($data as $menu) {
            foreach ($functions as $action) {
                \App\Models\Permission::create([
                    'name' => $action. ' ' .$menu,
                    'model' => $menu
                ]);
            }
        }
        \App\Models\Permission::create(['name' => 'UPDATE SETTING', 'model' => 'SETTING']);
        \App\Models\Permission::create(['name' => 'VIEW DASHBOARD', 'model' => 'DASHBOARD']);
        // DB::table('permissions')->insert($data);
    }
}
