<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class ReportService
{
    public function generate(User $user, int $month, int $year): array
    {
        return is_task_manager()
            ? $this->adminReport($month, $year)
            : $this->userReport($user, $month, $year);
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

        $pending = Task::where('status', '!=', 'complete')
            ->with(['owner:id,name', 'project:id,name', 'assignedTo:id,name'])
            ->orderBy('target_date')->get();

        return [
            'type' => 'admin', 'user_name' => 'Admin', 'month' => $month, 'year' => $year,
            'per_user' => $perUser, 'completed_total' => $completed->count(),
            'pending' => $pending->map(fn (Task $t) => [
                'id' => $t->id, 'title' => $t->title, 'project' => $t->project?->name ?? '—',
                'type' => $t->type, 'status' => $t->status, 'priority' => $t->priority,
                'assigned_to' => $t->assignedTo?->name ?? $t->owner?->name ?? '—',
                'target_date' => $t->target_date?->format('Y-m-d'),
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

        $types = my_task_types();
        $pending = Task::where('status', '!=', 'complete')->whereIn('type', $types)
            ->where(fn ($q) => $q->where('assigned_to', $user->id)->orWhere('owner_id', $user->id))
            ->with(['project:id,name'])->orderBy('target_date')->get();

        return [
            'type' => 'user', 'user_name' => $user->name, 'month' => $month, 'year' => $year,
            'completed' => $completed->map(fn (Task $t) => [
                'id' => $t->id, 'title' => $t->title, 'project' => $t->project?->name ?? '—',
                'type' => $t->type, 'priority' => $t->priority, 'completed_at' => $t->updated_at?->format('Y-m-d H:i'),
            ])->values()->all(), 'completed_total' => $completed->count(),
            'pending' => $pending->map(fn (Task $t) => [
                'id' => $t->id, 'title' => $t->title, 'project' => $t->project?->name ?? '—',
                'type' => $t->type, 'status' => $t->status, 'priority' => $t->priority,
                'target_date' => $t->target_date?->format('Y-m-d'),
            ])->values()->all(), 'pending_total' => $pending->count(),
            'summary' => ['total_completed' => $completed->count(), 'total_pending' => $pending->count()],
        ];
    }
}
