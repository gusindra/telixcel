<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;

class ReportService
{
    public function generate(User $user, int $month, int $year): array
    {
        return is_task_manager()
            ? $this->adminReport($month, $year)
            : $this->userReport($user, $month, $year);
    }

    /** "Agustus 2026" for headings. */
    private function periodLabel(int $month, int $year): string
    {
        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        return ($months[$month] ?? $month) . ' ' . $year;
    }

    private function adminReport(int $month, int $year): array
    {
        $completed = Task::where('status', 'complete')
            ->whereMonth('updated_at', $month)->whereYear('updated_at', $year)
            ->with(['owner:id,name', 'project:id,name', 'assignedTo:id,name'])
            ->orderBy('updated_at', 'desc')->get();

        $byUser = $completed->groupBy(fn (Task $t) => $t->assignedTo?->name ?? $t->owner?->name ?? 'Unassigned');

        $perUser = [];
        foreach ($byUser as $userName => $tasks) {
            $perUser[] = [
                'user_name' => $userName, 'task_count' => $tasks->count(),
                'tasks' => $tasks->map(fn (Task $t) => [
                    'id' => $t->id, 'title' => $t->title,
                    'project' => $t->project?->name ?? '—', 'type' => $t->type,
                    'priority' => $t->priority, 'completed_at' => $t->updated_at?->format('Y-m-d H:i'),
                ])->values()->all(),
            ];
        }

        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
        $today = Carbon::now()->startOfDay();

        // Outstanding as of the report month: overdue + due within the month
        // (target_date <= end of month), plus open tasks with no deadline.
        // Future-dated tasks (next month onward) are excluded.
        $pending = Task::where('status', '!=', 'complete')
            ->where(fn ($q) => $q->whereNull('target_date')->orWhere('target_date', '<=', $endOfMonth))
            ->with(['owner:id,name', 'project:id,name', 'assignedTo:id,name'])
            ->orderBy('target_date')->get();

        return [
            'type' => 'admin', 'user_name' => 'Admin', 'month' => $month, 'year' => $year,
            'period_label' => $this->periodLabel($month, $year),
            'per_user' => $perUser, 'completed_total' => $completed->count(),
            'pending' => $pending->map(fn (Task $t) => [
                'id' => $t->id, 'title' => $t->title, 'project' => $t->project?->name ?? '—',
                'type' => $t->type, 'status' => $t->status, 'priority' => $t->priority,
                'assigned_to' => $t->assignedTo?->name ?? $t->owner?->name ?? '—',
                'target_date' => $t->target_date?->format('Y-m-d'),
                'overdue' => $t->target_date && $t->target_date->lt($today),
            ])->values()->all(), 'pending_total' => $pending->count(),
            'summary' => ['total_completed' => $completed->count(), 'total_pending' => $pending->count(), 'total_users' => count($perUser)],
        ];
    }

    private function userReport(User $user, int $month, int $year): array
    {
        $completed = Task::where('status', 'complete')
            ->where(fn ($q) => $q->where('assigned_to', $user->id)->orWhere('owner_id', $user->id))
            ->whereMonth('updated_at', $month)->whereYear('updated_at', $year)
            ->with(['project:id,name'])->orderBy('updated_at', 'desc')->get();

        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
        $today = Carbon::now()->startOfDay();

        $types = my_task_types();
        $pending = Task::where('status', '!=', 'complete')->whereIn('type', $types)
            ->where(fn ($q) => $q->where('assigned_to', $user->id)->orWhere('owner_id', $user->id))
            ->where(fn ($q) => $q->whereNull('target_date')->orWhere('target_date', '<=', $endOfMonth))
            ->with(['project:id,name'])->orderBy('target_date')->get();

        return [
            'type' => 'user', 'user_name' => $user->name, 'month' => $month, 'year' => $year,
            'period_label' => $this->periodLabel($month, $year),
            'completed' => $completed->map(fn (Task $t) => [
                'id' => $t->id, 'title' => $t->title, 'project' => $t->project?->name ?? '—',
                'type' => $t->type, 'priority' => $t->priority, 'completed_at' => $t->updated_at?->format('Y-m-d H:i'),
            ])->values()->all(), 'completed_total' => $completed->count(),
            'pending' => $pending->map(fn (Task $t) => [
                'id' => $t->id, 'title' => $t->title, 'project' => $t->project?->name ?? '—',
                'type' => $t->type, 'status' => $t->status, 'priority' => $t->priority,
                'target_date' => $t->target_date?->format('Y-m-d'),
                'overdue' => $t->target_date && $t->target_date->lt($today),
            ])->values()->all(), 'pending_total' => $pending->count(),
            'summary' => ['total_completed' => $completed->count(), 'total_pending' => $pending->count()],
        ];
    }
}
