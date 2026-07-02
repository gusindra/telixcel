<?php

namespace Tests\Feature;

use App\Http\Livewire\Task\Todo;
use App\Models\Project;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskTodoTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        foreach (['TASK', 'PROJECT', 'SETTING'] as $m) {
            foreach (['VIEW', 'CREATE', 'UPDATE', 'DELETE', 'AUDIT'] as $a) {
                \App\Models\Permission::updateOrCreate(['name' => "{$a} {$m}"], ['model' => $m]);
            }
        }
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'admin', 'description' => 'Admin']);
        foreach (\App\Models\Permission::all() as $perm) {
            \App\Models\PermissionRole::updateOrCreate(['role_id' => $role->id, 'permission_id' => $perm->id]);
        }
        $user = User::create(['name' => 'Admin', 'email' => uniqid('a') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        RoleUser::create(['user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1, 'status' => 'active', 'active' => 1, 'working_id' => 'A']);
        $this->actingAs($user->fresh());
        return $user;
    }

    private function actingAsSuperAdmin(): User
    {
        foreach (['TASK', 'PROJECT', 'SETTING'] as $m) {
            foreach (['VIEW', 'CREATE', 'UPDATE', 'DELETE', 'AUDIT'] as $a) {
                \App\Models\Permission::updateOrCreate(['name' => "{$a} {$m}"], ['model' => $m]);
            }
        }
        $role = Role::create(['name' => 'Super Admin', 'type' => 'admin', 'role_for' => 'admin', 'description' => 'SA']);
        foreach (\App\Models\Permission::all() as $perm) {
            \App\Models\PermissionRole::updateOrCreate(['role_id' => $role->id, 'permission_id' => $perm->id]);
        }
        $user = User::create(['name' => 'SA', 'email' => uniqid('sa') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        RoleUser::create(['user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1, 'status' => 'active', 'active' => 1, 'working_id' => 'SA']);
        $this->actingAs($user->fresh());
        return $user;
    }

    private function makeProject(): Project
    {
        return Project::create(['name' => 'Test Project', 'team_id' => 1, 'status' => 'active']);
    }

    private function makeTask(array $overrides = []): Task
    {
        return Task::create(array_merge([
            'project_id' => 1, 'title' => 'Test Task', 'type' => 'admin',
            'status' => 'pending', 'team_id' => 1, 'owner_id' => auth()->id(),
            'target_date' => now()->addDays(7),
        ], $overrides));
    }

    // ──────────────────── CREATE ────────────────────

    /** @test */
    public function it_creates_a_root_task(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();

        Livewire::test(Todo::class, ['id' => $project->id])
            ->set('title', 'New Root Task')
            ->set('type', 'admin')
            ->set('priority', 'high')
            ->set('target_date', now()->addDays(7)->toDateString())
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Root Task',
            'type' => 'admin',
            'priority' => 'high',
            'parent_id' => 0,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_creates_a_task_assigned_to_a_user(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $assignee = User::create(['name' => 'Assignee', 'email' => uniqid('asg') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->set('title', 'Assigned Task')
            ->set('type', 'admin')
            ->set('assigned_to', $assignee->id)
            ->set('target_date', now()->addDays(7)->toDateString())
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Assigned Task',
            'assigned_to' => $assignee->id,
        ]);
    }

    /** @test */
    public function assignable_users_include_project_members_and_owner(): void
    {
        $admin = $this->actingAsAdmin();
        $project = $this->makeProject();
        $member = User::create(['name' => 'Member', 'email' => uniqid('m') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        $project->members()->attach($member->id);

        $users = Livewire::test(Todo::class, ['id' => $project->id])->viewData('assignableUsers');

        $this->assertTrue($users->contains('id', $member->id));  // project member
        $this->assertTrue($users->contains('id', $admin->id));   // current user
    }

    /** @test */
    public function it_creates_a_sub_task_that_inherits_type_from_parent(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $parent = $this->makeTask(['project_id' => $project->id, 'type' => 'operasional']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->set('parent_id', $parent->id)
            ->set('title', 'Sub Task')
            ->set('priority', 'medium')
            ->set('target_date', now()->addDays(7)->toDateString())
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Sub Task',
            'type' => 'operasional',  // inherited from parent
            'parent_id' => $parent->id,
        ]);
    }

    /** @test */
    public function create_validates_title_is_required(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();

        Livewire::test(Todo::class, ['id' => $project->id])
            ->set('title', '')
            ->call('create')
            ->assertHasErrors(['title']);
    }

    /** @test */
    public function create_validates_type_is_required_for_root(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();

        Livewire::test(Todo::class, ['id' => $project->id])
            ->set('title', 'No Type')
            ->set('type', '')
            ->call('create')
            ->assertHasErrors(['type']);
    }

    /** @test */
    public function create_validates_target_date_is_required(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();

        Livewire::test(Todo::class, ['id' => $project->id])
            ->set('title', 'No Date')
            ->set('type', 'admin')
            ->set('target_date', '')
            ->call('create')
            ->assertHasErrors(['target_date']);
    }

    // ──────────────────── STATUS ────────────────────

    /** @test */
    public function set_status_updates_to_valid_status(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $task = $this->makeTask(['project_id' => $project->id, 'status' => 'pending']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('setStatus', $task->id, 'progress');

        $this->assertSame('progress', $task->fresh()->status);
    }

    /** @test */
    public function set_status_ignores_invalid_status(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $task = $this->makeTask(['project_id' => $project->id, 'status' => 'pending']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('setStatus', $task->id, 'bogus');

        $this->assertSame('pending', $task->fresh()->status);
    }

    /** @test */
    public function terminal_status_opens_comment_modal(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $task = $this->makeTask(['project_id' => $project->id, 'status' => 'progress']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('setStatus', $task->id, 'declined')
            ->assertSet('commentModal', true)
            ->assertSet('commentTaskId', $task->id)
            ->assertSet('commentStatus', 'declined');
    }

    /** @test */
    public function confirm_status_comment_applies_terminal_status(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $task = $this->makeTask(['project_id' => $project->id, 'status' => 'progress']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('requestStatusComment', $task->id, 'cancelled')
            ->set('statusComment', 'Client backed out')
            ->call('confirmStatusComment')
            ->assertSet('commentModal', false);

        $this->assertSame('cancelled', $task->fresh()->status);
        $this->assertSame('Client backed out', $task->fresh()->status_note);
    }

    /** @test */
    public function confirm_status_comment_requires_reason(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $task = $this->makeTask(['project_id' => $project->id, 'status' => 'progress']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('requestStatusComment', $task->id, 'declined')
            ->set('statusComment', 'ab')
            ->call('confirmStatusComment')
            ->assertHasErrors(['statusComment']);
    }

    // ──────────────────── SUBTASK STATUS SYNC ────────────────────

    /** @test */
    public function parent_becomes_complete_when_all_children_are_complete(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $parent = $this->makeTask(['project_id' => $project->id, 'status' => 'progress']);
        $child = $this->makeTask(['project_id' => $project->id, 'parent_id' => $parent->id, 'type' => 'admin', 'status' => 'progress']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('setStatus', $child->id, 'complete');

        $this->assertSame('complete', $parent->fresh()->status);
    }

    /** @test */
    public function parent_reverts_to_progress_when_any_child_is_not_complete(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $parent = $this->makeTask(['project_id' => $project->id, 'status' => 'complete']);
        $childA = $this->makeTask(['project_id' => $project->id, 'parent_id' => $parent->id, 'type' => 'admin', 'status' => 'complete']);
        $childB = $this->makeTask(['project_id' => $project->id, 'parent_id' => $parent->id, 'type' => 'admin', 'status' => 'complete']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('setStatus', $childB->id, 'pending');

        $this->assertSame('progress', $parent->fresh()->status);
    }

    // ──────────────────── DELETE ────────────────────

    /** @test */
    public function confirm_delete_shows_modal(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $task = $this->makeTask(['project_id' => $project->id]);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('confirmDelete', $task->id)
            ->assertSet('confirmingDelete', true)
            ->assertSet('deleteId', $task->id);
    }

    /** @test */
    public function delete_task_removes_it(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $task = $this->makeTask(['project_id' => $project->id]);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('confirmDelete', $task->id)
            ->call('deleteTask')
            ->assertSet('confirmingDelete', false);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function delete_task_reparents_children(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $parent = $this->makeTask(['project_id' => $project->id, 'title' => 'Parent']);
        $child = $this->makeTask(['project_id' => $project->id, 'parent_id' => $parent->id, 'type' => 'admin', 'title' => 'Child']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('confirmDelete', $parent->id)
            ->call('deleteTask');

        $this->assertSoftDeleted('tasks', ['id' => $parent->id]);
        $this->assertDatabaseHas('tasks', ['id' => $child->id, 'parent_id' => 0]);
    }

    /** @test */
    public function delete_shows_warning_when_task_has_children(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $parent = $this->makeTask(['project_id' => $project->id]);
        $this->makeTask(['project_id' => $project->id, 'parent_id' => $parent->id, 'type' => 'admin']);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('confirmDelete', $parent->id)
            ->assertSet('deleteIsParent', true);
    }

    // ──────────────────── FORM MODAL ────────────────────

    /** @test */
    public function action_show_modal_opens_form(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('actionShowModal')
            ->assertSet('showForm', true);
    }

    /** @test */
    public function action_show_modal_pre_selects_parent(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $parent = $this->makeTask(['project_id' => $project->id]);

        Livewire::test(Todo::class, ['id' => $project->id])
            ->call('actionShowModal', $parent->id)
            ->assertSet('parent_id', $parent->id)
            ->assertSet('showForm', true);
    }

    // ──────────────────── VISIBILITY ────────────────────

    /** @test */
    public function super_admin_sees_all_task_types(): void
    {
        $this->actingAsSuperAdmin();
        $project = $this->makeProject();
        $this->makeTask(['project_id' => $project->id, 'type' => 'finance']);
        $this->makeTask(['project_id' => $project->id, 'type' => 'operasional']);

        $component = Livewire::test(Todo::class, ['id' => $project->id])->instance();
        $ref = new \ReflectionMethod($component, 'scopedQuery');
        $ref->setAccessible(true);

        $types = $ref->invoke($component)->pluck('type')->unique()->sort()->values()->all();
        $this->assertContains('finance', $types);
        $this->assertContains('operasional', $types);
    }

    // ──────────────────── OWNER SCOPE ────────────────────

    /** @test */
    public function owner_scope_filters_by_assigned_user(): void
    {
        $this->actingAsAdmin();
        $project = $this->makeProject();
        $otherUser = User::create(['name' => 'Other', 'email' => 'other@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        $this->makeTask(['project_id' => $project->id, 'owner_id' => $otherUser->id, 'title' => 'Other Task']);
        $this->makeTask(['project_id' => $project->id, 'owner_id' => 1, 'title' => 'My Task']);

        $component = Livewire::test(Todo::class, ['id' => $project->id, 'ownerId' => 1])->instance();
        $ref = new \ReflectionMethod($component, 'scopedQuery');
        $ref->setAccessible(true);

        $titles = $ref->invoke($component)->pluck('title')->all();
        $this->assertContains('My Task', $titles);
        $this->assertNotContains('Other Task', $titles);
    }
}
