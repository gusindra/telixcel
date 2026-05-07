<?php

namespace Tests\Feature;

use App\Http\Livewire\Permission\Add as PermissionAdd;
use App\Http\Livewire\Permission\Flow as PermissionFlow;
use App\Http\Livewire\Role\Edit as RoleEdit;
use App\Http\Livewire\Role\Permissions as RolePermissions;
use App\Http\Livewire\Role\Roles as RoleAdd;
use App\Http\Livewire\Setting\Company\CompanyAdd;
use App\Http\Livewire\Setting\Company\CompanyEdit;
use App\Http\Livewire\Setting\Company\PaymentCompanyAdd;
use App\Http\Livewire\Setting\Notification\Add as NotificationAdd;
use App\Models\Company;
use App\Models\CompanyPayment;
use App\Models\FlowSetting;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_routes_are_available_for_super_admin()
    {
        $this->actingAsSuperAdmin();

        foreach ($this->settingsRoutes() as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_company_and_payment_account_crud_flows_are_working()
    {
        $this->actingAsSuperAdmin();

        Livewire::test(CompanyAdd::class)
            ->set('input.name', 'Settings Company')
            ->set('input.code', 'SET')
            ->set('input.tax_id', '99.999.999.9-999.999')
            ->set('input.post_code', '12345')
            ->set('input.province', 'DKI Jakarta')
            ->set('input.city', 'Jakarta')
            ->set('input.address', 'Settings Street 1')
            ->set('input.person_in_charge', 'Settings PIC')
            ->call('create')
            ->assertHasNoErrors();

        $company = Company::firstWhere('code', 'SET');

        $this->assertNotNull($company);
        $this->assertSame(0, (int) $company->user_id);

        Livewire::test(CompanyEdit::class, ['company' => $company])
            ->set('input.name', 'Settings Company Updated')
            ->set('input.code', 'SET-UPD')
            ->set('input.tax_id', '88.888.888.8-888.888')
            ->set('input.person_in_charge', 'Updated PIC')
            ->call('update', $company->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Settings Company Updated',
            'code' => 'SET-UPD',
            'person_in_charge' => 'Updated PIC',
        ]);

        Livewire::test(PaymentCompanyAdd::class, ['data' => $company->fresh()])
            ->set('input.method', 'transfer')
            ->set('input.provider_name', 'BCA')
            ->set('input.account_number', '1234567890')
            ->set('input.account_name', 'Settings Company Updated')
            ->set('input.provider_location', 'Jakarta')
            ->set('input.notes', 'Primary account')
            ->call('create')
            ->assertHasNoErrors();

        $payment = CompanyPayment::firstWhere('company_id', $company->id);

        $this->assertNotNull($payment);

        Livewire::test(PaymentCompanyAdd::class, ['data' => $company->fresh()])
            ->call('updateShowModal', $payment->id)
            ->set('input.method', 'transfer')
            ->set('input.provider_name', 'Mandiri')
            ->set('input.account_number', '0987654321')
            ->set('input.account_name', 'Settings Payment Updated')
            ->set('input.provider_location', 'Bandung')
            ->set('input.notes', 'Updated account')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('company_payments', [
            'id' => $payment->id,
            'provider_name' => 'Mandiri',
            'account_number' => '0987654321',
            'account_name' => 'Settings Payment Updated',
        ]);

        Livewire::test(PaymentCompanyAdd::class, ['data' => $company->fresh()])
            ->call('deleteShowModal', $payment->id)
            ->call('delete')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('company_payments', [
            'id' => $payment->id,
        ]);
    }

    public function test_role_menu_permission_and_flow_crud_flows_are_working()
    {
        $this->actingAsSuperAdmin();

        Livewire::test(RoleAdd::class)
            ->set('type', 'admin')
            ->set('name', 'Settings Tester')
            ->set('description', 'Role created from settings test')
            ->call('create')
            ->assertHasNoErrors();

        $role = Role::firstWhere('name', 'Settings Tester');

        $this->assertNotNull($role);

        Livewire::test(RoleEdit::class, ['uuid' => $role->id])
            ->set('name', 'Settings Tester Updated')
            ->set('description', 'Updated role description')
            ->set('status', true)
            ->set('type', 'admin')
            ->set('role_for', 'admin')
            ->call('update', $role->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Settings Tester Updated',
            'description' => 'Updated role description',
        ]);

        Livewire::test(PermissionAdd::class)
            ->set('model', 'settings tracker')
            ->set('type', ['view' => true, 'create' => true, 'update' => true])
            ->call('create')
            ->assertHasNoErrors();

        $permission = Permission::firstWhere('name', 'VIEW SETTINGS TRACKER');

        $this->assertNotNull($permission);

        Livewire::test(RolePermissions::class, ['id' => $role->id])
            ->call('check', $permission->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);

        Livewire::test(RolePermissions::class, ['id' => $role->id])
            ->call('check', $permission->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);

        $flowComponent = Livewire::test(PermissionFlow::class, ['model' => 'settings tracker']);

        $this->assertGreaterThan(0, $flowComponent->instance()->role->count());

        $flowComponent
            ->set('input.role_id', $role->id)
            ->set('input.team_id', auth()->user()->currentTeam->id)
            ->set('input.description', 'Settings approval task')
            ->set('input.after_status', 'submit')
            ->set('input.status', 'approved')
            ->call('addFlow')
            ->assertHasNoErrors();

        $flow = FlowSetting::firstWhere('description', 'Settings approval task');

        $this->assertNotNull($flow);

        Livewire::test(PermissionFlow::class, ['model' => 'settings tracker'])
            ->call('deleteFlow', $flow->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('flow_settings', [
            'id' => $flow->id,
        ]);
    }

    public function test_notification_send_flow_is_working()
    {
        $admin = $this->actingAsSuperAdmin();

        Livewire::test(NotificationAdd::class)
            ->set('input.type', 'app')
            ->set('grouptype', 'user')
            ->set('input.group', [$admin->id])
            ->set('input.message', 'Settings notification test')
            ->call('sendAction')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('notifications', [
            'type' => 'app',
            'notification' => 'Settings notification test',
            'user_id' => $admin->id,
            'status' => 'unread',
        ]);
    }

    public function test_settings_top_level_delete_actions_are_working()
    {
        $admin = $this->actingAsSuperAdmin();

        $company = Company::create([
            'name' => 'Delete Settings Company',
            'code' => 'DEL-SET',
            'tax_id' => '-',
            'post_code' => '12345',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta',
            'address' => 'Settings Delete Street',
            'logo' => '',
            'person_in_charge' => 'Delete PIC',
            'user_id' => 0,
        ]);

        $role = Role::create([
            'name' => 'Delete Settings Role',
            'role_for' => 'team',
            'type' => 'admin',
            'description' => 'Delete role',
            'status' => 'active',
        ]);

        $permission = Permission::create([
            'name' => 'DELETE SETTINGS MENU',
            'model' => 'DELETE SETTINGS',
        ]);

        $notification = Notification::create([
            'type' => 'app',
            'notification' => 'Delete settings notification',
            'user_id' => $admin->id,
            'status' => 'unread',
        ]);

        foreach ([
            'company' => [$company->id, 'companies'],
            'role' => [$role->id, 'roles'],
            'permission' => [$permission->id, 'permissions'],
            'notification' => [$notification->id, 'notifications'],
        ] as $type => [$id, $table]) {
            $this->delete(route('records.destroy', [$type, $id]))
                ->assertRedirect();

            if ($type === 'notification') {
                $this->assertSoftDeleted($table, ['id' => $id]);
            } else {
                $this->assertDatabaseMissing($table, ['id' => $id]);
            }
        }
    }

    private function actingAsSuperAdmin(): User
    {
        $this->seed();

        $user = User::where('email', 'admin@telixcel.com')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }

    private function settingsRoutes(): array
    {
        return [
            '/settings',
            '/settings/company',
            '/notif-center',
            '/roles',
            '/permission',
        ];
    }
}
