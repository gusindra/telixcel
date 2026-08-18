<?php

namespace App\Services\Agent;

use App\Models\AgentActionLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * AI Console — model-chosen tools (OpenAI tool calling) + AI LLM.
 *
 * Flow per user turn:
 *   1. POST AI_ENDPOINT with messages + tools (ToolSchemas)
 *   2. If assistant returns tool_calls → ToolExecutor on Laravel DB
 *   3. Append tool results → call AI again (loop, max AI_MAX_ITERATIONS)
 *   4. Final text reply; update_record → PendingActionStore → Approve UI
 *
 * Env:
 *   AI_ENDPOINT, AI_API_KEY, AI_MODEL, AI_TIMEOUT, AI_MAX_ITERATIONS
 */
class AgentRunner
{
    private const ALLOWED_TOOLS = [
        'query_records',
        'update_record',
        'generate_report',
        'download_report',
    ];

    /**
     * @return array{reply:string, pending:?array, model:string, metrics:array, driver:string}
     */
    public function run(array $history, string $userMessage, ?int $chatId = null): array
    {
        // Status changes: Laravel first (real pending + yellow card). Skip AI chatter.
        if ($local = $this->tryLocalStatusUpdate($userMessage, $history)) {
            return $local;
        }

        // Per-request PII tokenizer: DB values are masked before reaching the LLM,
        // then un-masked in the final reply. The model never sees real PII/financials.
        $tok = new Tokenizer();

        $result = $this->runToolLoop($history, $userMessage, $chatId, $tok);
        $result['driver'] = 'ai';

        $isUpdateTurn = $this->looksLikeStatusChangeIntent($userMessage)
            || in_array('update_record', $result['metrics']['tools_called'] ?? [], true)
            || in_array('update_record', $result['metrics']['pre_tools'] ?? [], true)
            || in_array(($result['metrics']['source'] ?? ''), [
                'laravel_pre_update',
                'ai_tool_calling_pending',
            ], true);

        if (empty($result['pending']) && $isUpdateTurn) {
            $result['pending'] = PendingActionStore::get(auth()->id());
        }

        if (empty($result['pending']) && $this->looksLikeStatusChangeIntent($userMessage)) {
            $materialized = $this->materializePendingFromTexts(
                $userMessage,
                (string) ($result['reply'] ?? ''),
                $history
            );
            if ($materialized) {
                $result['pending'] = $materialized;
                $result['metrics'] = array_merge($result['metrics'] ?? [], [
                    'pending_source' => 'materialized_from_text',
                ]);
                $result['reply'] = $this->formatPendingReply($materialized);
            }
        }

        if (! $isUpdateTurn) {
            $result['pending'] = null;
            $result['reply'] = $this->stripApprovalChatter((string) ($result['reply'] ?? ''));
        }

        // Never show architecture/curl noise to the user.
        $result['reply'] = $this->stripInfraChatter((string) ($result['reply'] ?? ''));

        // Un-mask PII tokens so the user sees real values (the LLM never did).
        $result['reply'] = $tok->detokenize((string) ($result['reply'] ?? ''));

        $this->logLlmTurn($result);

        return $result;
    }

    /**
     * Persist plain LLM turns (no tool call) into agent_action_logs so the
     * AI Log page shows "what the AI did". Tool calls are already logged by
     * ToolExecutor / ApprovalExecutor — never log those twice.
     */
    private function logLlmTurn(array $result): void
    {
        if (($result['driver'] ?? '') !== 'ai') {
            return;
        }
        if (! empty($result['metrics']['tools_called'] ?? [])) {
            return;
        }

        try {
            if (! Schema::hasTable('agent_action_logs')) {
                return;
            }
            AgentActionLog::create([
                'user_id' => auth()->id(),
                'tool' => 'chat',
                'model_key' => null,
                'arguments' => ['pending' => ! empty($result['pending'])],
                'result' => ['reply' => mb_substr((string) ($result['reply'] ?? ''), 0, 2000)],
                'status' => ! empty($result['pending']) ? 'proposed' : 'ok',
            ]);
        } catch (\Throwable $e) {
            // Logging must never break the agent flow.
        }
    }

    public function driver(): string
    {
        return 'ai';
    }

    /**
     * Full URL to chat completions (never /responses).
     */
    public static function endpoint(): string
    {
        $full = trim((string) config('ai.endpoint', ''));
        if ($full !== '') {
            return self::forceChatCompletions(rtrim($full, '/'));
        }

        $base = rtrim((string) (config('ai.base_url') ?: 'http://127.0.0.1:8645/v1'), '/');
        if ($base === '') {
            return '';
        }
        if (str_ends_with($base, '/chat/completions')) {
            return $base;
        }
        if (str_ends_with($base, '/v1') || str_contains($base, '/v1/')) {
            return self::forceChatCompletions($base . '/chat/completions');
        }

        return self::forceChatCompletions($base . '/v1/chat/completions');
    }

