<?php

namespace Tests\Feature;

use App\Http\Livewire\Commercial\Item\Add as ItemAdd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Commerce Item CRUD — type matches the dropdown (sku / nosku / one_time / monthly / anually). */
class CommerceItemCrudTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $u = User::create(['name' => 'U', 'email' => uniqid('u') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        $this->actingAs($u->fresh());

        return $u->fresh();
    }

    /** @test */
    public function it_creates_a_commerce_item(): void
    {
        $this->user();
        Livewire::test(ItemAdd::class)
            ->set('type', 'sku')
            ->set('name', 'Produk A')
            ->set('sku', 'SKU-001')
            ->set('price', 50000)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('commerce_items', ['name' => 'Produk A', 'sku' => 'SKU-001', 'type' => 'sku']);
    }

    /** @test */
    public function commerce_item_requires_type_name_sku_and_numeric_price(): void
    {
        $this->user();
        Livewire::test(ItemAdd::class)
            ->set('price', 'abc')
            ->call('create')
            ->assertHasErrors(['type', 'name', 'sku', 'price']);
    }

    /** @test */
    public function sku_must_be_unique(): void
    {
        $this->user();
        Livewire::test(ItemAdd::class)->set('type', 'sku')->set('name', 'A')->set('sku', 'DUP')->set('price', 1)->call('create')->assertHasNoErrors();
        Livewire::test(ItemAdd::class)->set('type', 'sku')->set('name', 'B')->set('sku', 'DUP')->set('price', 2)->call('create')->assertHasErrors(['sku']);
    }
}
