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

/**
 * Client / Customer CRUD.
 *
 * Coverage:
 *  - Model-level create/update of a Client (clients.sender + clients.uuid are NOT NULL).
 *  - Livewire-level create via App\Http\Livewire\Project\AddCustomer::createAndAttach(),
 *    which is the component that builds a Client (uuid + sender) and attaches it to a project.
 */
class ClientCrudTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::create([
            'name' => 'U', 'email' => uniqid('u') . '@test.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    /** @test */
    public function it_creates_a_client_at_model_level(): void
    {
        $this->actingUser();

        $client = Client::create([
            'uuid'    => (string) Str::uuid(),
            'sender'  => 'Sales',
            'title'   => 'PT Maju Jaya',
            'name'    => 'Andi Wijaya',
            'phone'   => '08123456789',
            'email'   => 'andi@maju.com',
            'address' => 'Jakarta',
            'user_id' => 1,
        ]);

        $this->assertNotNull($client->id);
        $this->assertDatabaseHas('clients', [
            'name'   => 'Andi Wijaya',
            'sender' => 'Sales',
            'title'  => 'PT Maju Jaya',
            'email'  => 'andi@maju.com',
        ]);
    }

    /** @test */
    public function it_updates_an_existing_client(): void
    {
        $this->actingUser();

        $client = Client::create([
            'uuid' => (string) Str::uuid(), 'sender' => 'Sales',
            'name' => 'Old Name', 'phone' => '0811', 'user_id' => 1,
        ]);

        $client->update(['name' => 'New Name', 'phone' => '0822']);

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'New Name', 'phone' => '0822']);
        $this->assertDatabaseMissing('clients', ['id' => $client->id, 'name' => 'Old Name']);
    }

    /** @test */
    public function add_customer_component_creates_and_attaches_a_client_to_a_project(): void
    {
        $user = $this->actingUser();

        // Minimal project. Project table columns used in mount(): customer_name,
        // customer_address, contact_id (all read; nullable in practice).
        $project = Project::create([
            'name'    => 'Proyek A',
            'user_id' => $user->id,
        ]);

        Livewire::test(AddCustomer::class, ['id' => $project->id])
            ->set('newClient.name', 'Citra Lestari')
            ->set('newClient.phone', '08987654321')
            ->set('newClient.email', 'citra@example.com')
            ->set('newClient.sender', 'Marketing')
            ->set('newClient.title', 'CV Sukses')
            ->call('createAndAttach')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'name'  => 'Citra Lestari',
            'phone' => '08987654321',
            'email' => 'citra@example.com',
        ]);

        // It must also be attached to the project via the project_client pivot.
        $client = Client::where('name', 'Citra Lestari')->first();
        $this->assertNotNull($client);
        $this->assertTrue($project->fresh()->clients->contains('id', $client->id));
    }

    /** @test */
    public function add_customer_component_requires_a_client_name(): void
    {
        $user = $this->actingUser();
        $project = Project::create(['name' => 'Proyek B', 'user_id' => $user->id]);

        Livewire::test(AddCustomer::class, ['id' => $project->id])
            ->set('newClient.phone', '0811')
            ->call('createAndAttach')
            ->assertHasErrors(['newClient.name']);
    }
}
