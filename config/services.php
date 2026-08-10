<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_AUTH_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Console — 1 endpoint (simple)
    |--------------------------------------------------------------------------
    |
    | AI_ENDPOINT = full chat URL (paling simple), contoh:
    |   http://127.0.0.1:8645/v1/chat/completions
    |
    | Atau AI_BASE_URL = …/v1  → otomatis + /chat/completions
    |
    | AI_DRIVER=hermes → POST ke endpoint itu (stream:false)
    | AI_DRIVER=ollama → tool-loop lokal (OLLAMA_*)
    |
    */
    'ai' => [
        'driver' => env('AI_DRIVER', 'hermes'),
        // Full URL (preferred). Example: http://127.0.0.1:8645/v1/chat/completions
        'endpoint' => env('AI_ENDPOINT'),
        // Base …/v1 if AI_ENDPOINT empty
        'base_url' => env('AI_BASE_URL', env('HERMES_BASE_URL', 'http://127.0.0.1:8645/v1')),
        'api_key' => env('AI_API_KEY', env('HERMES_API_KEY', env('OLLAMA_API_KEY'))),
        'model' => env('AI_MODEL', env('HERMES_MODEL', env('OLLAMA_MODEL', 'telixcel'))),
        'timeout' => (int) (env('AI_TIMEOUT') ?: env('HERMES_TIMEOUT') ?: env('OLLAMA_TIMEOUT', 180)),
        'max_iterations' => (int) (env('AI_MAX_ITERATIONS') ?: env('OLLAMA_MAX_ITERATIONS', 6)),
        'warmup' => filter_var(env('AI_WARMUP', true), FILTER_VALIDATE_BOOLEAN),
        'warmup_ttl' => (int) env('AI_WARMUP_TTL', 240),
        'warmup_timeout' => (int) env('AI_WARMUP_TIMEOUT', 8),
    ],

    'hermes' => [
        'base_url' => env('HERMES_BASE_URL', 'http://127.0.0.1:8645/v1'),
        'api_key' => env('HERMES_API_KEY'),
        'model' => env('HERMES_MODEL', 'telixcel'),
        'timeout' => (int) env('HERMES_TIMEOUT', 180),
    ],

    'agent_api' => [
        'token' => env('AGENT_API_TOKEN'),
        'public_base_url' => env('AGENT_API_BASE_URL', env('APP_URL', 'http://127.0.0.1:8000')),
    ],

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
        'model' => env('OLLAMA_MODEL', 'gemma4:31b-cloud'),
        'model_local' => env('OLLAMA_MODEL_LOCAL'),
        'api_key' => env('OLLAMA_API_KEY'),
        'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
        'max_iterations' => (int) env('OLLAMA_MAX_ITERATIONS', 6),
    ],

];
