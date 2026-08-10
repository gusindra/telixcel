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
    | AI Console — AI_* only (telixcel)
    |--------------------------------------------------------------------------
    |
    | AI_ENDPOINT=http://127.0.0.1:8645/v1/chat/completions
    | AI_DRIVER=hermes → POST AI_ENDPOINT
    | AI_DRIVER=ollama → tool-loop (OLLAMA_*)
    |
    */
    'ai' => [
        'driver' => env('AI_DRIVER', 'hermes'),
        'endpoint' => env('AI_ENDPOINT', 'http://127.0.0.1:8645/v1/chat/completions'),
        'base_url' => env('AI_BASE_URL', 'http://127.0.0.1:8645/v1'),
        'api_key' => env('AI_API_KEY'),
        'model' => env('AI_MODEL', 'telixcel'),
        'timeout' => (int) env('AI_TIMEOUT', 180),
        'max_iterations' => (int) env('AI_MAX_ITERATIONS', 6),
        'warmup' => filter_var(env('AI_WARMUP', true), FILTER_VALIDATE_BOOLEAN),
        'warmup_ttl' => (int) env('AI_WARMUP_TTL', 240),
        'warmup_timeout' => (int) env('AI_WARMUP_TIMEOUT', 8),
    ],

    // Console chat: per-user AgentUserToken (agt_…) + url('/api/agent') from APP_URL.
    // Optional shared service token (legacy): X-Agent-Token without agt_ prefix.
    'agent_api' => [
        'token' => env('AGENT_API_TOKEN'),
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