    private static function forceChatCompletions(string $url): string
    {
        $url = preg_replace('#/responses/?$#', '/chat/completions', $url) ?? $url;
        if (! str_contains($url, '/chat/completions')) {
            $url = rtrim($url, '/') . '/chat/completions';
        }

        return $url;
    }

    public static function maybeWarm(): void
    {
        if (! filter_var(config('ai.warmup', true), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        $ttl = max(30, (int) config('ai.warmup_ttl', 240));
        if (! Cache::add('ai:warmup:lock', 1, now()->addSeconds($ttl))) {
            return;
        }

        try {
            $endpoint = self::endpoint();
            $key = (string) (config('ai.api_key') ?: '');
            if ($endpoint === '') {
                return;
            }
            $timeout = max(3, (int) config('ai.warmup_timeout', 8));
            $http = Http::timeout($timeout)->acceptJson();
            if ($key !== '') {
                $http = $http->withToken($key);
            }
            $root = preg_replace('#/v1(?:/chat/completions)?$#', '', $endpoint) ?: $endpoint;
            try {
                $http->get($root . '/health');
            } catch (\Throwable $e) {
            }
            try {
                $models = preg_replace('#/chat/completions$#', '/models', $endpoint) ?: ($root . '/v1/models');
                $http->get($models);
            } catch (\Throwable $e) {
            }
        } catch (\Throwable $e) {
            Log::debug('AI warmup skipped: ' . $e->getMessage());
        }
    }

    /**
     * Model picks tools; Laravel executes them; AI formats final answer.
     *
     * @return array{reply:string, pending:?array, model:string, metrics:array, ai_response_id:?string}
     */
    private function runToolLoop(array $history, string $userMessage, ?int $chatId, Tokenizer $tok): array
    {
        $endpoint = self::endpoint();
        $apiKey = (string) (config('ai.api_key') ?: '');
        $model = (string) (config('ai.model') ?: 'telixcel');
        $timeout = (int) (config('ai.timeout') ?: 180);
        $maxIter = max(1, (int) (config('ai.max_iterations') ?: 6));

        if ($endpoint === '') {
            throw new \RuntimeException(
                'AI endpoint kosong. Set AI_ENDPOINT (contoh: http://127.0.0.1:8645/v1/chat/completions)'
            );
        }

        $userId = auth()->id() ?? 0;
        $chat = null;
        if ($chatId) {
            $chat = \App\Models\AgentChat::query()
                ->where('id', $chatId)
                ->where('user_id', $userId)
                ->first();
        }

        $aiSessionId = $chat?->ai_session_id
            ?: ($chatId ? "telixcel-chat-{$chatId}" : null);
        $conversation = "telixcel-user-{$userId}-chat-" . ($chatId ?: 'new');
        $sessionKey = "agent:telixcel:web:user-{$userId}:chat-" . ($chatId ?: 'new');

        $user = auth()->user();

        // Safety net: Laravel may pre-query so AI cannot invent "API down" stories.
        // Model can still call more tools via OpenAI tool_calls.
        $preTools = $this->preQueryIfDataIntent($userMessage, $history, $tok);
        $pending = $preTools['pending'] ?? null;
        $toolsCalled = $preTools['called'] ?? [];

        if ($pending) {
            $summary = (string) ($pending['summary'] ?? 'Perubahan diajukan.');

            return [
                'reply' => $summary."\n\nSilakan **konfirmasi di kartu di bawah** (Terapkan / Batalkan). "
                    .'Belum tersimpan ke database sampai Anda setujui.',
                'pending' => $pending,
                'model' => 'laravel-tools',
                'metrics' => [
                    'tools_called' => $toolsCalled,
                    'source' => 'laravel_pre_update',
                ],
                'ai_response_id' => null,
            ];
        }

        $ctx = [
            'current_user' => [
                'id' => $user->id ?? 0,
                'name' => $user->name ?? 'admin',
                'company_id' => $user->current_team_id ?? null,
            ],
            'conversation_id' => $chatId,
            'datetime' => now()->toDateTimeString(),
            'source' => 'telixcel-ai-console',
            'architecture' => [
                'llm' => 'ai',
                'data_tools' => 'laravel-tool-executor-only',
                'updates' => 'pending-approval-in-ui',
                'forbidden' => [
                    'curl',
                    'POST /api/agent',
                    '172.22.96.1',
                    ':8020',
                    'X-Agent-Token',
                    'telixcel-data skill HTTP',
                ],
            ],
            'tool_results' => $preTools['results'] ?? [],
            'tool_generated_at' => now()->toDateTimeString(),
        ];

        $input = "<request_context>\n"
            . json_encode($ctx, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            . "\n</request_context>\n\n"
            . $userMessage;

        $system = [
            'role' => 'system',
            'content' => $this->systemPrompt(),
        ];

        // Full transcript for this turn (tools need tool_call messages in-loop).
        // History content is scanned for embedded PII (email/phone/NIK) too.
        $historyMsgs = array_map(
            fn ($m) => ['role' => $m['role'], 'content' => $tok->tokenizeText((string) $m['content'])],
            $this->sanitizeHistory($history)
        );
        $messages = array_merge(
            [$system],
            $historyMsgs,
            [['role' => 'user', 'content' => $input]],
        );

        $http = Http::timeout($timeout)->acceptJson();
        if ($apiKey !== '') {
            $http = $http->withToken($apiKey);
        }

        $headers = [
            'X-AI-Session-Key' => $sessionKey,
        ];
        if ($aiSessionId) {
            $headers['X-AI-Session-Id'] = $aiSessionId;
        }

        $executor = app(ToolExecutor::class);
        $lastJson = [];
        $returnedSessionId = $aiSessionId;
        $usageTotal = [];

        for ($i = 0; $i < $maxIter; $i++) {
            try {
                $response = $http->withHeaders($headers)->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'tools' => ToolSchemas::all(),
                    'tool_choice' => 'auto',
                    'stream' => false,
                    'temperature' => 0.1,
                    'user' => $conversation,
                ]);
                $response->throw();
                $json = $response->json() ?? [];
                $lastJson = $json;
            } catch (\Throwable $e) {
                Log::warning('AI tool-loop error: ' . $e->getMessage());
                throw $e;
            }

            $returnedSessionId = $response->header('X-AI-Session-Id')
                ?: ($json['ai']['session_id'] ?? null)
                ?: $returnedSessionId;

            if (! empty($json['usage']) && is_array($json['usage'])) {
                foreach ($json['usage'] as $k => $v) {
                    if (is_numeric($v)) {
                        $usageTotal[$k] = ($usageTotal[$k] ?? 0) + (int) $v;
                    }
                }
            }

            if (! empty($json['ai']['failed']) || ($json['choices'][0]['finish_reason'] ?? '') === 'error') {
                $err = $json['ai']['error']
                    ?? ($json['choices'][0]['message']['content'] ?? 'AI error');
                throw new \RuntimeException('AI error: ' . $err);
            }

            $msg = $json['choices'][0]['message'] ?? [];
            $toolCalls = $this->extractToolCalls($msg);

            if ($toolCalls === []) {
                $content = $msg['content'] ?? '';
                $reply = trim(is_string($content) ? $content : json_encode($content)) ?: '(no response)';

                // AI skill legacy: invents network errors instead of using tool_results.
                if ($this->looksLikeNetworkExcuse($reply) && ! empty($preTools['results'])) {
                    Log::warning('AI network-excuse reply overridden with Laravel tool_results');
                    $reply = $this->formatFromToolResults($preTools['results'], $userMessage);
                }

                if ($chat && $returnedSessionId && $chat->ai_session_id !== $returnedSessionId) {
                    $chat->forceFill(['ai_session_id' => $returnedSessionId])->save();
                }

                return [
                    'reply' => $reply,
                    'pending' => $pending,
                    'model' => $json['model'] ?? $model,
                    'metrics' => [
                        'usage' => $usageTotal ?: ($json['usage'] ?? null),
                        'conversation' => $conversation,
                        'endpoint' => $endpoint,
                        'ai_session_id' => $returnedSessionId,
                        'tools_called' => $toolsCalled,
                        'iterations' => $i + 1,
                        'source' => 'ai_tool_calling',
                        'pre_tools' => $preTools['called'] ?? [],
                    ],
                    'ai_response_id' => $json['id'] ?? null,
                ];
            }

            // Append assistant message with tool_calls (OpenAI format)
            $messages[] = [
                'role' => 'assistant',
                'content' => is_string($msg['content'] ?? null) ? $msg['content'] : '',
                'tool_calls' => $toolCalls,
            ];

            foreach ($toolCalls as $call) {
                $name = (string) ($call['function']['name'] ?? '');
                $args = $this->decodeArgs($call['function']['arguments'] ?? []);
                // LLM may echo a token as an argument → restore the real value first.
                $args = $tok->detokenizeArgs($args);
                $callId = (string) ($call['id'] ?? ('call_' . uniqid()));

                if (! in_array($name, self::ALLOWED_TOOLS, true)) {
                    $result = [
                        'status' => 'error',
                        'message' => "Unknown or disallowed tool: {$name}",
                    ];
                } else {
                    $result = $executor->execute($name, $args);
                    $toolsCalled[] = $name;
                }

                if (($result['status'] ?? '') === 'pending' && ! empty($result['action'])) {
                    $pending = $result['action'];
                    $toolPayload = [
                        'status' => 'awaiting_user_approval',
                        'summary' => $result['action']['summary'] ?? 'Pending approval',
                        'message' => 'Tell the user to Approve or Reject in the UI card. Do not claim the change is saved.',
                    ];
                } else {
                    // Mask PII in read results before they go back to the LLM.
                    $toolPayload = $name === 'query_records'
                        ? $this->tokenizeQueryResult($result, (string) ($args['model'] ?? ''), $tok)
                        : $result;
                }

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $callId,
                    'content' => json_encode($toolPayload, JSON_UNESCAPED_UNICODE),
                ];
            }

            // Short-circuit after update proposal so Approve card shows immediately
            if ($pending) {
                $summary = (string) ($pending['summary'] ?? 'Perubahan diajukan.');

                if ($chat && $returnedSessionId && $chat->ai_session_id !== $returnedSessionId) {
                    $chat->forceFill(['ai_session_id' => $returnedSessionId])->save();
                }

                return [
                    'reply' => $summary . "\n\nSilakan **konfirmasi di kartu di bawah** (Terapkan / Batalkan). "
                        . 'Belum tersimpan ke database sampai Anda setujui.',
                    'pending' => $pending,
                    'model' => $lastJson['model'] ?? $model,
                    'metrics' => [
                        'usage' => $usageTotal ?: null,
                        'conversation' => $conversation,
                        'endpoint' => $endpoint,
                        'ai_session_id' => $returnedSessionId,
                        'tools_called' => $toolsCalled,
                        'iterations' => $i + 1,
                        'source' => 'ai_tool_calling_pending',
                    ],
                    'ai_response_id' => $lastJson['id'] ?? null,
                ];
            }
        }

        return [
            'reply' => 'Batas langkah tool tercapai. Coba sederhanakan atau pecah permintaan Anda.',
            'pending' => $pending,
            'model' => $model,
            'metrics' => [
                'tools_called' => $toolsCalled,
                'iterations' => $maxIter,
                'source' => 'ai_tool_calling_limit',
            ],
            'ai_response_id' => null,
        ];
    }

