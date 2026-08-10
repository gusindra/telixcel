<?php

namespace App\Services\Agent;

use Illuminate\Support\Facades\Http;

/**
 * Ollama / local OpenAI-compatible tool-calling loop for AI Console.
 * Used when AI_DRIVER=ollama (query/update DB via ToolExecutor).
 */
class OllamaAgentService
{
    public function __construct(private ToolExecutor $executor)
    {
    }

    /**
     * @param  array  $history  Prior messages: [['role'=>'user'|'assistant', 'content'=>...], ...]
     * @param  string|null  $model  Optional model override (used for cloud-vs-local benchmarking).
     * @return array{reply:string, pending:?array, model:string, metrics:array}
     */
    public function run(array $history, string $userMessage, ?string $model = null): array
    {
        $model = $model
            ?: config('services.ollama.model')
            ?: config('services.ai.model')
            ?: 'gemma4:31b-cloud';

        $messages = array_merge(
            [['role' => 'system', 'content' => $this->systemPrompt()]],
            $this->sanitizeHistory($history),
            [['role' => 'user', 'content' => $userMessage]],
        );

        $pending = null;
        $metrics = [];
        $max = max(1, (int) (config('services.ollama.max_iterations') ?: config('services.ai.max_iterations', 6)));

        for ($i = 0; $i < $max; $i++) {
            $response = $this->chat($messages, $model);
            $metrics[] = $this->extractMetrics($response);
            $msg = $response['message'] ?? [];

            $toolCalls = $this->extractToolCalls($msg);

            if (empty($toolCalls)) {
                return [
                    'reply' => trim($msg['content'] ?? '') ?: '(no response)',
                    'pending' => $pending,
                    'model' => $model,
                    'metrics' => $metrics,
                ];
            }

            $messages[] = [
                'role' => 'assistant',
                'content' => $msg['content'] ?? '',
                'tool_calls' => $toolCalls,
            ];

            foreach ($toolCalls as $call) {
                $name = $call['function']['name'] ?? '';
                $args = $this->decodeArgs($call['function']['arguments'] ?? []);
                $result = $this->executor->execute($name, $args);

                $toolMsg = [
                    'role' => 'tool',
                    'content' => $result['status'] === 'pending'
                        ? json_encode(['status' => 'awaiting_user_approval', 'summary' => $result['action']['summary']])
                        : json_encode($result),
                ];

                if (! empty($call['id'])) {
                    $toolMsg['tool_call_id'] = $call['id'];
                }

                $messages[] = $toolMsg;

                if ($result['status'] === 'pending') {
                    $pending = $result['action'];
                }
            }

            // A destructive proposal short-circuits the loop; the UI handles approval.
            if ($pending) {
                return [
                    'reply' => $pending['summary'] . ' Please review and approve below.',
                    'pending' => $pending,
                    'model' => $model,
                    'metrics' => $metrics,
                ];
            }
        }

        return [
            'reply' => 'Reached the step limit for this request. Please refine or split your request.',
            'pending' => $pending,
            'model' => $model,
            'metrics' => $metrics,
        ];
    }

    private function normalizeResponse(array $response, ?bool $isOpenAI = null): array
    {
        $isOpenAI = $isOpenAI ?? str_contains((string) config('services.ollama.base_url', ''), '/v1');

        if ($isOpenAI) {
            return [
                'message' => $response['choices'][0]['message'] ?? [],
                'model' => $response['model'] ?? null,
                'total_duration' => $response['usage']['total_tokens'] ?? null,
                'eval_count' => $response['usage']['completion_tokens'] ?? null,
                'eval_duration' => $response['usage']['prompt_tokens'] ?? null,
            ];
        }

        return $response;
    }

    private function chat(array $messages, string $model): array
    {
        $baseUrl = rtrim((string) config('services.ollama.base_url', 'http://localhost:11434'), '/');
        $apiKey = config('services.ollama.api_key') ?: config('services.ai.api_key');
        $timeout = (int) (config('services.ollama.timeout') ?: config('services.ai.timeout', 120));
        $isOpenAI = str_contains($baseUrl, '/v1');

        $request = Http::timeout($timeout)->acceptJson();

        if ($apiKey) {
            $request = $request->withToken($apiKey);
        }

        $url = $isOpenAI
            ? $baseUrl . '/chat/completions'
            : $baseUrl . '/api/chat';

        $payload = $isOpenAI
            ? [
                'model' => $model,
                'messages' => $messages,
                'tools' => ToolSchemas::all(),
                'stream' => false,
                'temperature' => 0.1,
            ]
            : [
                'model' => $model,
                'messages' => $messages,
                'tools' => ToolSchemas::all(),
                'stream' => false,
                'options' => ['temperature' => 0.1],
            ];

        $response = $request->post($url, $payload);
        $response->throw();

        return $this->normalizeResponse($response->json() ?? [], $isOpenAI);
    }

