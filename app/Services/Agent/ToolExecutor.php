<?php

namespace App\Services\Agent;

use App\Models\AgentActionLog;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * Executes the agent's tool calls against the database, enforcing the
 * ModelRegistry whitelist, RBAC permissions, and safe filtering.
 *
 * READ and CREATE execute immediately. UPDATE and DELETE never write here —
 * they return a structured 'pending' proposal for the UI approval flow.
 */
class ToolExecutor
{
    private const ALLOWED_OPS = ['=', '!=', 'like', 'in', '>', '<', '>=', '<='];
    private const MAX_LIMIT = 50;

    public function execute(string $tool, array $args): array
    {
        try {
            $result = match ($tool) {
                'query_records' => $this->query($args),
                'update_record' => $this->proposeUpdate($args),
                'generate_report'  => $this->generateReport($args),
                'download_report'  => $this->downloadReport($args),
                default => $this->err("Unknown tool: {$tool}"),
            };
        } catch (\Throwable $e) {
            $result = $this->err('Tool error: ' . $e->getMessage());
        }

        $this->log($tool, $args, $result);

        // Hermes/API path: surface pending proposals to the Livewire approval card.
        if (($result['status'] ?? '') === 'pending' && ! empty($result['action'])) {
            $uid = (int) (auth()->id() ?? 0);
            if ($uid > 0) {
                PendingActionStore::put($uid, $result['action']);
            }
        }

        return $result;
    }

    private function query(array $a): array
    {
        [$reg, $err] = $this->guard($a['model'] ?? '');
        if ($err) {
            return $err;
        }

        $q = $reg['class']::query();
        $this->applyFilters($q, $a['filters'] ?? [], $reg);

        $limit = min(self::MAX_LIMIT, max(1, (int) ($a['limit'] ?? 20)));

        // Task queries: load names so "siapa ditugaskan" answers without extra joins.
        $modelKey = strtolower((string) ($a['model'] ?? ''));
        if ($modelKey === 'task') {
            $q->with([
                'assignedTo:id,name',
                'owner:id,name',
                'project:id,name',
            ]);
        }

        $rows = $q->limit($limit)->get($reg['readable']);

        return [
            'status' => 'ok',
            'message' => "Found {$rows->count()} {$reg['label']} record(s).",
            'data' => $rows->map(function ($r) use ($reg, $modelKey) {
                $row = $r->only($reg['readable']);
                if ($modelKey === 'task') {
                    $row['assigned_to_name'] = $r->assignedTo?->name;
                    $row['owner_name'] = $r->owner?->name;
                    $row['project_name'] = $r->project?->name;
                }

                return $row;
            })->all(),
        ];
    }

    private function create(array $a): array
    {
        [$reg, $err] = $this->guard($a['model'] ?? '');
        if ($err) {
            return $err;
        }

        $values = $this->onlyWritable($a['values'] ?? [], $reg);
        if (empty($values)) {
            return $this->err('No writable values provided.');
        }

        if ($enumErr = $this->validateEnums($values, $reg)) {
            return $this->err($enumErr);
        }

        $record = $reg['class']::create($values);

        return [
            'status' => 'ok',
            'message' => "Created {$reg['label']} record.",
            'data' => $record->only($reg['readable']),
        ];
    }

    private function proposeUpdate(array $a): array
    {
        [$reg, $err] = $this->guard($a['model'] ?? '');
        if ($err) {
            return $err;
        }

        $values = $this->onlyWritable($a['values'] ?? [], $reg);
        if (empty($values)) {
            return $this->err('No writable values to update.');
        }

        if ($enumErr = $this->validateEnums($values, $reg)) {
            return $this->err($enumErr);
        }

        $q = $reg['class']::query();

        // Single-record by ID takes precedence over filters.
        if (! empty($a['id'])) {
            $q->where($q->getModel()->getKeyName(), (int) $a['id']);
        } else {
            $this->applyFilters($q, $a['filters'] ?? [], $reg);
        }

        $targets = $q->limit(self::MAX_LIMIT)->get();

        if ($targets->isEmpty()) {
            return $this->err('No matching records to update.');
        }

        $key = $targets->first()->getKeyName();

        $diff = $targets->map(fn ($r) => [
            'label' => $this->recordLabel($r),
            'before' => collect($values)->mapWithKeys(fn ($v, $k) => [$k => $r->{$k}])->all(),
            'after' => $values,
        ])->all();

        return [
            'status' => 'pending',
            'action' => [
                'type' => 'update',
                'model' => strtolower($a['model']),
                'permission' => $reg['permission'],
                'ids' => $targets->pluck($key)->all(),
                'values' => $values,
                'diff' => $diff,
                'summary' => "Update {$targets->count()} {$reg['label']} record(s).",
            ],
        ];
    }

    private function proposeDelete(array $a): array
    {
        [$reg, $err] = $this->guard($a['model'] ?? '');
        if ($err) {
            return $err;
        }

        $q = $reg['class']::query();

        // Single-record by ID takes precedence over filters.
        if (! empty($a['id'])) {
            $q->where($q->getModel()->getKeyName(), (int) $a['id']);
        } else {
            $this->applyFilters($q, $a['filters'] ?? [], $reg);
        }

        $targets = $q->limit(self::MAX_LIMIT)->get();

        if ($targets->isEmpty()) {
            return $this->err('No matching records to delete.');
        }

        $key = $targets->first()->getKeyName();
        $soft = in_array(SoftDeletes::class, class_uses_recursive($reg['class']), true);

        return [
            'status' => 'pending',
            'action' => [
                'type' => 'delete',
                'model' => strtolower($a['model']),
                'permission' => $reg['permission'],
                'ids' => $targets->pluck($key)->all(),
                'soft' => $soft,
                'diff' => $targets->map(fn ($r) => [
                    'label' => $this->recordLabel($r),
                    'before' => $r->only($reg['readable']),
                ])->all(),
                'summary' => "Delete {$targets->count()} {$reg['label']} record(s)"
                    . ($soft ? ' (soft delete).' : ' (PERMANENT).'),
            ],
        ];
    }

