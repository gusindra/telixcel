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

            $message = $this->buildUpdateDetail($action, $reg, $count);

            return $this->result(true, $message, $action);
        }

        if (($action['type'] ?? null) === 'delete') {
            $count = 0;
            $deletedLabels = [];
            foreach ($ids as $id) {
                $record = $class::find($id);
                if ($record) {
                    $label = $record->name
                        ?? $record->title
                        ?? ($record->no ? (string) $record->no : null)
                        ?? ('#' . $record->getKey());
                    $deletedLabels[] = $label;
                    $record->delete();
                    $count++;
                }
            }

            $message = $this->buildDeleteDetail($action, $reg, $count, $deletedLabels);

            return $this->result(true, $message, $action);
        }

        return $this->result(false, 'Unknown action type.', $action);
    }

    /**
     * Build a detailed update confirmation message showing what changed.
     */
    private function buildUpdateDetail(array $action, array $reg, int $count): string
    {
        $label = $reg['label'];
        $diff = $action['diff'] ?? [];
        $ids = $action['ids'] ?? [];

        $lines = ["Berhasil update {$count} {$label}:"];
        $lines[] = '';

        foreach ($diff as $i => $row) {
            $recordId = $ids[$i] ?? '?';
            $lines[] = "**{$row['label']}** (ID: {$recordId})";

            foreach ($row['after'] as $field => $newValue) {
                $oldValue = $row['before'][$field] ?? '—';
                $lines[] = "- `{$field}`: ~~{$oldValue}~~ → **{$newValue}**";
            }

            if ($i < count($diff) - 1) {
                $lines[] = '';
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Build a detailed delete confirmation message.
     */
    private function buildDeleteDetail(array $action, array $reg, int $count, array $labels): string
    {
        $label = $reg['label'];
        $soft = $action['soft'] ?? false;
        $type = $soft ? 'soft-delete' : 'hapus permanen';

        $lines = ["Berhasil {$type} {$count} {$label}:"];
        $lines[] = '';

        foreach ($labels as $i => $name) {
            $lines[] = "- **{$name}**";
        }

        return implode("\n", $lines);
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
