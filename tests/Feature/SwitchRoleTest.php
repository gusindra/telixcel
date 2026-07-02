<?php

namespace Tests\Feature;

use App\Http\Livewire\SwitchRole;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * SwitchRole::updateRole($pivotId) must activate exactly one RoleUser row
 * (active=1) for the user and deactivate every other row.
 */
class SwitchRoleTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        $u = User::create([
            'name' => 'U', 'email' => uniqid('u') . '@test.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
        $this->actingAs($u->fresh());

        return $u->fresh();
    }

    private function makeRole(string $name): Role
    {
        return Role::create([
            'name' => $name, 'type' => 'admin', 'role_for' => 'team', 'description' => 'x',
        ]);
    }

    private function pivot(User $u, Role $r, $active): RoleUser
    {
        return RoleUser::create([
            'user_id' => $u->id, 'role_id' => $r->id, 'team_id' => 1,
            'status' => 'active', 'active' => $active, 'working_id' => 'A',
        ]);
    }

    /** @test */
    public function switching_activates_exactly_one_role_and_deactivates_others(): void
    {
        $user = $this->makeUser();

        $roleA = $this->makeRole('Admin');
        $roleB = $this->makeRole('Finance');
        $roleC = $this->makeRole('Operasional');

        // A starts active, B and C inactive.
        $pivotA = $this->pivot($user, $roleA, 1);
        $pivotB = $this->pivot($user, $roleB, null);
        $pivotC = $this->pivot($user, $roleC, null);

        Livewire::test(SwitchRole::class)
            ->call('updateRole', $pivotB->id);

        // Exactly one active row for this user.
        $this->assertEquals(
            1,
            RoleUser::where('user_id', $user->id)->where('active', 1)->count()
        );

        // The chosen pivot is the active one.
        $this->assertEquals(1, $pivotB->fresh()->active);

        // The others are deactivated.
        $this->assertNull($pivotA->fresh()->active);
        $this->assertNull($pivotC->fresh()->active);
    }

    /** @test */
    public function switching_again_moves_the_active_flag(): void
    {
        $user = $this->makeUser();

        $roleA = $this->makeRole('Admin');
        $roleB = $this->makeRole('Finance');

        $pivotA = $this->pivot($user, $roleA, 1);
        $pivotB = $this->pivot($user, $roleB, null);

        // Switch to B.
        Livewire::test(SwitchRole::class)->call('updateRole', $pivotB->id);
        $this->assertEquals(1, $pivotB->fresh()->active);
        $this->assertNull($pivotA->fresh()->active);

        // Switch back to A.
        Livewire::test(SwitchRole::class)->call('updateRole', $pivotA->id);
        $this->assertEquals(1, $pivotA->fresh()->active);
        $this->assertNull($pivotB->fresh()->active);

        $this->assertEquals(
            1,
            RoleUser::where('user_id', $user->id)->where('active', 1)->count()
        );
    }
}
