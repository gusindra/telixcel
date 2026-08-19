<?php

namespace Tests\Feature;

use App\Http\Livewire\Ai\SettingsPage;
use App\Models\AiSetting;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AiGatewaySettingsTest extends TestCase
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
    public function admin_can_save_upstream_endpoint(): void
    {
        $this->admin();

        Livewire::test(SettingsPage::class)
            ->set('base_url', 'http://localhost:20128/v1')
            ->set('api_key', 'secret-key')
            ->call('save')
            ->assertHasNoErrors();

        $setting = AiSetting::stored();
        $this->assertNotNull($setting);
        $this->assertSame('http://localhost:20128/v1', $setting->base_url);
        $this->assertSame('secret-key', $setting->api_key);
        $this->assertSame('http://localhost:20128/v1', config('ai.base_url'));
        $this->assertSame('http://localhost:20128/v1/chat/completions', config('ai.endpoint'));
    }

    /** @test */
    public function endpoint_must_start_with_http_or_https(): void
    {
        $this->admin();

        Livewire::test(SettingsPage::class)
            ->set('base_url', 'localhost:20128/v1')
            ->call('save')
            ->assertHasErrors('base_url');

        $this->assertNull(AiSetting::stored());
    }

    /** @test */
    public function admin_can_clear_endpoint_to_fall_back_to_env(): void
    {
        config(['ai.base_url' => 'https://router.noonight.cloud/v1']);
        $this->admin();
        AiSetting::create(['base_url' => 'http://localhost:20128/v1', 'api_key' => 'x']);

        Livewire::test(SettingsPage::class)
            ->set('base_url', '')
            ->set('api_key', '')
            ->call('save')
            ->assertHasNoErrors();

        $setting = AiSetting::stored();
        $this->assertNotNull($setting);
        $this->assertNull($setting->base_url);
        $this->assertNull($setting->api_key);
        $this->assertSame('https://router.noonight.cloud/v1', config('ai.base_url'));
    }

    /** @test */
    public function non_admin_cannot_open_settings_page(): void
    {
        $this->get(route('ai.settings'))->assertRedirect(route('login'));
    }
}
