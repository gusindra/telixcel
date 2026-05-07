<?php

namespace Tests\Feature;

use App\Http\Livewire\Commercial\Contract\Add as ContractAdd;
use App\Http\Livewire\Commercial\Contract\Edit as ContractEdit;
use App\Http\Livewire\Commercial\Item\Add as ItemAdd;
use App\Http\Livewire\Commercial\Item\Edit as ItemEdit;
use App\Http\Livewire\Commercial\Quotation\Add as QuotationAdd;
use App\Http\Livewire\Commercial\Quotation\Edit as QuotationEdit;
use App\Http\Livewire\Commercial\Quotation\Item as QuotationItem;
use App\Http\Livewire\Commission\Edit as CommissionEdit;
use App\Http\Livewire\Order\Add as OrderAdd;
use App\Http\Livewire\Order\Edit as OrderEdit;
use App\Http\Livewire\Order\Item as OrderItem;
use App\Http\Livewire\Project\Add as ProjectAdd;
use App\Http\Livewire\Project\AddCustomer;
use App\Http\Livewire\Project\Edit as ProjectEdit;
use App\Http\Livewire\Project\EditType;
use App\Models\Client;
use App\Models\Commision;
use App\Models\CommerceItem;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Billing;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class AssistanceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_manager_menu_routes_are_available()
    {
        $this->actingAsProjectManager();

        foreach ($this->assistantRoutes() as $path => $label) {
            $this->get($path)->assertOk();
        }
    }

    public function test_project_crud_flows_are_working()
    {
        $user = $this->actingAsProjectManager();
        $company = $this->seededCompany();

        Livewire::test(ProjectAdd::class)
            ->set('type', 'selling')
            ->set('name', 'Project Alpha')
            ->set('entity', $company->id)
            ->call('create')
            ->assertHasNoErrors();

        $project = Project::firstWhere('name', 'Project Alpha');

        $this->assertNotNull($project);
        $this->assertSame($user->currentTeam->id, (int) $project->team_id);

        Livewire::test(ProjectEdit::class, ['uuid' => $project->id])
            ->set('name', 'Project Alpha Updated')
            ->set('status', 'active')
            ->set('entity', $company->id)
            ->set('type', 'saas')
            ->call('update', $project->id)
            ->assertHasNoErrors();

        Livewire::test(AddCustomer::class, ['id' => $project->id])
            ->set('customer_name', 'Customer Alpha')
            ->set('customer_address', 'customer.alpha@example.test')
            ->call('save')
            ->assertHasNoErrors();

        Livewire::test(EditType::class, ['id' => $project->id, 'disabled' => false])
            ->set('referrer_name', 'Referral Partner')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Project Alpha Updated',
            'status' => 'active',
            'customer_name' => 'Customer Alpha',
            'customer_address' => 'customer.alpha@example.test',
            'referrer_name' => 'Referral Partner',
        ]);
    }

    public function test_commercial_item_crud_flows_are_working()
    {
        $user = $this->actingAsProjectManager();

        Livewire::test(ItemAdd::class)
            ->set('type', 'nosku')
            ->set('name', 'Implementation Service')
            ->set('sku', 'SVC-001')
            ->set('price', 1500000)
            ->call('create')
            ->assertHasNoErrors();

        $item = CommerceItem::firstWhere('sku', 'SVC-001');

        $this->assertNotNull($item);
        $this->assertSame($user->id, (int) $item->user_id);

        Livewire::test(ItemEdit::class, ['code' => $item->id])
            ->set('name', 'Implementation Service Plus')
            ->set('status', 'active')
            ->set('sku', 'SVC-001-UPD')
            ->set('type', 'nosku')
            ->set('description', 'Implementation support')
            ->set('spec', 'Remote')
            ->set('price', 2500000)
            ->set('unit', 'day')
            ->set('discount', 5)
            ->set('import', 'manual')
            ->call('update', $item->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('commerce_items', [
            'id' => $item->id,
            'name' => 'Implementation Service Plus',
            'sku' => 'SVC-001-UPD',
            'unit_price' => 2500000,
            'unit' => 'day',
        ]);
    }

    public function test_quotation_crud_flows_are_working()
    {
        $this->actingAsProjectManager();

        $company = $this->seededCompany();
        $project = Project::create([
            'name' => 'Quotation Source Project',
            'type' => 'selling',
            'entity_party' => $company->id,
            'customer_name' => 'Customer Alpha',
            'customer_address' => 'customer.alpha@example.test',
            'team_id' => auth()->user()->currentTeam->id,
        ]);

        Livewire::test(QuotationAdd::class)
            ->set('type', 'PROJECT')
            ->set('title', 'Quotation Alpha')
            ->set('date', '2026-05-07')
            ->set('valid_day', 14)
            ->set('model', 'PROJECT')
            ->set('source', $project->id)
            ->call('create')
            ->assertHasNoErrors();

        $quotation = Quotation::firstWhere('title', 'Quotation Alpha');

        $this->assertNotNull($quotation);

        Livewire::test(QuotationEdit::class, ['code' => $quotation->id])
            ->set('quoteNo', 'Q-20260507-001')
            ->set('name', 'Quotation Alpha Updated')
            ->set('date', '2026-05-08')
            ->set('valid_day', 30)
            ->set('status', 'reviewed')
            ->set('type', 'PROJECT')
            ->set('terms', 'Payment due in 14 days')
            ->set('description', 'Updated quotation scope')
            ->set('created_by', 'Project Manager')
            ->set('addressed_name', 'Customer Alpha')
            ->call('update', $quotation->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'title' => 'Quotation Alpha Updated',
            'quote_no' => 'Q-20260507-001',
            'valid_day' => 30,
            'status' => 'reviewed',
        ]);

        Livewire::test(QuotationItem::class, ['data' => $quotation->fresh()])
            ->set('name', 'Consulting Package')
            ->set('price', 3000000)
            ->set('qty', 2)
            ->set('unit', 'package')
            ->set('description', 'Quotation item note')
            ->call('create')
            ->assertHasNoErrors();

        $line = OrderProduct::where('model', 'Quotation')
            ->where('model_id', $quotation->id)
            ->first();

        $this->assertNotNull($line);

        Livewire::test(QuotationItem::class, ['data' => $quotation->fresh()])
            ->call('updateShowModal', $line->id)
            ->set('name', 'Consulting Package Updated')
            ->set('price', 4500000)
            ->set('qty', 3)
            ->set('unit', 'package')
            ->set('description', 'Updated quotation item note')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('order_products', [
            'id' => $line->id,
            'model' => 'Quotation',
            'model_id' => $quotation->id,
            'name' => 'Consulting Package Updated',
            'price' => 4500000,
            'qty' => 3,
        ]);

        Livewire::test(QuotationItem::class, ['data' => $quotation->fresh()])
            ->call('deleteShowModal', $line->id)
            ->call('delete')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('order_products', [
            'id' => $line->id,
        ]);
    }

    public function test_contract_crud_flows_are_working()
    {
        $this->actingAsProjectManager();

        Livewire::test(ContractAdd::class)
            ->set('title', 'Contract Alpha')
            ->call('create')
            ->assertHasNoErrors();

        $contract = Contract::firstWhere('title', 'Contract Alpha');

        $this->assertNotNull($contract);

        Livewire::test(ContractEdit::class, ['code' => $contract->id])
            ->set('input.title', 'Contract Alpha Updated')
            ->set('input.signer_email', 'signer@example.test')
            ->set('input.actived_at', '2026-05-07 00:00:00')
            ->set('input.expired_at', '2026-06-07 00:00:00')
            ->set('input.model', '')
            ->set('input.model_id', '')
            ->call('update', $contract->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'title' => 'Contract Alpha Updated',
            'signer_email' => 'signer@example.test',
        ]);
    }

    public function test_order_crud_flows_are_working()
    {
        $user = $this->actingAsProjectManager();
        $company = $this->seededCompany();
        $client = $this->clientFor($user);

        Livewire::test(OrderAdd::class)
            ->set('type', 'service')
            ->set('entity', $company->id)
            ->call('create')
            ->assertHasNoErrors();

        $order = Order::firstWhere('type', 'service');

        $this->assertNotNull($order);
        $this->assertSame('draft', $order->status);

        Livewire::test(OrderEdit::class, ['uuid' => $order->id])
            ->set('input.name', 'Order Alpha')
            ->set('input.no', 'ORD-20260507-001')
            ->set('input.status', 'active')
            ->set('input.date', '2026-05-07')
            ->set('input.customer_id', $client->uuid)
            ->set('input.type', 'service')
            ->call('update', $order->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'name' => 'Order Alpha',
            'no' => 'ORD-20260507-001',
            'status' => 'active',
            'customer_id' => $client->uuid,
        ]);

        Livewire::test(OrderItem::class, ['data' => $order->fresh()])
            ->set('name', 'Implementation Milestone')
            ->set('price', 5000000)
            ->set('qty', 1)
            ->set('unit', 'milestone')
            ->set('percentage', 100)
            ->set('description', 'Order line note')
            ->call('create')
            ->assertHasNoErrors();

        $line = OrderProduct::where('model', 'Order')
            ->where('model_id', $order->id)
            ->first();

        $this->assertNotNull($line);

        Livewire::test(OrderItem::class, ['data' => $order->fresh()])
            ->call('updateShowModal', $line->id)
            ->set('name', 'Implementation Milestone Updated')
            ->set('price', 6500000)
            ->set('qty', 2)
            ->set('unit', 'milestone')
            ->set('percentage', 100)
            ->set('description', 'Updated order line note')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('order_products', [
            'id' => $line->id,
            'model' => 'Order',
            'model_id' => $order->id,
            'name' => 'Implementation Milestone Updated',
            'price' => 6500000,
            'qty' => 2,
        ]);

        Livewire::test(OrderItem::class, ['data' => $order->fresh()])
            ->set('tax', 11)
            ->call('updateTax')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'vat' => 11,
        ]);

        Livewire::test(OrderItem::class, ['data' => $order->fresh()])
            ->call('deleteShowModal', $line->id)
            ->call('delete')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('order_products', [
            'id' => $line->id,
        ]);
    }

    public function test_commission_agent_assignment_flows_are_working()
    {
        $user = $this->actingAsProjectManager();
        $company = $this->seededCompany();
        $client = $this->clientFor($user);

        $order = Order::create([
            'name' => 'Commission Order',
            'type' => 'service',
            'entity_party' => $company->id,
            'status' => 'draft',
            'user_id' => $user->id,
        ]);

        Livewire::test(CommissionEdit::class, [
            'model' => 'order',
            'data' => $order,
            'disabled' => false,
        ])
            ->set('type', 'percentage')
            ->set('rate', 10)
            ->set('clientId', $client->id)
            ->call('update', $order->id)
            ->assertHasNoErrors();

        $commission = Commision::where('model', 'order')
            ->where('model_id', $order->id)
            ->first();

        $this->assertNotNull($commission);
        $this->assertSame('10', (string) $commission->ratio);

        Livewire::test(CommissionEdit::class, [
            'model' => 'order',
            'data' => $order,
            'disabled' => false,
        ])
            ->set('type', 'percentage')
            ->set('rate', 15)
            ->set('clientId', $client->id)
            ->call('update', $order->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('commisions', [
            'id' => $commission->id,
            'model' => 'order',
            'model_id' => $order->id,
            'ratio' => 15,
            'client_id' => $client->id,
        ]);

        Livewire::test(CommissionEdit::class, [
            'model' => 'order',
            'data' => $order,
            'disabled' => false,
        ])
            ->call('removeAgent')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('commisions', [
            'id' => $commission->id,
        ]);
    }

    public function test_top_level_delete_actions_are_working()
    {
        $user = $this->actingAsProjectManager();
        $company = $this->seededCompany();
        $client = $this->clientFor($user);

        $project = Project::create([
            'name' => 'Delete Project',
            'type' => 'selling',
            'entity_party' => $company->id,
            'team_id' => $user->currentTeam->id,
        ]);

        $item = CommerceItem::create([
            'name' => 'Delete Item',
            'sku' => 'DEL-ITEM',
            'type' => 'nosku',
            'unit_price' => 1000,
            'user_id' => $user->id,
        ]);

        $quotation = Quotation::create([
            'title' => 'Delete Quotation',
            'model' => 'PROJECT',
            'model_id' => $project->id,
            'date' => '2026-05-07',
            'valid_day' => 7,
            'user_id' => $user->id,
        ]);

        $contract = Contract::create([
            'title' => 'Delete Contract',
            'status' => 'draft',
            'user_id' => $user->id,
        ]);

        $order = Order::create([
            'name' => 'Delete Order',
            'type' => 'service',
            'entity_party' => $company->id,
            'customer_id' => $client->uuid,
            'user_id' => $user->id,
        ]);

        $invoice = Billing::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'INV-DELETE',
            'description' => 'Delete invoice',
            'amount' => 1000,
            'status' => 'unpaid',
            'user_id' => $user->id,
        ]);

        $commission = Commision::create([
            'model' => 'order',
            'model_id' => $order->id,
            'client_id' => $client->id,
            'type' => 'percentage',
            'ratio' => 5,
            'total' => 500,
            'status' => 'draft',
        ]);

        foreach ([
            'project' => [$project->id, 'projects'],
            'commerce-item' => [$item->id, 'commerce_items'],
            'quotation' => [$quotation->id, 'quotations'],
            'contract' => [$contract->id, 'contracts'],
            'invoice' => [$invoice->id, 'billings'],
            'commission' => [$commission->id, 'commisions'],
            'order' => [$order->id, 'orders'],
        ] as $type => [$id, $table]) {
            $this->delete(route('records.destroy', [$type, $id]))
                ->assertRedirect();

            $this->assertDatabaseMissing($table, ['id' => $id]);
        }
    }

    private function actingAsProjectManager(): User
    {
        $this->seed();

        $user = User::where('email', 'user@telixcel.com')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }

    private function seededCompany(): Company
    {
        return Company::where('code', 'TLX')->firstOrFail();
    }

    private function clientFor(User $user): Client
    {
        return Client::create([
            'uuid' => (string) Str::uuid(),
            'sender' => 'manual',
            'title' => 'Mr',
            'name' => 'Agent Alpha',
            'phone' => '081234567890',
            'email' => 'agent.alpha@example.test',
            'address' => 'Agent Street 1',
            'note' => 'Seeded for assistance CRUD tests',
            'user_id' => $user->currentTeam->user_id,
        ]);
    }

    private function assistantRoutes(): array
    {
        return [
            '/assistant' => 'Assistant',
            '/project' => 'Project',
            '/commercial' => 'Commercial',
            '/commercial/item' => 'Item',
            '/commercial/quotation' => 'Quotation',
            '/commercial/contract' => 'Contract',
            '/order' => 'Order',
            '/invoice' => 'Invoice',
            '/commission' => 'Commission',
        ];
    }
}
