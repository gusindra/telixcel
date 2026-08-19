<?php

namespace App\Services\Agent;

use Illuminate\Support\Facades\Cache;

/**
 * Handoff UPDATE/DELETE proposals from the agent API (AI curl) back to
 * the Livewire AI Console. AI cannot set Livewire $pendingAction itself.
 *
 * Kept until approve/reject (forget) so a late poll still finds it.
 */
class PendingActionStore
{
    private static function key(int $userId): string
    {
        return "agent:pending_action:user:{$userId}";
    }

    public static function put(int $userId, array $action): void
    {
        if ($userId <= 0 || empty($action)) {
            return;
        }

        Cache::put(self::key($userId), $action, now()->addMinutes(30));
    }

    /** Read without removing (safe for runner + poll). */
    public static function get(?int $userId): ?array
    {
        if (! $userId || $userId <= 0) {
            return null;
        }

        $action = Cache::get(self::key($userId));

        return is_array($action) ? $action : null;
    }

    /** @deprecated use get() + forget() — kept for callers that want one-shot */
    public static function pull(?int $userId): ?array
    {
        $action = self::get($userId);
        if ($action !== null) {
            self::forget($userId);
        }

        return $action;
    }

    public static function forget(?int $userId): void
    {
        if (! $userId || $userId <= 0) {
            return;
        }

        Cache::forget(self::key($userId));
    }
}
