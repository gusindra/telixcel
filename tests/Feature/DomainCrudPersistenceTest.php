<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Order;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Persistence for modules whose create flow is gated by a policy (Project, Order):
 * verify the records save with the dropdown type values + required data.
 */
class DomainCrudPersistenceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function project_persists_with_each_dropdown_type(): void
    {
        foreach (['selling', 'saas', 'referral'] as $type) {
            $p = Project::create([
                'name' => "Proyek $type", 'type' => $type, 'entity_party' => '1',
                'status' => 'draft', 'team_id' => 1,
            ]);
            $this->assertDatabaseHas('projects', ['id' => $p->id, 'type' => $type, 'entity_party' => '1']);
        }
    }

    /** @test */
    public function order_persists_with_each_dropdown_type(): void
    {
        foreach (['selling', 'saas', 'referral'] as $type) {
            $o = Order::create([
                'type' => $type, 'entity_party' => '1', 'status' => 'draft', 'user_id' => 1,
            ]);
            $this->assertDatabaseHas('orders', ['id' => $o->id, 'type' => $type, 'status' => 'draft']);
        }
    }

    /** @test */
    public function company_persists_with_required_fields(): void
    {
        $c = Company::create([
            'name' => 'PT Telixcel', 'code' => 'TLX', 'tax_id' => '01.234', 'post_code' => '10110',
            'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'address' => 'Jl. Sudirman',
            'logo' => '-', 'person_in_charge' => 'Budi', 'user_id' => 0,
        ]);
        $this->assertDatabaseHas('companies', ['id' => $c->id, 'name' => 'PT Telixcel', 'code' => 'TLX']);
    }
}