    private function systemPrompt(): string
    {
        $user = auth()->user();
        $schema = SchemaContext::build();
        $name = $user->name ?? 'admin';
        $id = $user->id ?? 0;
        $now = now()->toDateTimeString();

        return <<<PROMPT
You are the Telixcel AI Console assistant (project / ops data).

{$schema}

CURRENT USER: {$name} (ID: {$id})
CURRENT DATETIME: {$now}

ARCHITECTURE (critical — read carefully):
- Live data tools run ONLY on Laravel (ToolExecutor). You never reach MySQL yourself.
- Prefer OpenAI function tools: query_records, update_record, generate_report, download_report.
- If request_context.tool_results is non-empty, that data is LIVE — answer from it.
- LIST/READ: table only. No approve/kartu language.
- Do NOT mention curl, API, network, retired paths, tools architecture, or localhost to the user.
- Never invent IDs/statuses. Prefer user language (ID/EN). Be short.

TOOLS:
- query_records — list/filter whitelisted models (immediate, live data).
- update_record — propose UPDATE only (pending approval in UI; not written until Approve).
- generate_report / download_report — monthly report then optional PDF.

STATUS CHANGE (very important — act, don't ask):
- Any phrasing/language where the user reports a task is done/finished/complete/selesai/kelar/beres,
  OR asks to change a status ("jadikan complete", "set pending", "i have finished X", "close X"):
  1) FIRST call query_records (model=task, filters title LIKE the task name) to find the exact id.
  2) THEN call update_record (model=task, id=<found id>, values={"status":"complete|pending|progress"}).
- NEVER ask the user for the ID — resolve it yourself with query_records.
- NEVER say the change is saved/applied/done. update_record only PROPOSES; the UI shows an approval card.
- If query_records returns multiple matches, ask which one (show id + title). If none, say so.

PATTERNS:
- Cross-model: query child first → collect ids → parent with op "in".
- List tasks as Markdown table: ID | Judul | Status | Priority | Tipe | Assignee | Project | Target.
- If a tool returns awaiting_user_approval → tell the user to Approve in the UI card (do not claim it's saved).

RULES:
- UPDATE only via tools (no create/delete). Be concise. Answer in the user's language.
PROMPT;
    }

