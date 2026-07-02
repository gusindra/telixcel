<?php

namespace Tests\Feature;

use App\Http\Livewire\Role\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Role CRUD — type must match the dropdown (admin / finance / operasional). */
class RoleCrudTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $u = User::create(['name' => 'U', 'email' => uniqid('u') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        $this->actingAs($u->fresh());

        return $u->fresh();
    }

    /** @test */
    public function it_creates_a_role_for_each_valid_type(): void
    {
        $this->user();
        foreach (['admin', 'finance', 'operasional'] as $type) {
            Livewire::test(Roles::class)
                ->set('type', $type)
                ->set('name', ucfirst($type) . ' Role')
                ->set('description', 'desc')
                ->call('create')
                ->assertHasNoErrors();

            $this->assertDatabaseHas('roles', [
                'name' => ucfirst($type) . ' Role', 'type' => $type, 'role_for' => 'team',
            ]);
        }
    }

    /** @test */
    public function role_requires_type_name_and_description(): void
    {
        $this->user();
        Livewire::test(Roles::class)->call('create')->assertHasErrors(['type', 'name', 'description']);
    }
}
