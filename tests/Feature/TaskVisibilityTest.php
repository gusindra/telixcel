<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Role-based task visibility:
 *  - task.type must match the ACTIVE role's type
 *  - Super Admin (active role) sees all types
 *  - visibility changes when the user switches role
 *  - tasks are scoped to projects the user is invited to
 * Tested at helper/scope level (Todo tree() uses MySQL FIELD() which sqlite can't run).
 */
class TaskVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            ['Super Admin', 'admin'],
            ['Admin', 'admin'],
            ['Accounting', 'finance'],
            ['Commercial Manager', 'finance'],
            ['Project Manager', 'operasional'],
            ['Agent', 'operasional'],
        ] as [$name, $type]) {
            Role::create([
                'name' => $name, 'type' => $type,
                'role_for' => 'team', 'description' => "$name role",
            ]);
        }
    }

    private function makeUser(int $teamId = 1): User
    {
        return User::create([
            'name'            => 'User ' . uniqid(),
            'email'           => uniqid('u') . '@test.com',
            'password'        => bcrypt('password'),
            'current_team_id' => $teamId,
        ]);
    }

    private function actingAsRole(string $roleName, int $teamId = 1): User
    {
        $user = $this->makeUser($teamId);
        $role = Role::where('name', $roleName)->firstOrFail();
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => $teamId,
            'status' => 'active', 'active' => 1, 'working_id' => 'T' . $user->id,
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    private function makeProject(int $teamId = 1, array $memberIds = []): Project
    {
        $p = Project::create(['name' => 'Project ' . uniqid(), 'team_id' => $teamId, 'status' => 'active']);
        foreach ($memberIds as $uid) {
            $p->members()->attach($uid);
        }

        return $p;
    }

    private function makeTask(string $type, int $projectId, int $teamId = 1, int $parentId = 0): Task
    {
        return Task::create([
            'project_id' => $projectId, 'parent_id' => $parentId, 'title' => "$type task",
            'type' => $type, 'priority' => 'medium', 'status' => 'pending',
            'team_id' => $teamId, 'owner_id' => 1, 'target_date' => now(),
        ]);
    }

    /** @test */
    public function my_task_types_follows_the_active_role(): void
    {
        $this->actingAsRole('Accounting');
        $this->assertSame(['finance'], my_task_types());

        $this->actingAsRole('Project Manager');
        $this->assertSame(['operasional'], my_task_types());

        $this->actingAsRole('Super Admin');
        $this->assertEqualsCanonicalizing(['admin', 'finance', 'operasional'], my_task_types());
    }

    /** @test */
    public function only_super_admin_is_a_task_manager(): void
    {
        $this->actingAsRole('Super Admin');
        $this->assertTrue(is_task_manager());

        $this->actingAsRole('Admin');
        $this->assertFalse(is_task_manager(), 'Regular Admin role must NOT see all types');

        $this->actingAsRole('Accounting');
        $this->assertFalse(is_task_manager());
    }

    /** @test */
    public function accounting_role_sees_only_finance_tasks(): void
    {
        $user = $this->actingAsRole('Accounting');
        $project = $this->makeProject(1, [$user->id]);
        $this->makeTask('finance', $project->id);
        $this->makeTask('operasional', $project->id);
        $this->makeTask('admin', $project->id);

        $types = Task::where('team_id', 1)->forMyType()->pluck('type')->unique()->values()->all();
        $this->assertSame(['finance'], $types);
    }

    /** @test */
    public function project_manager_sees_only_operasional_tasks(): void
    {
        $user = $this->actingAsRole('Project Manager');
        $project = $this->makeProject(1, [$user->id]);
        $this->makeTask('finance', $project->id);
        $this->makeTask('operasional', $project->id);

        $types = Task::where('team_id', 1)->forMyType()->pluck('type')->unique()->values()->all();
        $this->assertSame(['operasional'], $types);
    }

    /** @test */
    public function super_admin_sees_every_task_type(): void
    {
        $this->actingAsRole('Super Admin');
        $project = $this->makeProject(1);
        $this->makeTask('finance', $project->id);
        $this->makeTask('operasional', $project->id);
        $this->makeTask('admin', $project->id);

        $this->assertSame(3, Task::where('team_id', 1)->forMyType()->count());
    }

    /** @test */
    public function switching_active_role_changes_visible_task_types(): void
    {
        $user = $this->makeUser();
        foreach (['Accounting', 'Project Manager', 'Super Admin'] as $rn) {
            $rid = Role::where('name', $rn)->value('id');
            RoleUser::create([
                'user_id' => $user->id, 'role_id' => $rid, 'team_id' => 1,
                'status' => 'active', 'active' => null, 'working_id' => 'T',
            ]);
        }
        $project = $this->makeProject(1, [$user->id]);
        $this->makeTask('finance', $project->id);
        $this->makeTask('operasional', $project->id);
        $this->makeTask('admin', $project->id);

        $switchTo = function (string $rn) use ($user) {
            RoleUser::where('user_id', $user->id)->update(['active' => null]);
            $rid = Role::where('name', $rn)->value('id');
            RoleUser::where('user_id', $user->id)->where('role_id', $rid)->update(['active' => 1]);
            $this->actingAs(User::find($user->id));
        };

        $switchTo('Accounting');
        $this->assertSame(['finance'], Task::where('team_id', 1)->forMyType()->pluck('type')->unique()->values()->all());

        $switchTo('Project Manager');
        $this->assertSame(['operasional'], Task::where('team_id', 1)->forMyType()->pluck('type')->unique()->values()->all());

        $switchTo('Super Admin');
        $this->assertSame(3, Task::where('team_id', 1)->forMyType()->count());
    }

    /** @test */
    public function tasks_are_scoped_to_invited_projects(): void
    {
        $user = $this->actingAsRole('Accounting');
        $invited = $this->makeProject(1, [$user->id]);
        $notInvited = $this->makeProject(1, []);
        $this->makeTask('finance', $invited->id);
        $this->makeTask('finance', $notInvited->id);

        $invitedIds = my_invited_project_ids();
        $this->assertContains($invited->id, $invitedIds);
        $this->assertNotContains($notInvited->id, $invitedIds);

        $visibleProjects = Task::where('team_id', 1)->forMyType()
            ->whereIn('project_id', $invitedIds)
            ->pluck('project_id')->unique()->all();
        $this->assertContains($invited->id, $visibleProjects);
        $this->assertNotContains($notInvited->id, $visibleProjects);
    }

    /** @test */
    public function super_admin_is_not_restricted_to_invited_projects(): void
    {
        $this->actingAsRole('Super Admin');
        $this->assertNull(my_invited_project_ids(), 'null means all projects (no invite restriction)');
    }
}
