<?php

$endpoint = (string) env('AI_ENDPOINT', 'https://router.noonight.cloud/v1/chat/completions');
$derivedBase = preg_replace('#/chat/completions/?$#', '', $endpoint) ?: 'https://router.noonight.cloud/v1';

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI-compatible upstream
    |--------------------------------------------------------------------------
    |
    | Clients never see these values. They talk only to Telixcel.
    | AI_BASE_URL is the API prefix (e.g. https://host/v1).
    | Telixcel appends /chat/completions.
    |
    */

    'base_url' => env('AI_BASE_URL', $derivedBase),
    'endpoint' => env('AI_ENDPOINT', $derivedBase.'/chat/completions'),
    'api_key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL', 'kr/auto'),
    'timeout' => (int) env('AI_TIMEOUT', 180),
    'max_iterations' => (int) env('AI_MAX_ITERATIONS', 6),
    'warmup' => filter_var(env('AI_WARMUP', true), FILTER_VALIDATE_BOOLEAN),
    'warmup_ttl' => (int) env('AI_WARMUP_TTL', 240),
    'warmup_timeout' => (int) env('AI_WARMUP_TIMEOUT', 8),

    'request_id_prefix' => 'req_ai_',

    // Prefix on model ids (kr/claude-...) is only for Telixcel. Clients see the public name.
    'providers' => [
        'kr' => 'Kiro',
        'kiro' => 'Kiro',
        'ag' => 'Ag',
        'openai' => 'OpenAI',
        'anthropic' => 'Anthropic',
        'google' => 'Google',
        'gemini' => 'Gemini',
        'upstream' => 'Upstream',
    ],

    // Same public name from several sources (kr/claude-..., ag/claude-...) is one client model.
    // Completions try the next source when the first is down. Order is Telixcel-only.
    'fallback' => [
        'enabled' => filter_var(env('AI_MODEL_FALLBACK', true), FILTER_VALIDATE_BOOLEAN),
        'providers' => array_values(array_filter(array_map('trim', explode(',', (string) env(
            'AI_FALLBACK_PROVIDERS',
            'kr,kiro,ag,openai,anthropic,google,gemini,upstream'
        ))))),
    ],

    // Usage cost is stored in USD. IDR limits are compared with this rate.
    'usd_idr' => (float) env('AI_USD_IDR', 16500),

    // Fallback USD per 1M tokens when the provider does not return usage.cost.
    'model_prices' => [
        'haiku' => ['input' => 1.0, 'output' => 5.0],
        'sonnet' => ['input' => 3.0, 'output' => 15.0],
        'opus' => ['input' => 15.0, 'output' => 75.0],
        'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.6],
        'gpt-4o' => ['input' => 2.5, 'output' => 10.0],
        'gemini' => ['input' => 0.3, 'output' => 2.5],
        'default' => ['input' => 1.0, 'output' => 5.0],
    ],

    'cache' => [
        'model_ttl' => 60,
        'permission_ttl' => 60,
    ],

    // Hours a successful probe stays trusted before Telixcel checks the model again.
    'verify_ttl_hours' => (int) env('AI_VERIFY_TTL_HOURS', 6),

    'guard' => [
        'max_messages' => (int) env('AI_MAX_MESSAGES', 40),
        'max_content_chars' => (int) env('AI_MAX_CONTENT_CHARS', 32000),
        'max_tokens' => (int) env('AI_MAX_COMPLETION_TOKENS', 8192),
        'ip_per_minute' => (int) env('AI_IP_PER_MINUTE', 120),
    ],

];
