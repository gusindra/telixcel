<?php

namespace Tests\Feature;

use App\Http\Livewire\Dashboard\DashboardOverview;
use App\Models\Project;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The /user/{id} detail page reuses DashboardOverview with a `forUserId` so the dashboard cards
 * are scoped to that user's owned/assigned tasks, while the default dashboard stays team-wide.
 */
class UserDashboardScopeTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: User, 1: Team} */
    private function bootSuperAdmin(): array
    {
        $u = User::create(['name' => 'Admin', 'email' => uniqid('a') . '@test.com', 'password' => bcrypt('x')]);
        $team = Team::forceCreate(['user_id' => $u->id, 'name' => 'T', 'personal_team' => true]);
        $u->forceFill(['current_team_id' => $team->id])->save();
        $role = Role::create(['name' => 'Super Admin', 'type' => 'admin', 'role_for' => 'admin', 'description' => 'SA']);
        RoleUser::create(['user_id' => $u->id, 'role_id' => $role->id, 'team_id' => $team->id, 'status' => 'active', 'active' => 1, 'working_id' => 'SA']);
        $this->actingAs(User::find($u->id));

        return [User::find($u->id), $team];
    }

    /** @test */
    public function dashboard_scoped_to_a_user_counts_only_their_owned_or_assigned_tasks(): void
    {
        [$admin, $team] = $this->bootSuperAdmin();
        $target = User::create(['name' => 'Member', 'email' => uniqid('m') . '@test.com', 'password' => bcrypt('x')]);
        $project = Project::create(['name' => 'P', 'type' => 'selling', 'status' => 'active', 'team_id' => $team->id]);

        Task::create(['project_id' => $project->id, 'title' => 'Owned A', 'type' => 'admin', 'status' => 'pending', 'team_id' => $team->id, 'owner_id' => $target->id]);
        Task::create(['project_id' => $project->id, 'title' => 'Owned B', 'type' => 'admin', 'status' => 'complete', 'team_id' => $team->id, 'owner_id' => $target->id]);
        $assigned = Task::create(['project_id' => $project->id, 'title' => 'Assigned', 'type' => 'admin', 'status' => 'progress', 'team_id' => $team->id, 'owner_id' => $admin->id]);
        $assigned->forceFill(['assigned_to' => $target->id])->save();
        Task::create(['project_id' => $project->id, 'title' => 'Other', 'type' => 'admin', 'status' => 'pending', 'team_id' => $team->id, 'owner_id' => $admin->id]);

        $c = Livewire::test(DashboardOverview::class, ['forUserId' => $target->id]);

        $this->assertSame(3, $c->get('totalTasks'));     // 2 owned + 1 assigned (not the admin-only "Other")
        $this->assertSame(1, $c->get('tasksCompleted')); // Owned B
    }

    /** @test */
    public function default_dashboard_stays_team_wide(): void
    {
        [$admin, $team] = $this->bootSuperAdmin();
        $project = Project::create(['name' => 'P', 'type' => 'selling', 'status' => 'active', 'team_id' => $team->id]);
        Task::create(['project_id' => $project->id, 'title' => 'T1', 'type' => 'admin', 'status' => 'pending', 'team_id' => $team->id, 'owner_id' => $admin->id]);
        Task::create(['project_id' => $project->id, 'title' => 'T2', 'type' => 'finance', 'status' => 'progress', 'team_id' => $team->id, 'owner_id' => 999]);

        // Super admin with no forUserId sees every team task regardless of owner/type.
        $c = Livewire::test(DashboardOverview::class);

        $this->assertSame(2, $c->get('totalTasks'));
    }
}
