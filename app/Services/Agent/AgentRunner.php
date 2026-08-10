<?php

namespace App\Services\Agent;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Console — OpenAI-compatible via AI_* env only (telixcel).
 *
 *   AI_DRIVER=hermes|ollama
 *   AI_ENDPOINT=http://127.0.0.1:8645/v1/chat/completions
 *   AI_API_KEY=...
 *   AI_MODEL=telixcel
 *   AI_TIMEOUT=180
 *
 * Hermes: POST AI_ENDPOINT. Ollama: tool-loop (OLLAMA_*).
 */
class AgentRunner
{
    public function __construct(private OllamaAgentService $ollama)
    {
    }

    /**
     * @return array{reply:string, pending:?array, model:string, metrics:array, driver:string}
     */
    public function run(array $history, string $userMessage, ?int $chatId = null): array
    {
        // Status updates → propose locally so the yellow approval card appears immediately.
        // Hermes often only *talks* about approval without calling /api/agent.
        if ($local = $this->tryLocalStatusUpdate($userMessage, $history)) {
            return $local;
        }

        if ($this->driver() === 'ollama') {
            $result = $this->ollama->run($history, $userMessage);
            $result['driver'] = 'ollama';
            if (empty($result['pending'])) {
                $result['pending'] = PendingActionStore::get(auth()->id());
            }

            return $result;
        }

        $result = $this->runRemoteChat($history, $userMessage, $chatId);
        $result['driver'] = 'hermes';
        // Hermes may have called POST /api/agent mid-request → store has the proposal.
        if (empty($result['pending'])) {
            $result['pending'] = PendingActionStore::get(auth()->id());
        }

        return $result;
    }

    public function driver(): string
    {
        return strtolower(trim((string) config('services.ai.driver', 'hermes'))) === 'ollama'
            ? 'ollama'
            : 'hermes';
    }

