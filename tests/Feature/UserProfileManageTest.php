<?php

namespace Tests\Feature;

use App\Http\Livewire\User\Profile;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * User profile management via App\Http\Livewire\User\Profile.
 *  - saveUser()  updates name / nick / email / phone_no
 *  - saveRole()  activates the chosen role (exactly one RoleUser active=1)
 * (savePassword is covered by AdminResetPasswordTest — not retested here.)
 */
class UserProfileManageTest extends TestCase
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

    private function targetUser(): User
    {
        return User::create([
            'name' => 'Old Name', 'email' => uniqid('t') . '@test.com',
            'password' => bcrypt('password'), 'current_team_id' => 1,
            'nick' => 'oldnick', 'phone_no' => '111',
        ]);
    }

    /** @test */
    public function save_user_updates_name_nick_email_and_phone(): void
    {
        $this->actingUser();
        $target = $this->targetUser();
        $newEmail = uniqid('new') . '@test.com';

        Livewire::test(Profile::class, ['user' => $target])
            ->set('inputuser.name', 'New Name')
            ->set('inputuser.nick', 'newnick')
            ->set('inputuser.email', $newEmail)
            ->set('inputuser.phone', '999888')
            ->call('saveUser', $target->id)
            ->assertHasNoErrors();

        $fresh = $target->fresh();
        $this->assertEquals('New Name', $fresh->name);
        $this->assertEquals('newnick', $fresh->nick);
        $this->assertEquals($newEmail, $fresh->email);
        $this->assertEquals('999888', $fresh->phone_no);
    }

    /** @test */
    public function save_user_persists_to_database(): void
    {
        $this->actingUser();
        $target = $this->targetUser();
        $newEmail = uniqid('db') . '@test.com';

        Livewire::test(Profile::class, ['user' => $target])
            ->set('inputuser.name', 'Persisted')
            ->set('inputuser.nick', 'pnick')
            ->set('inputuser.email', $newEmail)
            ->set('inputuser.phone', '12345')
            ->call('saveUser', $target->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Persisted',
            'nick' => 'pnick',
            'email' => $newEmail,
            'phone_no' => '12345',
        ]);
    }

    /** @test */
    public function save_role_activates_the_chosen_role_only(): void
    {
        $this->actingUser();
        $target = $this->targetUser();

        $roleA = Role::create(['name' => 'Finance', 'type' => 'finance', 'role_for' => 'team', 'description' => 'x']);
        $roleB = Role::create(['name' => 'Operasional', 'type' => 'operasional', 'role_for' => 'team', 'description' => 'x']);

        // Existing active pivot for roleA.
        $pivotA = RoleUser::create([
            'user_id' => $target->id, 'role_id' => $roleA->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'A',
        ]);

        Livewire::test(Profile::class, ['user' => $target])
            ->set('selectedRole', $roleB->id)
            ->call('saveRole')
            ->assertHasNoErrors();

        // Exactly one active row for this user.
        $this->assertEquals(
            1,
            RoleUser::where('user_id', $target->id)->where('active', 1)->count()
        );

        // The chosen role is the active one.
        $this->assertDatabaseHas('role_user', [
            'user_id' => $target->id, 'role_id' => $roleB->id, 'active' => 1,
        ]);

        // The previously active role is deactivated.
        $this->assertNull($pivotA->fresh()->active);
    }

    /** @test */
    public function save_role_creates_pivot_when_none_exists(): void
    {
        $this->actingUser();
        $target = $this->targetUser();

        $role = Role::create(['name' => 'Finance', 'type' => 'finance', 'role_for' => 'team', 'description' => 'x']);

        $this->assertEquals(0, RoleUser::where('user_id', $target->id)->count());

        Livewire::test(Profile::class, ['user' => $target])
            ->set('selectedRole', $role->id)
            ->call('saveRole')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('role_user', [
            'user_id' => $target->id, 'role_id' => $role->id, 'active' => 1, 'status' => 'active',
        ]);
        $this->assertEquals(
            1,
            RoleUser::where('user_id', $target->id)->where('active', 1)->count()
        );
    }

    /** @test */
    public function save_role_requires_an_existing_role(): void
    {
        $this->actingUser();
        $target = $this->targetUser();

        Livewire::test(Profile::class, ['user' => $target])
            ->set('selectedRole', 999999)
            ->call('saveRole')
            ->assertHasErrors(['selectedRole']);
    }
}
