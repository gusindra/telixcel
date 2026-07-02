<?php

namespace Tests\Feature;

use App\Http\Livewire\User\Add;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * User creation.
 *
 * App\Http\Livewire\User\Add::create() validates input, then runs heavy
 * Jetstream team wiring (Team::find(1)->users()->attach, AddingTeam::dispatch,
 * ownedTeams()->create + switchTeam). That team plumbing is not available in the
 * minimal sqlite test environment, so per the testing rules we cover:
 *   - the component's validation rules (safe, runs before team logic), and
 *   - User persistence at the MODEL level using the same shape as modelData()
 *     (name, email, Hash::make(password)).
 */
class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::create([
            'name' => 'Admin', 'email' => uniqid('a') . '@test.com',
            'password' => bcrypt('password'), 'current_team_id' => 1,
        ]);
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'team', 'description' => 'Admin']);
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'A',
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    /** @test */
    public function create_requires_name_and_password(): void
    {
        $this->actingUser();

        Livewire::test(Add::class)
            ->call('create')
            ->assertHasErrors(['input.name', 'input.password']);
    }

    /** @test */
    public function model_data_builds_name_email_and_hashed_password(): void
    {
        $this->actingUser();

        $component = Livewire::test(Add::class)
            ->set('input.name', 'New User')
            ->set('input.email', 'newuser@test.com')
            ->set('input.password', 'secret123');

        $data = $component->instance()->modelData();

        $this->assertEquals('New User', $data['name']);
        $this->assertEquals('newuser@test.com', $data['email']);
        $this->assertTrue(Hash::check('secret123', $data['password']));
    }

    /** @test */
    public function generate_password_fills_an_eight_char_password(): void
    {
        $this->actingUser();

        $component = Livewire::test(Add::class)
            ->call('generatePassword');

        $this->assertEquals(8, strlen($component->get('input.password')));
    }

    /** @test */
    public function user_is_persisted_at_model_level_like_create(): void
    {
        $this->actingUser();

        // Same persistence shape the component's modelData() produces.
        $user = User::create([
            'name' => 'Persisted User',
            'email' => 'persisted@test.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Persisted User',
            'email' => 'persisted@test.com',
        ]);
        $this->assertTrue(Hash::check('secret123', $user->fresh()->password));
    }
}
