<?php

namespace Tests\Feature;

use App\Http\Livewire\Commercial\Contract\Add as ContractAdd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Contract CRUD. */
class ContractCrudTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $u = User::create(['name' => 'U', 'email' => uniqid('u') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        $this->actingAs($u->fresh());

        return $u->fresh();
    }

    /** @test */
    public function it_creates_a_draft_contract(): void
    {
        $this->user();
        Livewire::test(ContractAdd::class)
            ->set('title', 'Kontrak Kerja Sama A')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contracts', ['title' => 'Kontrak Kerja Sama A', 'status' => 'draft']);
    }

    /** @test */
    public function contract_requires_a_title(): void
    {
        $this->user();
        Livewire::test(ContractAdd::class)->call('create')->assertHasErrors(['title']);
    }
}
