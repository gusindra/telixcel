<?php

namespace Tests\Feature;

use App\Http\Livewire\Ai\LogsPage;
use App\Models\LogChange;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AiGatewayLogsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Permission::updateOrCreate(['name' => 'VIEW PROJECT'], ['model' => 'PROJECT']);
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'admin', 'description' => 'Admin']);
        foreach (Permission::all() as $perm) {
            PermissionRole::updateOrCreate([
                'role_id' => $role->id,
                'permission_id' => $perm->id,
            ]);
        }
        $user = User::create([
            'name' => 'Admin',
            'email' => uniqid('a').'@test.com',
            'password' => bcrypt('password'),
            'current_team_id' => 1,
        ]);
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'A',
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    /** @test */
    public function admin_can_open_logs_page(): void
    {
        $this->admin();

        $this->get(route('ai.logs'))
            ->assertOk()
            ->assertSee('AI Activity Log')
            ->assertSee('Applications')
            ->assertSee('Log')
            ->assertDontSee('Cari tool, model, user');
    }

    /** @test */
    public function logs_page_shows_ai_audit_entries(): void
    {
        $this->admin();

        LogChange::create([
            'model' => 'AiApplication',
            'model_id' => 5,
            'before' => ['name' => 'Old'],
            'remark' => 'updated by Admin #1',
        ]);

        Livewire::test(LogsPage::class)
            ->assertSee('AiApplication')
            ->assertSee('#5')
            ->assertSee('updated by Admin #1');
    }

    /** @test */
    public function logs_page_can_search_audit_entries(): void
    {
        $this->admin();

        LogChange::create([
            'model' => 'AiApplication',
            'model_id' => 5,
            'remark' => 'updated Hireach key',
        ]);
        LogChange::create([
            'model' => 'AiSetting',
            'model_id' => 1,
            'remark' => 'upstream endpoint updated',
        ]);

        Livewire::test(LogsPage::class)
            ->set('search', 'Hireach')
            ->assertSee('updated Hireach key')
            ->assertDontSee('upstream endpoint updated');
    }

    /** @test */
    public function non_admin_cannot_open_logs_page(): void
    {
        $this->get(route('ai.logs'))->assertRedirect(route('login'));
    }
}
