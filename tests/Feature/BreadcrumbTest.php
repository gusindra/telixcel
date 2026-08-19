<?php

namespace Tests\Feature;

use App\Models\AiApplication;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Project;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Team;
use App\Models\User;
use App\Support\Breadcrumbs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BreadcrumbTest extends TestCase
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
        ]);
        $team = Team::forceCreate([
            'user_id' => $user->id,
            'name' => 'T',
            'personal_team' => true,
        ]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        RoleUser::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'team_id' => $team->id,
            'status' => 'active',
            'active' => 1,
            'working_id' => 'A',
        ]);

        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    /** @test */
    public function dashboard_shows_home_and_current_page(): void
    {
        $this->admin();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('tx-crumb', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('Dashboard');
    }

    /** @test */
    public function ai_application_usage_includes_the_full_trail(): void
    {
        $this->admin();
        Http::fake(['*' => Http::response(['data' => []], 200)]);
        $app = AiApplication::factory()->create(['name' => 'Hireach']);

        $html = $this->get(route('ai.applications.usage', $app))->assertOk()->getContent();

        $this->assertStringContainsString('tx-crumb', $html);
        $this->assertStringContainsString('AI Manager', $html);
        $this->assertStringContainsString('Applications', $html);
        $this->assertStringContainsString('Hireach', $html);
        $this->assertStringContainsString('Usage', $html);
        $this->assertStringContainsString(route('ai.applications'), $html);
        $this->assertStringContainsString(route('ai.applications.show', $app), $html);
    }

    /** @test */
    public function project_detail_uses_the_project_name(): void
    {
        $this->admin();
        $project = Project::create([
            'name' => 'Rollout Utara',
            'type' => 'selling',
            'status' => 'active',
        ]);

        $labels = collect(Breadcrumbs::items('project.show', ['project' => $project]))->pluck('label')->all();

        $this->assertSame(['Dashboard', 'Assistant', 'Project', 'Rollout Utara'], $labels);
    }

    /** @test */
    public function user_balance_uses_the_user_name(): void
    {
        $admin = $this->admin();

        $labels = collect(Breadcrumbs::items('user.show.balance', ['user' => $admin]))->pluck('label')->all();

        $this->assertSame(['Dashboard', 'Users', 'Admin', 'Balance'], $labels);
    }
}
