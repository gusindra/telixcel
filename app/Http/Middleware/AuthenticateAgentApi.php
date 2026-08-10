<?php

namespace App\Http\Middleware;

use App\Models\AgentUserToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Auth for POST /api/agent.
 *
 * Accepted:
 *  1) Per-user token (preferred): Authorization: Bearer agt_…  OR X-Agent-Token: agt_…
 *     → user derived from token; scopes = token.abilities
 *  2) Service token (legacy Hermes): X-Agent-Token: AGENT_API_TOKEN + X-Agent-User-Id
 *     → full tools for that user (service barrier)
 */
class AuthenticateAgentApi
{
    public function handle(Request $request, Closure $next)
    {
        $provided = (string) ($request->header('X-Agent-Token')
            ?? $request->bearerToken()
            ?? '');

        if ($provided === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing agent token (X-Agent-Token or Bearer).',
            ], 401);
        }

        // --- Per-user token ---
        if (str_starts_with($provided, 'agt_')) {
            $row = AgentUserToken::findByPlainToken($provided);
            if (! $row) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid user agent token.',
                ], 401);
            }

            $user = User::find($row->user_id);
            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token user not found.',
                ], 404);
            }

            $row->touchLastUsed();
            Auth::login($user);
            $request->setUserResolver(fn () => $user);
            $request->attributes->set('agent_token', $row);
            $request->attributes->set('agent_abilities', $row->abilities ?? AgentUserToken::DEFAULT_ABILITIES);

            return $next($request);
        }

        // --- Shared service token + user id header ---
        $expected = (string) config('services.agent_api.token', '');
        if ($expected === '' || ! hash_equals($expected, $provided)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or missing agent API token.',
            ], 401);
        }

        $userId = (int) ($request->header('X-Agent-User-Id')
            ?? $request->input('user_id')
            ?? 0);

        if ($userId <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'X-Agent-User-Id header is required with service token.',
            ], 400);
        }

        $user = User::find($userId);
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => "User {$userId} not found.",
            ], 404);
        }

        Auth::login($user);
        $request->setUserResolver(fn () => $user);
        // Service token = full default abilities
        $request->attributes->set('agent_token', null);
        $request->attributes->set('agent_abilities', ['read', 'update_limited', 'update', 'report']);

        return $next($request);
    }
}