    /**
     * Mask sensitive columns of a query_records result before it reaches the LLM.
     * Returns a copy; the caller's raw result (used for id resolution) is untouched.
     */
    private function tokenizeQueryResult(array $result, string $model, Tokenizer $tok): array
    {
        if (($result['status'] ?? '') !== 'ok' || empty($result['data']) || ! is_array($result['data'])) {
            return $result;
        }

        $sensitive = ModelRegistry::sensitive($model);
        $result['data'] = array_map(
            fn ($row) => is_array($row) ? $tok->tokenizeRow($row, $sensitive) : $row,
            $result['data']
        );

        return $result;
    }

    /**
     * Laravel-side query when the user clearly asks for data (safety net vs AI skills).
     *
     * @param  array<int,array{role?:string,content?:string}>  $history
     * @return array{results:list<array>,called:list<string>,pending:?array}
     */
    private function preQueryIfDataIntent(string $message, array $history, Tokenizer $tok): array
    {
        $isStatusChange = $this->looksLikeStatusChangeIntent($message);
        $wants = $isStatusChange || (bool) preg_match(
            '/\b(task|tugas|status|pending|progress|completed?|complete|selesai|daftar|list|tampil|lihat|cek|berapa|assign|project|proyek|ed\b|#\s*\d+)\b/iu',
            $message
        );
        if (! $wants || ! auth()->check()) {
            return ['results' => [], 'called' => [], 'pending' => null];
        }

        $executor = app(ToolExecutor::class);
        $called = [];
        $results = [];
        $pending = null;

        $args = ['model' => 'task', 'limit' => 30];
        if (preg_match('/#\s*(\d+)\b/', $message, $m) || preg_match('/\b(?:task\s*)?id\s*[:=]?\s*(\d+)\b/iu', $message, $m)) {
            $args['filters'] = [['field' => 'id', 'op' => '=', 'value' => (int) $m[1]]];
            $args['limit'] = 5;
        } elseif ($isStatusChange) {
            // Find the task by title fragment before proposing update (never filter by target status).
            $title = $this->extractTitleHint($message);
            if ($title) {
                $args['filters'] = [['field' => 'title', 'op' => 'like', 'value' => $title]];
                $args['limit'] = 10;
            }
        } elseif (
            // Explicit status list only: "list pending", "task status complete", NOT bare "list task".
            preg_match('/\b(list|daftar|tampil|lihat|cek|tampilkan).{0,40}\b(pending|progress|completed?|complete|selesai)\b/iu', $message)
            || preg_match('/\b(pending|progress|completed?|complete|selesai).{0,40}\b(task|tugas)\b/iu', $message)
            || preg_match('/\btask\s+(pending|progress|completed?|complete|selesai)\b/iu', $message)
            || preg_match('/\bstatus\s+(pending|progress|completed?|complete|selesai)\b/iu', $message)
        ) {
            if (preg_match('/\b(pending|progress|completed?|complete|selesai)\b/iu', $message, $sm)) {
                $st = strtolower($sm[1]);
                if ($st === 'selesai') {
                    $st = 'complete';
                }
                $args['filters'] = [['field' => 'status', 'op' => '=', 'value' => $st]];
            }
        }
        // else: bare "list task" / "daftar task" → no status filter (all tasks)

        $r = $executor->execute('query_records', $args);
        $called[] = 'query_records';
        $results[] = [
            'tool' => 'query_records',
            'arguments' => $args,
            'result' => $this->tokenizeQueryResult($r, (string) ($args['model'] ?? 'task'), $tok),
        ];

        if ($isStatusChange) {
            $status = $this->extractTargetStatus($message) ?? 'complete';
            $uArgs = ['model' => 'task', 'values' => ['status' => $status], 'limit' => 5];
            $id = $this->extractTaskIdFromText($message);
            if ($id === null) {
                $rows = $r['data'] ?? [];
                if (is_array($rows) && count($rows) === 1 && isset($rows[0]['id'])) {
                    $id = (int) $rows[0]['id'];
                }
            }
            if ($id !== null) {
                $uArgs['id'] = $id;
                $ur = $executor->execute('update_record', $uArgs);
                $called[] = 'update_record';
                $results[] = ['tool' => 'update_record', 'arguments' => $uArgs, 'result' => $ur];
                if (($ur['status'] ?? '') === 'pending' && ! empty($ur['action'])) {
                    $pending = $ur['action'];
                }
            }
        }

        return ['results' => $results, 'called' => $called, 'pending' => $pending];
    }

