<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Task;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Monthly report "Outstanding / Overdue" pending scope:
 * overdue + due within the report month (and no-deadline) are listed;
 * tasks due next month onward are excluded.
 */
class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'Super Admin', 'type' => 'admin', 'role_for' => 'team', 'description' => 'Super Admin']);
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::create([
            'name' => 'Boss',
            'email' => uniqid('a') . '@test.com',
            'password' => bcrypt('password'),
            'current_team_id' => 1,
        ]);
        $role = Role::where('name', 'Super Admin')->firstOrFail();
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'A' . $user->id,
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    private function makeTask(string $title, $targetDate, int $ownerId): Task
    {
        return Task::create([
            'project_id' => null, 'parent_id' => 0, 'title' => $title,
            'type' => 'operasional', 'priority' => 'medium', 'status' => 'pending',
            'team_id' => 1, 'owner_id' => $ownerId, 'target_date' => $targetDate,
        ]);
    }

    /** @test */
    public function monthly_pending_lists_overdue_and_due_this_month_but_excludes_next_month(): void
    {
        $user = $this->actingAsSuperAdmin(); // is_task_manager() → adminReport

        $overdue = $this->makeTask('Overdue task', now()->copy()->subDays(40), $user->id);
        $dueThisMonth = $this->makeTask('Due this month', now()->copy()->endOfMonth(), $user->id);
        $noDeadline = $this->makeTask('No deadline', null, $user->id);
        $future = $this->makeTask('Next month task', now()->copy()->endOfMonth()->addDays(3), $user->id);

        $data = app(ReportService::class)->generate($user, (int) now()->month, (int) now()->year);

        $ids = collect($data['pending'])->pluck('id')->all();

        $this->assertContains($overdue->id, $ids, 'overdue task must be listed');
        $this->assertContains($dueThisMonth->id, $ids, 'task due this month must be listed');
        $this->assertContains($noDeadline->id, $ids, 'open task without a deadline must be listed');
        $this->assertNotContains($future->id, $ids, 'task due next month must NOT be listed');

        $rows = collect($data['pending'])->keyBy('id');
        $this->assertTrue($rows[$overdue->id]['overdue'], 'past-due task must be flagged overdue');
        $this->assertFalse($rows[$noDeadline->id]['overdue'], 'no-deadline task is not overdue');
    }
}
