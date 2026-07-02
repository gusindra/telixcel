<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Ticket model — status matches the dropdown (open / resolved / closed). */
class TicketTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The SmsBlast-style observer on Ticket only acts on chat-tickets (request_id) — but to keep this
     * an isolated model test we disable events and create a standalone ticket.
     */
    private function make(array $attr = []): Ticket
    {
        return Ticket::withoutEvents(fn () => Ticket::create(array_merge([
            'reasons' => 'Koneksi internet mati',
            'solution' => '-',
            'status' => 'open',
            'priority' => 'high',
            'created_by' => 1,
        ], $attr)));
    }

    /** @test */
    public function it_creates_a_ticket(): void
    {
        $t = $this->make();
        $this->assertDatabaseHas('tickets', ['id' => $t->id, 'reasons' => 'Koneksi internet mati', 'status' => 'open']);
    }

    /** @test */
    public function it_persists_each_dropdown_status(): void
    {
        foreach (['open', 'resolved', 'closed'] as $status) {
            $t = $this->make(['status' => $status]);
            $this->assertDatabaseHas('tickets', ['id' => $t->id, 'status' => $status]);
        }
    }
}
