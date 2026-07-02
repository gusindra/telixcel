<?php

namespace Tests\Feature;

use App\Http\Livewire\Commercial\Quotation\Add as QuotationAdd;
use App\Models\OrderProduct;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Quotation auto-fill: a new quotation copies line items from the latest existing one. */
class QuotationAutoFillTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::create([
            'name' => 'U', 'email' => uniqid('u') . '@test.com',
            'password' => bcrypt('x'), 'current_team_id' => 1,
        ]);
        $this->actingAs($user->fresh());

        return $user->fresh();
    }

    /** @test */
    public function new_quotation_copies_items_from_the_latest_quotation(): void
    {
        $this->actingUser();

        $prior = Quotation::create([
            'type' => 'project', 'title' => 'Prior', 'valid_day' => 30,
            'date' => now(), 'user_id' => 1, 'status' => 'draft',
        ]);
        OrderProduct::create(['name' => 'Item A', 'model' => 'Quotation', 'model_id' => $prior->id, 'qty' => 1, 'unit' => 'pcs', 'price' => 1000, 'user_id' => 1]);
        OrderProduct::create(['name' => 'Item B', 'model' => 'Quotation', 'model_id' => $prior->id, 'qty' => 2, 'unit' => 'pcs', 'price' => 2000, 'user_id' => 1]);

        Livewire::test(QuotationAdd::class)
            ->set('type', 'project')
            ->set('title', 'New Quotation')
            ->set('date', now()->toDateString())
            ->set('valid_day', 30)
            ->call('create');

        $new = Quotation::where('title', 'New Quotation')->first();
        $this->assertNotNull($new);

        $copied = OrderProduct::where('model', 'Quotation')->where('model_id', $new->id)->get();
        $this->assertCount(2, $copied);
        $this->assertEqualsCanonicalizing(['Item A', 'Item B'], $copied->pluck('name')->all());
    }

    /** @test */
    public function it_does_not_fail_when_there_is_no_previous_quotation(): void
    {
        $this->actingUser();

        Livewire::test(QuotationAdd::class)
            ->set('type', 'project')
            ->set('title', 'First Ever')
            ->set('date', now()->toDateString())
            ->set('valid_day', 7)
            ->call('create')
            ->assertHasNoErrors();

        $new = Quotation::where('title', 'First Ever')->first();
        $this->assertNotNull($new);
        $this->assertSame(0, OrderProduct::where('model', 'Quotation')->where('model_id', $new->id)->count());
    }
}
