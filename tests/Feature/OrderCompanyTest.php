<?php

namespace Tests\Feature;

use App\Http\Livewire\Order\Add as OrderAdd;
use App\Models\Company;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/** Order: an admin can pick ANY company from Company Settings (Company::all), not only their own. */
class OrderCompanyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function order_form_lists_all_companies_for_a_role_user(): void
    {
        $user = User::create([
            'name' => 'Admin', 'email' => uniqid('a') . '@test.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
        $role = Role::create(['name' => 'Admin', 'type' => 'admin', 'role_for' => 'team', 'description' => 'Admin']);
        RoleUser::create([
            'user_id' => $user->id, 'role_id' => $role->id, 'team_id' => 1,
            'status' => 'active', 'active' => 1, 'working_id' => 'A',
        ]);
        $this->actingAs($user->fresh());

        // Companies owned by various users (incl. global user_id = 0).
        // companies table has many NOT NULL columns, so insert directly.
        $makeCompany = function (string $name, int $ownerId) {
            DB::table('companies')->insert([
                'name' => $name, 'code' => substr($name, 0, 3), 'tax_id' => '0', 'post_code' => '0',
                'province' => '-', 'city' => '-', 'address' => '-', 'logo' => '-', 'person_in_charge' => '-',
                'user_id' => $ownerId, 'created_at' => now(), 'updated_at' => now(),
            ]);
        };
        $makeCompany('Telixcel Project', 0);
        $makeCompany('Ertt', 0);
        $makeCompany('Other Owner Co', 999);

        Livewire::test(OrderAdd::class)
            ->assertViewHas('companies', fn ($companies) => $companies->count() === 3)
            ->assertSee('Telixcel Project')
            ->assertSee('Ertt')
            ->assertSee('Other Owner Co');
    }
}
