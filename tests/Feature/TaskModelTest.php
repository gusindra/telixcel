<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Task model additions: terminal statuses + reason note, recursive children, strict type scope. */
class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_terminal_statuses_with_a_reason_note(): void
    {
        foreach (['declined', 'cancelled', 'aborted'] as $status) {
            $task = Task::create([
                'parent_id' => 0, 'title' => "T $status", 'type' => 'admin',
                'status' => $status, 'status_note' => "Reason for $status",
                'team_id' => 1, 'owner_id' => 1, 'target_date' => now(),
            ]);
            $this->assertDatabaseHas('tasks', [
                'id' => $task->id, 'status' => $status, 'status_note' => "Reason for $status",
            ]);
        }
    }

    /** @test */
    public function children_recursive_loads_the_whole_subtree(): void
    {
        $root  = Task::create(['parent_id' => 0, 'title' => 'root', 'type' => 'operasional', 'team_id' => 1, 'owner_id' => 1, 'target_date' => now()]);
        $child = Task::create(['parent_id' => $root->id, 'title' => 'child', 'type' => 'operasional', 'team_id' => 1, 'owner_id' => 1, 'target_date' => now()]);
        Task::create(['parent_id' => $child->id, 'title' => 'grandchild', 'type' => 'operasional', 'team_id' => 1, 'owner_id' => 1, 'target_date' => now()]);

        $loaded = Task::with('childrenRecursive')->find($root->id);

        $this->assertCount(1, $loaded->childrenRecursive);
        $this->assertSame('child', $loaded->childrenRecursive->first()->title);
        $this->assertCount(1, $loaded->childrenRecursive->first()->childrenRecursive);
        $this->assertSame('grandchild', $loaded->childrenRecursive->first()->childrenRecursive->first()->title);
    }

    /** @test */
    public function for_my_type_returns_nothing_when_role_has_no_matching_type(): void
    {
        $user = User::create([
            'name' => 'NoType', 'email' => uniqid() . '@test.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
        $role = Role::create(['name' => 'Random Role', 'type' => null, 'role_for' => 'team', 'description' => 'no type']);
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'R',
        ]);
        $this->actingAs($user->fresh());

        Task::create(['parent_id' => 0, 'title' => 't', 'type' => 'finance', 'team_id' => 1, 'owner_id' => 1, 'target_date' => now()]);

        $this->assertSame([], my_task_types());
        $this->assertSame(0, Task::where('team_id', 1)->forMyType()->count());
    }
}
