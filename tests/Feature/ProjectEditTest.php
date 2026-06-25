<?php

namespace Tests\Feature;

use App\Http\Livewire\Project\Edit;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Project edit via the Edit Livewire component. */
class ProjectEditTest extends TestCase
{
    use RefreshDatabase;

    private function actor(): User
    {
        $user = User::create([
            'name' => 'U', 'email' => uniqid('u') . '@test.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
        $user->ownedTeams()->create(['name' => 'Test', 'slug' => uniqid('t'), 'personal_team' => false])
            ->forceFill(['id' => 1])->save();
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    private function project(): Project
    {
        return Project::create([
            'name' => 'Original', 'type' => 'selling', 'status' => 'draft',
            'entity_party' => '1', 'team_id' => 1,
        ]);
    }

    /** @test */
    public function it_updates_project_fields(): void
    {
        $this->actor();
        $project = $this->project();

        Livewire::test(Edit::class, ['uuid' => $project->id])
            ->set('name', 'Updated Name')
            ->set('type', 'saas')
            ->set('entity', '2')
            ->set('status', 'active')
            ->call('update', $project->id)
            ->assertHasNoErrors();

        $fresh = $project->fresh();
        $this->assertSame('Updated Name', $fresh->name);
        $this->assertSame('saas', $fresh->type);
        $this->assertSame('2', $fresh->entity_party);
        $this->assertSame('active', $fresh->status);
    }

    /** @test */
    public function it_mounts_with_existing_values(): void
    {
        $this->actor();
        $project = $this->project();

        Livewire::test(Edit::class, ['uuid' => $project->id])
            ->assertSet('name', 'Original')
            ->assertSet('type', 'selling')
            ->assertSet('entity', '1')
            ->assertSet('status', 'draft');
    }

    /** @test */
    public function it_accepts_the_referral_type(): void
    {
        $this->actor();
        $project = $this->project();

        Livewire::test(Edit::class, ['uuid' => $project->id])
            ->set('type', 'referral')
            ->call('update', $project->id)
            ->assertHasNoErrors();

        $this->assertSame('referral', $project->fresh()->type);
    }

    /** @test */
    public function name_is_required(): void
    {
        $this->actor();
        $project = $this->project();

        Livewire::test(Edit::class, ['uuid' => $project->id])
            ->set('name', '')
            ->call('update', $project->id)
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function required_fields_are_validated(): void
    {
        $this->actor();
        $project = $this->project();

        Livewire::test(Edit::class, ['uuid' => $project->id])
            ->set('name', '')
            ->set('status', '')
            ->set('entity', '')
            ->set('type', '')
            ->call('update', $project->id)
            ->assertHasErrors(['name', 'status', 'entity', 'type']);
    }
}
