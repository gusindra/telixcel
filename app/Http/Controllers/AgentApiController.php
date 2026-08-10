<?php

namespace App\Http\Controllers;

use App\Services\Agent\ModelRegistry;
use App\Services\Agent\ToolExecutor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Satu API internal untuk Hermes / agent eksternal.
 *
 * POST /api/agent
 *   { "action": "health" }
 *   { "action": "schema" }
 *   { "action": "query", "model": "task", "filters": [], "limit": 20 }
 *   { "action": "execute", "tool": "query_records", "arguments": { ... } }
 *
 * Auth: middleware agent.api (X-Agent-Token + X-Agent-User-Id).
 */
class AgentApiController extends Controller
{
    private const TOOLS = ['query_records', 'update_record', 'generate_report', 'download_report'];

    public function __invoke(Request $request, ToolExecutor $executor): JsonResponse
    {
        $action = strtolower((string) $request->input('action', ''));

        // Shorthand: body has "tool" without action → execute
        if ($action === '' && $request->filled('tool')) {
            $action = 'execute';
        }
        if ($action === '') {
            $action = 'health';
        }

        return match ($action) {
            'health' => $this->health(),
            'schema' => $this->schema(),
            'query' => $this->query($request, $executor),
            'execute' => $this->execute($request, $executor),
            default => response()->json([
                'status' => 'error',
                'message' => 'Unknown action. Use: health, schema, query, execute.',
            ], 422),
        };
    }

    private function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'telixcel-agent-api',
            'user_id' => auth()->id(),
            'models' => ModelRegistry::keys(),
            'actions' => ['health', 'schema', 'query', 'execute'],
            'time' => now()->toDateTimeString(),
        ]);
    }

    private function schema(): JsonResponse
    {
        $map = [];
        foreach (ModelRegistry::map() as $key => $reg) {
            $map[$key] = [
                'label' => $reg['label'],
                'permission' => $reg['permission'],
                'readable' => $reg['readable'],
                'writable' => $reg['writable'],
                'statuses' => $reg['statuses'] ?? [],
                'types' => $reg['types'] ?? [],
                'priorities' => $reg['priorities'] ?? [],
                'notes' => $reg['notes'] ?? null,
            ];
        }

        return response()->json([
            'status' => 'ok',
            'models' => $map,
            'tools' => [
                'query_records' => 'Read/list records (immediate).',
                'update_record' => 'Propose UPDATE (returns pending; UI approval).',
                'generate_report' => 'Monthly task report data.',
                'download_report' => 'Queue PDF for a generated report_id.',
            ],
            'filter_ops' => ['=', '!=', 'like', 'in', '>', '<', '>=', '<='],
            'max_limit' => 50,
        ]);
    }

    private function query(Request $request, ToolExecutor $executor): JsonResponse
    {
        if ($deny = $this->denyToolIfScoped($request, 'query_records')) {
            return $deny;
        }

        $args = [
            'model' => $request->input('model'),
            'filters' => $request->input('filters', []),
            'limit' => $request->input('limit', 20),
        ];

        if (empty($args['model'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'model is required (e.g. task, project, order).',
            ], 422);
        }

        $result = $executor->execute('query_records', $args);

        return response()->json($result, $this->httpStatus($result));
    }

    private function execute(Request $request, ToolExecutor $executor): JsonResponse
    {
        $tool = (string) $request->input('tool', '');
        $args = $request->input('arguments', []);
        if (! is_array($args)) {
            $args = [];
        }

        if (! in_array($tool, self::TOOLS, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unknown or disallowed tool. Allowed: ' . implode(', ', self::TOOLS),
            ], 422);
        }

        if ($deny = $this->denyToolIfScoped($request, $tool)) {
            return $deny;
        }

        $result = $executor->execute($tool, $args);

        return response()->json($result, $this->httpStatus($result));
    }

    /** Enforce per-user token abilities (read / update_limited). */
    private function denyToolIfScoped(Request $request, string $tool): ?JsonResponse
    {
        /** @var \App\Models\AgentUserToken|null $token */
        $token = $request->attributes->get('agent_token');
        if (! $token) {
            return null; // service token — full tools
        }

        if (! $token->allowsTool($tool)) {
            return response()->json([
                'status' => 'error',
                'message' => "Tool \"{$tool}\" not allowed for this user token (abilities: "
                    . implode(', ', $token->abilities ?? []) . ').',
                'abilities' => $token->abilities,
            ], 403);
        }

        return null;
    }

    private function httpStatus(array $result): int
    {
        return ($result['status'] ?? '') === 'error' ? 422 : 200;
    }
}
