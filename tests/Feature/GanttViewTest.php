<?php

namespace Tests\Feature;

use App\Http\Livewire\Dashboard\GanttView;
use App\Models\Project;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Dashboard Gantt chart: project -> task -> subtask rows over a month-scaled timeline. */
class GanttViewTest extends TestCase
{
    use RefreshDatabase;

    private function bootUser(): User
    {
        $u = User::create(['name' => 'U', 'email' => uniqid('u') . '@test.com', 'password' => bcrypt('x')]);
        $team = Team::forceCreate(['user_id' => $u->id, 'name' => 'T', 'personal_team' => true]);
        $u->forceFill(['current_team_id' => $team->id])->save();
        $role = Role::create(['name' => 'Super Admin', 'type' => 'admin', 'role_for' => 'admin', 'description' => 'SA']);
        RoleUser::create(['user_id' => $u->id, 'role_id' => $role->id, 'team_id' => $team->id, 'status' => 'active', 'active' => 1, 'working_id' => 'SA']);
        $this->actingAs(User::find($u->id));

        return User::find($u->id);
    }

    private function task(array $attr): Task
    {
        $t = Task::create(array_merge([
            'title' => 'Task', 'type' => 'operasional', 'status' => 'progress',
            'target_date' => now()->addDays(5),
        ], $attr));
        // created_at drives the bar start; force it into the past for a visible bar
        $t->forceFill(['created_at' => $attr['created_at'] ?? now()->subDays(10)])->save();

        return $t;
    }

    /** @test */
    public function it_builds_project_task_and_subtask_rows(): void
    {
        $u = $this->bootUser();
        $project = Project::create(['name' => 'Proyek A', 'type' => 'selling', 'status' => 'active', 'team_id' => $u->currentTeam->id]);
        $parent = $this->task(['project_id' => $project->id, 'title' => 'Main Task', 'team_id' => $u->currentTeam->id, 'owner_id' => $u->id]);
        $this->task(['project_id' => $project->id, 'parent_id' => $parent->id, 'title' => 'Sub Task', 'status' => 'pending', 'team_id' => $u->currentTeam->id, 'owner_id' => $u->id]);

        $rows = collect(Livewire::test(GanttView::class)->get('rows'));

        $this->assertTrue($rows->contains(fn ($r) => $r['kind'] === 'project' && $r['label'] === 'Proyek A'));
        $this->assertTrue($rows->contains(fn ($r) => $r['kind'] === 'main_task' && $r['label'] === 'Main Task'));
        $this->assertTrue($rows->contains(fn ($r) => $r['kind'] === 'sub_task' && $r['label'] === 'Sub Task'));
    }

    /** @test */
    public function task_bar_has_a_position_within_the_window(): void
    {
        $u = $this->bootUser();
        $project = Project::create(['name' => 'Proyek B', 'type' => 'selling', 'status' => 'active', 'team_id' => $u->currentTeam->id]);
        $this->task(['project_id' => $project->id, 'title' => 'Bar Task', 'team_id' => $u->currentTeam->id, 'owner_id' => $u->id]);

        $rows = collect(Livewire::test(GanttView::class)->get('rows'));
        $task = $rows->firstWhere('label', 'Bar Task');

        $this->assertNotNull($task['bar']);
        $this->assertGreaterThan(0, $task['bar']['width']);
        $this->assertGreaterThanOrEqual(0, $task['bar']['left']);
    }

    /** @test */
    public function scale_controls_change_the_month_columns(): void
    {
        $this->bootUser();

        $c = Livewire::test(GanttView::class)->assertSet('scale', 12);
        $this->assertCount(12, $c->get('months'));

        $c->call('setScale', 3)->assertSet('scale', 3);
        $this->assertCount(3, $c->get('months'));

        $c->call('setScale', 6)->assertSet('scale', 6);
        $this->assertCount(6, $c->get('months'));

        $c->call('next')->call('prev')->call('today')->assertHasNoErrors();
    }

    /** @test */
    public function only_my_type_and_invited_projects_are_shown(): void
    {
        // A regular operasional user only sees operasional tasks in projects they belong to.
        $u = User::create(['name' => 'Op', 'email' => uniqid('u') . '@test.com', 'password' => bcrypt('x')]);
        $team = Team::forceCreate(['user_id' => $u->id, 'name' => 'T', 'personal_team' => true]);
        $u->forceFill(['current_team_id' => $team->id])->save();
        $role = Role::create(['name' => 'Operational Manager', 'type' => 'operasional', 'role_for' => 'team', 'description' => 'Op']);
        RoleUser::create(['user_id' => $u->id, 'role_id' => $role->id, 'team_id' => $team->id, 'status' => 'active', 'active' => 1, 'working_id' => 'OP']);
        $this->actingAs(User::find($u->id));
        $u = User::find($u->id);

        $invited = Project::create(['name' => 'Invited', 'type' => 'selling', 'status' => 'active', 'team_id' => $team->id]);
        $invited->members()->attach($u->id);
        $other = Project::create(['name' => 'Not Invited', 'type' => 'selling', 'status' => 'active', 'team_id' => $team->id]);

        $this->task(['project_id' => $invited->id, 'title' => 'Mine Op', 'type' => 'operasional', 'team_id' => $team->id, 'owner_id' => $u->id]);
        $this->task(['project_id' => $invited->id, 'title' => 'Finance Task', 'type' => 'finance', 'team_id' => $team->id, 'owner_id' => $u->id]);
        $this->task(['project_id' => $other->id, 'title' => 'Outsider', 'type' => 'operasional', 'team_id' => $team->id, 'owner_id' => $u->id]);

        $labels = collect(Livewire::test(GanttView::class)->get('rows'))->pluck('label')->all();

        $this->assertContains('Mine Op', $labels);
        $this->assertNotContains('Finance Task', $labels);  // wrong type
        $this->assertNotContains('Outsider', $labels);      // not invited
    }
}
