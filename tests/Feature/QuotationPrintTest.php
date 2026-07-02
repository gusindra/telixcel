<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\OrderProduct;
use App\Models\Quotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Quotation print fix: the template must read the chosen customer via clientRef
 * (client_id) — not the Source entity (model_id) — and list its items.
 */
class QuotationPrintTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function client_ref_points_to_the_chosen_customer_not_the_source(): void
    {
        $customer = Client::create([
            'uuid' => (string) Str::uuid(), 'sender' => 'Sales', 'name' => 'Budi Santoso',
            'title' => 'PT ABC Sejahtera', 'email' => 'budi@abc.com', 'phone' => '08123', 'user_id' => 0,
        ]);

        // model/model_id point at a PROJECT (the Source), client_id at the actual customer.
        $quote = Quotation::create([
            'type' => 'project', 'title' => 'Penawaran', 'valid_day' => 30, 'date' => now(),
            'user_id' => 1, 'status' => 'approved', 'model' => 'PROJECT', 'model_id' => 99,
            'client_id' => $customer->id, 'quote_no' => 'Q-001',
        ]);

        $quote->refresh();
        $this->assertNotNull($quote->clientRef, 'clientRef relation must resolve');
        $this->assertSame($customer->id, $quote->clientRef->id);
        $this->assertSame('Budi Santoso', $quote->clientRef->name);
        $this->assertSame('PT ABC Sejahtera', $quote->clientRef->title);
    }

    /** @test */
    public function items_relation_returns_the_quotation_line_items(): void
    {
        $quote = Quotation::create([
            'type' => 'project', 'title' => 'Q', 'valid_day' => 30, 'date' => now(),
            'user_id' => 1, 'status' => 'approved',
        ]);
        OrderProduct::create(['name' => 'Jasa A', 'model' => 'Quotation', 'model_id' => $quote->id, 'qty' => 1, 'unit' => 'paket', 'price' => 1000, 'user_id' => 1]);
        OrderProduct::create(['name' => 'Jasa B', 'model' => 'Quotation', 'model_id' => $quote->id, 'qty' => 1, 'unit' => 'paket', 'price' => 2000, 'user_id' => 1]);
        // an item belonging to a different quotation must NOT leak in
        OrderProduct::create(['name' => 'Lain', 'model' => 'Quotation', 'model_id' => $quote->id + 777, 'qty' => 1, 'unit' => 'x', 'price' => 1, 'user_id' => 1]);

        $this->assertCount(2, $quote->items);
        $this->assertEqualsCanonicalizing(['Jasa A', 'Jasa B'], $quote->items->pluck('name')->all());
    }
}
