<?php

namespace Tests\Feature;

use App\Http\Livewire\Commercial\Item\Add as ItemAdd;
use App\Http\Livewire\Order\Add as OrderAdd;
use App\Http\Livewire\Project\Add as ProjectAdd;
use App\Http\Livewire\Role\Roles as RoleAdd;
use App\Http\Livewire\Setting\Notification\Add as NotificationAdd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Best-practice guard: every static-dropdown / enum field must reject values outside its real
 * option set (server-side validation), so neither a form, an API call, nor a careless test can
 * persist an arbitrary value.
 */
class TypeValidationTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $u = User::create(['name' => 'U', 'email' => uniqid('u') . '@test.com', 'password' => bcrypt('x'), 'current_team_id' => 1]);
        $this->actingAs($u->fresh());

        return $u->fresh();
    }

    private function actingAsProjectManager(): User
    {
        $this->seed();
        $user = User::where('email', 'user@telixcel.com')->firstOrFail();
        $this->actingAs($user);

        return $user;
    }

    /** @test */
    public function project_rejects_a_type_outside_the_dropdown(): void
    {
        $this->actingAsProjectManager();
        Livewire::test(ProjectAdd::class)
            ->set('type', 'ngasal')->set('name', 'X')->set('entity', '1')
            ->call('create')->assertHasErrors(['type']);
    }

    /** @test */
    public function project_accepts_each_valid_dropdown_type(): void
    {
        $this->actingAsProjectManager();
        foreach (['selling', 'saas', 'referral'] as $t) {
            Livewire::test(ProjectAdd::class)
                ->set('type', $t)->set('name', 'X')->set('entity', '1')
                ->call('create')->assertHasNoErrors('type');
        }
    }

    /** @test */
    public function order_rejects_a_type_outside_the_dropdown(): void
    {
        $this->actingAsProjectManager();
        Livewire::test(OrderAdd::class)
            ->set('type', 'ngasal')->set('entity', '1')
            ->call('create')->assertHasErrors(['type']);
    }

    /** @test */
    public function order_accepts_each_valid_dropdown_type(): void
    {
        $this->actingAsProjectManager();
        foreach (['selling', 'saas', 'referral'] as $t) {
            Livewire::test(OrderAdd::class)
                ->set('type', $t)->set('entity', '1')
                ->call('create')->assertHasNoErrors('type');
        }
    }

    /** @test */
    public function role_rejects_a_type_outside_the_dropdown(): void
    {
        $this->user();
        Livewire::test(RoleAdd::class)
            ->set('type', 'ngasal')->set('name', 'X')->set('description', 'd')
            ->call('create')->assertHasErrors(['type']);
    }

    /** @test */
    public function role_accepts_each_valid_dropdown_type(): void
    {
        $this->user();
        foreach (['admin', 'finance', 'operasional'] as $t) {
            Livewire::test(RoleAdd::class)
                ->set('type', $t)->set('name', 'Role ' . $t)->set('description', 'd')
                ->call('create')->assertHasNoErrors('type');
        }
    }

    /** @test */
    public function commerce_item_rejects_a_type_outside_the_dropdown(): void
    {
        $this->user();
        Livewire::test(ItemAdd::class)
            ->set('type', 'ngasal')->set('name', 'X')->set('sku', 'SKU-X')->set('price', 1000)
            ->call('create')->assertHasErrors(['type']);
    }

    /** @test */
    public function commerce_item_accepts_each_valid_dropdown_type(): void
    {
        $this->user();
        foreach (['sku', 'nosku', 'one_time', 'monthly', 'anually'] as $i => $t) {
            Livewire::test(ItemAdd::class)
                ->set('type', $t)->set('name', 'Item ' . $t)->set('sku', 'SKU-' . $i)->set('price', 1000)
                ->call('create')->assertHasNoErrors('type');
        }
    }

    /** @test */
    public function notification_rejects_a_type_outside_the_dropdown(): void
    {
        $this->user();
        Livewire::test(NotificationAdd::class)
            ->set('input.type', 'ngasal')->set('grouptype', 'user')->set('input.message', 'hi')->set('input.group', [1])
            ->call('sendAction')->assertHasErrors(['input.type']);
    }

    /** @test */
    public function notification_rejects_a_grouptype_outside_the_dropdown(): void
    {
        $this->user();
        Livewire::test(NotificationAdd::class)
            ->set('input.type', 'app')->set('grouptype', 'ngasal')->set('input.message', 'hi')->set('input.group', [1])
            ->call('sendAction')->assertHasErrors(['grouptype']);
    }
}