    /**
     * Handles BOTH Ollama's native message.tool_calls AND a fallback where the
     * model (e.g. some Gemma variants) emits a JSON tool call inside content,
     * optionally fenced in a ```json block.
     */
    private function extractToolCalls(array $msg): array
    {
        if (! empty($msg['tool_calls']) && is_array($msg['tool_calls'])) {
            return $msg['tool_calls'];
        }

        $json = $this->extractJsonObject($msg['content'] ?? '');
        if ($json && isset($json['name'])) {
            return [[
                'function' => [
                    'name' => $json['name'],
                    'arguments' => $json['arguments'] ?? ($json['parameters'] ?? []),
                ],
            ]];
        }

        return [];
    }

    private function extractJsonObject(string $content): ?array
    {
        if ($content === '') {
            return null;
        }

        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $content, $m)) {
            $candidate = $m[1];
        } elseif (preg_match('/(\{(?:[^{}]|(?R))*\})/s', $content, $m)) {
            $candidate = $m[1];
        } else {
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

    private function extractMetrics(array $response): array
    {
        return [
            'model' => $response['model'] ?? null,
            'total_duration' => $response['total_duration'] ?? null,
            'eval_count' => $response['eval_count'] ?? null,
            'eval_duration' => $response['eval_duration'] ?? null,
        ];
    }

    private function sanitizeHistory(array $history): array
    {
        return collect($history)
            ->filter(fn ($m) => in_array($m['role'] ?? '', ['user', 'assistant'], true))
            ->map(fn ($m) => ['role' => $m['role'], 'content' => (string) ($m['content'] ?? '')])
            ->values()
            ->all();
    }

    private function systemPrompt(): string
    {
        $user = auth()->user();

        return bind_to_template([
            'SCHEMA_CONTEXT' => SchemaContext::build(),
            'username' => $user->name ?? 'admin',
            'userid' => $user->id ?? 0,
            'now' => now()->toDateTimeString(),
        ], $this->template());
    }

    private function template(): string
    {
        return "You are an AI assistant for Telixcel, a project management platform.\n\n"
            . "{SCHEMA_CONTEXT}\n\n"
            . "CURRENT USER: {username} (ID: {userid})\n"
            . "CURRENT DATETIME: {now}\n\n"
            . "CROSS-MODEL QUERY PATTERN:\n"
            . "When asked for cross-model data (e.g. \"projects with running tasks\"):\n"
            . "1. First query the child model (e.g. task status=progress, limit=50) to collect linking IDs.\n"
            . "2. Then query the parent model (e.g. project) with op=\"in\" on the id column.\n"
            . "3. Summarise both results clearly.\n\n"
            . "DATE FILTER PATTERN:\n"
            . "- Use op=\"<\" or \"<=\" on datetime columns for expiry/deadline queries.\n"
            . "- Use op=\">\" or \">=\" for future/not-started queries.\n"
            . "- Date format: YYYY-MM-DD or YYYY-MM-DD HH:MM:SS.\n"
            . "- Example — contracts expiring in 30 days: filter expired_at <= (today + 30 days), status=active.\n\n"
            . "COMMON QUERIES (use these as guides):\n"
            . "- Projects with running tasks → query task (status=progress) → project_ids → query project (id in [...])\n"
            . "- Contracts expiring soon → query contract (expired_at <= next-30-days AND status=active)\n"
            . "- Project activity report → query task (project_id=X), summarise count per status\n"
            . "- Overdue tasks → query task (status!=complete AND target_date < today)\n\n"
            . "UPDATE BY ID:\n"
                        . "- When listing records, always include the ID field so users can reference specific records.\n"
                        . "- When the user asks to update a specific record, use the \"id\" parameter — NOT filters.\n"
                        . "- Use \"filters\" only for bulk operations.\n\n"
                        . "MONTHLY REPORT (2-STEP):\n"
                        . "- Step 1: When user asks for a monthly report, call generate_report.\n"
                        . "- Step 2: Display the report summary + task lists in chat. Use a clear table or bullet format.\n"
                        . "- Step 3: Ask the user: \"Mau download laporan ini sebagai PDF?\"\n"
                        . "- Step 4: If user confirms (yes/ya/oke/pdf/download), call download_report with the report_id from step 1.\n"
                        . "- If user says no/tidak/display aja, do nothing — report stays in chat.\n"
                        . "- Super Admin → generate_report type=admin. Regular user → type=user.\n"
                        . "- Default to current month/year if not specified.\n\n"
                        . "RULES:\n"
                        . "- You can only UPDATE records — you CANNOT create or delete.\n"
                        . "- Always confirm before UPDATE; show the diff first.\n"
                        . "- Never expose internal IDs unless asked.\n"
                        . "- Ask for clarification when the request is ambiguous.\n"
                        . "- Answer in the same language the user uses (Indonesian or English).";
    }
}
