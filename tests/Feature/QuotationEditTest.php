<?php

namespace Tests\Feature;

use App\Http\Livewire\Commercial\Quotation\Edit as QuotationEdit;
use App\Models\Quotation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Quotation edit — update persists the changed title and quote number. */
class QuotationEditTest extends TestCase
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

    private function quotation(User $u): Quotation
    {
        return Quotation::create([
            'type' => 'project', 'title' => 'Penawaran Lama', 'valid_day' => 30, 'date' => now(),
            'user_id' => $u->id, 'status' => 'draft', 'quote_no' => 'Q-001',
        ]);
    }

    /** @test */
    public function it_updates_a_quotation(): void
    {
        $u = $this->user();
        $q = $this->quotation($u);

        Livewire::test(QuotationEdit::class, ['code' => $q->id])
            ->set('name', 'Penawaran Baru')
            ->set('quoteNo', 'Q-002')
            ->call('update', $q->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('quotations', ['id' => $q->id, 'title' => 'Penawaran Baru', 'quote_no' => 'Q-002']);
    }

    /** @test */
    public function quote_no_is_required(): void
    {
        $u = $this->user();
        $q = $this->quotation($u);

        Livewire::test(QuotationEdit::class, ['code' => $q->id])
            ->set('quoteNo', '')
            ->call('update', $q->id)
            ->assertHasErrors(['quoteNo']);
    }
}
