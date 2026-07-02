<?php

namespace Tests\Feature;

use App\Http\Livewire\Project\AddCustomer;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

/** Project <-> Client (project_client pivot) via AddCustomer component. */
class ProjectClientTest extends TestCase
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

    private function project(): Project
    {
        return Project::create([
            'name' => 'P', 'type' => 'selling', 'status' => 'draft',
            'team_id' => 1,
        ]);
    }

    private function client(string $name = 'C'): Client
    {
        return Client::create([
            'uuid' => (string) Str::uuid(), 'sender' => 'S', 'name' => $name,
            'phone' => '08123', 'email' => $name . '@test.com', 'user_id' => 0,
        ]);
    }

    /** @test */
    public function attaching_an_existing_client_adds_it_to_project_clients(): void
    {
        $this->actor();
        $project = $this->project();
        $client = $this->client();

        Livewire::test(AddCustomer::class, ['id' => $project->id])
            ->set('selectedClient', $client->id)
            ->call('attachExisting')
            ->assertHasNoErrors();

        $this->assertTrue($project->fresh()->clients->contains($client->id));
        $this->assertDatabaseHas('project_client', [
            'project_id' => $project->id, 'client_id' => $client->id,
        ]);
    }

    /** @test */
    public function create_and_attach_makes_a_new_client_and_links_it(): void
    {
        $this->actor();
        $project = $this->project();

        Livewire::test(AddCustomer::class, ['id' => $project->id])
            ->set('newClient.name', 'New Co')
            ->set('newClient.phone', '0899')
            ->set('newClient.email', 'new@test.com')
            ->set('newClient.sender', 'S')
            ->call('createAndAttach')
            ->assertHasNoErrors();

        $client = Client::where('name', 'New Co')->first();
        $this->assertNotNull($client);
        $this->assertTrue($project->fresh()->clients->contains($client->id));
    }

    /** @test */
    public function detaching_a_client_removes_it_from_project_clients(): void
    {
        $this->actor();
        $project = $this->project();
        $client = $this->client();
        $project->clients()->attach($client->id);

        $this->assertTrue($project->fresh()->clients->contains($client->id));

        Livewire::test(AddCustomer::class, ['id' => $project->id])
            ->call('confirmDetachClient', $client->id)
            ->assertSet('detachId', $client->id)
            ->call('detachClient')
            ->assertHasNoErrors();

        $this->assertFalse($project->fresh()->clients->contains($client->id));
        $this->assertDatabaseMissing('project_client', [
            'project_id' => $project->id, 'client_id' => $client->id,
        ]);
    }

    /** @test */
    public function attach_existing_requires_a_valid_client(): void
    {
        $this->actor();
        $project = $this->project();

        Livewire::test(AddCustomer::class, ['id' => $project->id])
            ->set('selectedClient', null)
            ->call('attachExisting')
            ->assertHasErrors(['selectedClient']);
    }

    /** @test */
    public function attaching_twice_does_not_duplicate_the_pivot_row(): void
    {
        $this->actor();
        $project = $this->project();
        $client = $this->client();

        $component = Livewire::test(AddCustomer::class, ['id' => $project->id])
            ->set('selectedClient', $client->id)
            ->call('attachExisting');

        $component->set('selectedClient', $client->id)->call('attachExisting');

        $this->assertSame(1, $project->fresh()->clients()->count());
    }
}