    private function looksLikeStatusChangeIntent(string $message): bool
    {
        // Include casual Indo: jadiin, bikin selesai, etc. (jadiin ≠ \bjadi\b)
        $hasChange = (bool) preg_match(
            '/\b(ubah|ubdah|ganti|set|jadiin|jadikan|jadi|mark|update|tandai|selesaikan|ajukan|bikin)\b/iu',
            $message
        );
        $hasStatus = (bool) preg_match(
            '/\b(selesai|selesaikan|kelar|beres|completed?|complete|done|pending|progress|berjalan)\b/iu',
            $message
        );

        return $hasChange && $hasStatus;
    }

    /**
     * Propose update_record immediately so Livewire shows the approval card.
     * Does not call AI (avoids "curl retired" / fake approve chat).
     *
     * @param  array<int,array{role?:string,content?:string}>  $history
     * @return array{reply:string,pending:?array,model:string,metrics:array,driver:string}|null
     */
    private function tryLocalStatusUpdate(string $message, array $history = []): ?array
    {
        if (! auth()->check() || ! $this->looksLikeStatusChangeIntent($message)) {
            return null;
        }

        $status = $this->extractTargetStatus($message);
        if ($status === null) {
            return null;
        }

        $id = $this->extractTaskIdFromText($message);
        if ($id === null) {
            $id = $this->resolveTaskIdByTitle($message, $history);
        }

        if ($id === null) {
            return [
                'reply' => 'Saya belum yakin task mana. Sebutkan judul lengkap atau ID (mis. #12).',
                'pending' => null,
                'model' => 'local-update',
                'metrics' => ['source' => 'laravel_pre_update', 'need_context' => true],
                'driver' => 'local',
            ];
        }

        try {
            $ur = app(ToolExecutor::class)->execute('update_record', [
                'model' => 'task',
                'id' => $id,
                'values' => ['status' => $status],
            ]);
        } catch (\Throwable $e) {
            Log::debug('tryLocalStatusUpdate failed: '.$e->getMessage());

            return null;
        }

        if (($ur['status'] ?? '') === 'error') {
            return [
                'reply' => (string) ($ur['message'] ?? 'Gagal mengajukan update.'),
                'pending' => null,
                'model' => 'local-update',
                'metrics' => ['source' => 'laravel_pre_update', 'error' => true],
                'driver' => 'local',
            ];
        }

        if (($ur['status'] ?? '') !== 'pending' || empty($ur['action'])) {
            return null;
        }

        $action = $ur['action'];

        return [
            'reply' => $this->formatPendingReply($action),
            'pending' => $action,
            'model' => 'local-update',
            'metrics' => [
                'source' => 'laravel_pre_update',
                'tools_called' => ['update_record'],
                'task_id' => $id,
                'status' => $status,
            ],
            'driver' => 'local',
        ];
    }

