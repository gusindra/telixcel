<?php

namespace Tests\Feature;

use App\Http\Livewire\Project\AddAgent;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Project <-> User members (project_user pivot) via AddAgent component. */
class ProjectMemberTest extends TestCase
{
    use RefreshDatabase;

    private function actor(): User
    {
        $user = User::create([
            'name' => 'U', 'email' => uniqid('u') . '@test.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    private function makeUser(string $name): User
    {
        return User::create([
            'name' => $name, 'email' => uniqid('m') . '@test.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
    }

    private function project(): Project
    {
        return Project::create([
            'name' => 'P', 'type' => 'selling', 'status' => 'draft',
            'team_id' => 1,
        ]);
    }

    /** @test */
    public function assigning_a_user_adds_them_to_project_members(): void
    {
        $this->actor();
        $project = $this->project();
        $member = $this->makeUser('Agent One');

        Livewire::test(AddAgent::class, ['id' => $project->id])
            ->set('selectedUser', $member->id)
            ->call('assign')
            ->assertHasNoErrors();

        $this->assertTrue($project->fresh()->members->contains($member->id));
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id, 'user_id' => $member->id,
        ]);
    }

    /** @test */
    public function unassigning_a_user_removes_them_from_project_members(): void
    {
        $this->actor();
        $project = $this->project();
        $member = $this->makeUser('Agent Two');
        $project->members()->attach($member->id);

        $this->assertTrue($project->fresh()->members->contains($member->id));

        Livewire::test(AddAgent::class, ['id' => $project->id])
            ->call('confirmUnassign', $member->id)
            ->assertSet('unassignId', $member->id)
            ->call('unassign')
            ->assertHasNoErrors();

        $this->assertFalse($project->fresh()->members->contains($member->id));
        $this->assertDatabaseMissing('project_user', [
            'project_id' => $project->id, 'user_id' => $member->id,
        ]);
    }

    /** @test */
    public function assign_requires_a_valid_user(): void
    {
        $this->actor();
        $project = $this->project();

        Livewire::test(AddAgent::class, ['id' => $project->id])
            ->set('selectedUser', null)
            ->call('assign')
            ->assertHasErrors(['selectedUser']);
    }

    /** @test */
    public function assigning_twice_does_not_duplicate_the_pivot_row(): void
    {
        $this->actor();
        $project = $this->project();
        $member = $this->makeUser('Agent Three');

        $component = Livewire::test(AddAgent::class, ['id' => $project->id])
            ->set('selectedUser', $member->id)
            ->call('assign');

        $component->set('selectedUser', $member->id)->call('assign');

        $this->assertSame(1, $project->fresh()->members()->count());
    }
}