    /** @return array{0:?array,1:?array} [registryEntry, errorOrNull] */
    private function guard(string $modelKey): array
    {
        $reg = ModelRegistry::resolve($modelKey);
        if (! $reg) {
            return [null, $this->err("Model '{$modelKey}' is not allowed.")];
        }
        if (! checkPermisissions([$reg['permission']])) {
            return [null, $this->err("You don't have permission to access {$reg['label']} data.")];
        }

        return [$reg, null];
    }

    private function applyFilters($query, array $filters, array $reg): void
    {
        foreach ($filters as $f) {
            // Accept both {field,op,value} objects AND [field,op,value] indexed arrays
            if (array_is_list($f) && count($f) >= 3) {
                $field = $f[0];
                $op    = $f[1];
                $value = $f[2];
            } else {
                // Accept field (tool schema) or column (Hermes skill / API docs).
                $field = $f['field'] ?? $f['column'] ?? null;
                $op    = $f['op']    ?? '=';
                $value = $f['value'] ?? null;
            }

            if (! in_array($field, $reg['readable'], true)) {
                continue;
            }
            if (! in_array($op, self::ALLOWED_OPS, true)) {
                continue;
            }

            if ($op === 'in') {
                $query->whereIn($field, (array) $value);
            } elseif ($op === 'like') {
                $query->where($field, 'like', '%' . $value . '%');
            } else {
                $query->where($field, $op, $value);
            }
        }
    }

    private function onlyWritable(array $values, array $reg): array
    {
        return collect($values)->only($reg['writable'])->all();
    }

    /**
     * Validate enum-constrained fields (type, priority, status) against the
     * registry's declared allowed values. Returns an error string or null.
     */
    private function validateEnums(array $values, array $reg): ?string
    {
        $checks = [
            'status'   => $reg['statuses']   ?? null,
            'type'     => $reg['types']       ?? null,
            'priority' => $reg['priorities']  ?? null,
        ];

        foreach ($checks as $field => $allowed) {
            if ($allowed === null || ! array_key_exists($field, $values)) {
                continue;
            }
            if (! in_array($values[$field], $allowed, true)) {
                return "Invalid value \"{$values[$field]}\" for \"{$field}\". "
                    . 'Allowed: ' . implode(', ', $allowed) . '.';
            }
        }

        return null;
    }

    private function recordLabel($record): string
    {
        return $record->name
            ?? $record->title
            ?? ($record->no ? (string) $record->no : null)
            ?? ('#' . $record->getKey());
    }

    private function err(string $message): array
    {
        return ['status' => 'error', 'message' => $message];
    }

    private function generateReport(array $a): array
    {
        $type = $a['type'] ?? 'user';
        if (! in_array($type, ['admin', 'user'], true)) {
            return $this->err('Report type must be "admin" or "user".');
        }

        if ($type === 'admin' && ! is_task_manager()) {
            return $this->err('Only Super Admin can generate admin reports. Use type "user" for personal reports.');
        }

        $month = max(1, min(12, (int) ($a['month'] ?? now()->month)));
        $year  = max(2020, (int) ($a['year'] ?? now()->year));

        $user = \App\Models\User::find(auth()->id());
        $data = app(\App\Services\ReportService::class)->generate($user, $month, $year);

        // Save report record so we can later dispatch PDF generation
        $report = \App\Models\Report::create([
            'user_id'  => auth()->id(),
            'type'     => $type,
            'month'    => $month,
            'year'     => $year,
            'status'   => 'generated',
            'metadata' => $data['summary'],
        ]);

        return [
            'status'  => 'ok',
            'message' => "Report data for {$type} — {$month}/{$year} ready. Display the summary to the user and ask if they want to download as PDF.",
            'data'    => array_merge($data, ['report_id' => $report->id]),
        ];
    }

    private function downloadReport(array $a): array
    {
        $reportId = (int) ($a['report_id'] ?? 0);
        if (! $reportId) {
            return $this->err('report_id is required.');
        }

        $report = \App\Models\Report::where('user_id', auth()->id())->find($reportId);
        if (! $report) {
            return $this->err('Report not found.');
        }
        if ($report->status !== 'generated') {
            return $this->err("Report status is '{$report->status}', not 'generated'. Cannot generate PDF.");
        }

        $report->update(['status' => 'pending']);
        \App\Jobs\GenerateMonthlyReport::dispatch($report->id);

        return [
            'status'  => 'ok',
            'message' => 'PDF sedang digenerate di background. Anda akan diberi notifikasi saat sudah siap.',
            'data'    => ['report_id' => $report->id],
        ];
    }

    private function log(string $tool, array $args, array $result): void
    {
        try {
            if (! Schema::hasTable('agent_action_logs')) {
                return;
            }
            AgentActionLog::create([
                'user_id' => auth()->id(),
                'tool' => $tool,
                'model_key' => $args['model'] ?? null,
                'arguments' => $args,
                'result' => $result,
                'status' => $result['status'] === 'pending' ? 'proposed' : $result['status'],
            ]);
        } catch (\Throwable $e) {
            // Audit logging must never break the agent flow.
        }
    }
}