    /**
     * @param  array<int,array{role?:string,content?:string}>  $history
     */
    private function resolveTaskIdByTitle(string $message, array $history = []): ?int
    {
        $title = $this->extractTitleHint($message);
        if ($title === null || mb_strlen($title) < 3) {
            return null;
        }

        try {
            $found = app(ToolExecutor::class)->execute('query_records', [
                'model' => 'task',
                'filters' => [['field' => 'title', 'op' => 'like', 'value' => $title]],
                'limit' => 10,
            ]);
        } catch (\Throwable $e) {
            return null;
        }

        $rows = $found['data'] ?? [];
        if (! is_array($rows) || $rows === []) {
            // Retry with first 3 words if full title too strict
            $words = preg_split('/\s+/u', $title) ?: [];
            $short = implode(' ', array_slice($words, 0, 3));
            if (mb_strlen($short) >= 3 && $short !== $title) {
                try {
                    $found = app(ToolExecutor::class)->execute('query_records', [
                        'model' => 'task',
                        'filters' => [['field' => 'title', 'op' => 'like', 'value' => $short]],
                        'limit' => 10,
                    ]);
                    $rows = $found['data'] ?? [];
                } catch (\Throwable $e) {
                    return null;
                }
            }
        }

        if (! is_array($rows) || $rows === []) {
            return null;
        }
        if (count($rows) === 1 && isset($rows[0]['id'])) {
            return (int) $rows[0]['id'];
        }

        // Prefer exact-ish title match (case-insensitive)
        $needle = mb_strtolower($title);
        foreach ($rows as $row) {
            $t = mb_strtolower((string) ($row['title'] ?? ''));
            if ($t === $needle || str_contains($t, $needle) || str_contains($needle, $t)) {
                return (int) $row['id'];
            }
        }

        return null;
    }

    private function formatPendingReply(array $action): string
    {
        $summary = (string) ($action['summary'] ?? 'Perubahan diajukan.');
        $label = $action['diff'][0]['label'] ?? null;
        $before = $action['diff'][0]['before']['status'] ?? null;
        $after = $action['values']['status'] ?? ($action['diff'][0]['after']['status'] ?? null);
        $id = $action['ids'][0] ?? null;

        $lines = [$summary];
        if ($label || $id) {
            $lines[] = '';
            $lines[] = '**'.($label ?: 'Task').'**'.($id ? " (ID: {$id})" : '');
        }
        if ($before !== null || $after !== null) {
            $lines[] = '- `status`: ~~'.($before ?? '—').'~~ → **'.($after ?? '—').'**';
        }
        $lines[] = '';
        $lines[] = 'Silakan **konfirmasi di kartu kuning di bawah** (Terapkan / Batalkan).';
        $lines[] = 'Belum tersimpan ke database sampai Anda setujui.';

        return implode("\n", $lines);
    }

    private function stripApprovalChatter(string $reply): string
    {
        // Only called when there is NO real pending → drop any misleading "approve in UI" chatter
        // so the user never sees "menunggu approval" without a card.
        $lines = preg_split('/\r\n|\r|\n/', $reply) ?: [$reply];
        $keep = [];
        foreach ($lines as $line) {
            if (preg_match(
                '/\b(approval|approve|menunggu|konfirmasi|kartu\s+(kuning|update|approve)|card\s+update|terapkan|batalkan)\b/iu',
                $line
            )) {
                continue;
            }
            $keep[] = $line;
        }
        $out = trim(implode("\n", $keep));

        return $out !== '' ? $out : trim($reply);
    }

