<?php

namespace App\Services\Agent;

/**
 * Per-request PII tokenizer for the AI Console.
 *
 * Sensitive values are replaced with stable placeholders (e.g. [[NAME_1]],
 * [[PHONE_2]], [[MONEY_3]]) BEFORE any data is sent to the LLM, so the model
 * can reason/reference records without ever seeing the real value. The final
 * reply shown to the user is de-tokenized back to the real values.
 *
 * Same value → same token (within a request), so the model sees consistency.
 */
class Tokenizer
{
    /** token => real value */
    private array $map = [];

    /** "TYPE\0value" => token (reuse the same token for the same value) */
    private array $seen = [];

    /** per-type running counter */
    private array $counters = [];

    /** Regex PII scanners for free-text fields (Indonesia-flavoured). */
    private const PII_PATTERNS = [
        'EMAIL' => '/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/',
        'NPWP'  => '/\b\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}\b/',
        'NIK'   => '/\b\d{16}\b/',
        'PHONE' => '/\b(?:\+?62|0)8\d{7,12}\b/',
    ];

    /** Token pattern for de-tokenizing (also used to detect leftover tokens). */
    public const TOKEN_RE = '/\[\[[A-Z]+_\d+\]\]/';

    /** Mint (or reuse) a token for a real value of a given type. */
    public function token(string $type, mixed $value): string
    {
        $value = (string) $value;
        if ($value === '') {
            return $value;
        }

        $key = $type . "\0" . $value;
        if (isset($this->seen[$key])) {
            return $this->seen[$key];
        }

        $n = ($this->counters[$type] = ($this->counters[$type] ?? 0) + 1);
        $tok = "[[{$type}_{$n}]]";
        $this->map[$tok] = $value;
        $this->seen[$key] = $tok;

        return $tok;
    }

    /**
     * Tokenize the sensitive columns of a DB row before it reaches the LLM.
     *
     * @param  array<string,string>  $sensitive  field => TYPE (NAME|PHONE|EMAIL|MONEY|TEXT|...)
     */
    public function tokenizeRow(array $row, array $sensitive): array
    {
        foreach ($row as $field => $val) {
            if ($val === null || $val === '' || is_array($val)) {
                continue;
            }

            $type = $sensitive[$field] ?? $this->commonKeyType($field);

            if ($type === null) {
                // Unmarked field: still scan strings for embedded PII (defense in depth).
                if (is_string($val)) {
                    $row[$field] = $this->tokenizeText($val);
                }
                continue;
            }

            if ($type === 'TEXT') {
                // Free-text: only tokenize embedded PII, keep the rest readable.
                $row[$field] = $this->tokenizeText((string) $val);
            } else {
                $row[$field] = $this->token($type, $val);
            }
        }

        return $row;
    }

    /** Infer a sensitive type from common column names (names/phones/emails). */
    private function commonKeyType(string $field): ?string
    {
        $k = strtolower($field);
        if (in_array($k, ['assigned_to_name', 'owner_name', 'customer_name', 'client_name', 'project_name'], true)) {
            return 'NAME';
        }
        if (str_contains($k, 'msisdn') || str_contains($k, 'phone') || $k === 'hp' || str_contains($k, 'no_hp')) {
            return 'PHONE';
        }
        if (str_contains($k, 'email')) {
            return 'EMAIL';
        }

        return null;
    }

    /** Replace embedded PII patterns (email/phone/NIK/NPWP) inside free text. */
    public function tokenizeText(string $text): string
    {
        foreach (self::PII_PATTERNS as $type => $re) {
            $text = preg_replace_callback($re, fn ($m) => $this->token($type, $m[0]), $text) ?? $text;
        }

        return $text;
    }

    /** Recursively de-tokenize LLM tool_call arguments before executing them. */
    public function detokenizeArgs(mixed $args): mixed
    {
        if (is_string($args)) {
            return $this->detokenize($args);
        }
        if (is_array($args)) {
            $out = [];
            foreach ($args as $k => $v) {
                $out[$k] = $this->detokenizeArgs($v);
            }

            return $out;
        }

        return $args;
    }

    /** Replace tokens back with real values (for the user-facing reply). */
    public function detokenize(string $text): string
    {
        if ($this->map === []) {
            return $text;
        }

        return strtr($text, $this->map);
    }

    public function hasTokens(): bool
    {
        return $this->map !== [];
    }
}
