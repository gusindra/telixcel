<?php

namespace Tests\Feature;

use App\Models\Commision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Commission (Commision) — type matches the dropdown (percentage / price); status (unpaid / paid / done). */
class CommissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_defaults_status_to_draft(): void
    {
        $c = Commision::create([
            'model' => 'Order', 'model_id' => 1, 'client_id' => 1,
            'type' => 'percentage', 'ratio' => 10, 'total' => 50000,
        ]);

        $this->assertDatabaseHas('commisions', ['id' => $c->id, 'status' => 'draft']);
    }

    /** @test */
    public function it_persists_each_dropdown_type(): void
    {
        foreach (['percentage', 'price'] as $type) {
            $c = Commision::create([
                'model' => 'Order', 'model_id' => 1, 'client_id' => 1,
                'type' => $type, 'ratio' => 5, 'total' => 1000,
            ]);
            $this->assertDatabaseHas('commisions', ['id' => $c->id, 'type' => $type]);
        }
    }

    /** @test */
    public function it_persists_each_dropdown_status(): void
    {
        foreach (['unpaid', 'paid', 'done'] as $status) {
            $c = Commision::create([
                'model' => 'Order', 'model_id' => 1, 'client_id' => 1,
                'type' => 'price', 'ratio' => 1, 'total' => 1, 'status' => $status,
            ]);
            $this->assertDatabaseHas('commisions', ['id' => $c->id, 'status' => $status]);
        }
    }
}
