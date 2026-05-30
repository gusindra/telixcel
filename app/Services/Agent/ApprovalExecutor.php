<?php

namespace App\Services\Agent;

use App\Models\AgentActionLog;
use Illuminate\Support\Facades\Schema;

/**
 * Executes a previously proposed UPDATE/DELETE after the user approves it in
 * the UI. Re-checks permissions before touching the database.
 */
class ApprovalExecutor
{
    public function apply(array $action): array
    {
        $reg = ModelRegistry::resolve($action['model'] ?? '');
        if (! $reg) {
            return $this->result(false, 'This model is no longer allowed.', $action);
        }
        if (! checkPermisissions([$reg['permission']])) {
            return $this->result(false, "You don't have permission for {$reg['label']}.", $action);
        }

        $class = $reg['class'];
        $key = (new $class)->getKeyName();
        $ids = $action['ids'] ?? [];

        if (empty($ids)) {
            return $this->result(false, 'No target records to act on.', $action);
        }

        if (($action['type'] ?? null) === 'update') {
            $values = collect($action['values'] ?? [])->only($reg['writable'])->all();
            $count = $class::whereIn($key, $ids)->update($values);

            return $this->result(true, "Updated {$count} {$reg['label']} record(s).", $action);
        }

        if (($action['type'] ?? null) === 'delete') {
            $count = 0;
            foreach ($ids as $id) {
                $record = $class::find($id);
                if ($record) {
                    $record->delete();
                    $count++;
                }
            }

            return $this->result(true, "Deleted {$count} {$reg['label']} record(s).", $action);
        }

        return $this->result(false, 'Unknown action type.', $action);
    }

    private function result(bool $ok, string $message, array $action): array
    {
        $this->log($ok, $message, $action);

        return ['ok' => $ok, 'message' => $message];
    }

    private function log(bool $ok, string $message, array $action): void
    {
        try {
            if (! Schema::hasTable('agent_action_logs')) {
                return;
            }
            AgentActionLog::create([
                'user_id' => auth()->id(),
                'tool' => ($action['type'] ?? 'unknown') . '_record',
                'model_key' => $action['model'] ?? null,
                'arguments' => $action,
                'result' => ['ok' => $ok, 'message' => $message],
                'status' => $ok ? 'approved' : 'error',
            ]);
        } catch (\Throwable $e) {
            // Never break the approval flow on logging errors.
        }
    }
}
