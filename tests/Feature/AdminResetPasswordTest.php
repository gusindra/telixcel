<?php

namespace Tests\Feature;

use App\Http\Livewire\User\Profile;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

/** Admin "Update Password" feature on the user-management profile page. */
class AdminResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
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

    private function targetUser(): User
    {
        return User::create([
            'name' => 'Target', 'email' => uniqid('t') . '@test.com',
            'password' => bcrypt('oldpassword'), 'current_team_id' => 1,
        ]);
    }

    /** @test */
    public function admin_can_reset_another_users_password(): void
    {
        $this->admin();
        $target = $this->targetUser();

        Livewire::test(Profile::class, ['user' => $target])
            ->set('password', 'newsecret123')
            ->set('password_confirmation', 'newsecret123')
            ->call('savePassword', $target->id)
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('newsecret123', $target->fresh()->password));
    }

    /** @test */
    public function password_must_be_confirmed(): void
    {
        $this->admin();
        $target = $this->targetUser();

        Livewire::test(Profile::class, ['user' => $target])
            ->set('password', 'newsecret123')
            ->set('password_confirmation', 'different456')
            ->call('savePassword', $target->id)
            ->assertHasErrors(['password']);

        $this->assertTrue(Hash::check('oldpassword', $target->fresh()->password));
    }

    /** @test */
    public function password_must_be_at_least_8_characters(): void
    {
        $this->admin();
        $target = $this->targetUser();

        Livewire::test(Profile::class, ['user' => $target])
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('savePassword', $target->id)
            ->assertHasErrors(['password']);
    }
}
