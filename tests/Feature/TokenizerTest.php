<?php

namespace Tests\Feature;

use App\Services\Agent\ModelRegistry;
use App\Services\Agent\Tokenizer;
use Tests\TestCase;

/** PII tokenization: real values are masked before the LLM, restored for the user. */
class TokenizerTest extends TestCase
{
    /** @test */
    public function it_masks_marked_fields_and_embedded_pii(): void
    {
        $tok = new Tokenizer();

        $row = [
            'id' => 5,
            'msisdn' => '081234567890',
            'title' => 'Promo ke budi@mail.com',
            'price' => '150000',
            'status' => 'SENT',
        ];

        $masked = $tok->tokenizeRow($row, ModelRegistry::sensitive('blast-message'));

        // id / status stay readable; PII + financial are tokenized.
        $this->assertSame(5, $masked['id']);
        $this->assertSame('SENT', $masked['status']);
        $this->assertMatchesRegularExpression('/^\[\[PHONE_\d+\]\]$/', $masked['msisdn']);
        $this->assertMatchesRegularExpression('/^\[\[MONEY_\d+\]\]$/', $masked['price']);
        $this->assertStringContainsString('[[EMAIL_', $masked['title']); // embedded email masked
        $this->assertStringNotContainsString('budi@mail.com', json_encode($masked));
        $this->assertStringNotContainsString('081234567890', json_encode($masked));
    }

    /** @test */
    public function detokenize_restores_the_real_values(): void
    {
        $tok = new Tokenizer();
        $masked = $tok->tokenizeRow(
            ['customer_name' => 'Budi Santoso', 'total' => '5000000'],
            ModelRegistry::sensitive('order') + ['customer_name' => 'NAME']
        );

        $llmReply = "Order milik {$masked['customer_name']} senilai {$masked['total']}.";
        $restored = $tok->detokenize($llmReply);

        $this->assertSame('Order milik Budi Santoso senilai 5000000.', $restored);
        $this->assertDoesNotMatchRegularExpression(Tokenizer::TOKEN_RE, $restored);
    }

    /** @test */
    public function same_value_reuses_the_same_token(): void
    {
        $tok = new Tokenizer();
        $a = $tok->token('NAME', 'Budi');
        $b = $tok->token('NAME', 'Budi');
        $c = $tok->token('NAME', 'Siti');

        $this->assertSame($a, $b);
        $this->assertNotSame($a, $c);
    }
}
