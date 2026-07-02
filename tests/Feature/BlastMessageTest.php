<?php

namespace Tests\Feature;

use App\Models\BlastMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Blast SMS message persistence. The observer only tops-up balance when the gateway returns code 200,
 * so we disable events to test the record in isolation.
 */
class BlastMessageTest extends TestCase
{
    use RefreshDatabase;

    private function make(array $attr = []): BlastMessage
    {
        return BlastMessage::withoutEvents(fn () => BlastMessage::create(array_merge([
            'msg_id' => 'MSG-1',
            'user_id' => 1,
            'client_id' => '1',
            'type' => 'sms',
            'status' => 'sent',
            'message_content' => 'Halo pelanggan',
            'balance' => '0',
            'msisdn' => '628123456789',
        ], $attr)));
    }

    /** @test */
    public function it_persists_a_blast_message(): void
    {
        $m = $this->make();
        $this->assertDatabaseHas('blast_messages', [
            'id' => $m->id, 'msg_id' => 'MSG-1', 'msisdn' => '628123456789', 'type' => 'sms',
        ]);
    }

    /** @test */
    public function it_stores_message_content_and_recipient(): void
    {
        $m = $this->make(['message_content' => 'Promo diskon akhir tahun', 'msisdn' => '628999000111']);
        $this->assertSame('Promo diskon akhir tahun', $m->fresh()->message_content);
        $this->assertSame('628999000111', $m->fresh()->msisdn);
    }
}