    /**
     * Satu URL penuh ke chat completions (never /responses — that path hangs/times out).
     * AI_ENDPOINT full URL, or AI_BASE_URL (+ /chat/completions).
     */
    public static function endpoint(): string
    {
        $full = trim((string) config('services.ai.endpoint', ''));
        if ($full !== '') {
            return self::forceChatCompletions(rtrim($full, '/'));
        }

        $base = rtrim((string) (config('services.ai.base_url') ?: 'http://127.0.0.1:8645/v1'), '/');

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

    /** Normalize any mistaken /responses URL to /chat/completions. */
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
        if (strtolower((string) config('services.ai.driver', 'hermes')) === 'ollama') {
            return;
        }
        if (! filter_var(config('services.ai.warmup', true), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        $ttl = max(30, (int) config('services.ai.warmup_ttl', 240));
        if (! Cache::add('ai:warmup:lock', 1, now()->addSeconds($ttl))) {
            return;
        }

        try {
            $endpoint = self::endpoint();
            $key = (string) (config('services.ai.api_key') ?: '');
            if ($endpoint === '') {
                return;
            }
            $timeout = max(3, (int) config('services.ai.warmup_timeout', 8));
            $http = Http::timeout($timeout)->acceptJson();
            if ($key !== '') {
                $http = $http->withToken($key);
            }
            // health: strip /v1/chat/completions → host root
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
     * @return array{reply:string, pending:?array, model:string, metrics:array, hermes_response_id:?string}
     */
    private function runRemoteChat(array $history, string $userMessage, ?int $chatId): array
    {
        $endpoint = self::endpoint();
        $apiKey = (string) (config('services.ai.api_key') ?: '');
        $model = (string) (config('services.ai.model') ?: 'telixcel');
        $timeout = (int) (config('services.ai.timeout') ?: 180);

        if ($endpoint === '') {
            throw new \RuntimeException(
                'AI endpoint kosong. Set AI_ENDPOINT (contoh: http://127.0.0.1:8645/v1/chat/completions)'
            );
        }

        $userId = auth()->id() ?? 0;
        $conversation = "telixcel-user-{$userId}-chat-" . ($chatId ?: 'new');
        $sessionKey = "agent:telixcel:web:user-{$userId}:chat-" . ($chatId ?: 'new');

        $user = auth()->user();

        // Token per user dari DB (read + update_limited). Mint if belum ada.
        $agentPlain = null;
        $agentAbilities = [];
        if ($user) {
            try {
                [$tokenRow, $agentPlain] = \App\Models\AgentUserToken::ensureForUser($user);
                $agentAbilities = $tokenRow->abilities ?? \App\Models\AgentUserToken::DEFAULT_ABILITIES;
                // Pastikan ability read ada (query_records).
                if (! in_array('read', $agentAbilities, true)) {
                    $agentAbilities = array_values(array_unique(array_merge(
                        $agentAbilities,
                        \App\Models\AgentUserToken::DEFAULT_ABILITIES
                    )));
                    $tokenRow->forceFill(['abilities' => $agentAbilities])->save();
                }
            } catch (\Throwable $e) {
                Log::debug('Agent user token skipped: ' . $e->getMessage());
            }
        }

        // Base URL API = APP_URL + /api/agent (tanpa AGENT_API_BASE_URL terpisah).
        $agentApiUrl = url('/api/agent');
        $ctx = [
            'current_user' => [
                'id' => $user->id ?? 0,
                'name' => $user->name ?? 'admin',
                'company_id' => $user->current_team_id ?? null,
            ],
            'conversation_id' => $chatId,
            'datetime' => now()->toDateTimeString(),
            'source' => 'telixcel-ai-console',
            // Hermes: panggil API Laravel dengan token user ini saja (agt_…).
            'agent_api' => [
                'base_url' => $agentApiUrl,
                'token' => $agentPlain,
                'abilities' => $agentAbilities,
                'auth_header' => 'X-Agent-Token',
                'examples' => [
                    'health' => ['action' => 'health'],
                    'list_tasks' => ['action' => 'query', 'model' => 'task', 'limit' => 30],
                    'query_records' => [
                        'action' => 'execute',
                        'tool' => 'query_records',
                        'arguments' => ['model' => 'task', 'limit' => 30],
                    ],
                ],
            ],
        ];

        // Preload tasks for context only — AI still answers (no instant skip of thinking).
        $snapshot = $this->maybeTaskAssignmentSnapshot($userMessage);
        if ($snapshot !== null) {
            $ctx['data_snapshot'] = $snapshot;
        }

        $input = "<request_context>\n"
            . json_encode($ctx, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            . "\n</request_context>\n\n"
            . $userMessage;

        $messages = array_merge(
            [[
                'role' => 'system',
                'content' => 'You are the Telixcel AI Console assistant. '
                    . 'Use current_user for isolation. Be concise. Prefer user language (ID/EN). '
                    . 'Do not invent business data. '
                    . 'If request_context.data_snapshot.tasks is present, answer from that data (no tools). '
                    . 'When listing tasks, ALWAYS use a Markdown table with columns: '
                    . 'ID | Judul | Status | Priority | Tipe | Assignee | Project | Target. '
                    . 'NEVER use terminal/shell to dig files, MySQL, or Hermes sessions for Telixcel CRM data. '
                    . 'If you need more data: one curl POST agent_api.base_url with header '
                    . 'X-Agent-Token: agent_api.token (token agt_ milik current_user di DB, ability read). '
                    . 'Body: action=query model=task (atau execute tool=query_records). '
                    . 'Jangan pakai token lain / service token. Stop after 1-2 API calls. '
                    . 'Updates only via update_record (pending approval).',
            ]],
            collect($history)
                ->filter(fn ($m) => in_array($m['role'] ?? '', ['user', 'assistant'], true))
                ->map(fn ($m) => ['role' => $m['role'], 'content' => (string) ($m['content'] ?? '')])
                ->slice(-6)
                ->values()
                ->all(),
            [['role' => 'user', 'content' => $input]],
        );

        $http = Http::timeout($timeout)->acceptJson();
        if ($apiKey !== '') {
            $http = $http->withToken($apiKey);
        }

        try {
            $response = $http->withHeaders([
                'X-Hermes-Session-Key' => $sessionKey,
            ])->post($endpoint, [
                'model' => $model,
                'messages' => $messages,
                'stream' => false,
                'user' => $conversation,
            ]);
            $response->throw();
            $json = $response->json() ?? [];
        } catch (\Throwable $e) {
            // Local fallback if Hermes hangs but we already loaded a snapshot for related questions.
            if ($snapshot !== null) {
                Log::warning('Hermes timeout/error; using data_snapshot fallback: '.$e->getMessage());

                return [
                    'reply' => $this->formatTaskTableReply($snapshot, $userMessage),
                    'pending' => null,
                    'model' => 'local-snapshot',
                    'metrics' => [
                        'fallback' => 'data_snapshot',
                        'error' => $e->getMessage(),
                        'conversation' => $conversation,
                        'endpoint' => $endpoint,
                    ],
                    'hermes_response_id' => null,
                ];
            }
            throw $e;
        }

        $content = $json['choices'][0]['message']['content'] ?? '';
        $reply = trim(is_string($content) ? $content : json_encode($content)) ?: '(no response)';

        if (! empty($json['hermes']['failed']) || ($json['choices'][0]['finish_reason'] ?? '') === 'error') {
            throw new \RuntimeException('AI error: ' . ($json['hermes']['error'] ?? $reply));
        }

        return [
            'reply' => $reply,
            'pending' => null,
            'model' => $json['model'] ?? $model,
            'metrics' => [
                'usage' => $json['usage'] ?? null,
                'conversation' => $conversation,
                'endpoint' => $endpoint,
                'snapshot' => $snapshot !== null,
            ],
            'hermes_response_id' => $json['id'] ?? null,
        ];
    }

    private function looksLikeAssignmentQuestion(string $message): bool
    {
        return (bool) preg_match(
            '/\b(tugas|task|assign|ditugaskan|tugaskan|assignee|siapa\s+aja|yang\s+di\s*tugas)\b/iu',
            $message
        );
    }

    private function looksLikeTaskDataQuestion(string $message): bool
    {
        return (bool) preg_match(
            '/\b(tugas|task|assign|ditugaskan|tugaskan|assignee|project|proyek|job\s*list|status\s+task|daftar\s+task|table|tabel)\b/iu',
            $message
        );
    }

    /**
     * Compact task list with assignee names for the current user (RBAC via forMyType).
     *
     * @return array{tasks:list<array>,count:int,generated_at:string}|null
     */
    private function maybeTaskAssignmentSnapshot(string $userMessage): ?array
    {
        if (! $this->looksLikeTaskDataQuestion($userMessage)) {
            return null;
        }
        if (! auth()->check()) {
            return null;
        }

        try {
            $tasks = \App\Models\Task::query()
                ->forMyType()
                ->with([
                    'assignedTo:id,name',
                    'owner:id,name',
                    'project:id,name',
                ])
                ->orderByDesc('updated_at')
                ->limit(40)
                ->get([
                    'id', 'project_id', 'title', 'type', 'status', 'priority',
                    'target_date', 'owner_id', 'assigned_to', 'updated_at',
                ]);

            return [
                'count' => $tasks->count(),
                'generated_at' => now()->toDateTimeString(),
                'tasks' => $tasks->map(fn ($t) => [
                    'id' => $t->id,
                    'title' => $t->title,
                    'status' => $t->status,
                    'priority' => $t->priority,
                    'type' => $t->type,
                    'project' => $t->project?->name,
                    'owner' => $t->owner?->name,
                    'assigned_to' => $t->assignedTo?->name,
                    'assigned_to_id' => $t->assigned_to,
                    'target_date' => optional($t->target_date)->toDateString(),
                ])->all(),
            ];
        } catch (\Throwable $e) {
            Log::debug('Task snapshot skipped: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Always answer list/assignment snapshots as a Markdown table (renders in AI Console).
     */
    private function formatTaskTableReply(array $snapshot, string $userMessage = ''): string
    {
        $tasks = $snapshot['tasks'] ?? [];
        if ($tasks === []) {
            return 'Tidak ada task yang terlihat untuk akun Anda saat ini.';
        }

        $count = (int) ($snapshot['count'] ?? count($tasks));
        $lines = [
            "**Daftar task** ({$count} baris):",
            '',
            '| ID | Judul | Status | Priority | Tipe | Assignee | Project | Target |',
            '|---:|:------|:-------|:---------|:-----|:---------|:--------|:-------|',
        ];

        foreach ($tasks as $t) {
            $lines[] = sprintf(
                '| %s | %s | %s | %s | %s | %s | %s | %s |',
                $this->mdCell($t['id'] ?? ''),
                $this->mdCell($t['title'] ?? ''),
                $this->mdCell($t['status'] ?? ''),
                $this->mdCell($t['priority'] ?? ''),
                $this->mdCell($t['type'] ?? ''),
                $this->mdCell($t['assigned_to'] ?? '—'),
                $this->mdCell($t['project'] ?? '—'),
                $this->mdCell($t['target_date'] ?? '—')
            );
        }

        // Optional short summary by assignee when the user asked "siapa ditugaskan".
        if ($this->looksLikeAssignmentQuestion($userMessage)
            && preg_match('/\b(siapa|assign|ditugaskan|tugaskan|assignee)\b/iu', $userMessage)) {
            $by = [];
            foreach ($tasks as $t) {
                $name = $t['assigned_to'] ?? '(belum ditugaskan)';
                $by[$name] = ($by[$name] ?? 0) + 1;
            }
            ksort($by);
            $lines[] = '';
            $lines[] = '**Ringkas per assignee:** '.collect($by)
                ->map(fn ($n, $name) => "{$name} ({$n})")
                ->implode(', ');
        }

        return implode("\n", $lines);
    }

    private function mdCell(mixed $value): string
    {
        $s = trim((string) $value);
        // Escape pipes so markdown tables don't break.
        $s = str_replace('|', '\\|', $s);
        $s = str_replace(["\r\n", "\n", "\r"], ' ', $s);

        return $s === '' ? '—' : $s;
    }

    /**
     * Detect status-change intents and propose update_record immediately
     * so Livewire can show the approval card in the same turn.
     *
     * @param  array<int,array{role?:string,content?:string}>  $history
     * @return array{reply:string,pending:?array,model:string,metrics:array,driver:string}|null
     */
    private function tryLocalStatusUpdate(string $message, array $history = []): ?array
    {
        if (! auth()->check()) {
            return null;
        }

        if (! preg_match('/\b(ubah|ubdah|ganti|set|jadi|jadikan|mark|update|status|ajukan|approve|terapkan)\b/iu', $message)) {
            return null;
        }
        if (! preg_match('/\b(selesai|complete|done|pending|progress|berjalan)\b/iu', $message)) {
            return null;
        }

        $status = 'complete';
        if (preg_match('/\b(pending)\b/iu', $message)) {
            $status = 'pending';
        } elseif (preg_match('/\b(progress|berjalan)\b/iu', $message)) {
            $status = 'progress';
        } elseif (preg_match('/\b(selesai|complete|done)\b/iu', $message)) {
            $status = 'complete';
        }

        $ref = $this->resolveTaskReference($message, $history);
        if ($ref === null) {
            return [
                'reply' => 'Saya belum yakin task mana yang dimaksud. Sebutkan judul (mis. Write Articles) atau ID (mis. #5).',
                'pending' => null,
                'model' => 'local-update',
                'metrics' => ['source' => 'local_status_update', 'need_context' => true],
                'driver' => 'local',
            ];
        }

        $args = [
            'model' => 'task',
            'values' => ['status' => $status],
            'limit' => 5,
        ];
        if (isset($ref['id'])) {
            $args['id'] = $ref['id'];
        } else {
            $args['filters'] = [
                ['field' => 'title', 'op' => 'like', 'value' => $ref['title']],
            ];
        }

        try {
            /** @var ToolExecutor $executor */
            $executor = app(ToolExecutor::class);
            $result = $executor->execute('update_record', $args);
        } catch (\Throwable $e) {
            Log::debug('Local status update failed: '.$e->getMessage());

            return null;
        }

        if (($result['status'] ?? '') === 'error') {
            return [
                'reply' => (string) ($result['message'] ?? 'Gagal mengajukan update.'),
                'pending' => null,
                'model' => 'local-update',
                'metrics' => ['source' => 'local_status_update', 'error' => true, 'ref' => $ref],
                'driver' => 'local',
            ];
        }

        if (($result['status'] ?? '') !== 'pending' || empty($result['action'])) {
            return null;
        }

        $this->rememberTaskContext($result['action']);

        $summary = (string) ($result['action']['summary'] ?? 'Perubahan diajukan.');
        $label = $result['action']['diff'][0]['label'] ?? ($ref['title'] ?? ('#'.($ref['id'] ?? '?')));

        return [
            'reply' => $summary."\n\nTarget: **{$label}** → status `{$status}`.\n\n"
                .'Silakan **konfirmasi di kartu di bawah** (Terapkan / Batalkan). '
                .'Belum tersimpan ke database sampai Anda setujui.',
            'pending' => $result['action'],
            'model' => 'local-update',
            'metrics' => ['source' => 'local_status_update', 'ref' => $ref],
            'driver' => 'local',
        ];
    }

    /**
     * @param  array<int,array{role?:string,content?:string}>  $history
     * @return array{id?:int,title?:string}|null
     */
    private function resolveTaskReference(string $message, array $history): ?array
    {
        if (preg_match('/#\s*(\d+)\b/', $message, $m) || preg_match('/\b(?:task\s*)?id\s*[:=]?\s*(\d+)\b/iu', $message, $m)) {
            return ['id' => (int) $m[1]];
        }

        $title = $this->extractTaskTitleFromText($message);
        if ($title !== null) {
            return ['title' => $title];
        }

        $blob = collect($history)
            ->reverse()
            ->take(12)
            ->map(fn ($m) => (string) ($m['content'] ?? ''))
            ->implode("\n");

        if (preg_match('/\b(?:task\s*)?id\s*[:=]?\s*(\d+)\b/iu', $blob, $m)
            || preg_match('/#\s*(\d+)\b/', $blob, $m)
            || preg_match('/\bID\s+(\d+)\b/u', $blob, $m)) {
            return ['id' => (int) $m[1]];
        }

        if (preg_match('/\*\*([^*]{2,80})\*\*/u', $blob, $m)) {
            $t = $this->cleanTitleCandidate($m[1]);
            if ($t) {
                return ['title' => $t];
            }
        }
        if (preg_match('/\b(?:task|tugas|status)\s+([A-Za-z0-9][A-Za-z0-9 \/\-_.]{1,60})/iu', $blob, $m)) {
            $t = $this->cleanTitleCandidate($m[1]);
            if ($t) {
                return ['title' => $t];
            }
        }

        return $this->lastRememberedTask();
    }

    private function extractTaskTitleFromText(string $text): ?string
    {
        $title = preg_replace(
            '/\b(ubah|ubdah|ganti|set|jadi|jadikan|mark|update|status|selesai|complete|done|pending|progress|berjalan|ke|menjadi|task|tugas|please|tolong|ya|dong|coba|ulang|ajukan|masih|belum|sudah|mau|saya|lagi|itu|ini|nya|yang|untuk|di|dari|approve|terapkan)\b/iu',
            ' ',
            $text
        ) ?? $text;
        $title = $this->cleanTitleCandidate($title);

        if ($title === null || preg_match('/^(coba|ya|dong|please|ok|oke)$/iu', $title)) {
            return null;
        }

        return $title;
    }

    private function cleanTitleCandidate(string $raw): ?string
    {
        $title = trim(preg_replace('/\s+/u', ' ', $raw) ?? $raw);
        $title = trim($title, " \t\n\r\0\x0B\"'.,!?:;—–-");
        if (mb_strlen($title) < 2 || mb_strlen($title) > 120) {
            return null;
        }

        return $title;
    }

    private function rememberTaskContext(array $action): void
    {
        $uid = (int) (auth()->id() ?? 0);
        if ($uid <= 0) {
            return;
        }
        $id = $action['ids'][0] ?? null;
        $label = $action['diff'][0]['label'] ?? null;
        $payload = array_filter([
            'id' => $id ? (int) $id : null,
            'title' => is_string($label) ? $label : null,
        ]);
        if ($payload === []) {
            return;
        }
        Cache::put("agent:last_task:user:{$uid}", $payload, now()->addHours(2));
    }

    /** @return array{id?:int,title?:string}|null */
    private function lastRememberedTask(): ?array
    {
        $uid = (int) (auth()->id() ?? 0);
        if ($uid <= 0) {
            return null;
        }
        $v = Cache::get("agent:last_task:user:{$uid}");
        if (! is_array($v)) {
            return null;
        }
        if (! empty($v['id'])) {
            return ['id' => (int) $v['id']];
        }
        if (! empty($v['title']) && is_string($v['title'])) {
            return ['title' => $v['title']];
        }

        return null;
    }
}
