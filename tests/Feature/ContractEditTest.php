<?php

namespace Tests\Feature;

use App\Http\Livewire\Commercial\Contract\Add as ContractAdd;
use App\Http\Livewire\Commercial\Contract\Edit as ContractEdit;
use App\Models\Contract;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Contract edit — update persists the changed title. */
class ContractEditTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $u = User::create(['name' => 'U', 'email' => uniqid('u') . '@test.com', 'password' => bcrypt('x')]);
        $team = Team::forceCreate(['user_id' => $u->id, 'name' => 'Team', 'personal_team' => true]);
        $u->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs(User::find($u->id));

        return User::find($u->id);
    }

    private function contract(string $title): Contract
    {
        // create through the proven Add flow so every NOT NULL column is satisfied
        Livewire::test(ContractAdd::class)->set('title', $title)->call('create')->assertHasNoErrors();

        return Contract::where('title', $title)->firstOrFail();
    }

    /** @test */
    public function it_updates_a_contract(): void
    {
        $this->user();
        $contract = $this->contract('Kontrak Lama');

        Livewire::test(ContractEdit::class, ['code' => $contract->id])
            ->set('input.title', 'Kontrak Baru')
            ->call('update', $contract->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'title' => 'Kontrak Baru']);
    }

    /** @test */
    public function contract_title_is_required_on_update(): void
    {
        $this->user();
        $contract = $this->contract('Kontrak X');

        Livewire::test(ContractEdit::class, ['code' => $contract->id])
            ->set('input.title', '')
            ->call('update', $contract->id)
            ->assertHasErrors(['input.title']);
    }
}