    private function stripInfraChatter(string $reply): string
    {
        // Drop model rambling about curl / retired API / network.
        $lines = preg_split('/\r\n|\r|\n/', $reply) ?: [$reply];
        $keep = [];
        foreach ($lines as $line) {
            if (preg_match(
                '/\b(curl|172\.22|\/api\/agent|X-Agent-Token|retired|API unreachable|network blocker|service token)\b/iu',
                $line
            )) {
                continue;
            }
            $keep[] = $line;
        }
        $out = trim(implode("\n", $keep));

        return $out !== '' ? $out : trim($reply);
    }

    private function extractTargetStatus(string $text): ?string
    {
        if (preg_match('/\b(pending)\b/iu', $text)) {
            return 'pending';
        }
        if (preg_match('/\b(progress|berjalan)\b/iu', $text)) {
            return 'progress';
        }
        if (preg_match('/\b(selesai|selesaikan|kelar|beres|completed?|complete|done)\b/iu', $text)) {
            return 'complete';
        }

        return null;
    }

    private function extractTaskIdFromText(string $text): ?int
    {
        if (preg_match('/#\s*(\d+)\b/', $text, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/\b(?:task\s*)?id\s*[:=]?\s*(\d+)\b/iu', $text, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/\(\s*ID\s*:\s*(\d+)\s*\)/iu', $text, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/\bID\s*:\s*(\d+)\b/iu', $text, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    private function extractTitleHint(string $message): ?string
    {
        // Strip common command words, keep a title-ish fragment.
        $title = preg_replace(
            '/\b(ubah|ubdah|ganti|set|jadiin|jadikan|jadi|mark|update|status|selesai|selesaikan|kelar|beres|completed?|complete|done|pending|progress|berjalan|ke|menjadi|task|tugas|please|tolong|ya|dong|coba|ulang|ajukan|masih|belum|sudah|mau|saya|lagi|itu|ini|nya|yang|di|dari|approve|terapkan|tandai|bikin)\b/iu',
            ' ',
            $message
        ) ?? $message;
        $title = trim(preg_replace('/\s+/u', ' ', $title) ?? $title);
        $title = trim($title, " \t\n\r\0\x0B\"'.,!?:;—–-#");
        if (mb_strlen($title) < 2 || mb_strlen($title) > 120) {
            return null;
        }

        return $title;
    }

    /**
     * When the model only describes an update (ID + status + "approve"), create a real pending action.
     * Caller must already verify looksLikeStatusChangeIntent(userMessage).
     *
     * @param  array<int,array{role?:string,content?:string}>  $history
     */
    private function materializePendingFromTexts(string $userMessage, string $reply, array $history = []): ?array
    {
        if (! auth()->check()) {
            return null;
        }

        // Target status from the USER message only (not from a list table full of "pending").
        $status = $this->extractTargetStatus($userMessage);
        if ($status === null) {
            return null;
        }

        $id = $this->extractTaskIdFromText($userMessage);
        if ($id === null) {
            // Prefer a single id the model named in an update sentence, not every id in a table.
            if (preg_match(
                '/\b(?:update|ubah|ganti|jadikan|mark).*?\b(?:ID|id|#)\s*[:=]?\s*(\d+)\b/iu',
                $reply,
                $m
            )) {
                $id = (int) $m[1];
            } elseif (preg_match('/\(\s*ID\s*:\s*(\d+)\s*\)/iu', $reply, $m)
                && preg_match('/\b(update|ubah|jadi|complete|pending|progress)\b/iu', $reply)) {
                $id = (int) $m[1];
            }
        }
        if ($id === null) {
            $hist = collect($history)->map(fn ($m) => (string) ($m['content'] ?? ''))->implode("\n");
            $id = $this->extractTaskIdFromText($hist);
        }

        if ($id === null) {
            $title = $this->extractTitleHint($userMessage);
            if ($title) {
                try {
                    $found = app(ToolExecutor::class)->execute('query_records', [
                        'model' => 'task',
                        'filters' => [['field' => 'title', 'op' => 'like', 'value' => $title]],
                        'limit' => 5,
                    ]);
                    $rows = $found['data'] ?? [];
                    if (is_array($rows) && count($rows) === 1 && isset($rows[0]['id'])) {
                        $id = (int) $rows[0]['id'];
                    }
                } catch (\Throwable $e) {
                    return null;
                }
            }
        }

        if ($id === null) {
            return null;
        }

        try {
            $ur = app(ToolExecutor::class)->execute('update_record', [
                'model' => 'task',
                'id' => $id,
                'values' => ['status' => $status],
            ]);
        } catch (\Throwable $e) {
            Log::debug('materializePending failed: ' . $e->getMessage());

            return null;
        }

        if (($ur['status'] ?? '') === 'pending' && ! empty($ur['action'])) {
            return $ur['action'];
        }

        return null;
    }

    private function looksLikeNetworkExcuse(string $reply): bool
    {
        return (bool) preg_match(
            '/172\.22|:\s*8020|\/api\/agent|API (server|unreachable|tidak bisa)|network (blocker|routing)|firewall|X-Agent-Token|service token|konektivitas|tidak bisa diakses|infrastructure/iu',
            $reply
        );
    }

    /**
     * @param  list<array{tool?:string,result?:array}>  $toolResults
     */
    private function formatFromToolResults(array $toolResults, string $userMessage): string
    {
        foreach (array_reverse($toolResults) as $block) {
            $result = $block['result'] ?? null;
            if (! is_array($result) || ($result['status'] ?? '') !== 'ok') {
                continue;
            }
            $rows = $result['data'] ?? null;
            if (! is_array($rows)) {
                continue;
            }
            if ($rows === []) {
                return 'Tidak ada data yang cocok di database (query live dari Laravel).';
            }

            $lines = [
                '**Hasil live dari database** (Laravel ToolExecutor):',
                '',
                '| ID | Judul | Status | Priority | Tipe | Assignee | Project | Target |',
                '|---:|:------|:-------|:---------|:-----|:---------|:--------|:-------|',
            ];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $lines[] = sprintf(
                    '| %s | %s | %s | %s | %s | %s | %s | %s |',
                    $this->mdCell($row['id'] ?? ''),
                    $this->mdCell($row['title'] ?? ($row['name'] ?? '')),
                    $this->mdCell($row['status'] ?? ''),
                    $this->mdCell($row['priority'] ?? ''),
                    $this->mdCell($row['type'] ?? ''),
                    $this->mdCell($row['assigned_to_name'] ?? ($row['assigned_to'] ?? '—')),
                    $this->mdCell($row['project_name'] ?? ($row['project'] ?? '—')),
                    $this->mdCell($row['target_date'] ?? '—')
                );
            }
            $lines[] = '';
            $lines[] = '_Sumber: query_records di server app — bukan HTTP ke AI/API eksternal._';

            return implode("\n", $lines);
        }

        return 'Query Laravel dijalankan, tetapi tidak ada baris untuk ditampilkan.';
    }

    private function mdCell(mixed $value): string
    {
        $s = trim((string) $value);
        $s = str_replace('|', '\\|', $s);
        $s = str_replace(["\r\n", "\n", "\r"], ' ', $s);

        return $s === '' ? '—' : $s;
    }

    /**
     * OpenAI native tool_calls, or JSON fallback in content for weak models.
     *
     * @return list<array{id?:string,type?:string,function:array{name:string,arguments:mixed}}>
     */
    private function extractToolCalls(array $msg): array
    {
        if (! empty($msg['tool_calls']) && is_array($msg['tool_calls'])) {
            $out = [];
            foreach ($msg['tool_calls'] as $i => $call) {
                if (! is_array($call)) {
                    continue;
                }
                $fn = $call['function'] ?? null;
                if (! is_array($fn) || empty($fn['name'])) {
                    continue;
                }
                $out[] = [
                    'id' => (string) ($call['id'] ?? ('call_' . $i)),
                    'type' => $call['type'] ?? 'function',
                    'function' => [
                        'name' => (string) $fn['name'],
                        'arguments' => $fn['arguments'] ?? new \stdClass(),
                    ],
                ];
            }
            if ($out !== []) {
                return $out;
            }
        }

        // Fallback: model wrote a tool JSON in content
        $json = $this->extractJsonObject((string) ($msg['content'] ?? ''));
        if ($json && isset($json['name']) && in_array($json['name'], self::ALLOWED_TOOLS, true)) {
            return [[
                'id' => 'call_content_0',
                'type' => 'function',
                'function' => [
                    'name' => (string) $json['name'],
                    'arguments' => $json['arguments'] ?? ($json['parameters'] ?? []),
                ],
            ]];
        }

        // OpenAI-style { "tool_calls": [...] } inside content
        if ($json && ! empty($json['tool_calls']) && is_array($json['tool_calls'])) {
            return $this->extractToolCalls(['tool_calls' => $json['tool_calls'], 'content' => '']);
        }

        return [];
    }

    private function extractJsonObject(string $content): ?array
    {
        if ($content === '') {
            return null;
        }

        $candidate = null;
        if (preg_match('/```(?:json)?\s*(\{[\s\S]*?\})\s*```/', $content, $m)) {
            $candidate = $m[1];
        } else {
            $start = strpos($content, '{');
            $end = strrpos($content, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $candidate = substr($content, $start, $end - $start + 1);
            }
        }

        if ($candidate === null) {
            return null;
        }

        $decoded = json_decode($candidate, true);

        return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : null;
    }

    private function decodeArgs(mixed $args): array
    {
        if (is_array($args)) {
            return $args;
        }
        if (is_string($args)) {
            $decoded = json_decode($args, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * @param  array<int,array{role?:string,content?:string}>  $history
     * @return list<array{role:string,content:string}>
     */
    private function sanitizeHistory(array $history): array
    {
        return collect($history)
            ->filter(fn ($m) => in_array($m['role'] ?? '', ['user', 'assistant'], true))
            ->map(fn ($m) => [
                'role' => (string) $m['role'],
                'content' => (string) ($m['content'] ?? ''),
            ])
            ->slice(-20)
            ->values()
            ->all();
    }
}
