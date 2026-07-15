<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Task;
use Carbon\Carbon;
use Livewire\Component;

class GanttView extends Component
{
    /** Visible window size in months: 3, 6 or 12. */
    public $scale = 12;

    /** Start of the visible window (ISO date, first day of a month). */
    public $periodStartIso;

    /** Flattened rows: project header -> main tasks -> subtasks. */
    public $rows = [];

    /** Month header cells for the current window. */
    public $months = [];

    /** Label of the current window, e.g. "Jan 2026 - Dec 2026". */
    public $windowLabel = '';

    /** Today indicator left percentage (null if today is outside the window). */
    public $todayLeftPercent = null;

    /** Projects shown in the chart (with name and metadata). */
    public $chartProjects = [];

    /** When set, the timeline shows only this project's rows; the cards stay clickable. */
    public $selectedProjectId = null;

    public function mount()
    {
        $earliest = $this->visibleTasks()->min('created_at');
        $start = $earliest ? Carbon::parse($earliest) : Carbon::now();
        $this->periodStartIso = $start->copy()->startOfMonth()->toDateString();
        $this->build();
    }

    public function setScale($months)
    {
        $this->scale = in_array((int) $months, [3, 6, 12], true) ? (int) $months : 12;
        $this->build();
    }

    public function prev()
    {
        $this->periodStartIso = Carbon::parse($this->periodStartIso)->subMonths($this->scale)->startOfMonth()->toDateString();
        $this->build();
    }

    public function next()
    {
        $this->periodStartIso = Carbon::parse($this->periodStartIso)->addMonths($this->scale)->startOfMonth()->toDateString();
        $this->build();
    }

    public function today()
    {
        $this->periodStartIso = Carbon::now()->startOfMonth()->toDateString();
        $this->build();
    }

    /** Click a project card to show only its timeline; click it again to show all. */
    public function selectProject($projectId)
    {
        $this->selectedProjectId = ((int) $this->selectedProjectId === (int) $projectId) ? null : (int) $projectId;
        $this->build();
    }

    /** Clear the project filter and show every project's timeline again. */
    public function clearProjectFilter()
    {
        $this->selectedProjectId = null;
        $this->build();
    }

    /** Tasks the current user may see (active-role type + invited projects + team). */
    private function visibleTasks()
    {
        $teamId = auth()->user()->currentTeam?->id;
        if (! $teamId) {
            return collect();
        }
        $invited = my_invited_project_ids();

        return Task::whereHas('project', function ($p) use ($teamId, $invited) {
                $p->where('team_id', $teamId);
                if ($invited !== null) {
                    $p->whereIn('id', $invited);
                }
            })
            ->forMyType()
            ->with(['project', 'owner', 'assignedTo', 'children' => fn ($q) => $q->orderBy('created_at')])
            ->orderBy('created_at')
            ->get();
    }

    private function build()
    {
        $winStart = Carbon::parse($this->periodStartIso)->startOfMonth();
        $winEnd = $winStart->copy()->addMonths($this->scale - 1)->endOfMonth();
        $totalDays = $winStart->diffInDays($winEnd) + 1;
        $this->windowLabel = $winStart->format('M Y') . ' - ' . $winEnd->format('M Y');

        // Month header cells
        $this->months = [];
        $cursor = $winStart->copy();
        for ($i = 0; $i < $this->scale; $i++) {
            $this->months[] = [
                'label' => $cursor->format('M'),
                'year' => $cursor->format('Y'),
                'left' => round($winStart->diffInDays($cursor) / $totalDays * 100, 4),
                'width' => round($cursor->daysInMonth / $totalDays * 100, 4),
            ];
            $cursor->addMonth();
        }

        // Today indicator
        $now = Carbon::now()->startOfDay();
        if ($now->between($winStart, $winEnd)) {
            $this->todayLeftPercent = round($winStart->diffInDays($now) / $totalDays * 100, 4);
        } else {
            $this->todayLeftPercent = null;
        }

        // Group visible tasks by project
        $allTasks = $this->visibleTasks();
        $byProject = $allTasks->groupBy('project_id');

        $rows = [];
        $chartProjects = [];

        foreach ($byProject as $projectId => $projectTasks) {
            $project = $projectTasks->first()->project;
            if (! $project) {
                continue;
            }

            $chartProjects[] = [
                'id' => $projectId,
                'name' => $project->name,
                'type' => $project->type,
                'status' => $project->status,
            ];

            // When a project is selected, build rows for that project only (cards stay clickable).
            if ($this->selectedProjectId !== null && (int) $this->selectedProjectId !== (int) $projectId) {
                continue;
            }

            // Project row
            $minStart = $projectTasks->pluck('created_at')->filter()->min();
            $maxEnd = $projectTasks->pluck('target_date')->filter()->max() ?: $minStart;
            $rows[] = [
                'kind' => 'project',
                'depth' => 0,
                'label' => $project->name,
                'status' => null,
                'start' => $minStart ? Carbon::parse($minStart)->format('d M Y') : null,
                'end' => $maxEnd ? Carbon::parse($maxEnd)->format('d M Y') : null,
                'bar' => $this->bar($minStart, $maxEnd, $winStart, $winEnd, $totalDays),
                'color' => '#6366f1',
                'task_id' => null,
                'parent_id' => null,
                'root_task_id' => null,
                'has_children' => false,
                'assigned_to' => null,
                'progress' => null,
                'raw_start' => $minStart ? Carbon::parse($minStart)->toDateString() : null,
                'raw_end' => $maxEnd ? Carbon::parse($maxEnd)->toDateString() : null,
            ];

            // Build main task / subtask tree
            $ids = $projectTasks->pluck('id')->all();
            $byParent = $projectTasks->groupBy('parent_id');

            // Root tasks: parent_id == 0 or null or parent_id not in this project's task IDs
            $roots = $projectTasks->filter(fn ($t) => (int) $t->parent_id === 0 || ! in_array($t->parent_id, $ids));

            // Sort roots by created_at
            $roots = $roots->sortBy('created_at');

            foreach ($roots as $root) {
                $children = $byParent->get($root->id, collect())->sortBy('created_at');
                $hasChildren = $children->isNotEmpty();

                $this->emitTaskRow($rows, $root, $hasChildren, 1, $winStart, $winEnd, $totalDays, null, $root->id);

                if ($hasChildren) {
                    foreach ($children as $child) {
                        $grandChildren = $byParent->get($child->id, collect())->sortBy('created_at');
                        $hasGrandChildren = $grandChildren->isNotEmpty();

                        $this->emitTaskRow($rows, $child, $hasGrandChildren, 2, $winStart, $winEnd, $totalDays, $root->id, $root->id);

                        foreach ($grandChildren as $grandChild) {
                            $this->emitTaskRow($rows, $grandChild, false, 3, $winStart, $winEnd, $totalDays, $child->id, $root->id);
                        }
                    }
                }
            }
        }

        $this->chartProjects = $chartProjects;
        $this->rows = $rows;
    }

    private function emitTaskRow(&$rows, $task, $hasChildren, $depth, $winStart, $winEnd, $totalDays, $parentId = null, $rootTaskId = null)
    {
        $kind = $depth === 1 ? 'main_task' : 'sub_task';
        $assignedName = $task->assignedTo?->name ?? $task->owner?->name ?? 'Unassigned';
        $progress = $this->progressLabel($task->status);

        $rows[] = [
            'kind' => $kind,
            'depth' => $depth,
            'task_id' => $task->id,
            'parent_id' => $parentId,
            'root_task_id' => $rootTaskId ?? $task->id,
            'label' => $task->title,
            'status' => $task->status,
            'start' => $task->created_at ? Carbon::parse($task->created_at)->format('d M Y') : null,
            'end' => $task->target_date ? Carbon::parse($task->target_date)->format('d M Y') : null,
            'bar' => $this->bar($task->created_at, $task->target_date, $winStart, $winEnd, $totalDays),
            'color' => $this->color($task->status, $task->target_date),
            'has_children' => $hasChildren,
            'assigned_to' => $assignedName,
            'progress' => $progress,
            'raw_start' => $task->created_at ? Carbon::parse($task->created_at)->toDateString() : null,
            'raw_end' => $task->target_date ? Carbon::parse($task->target_date)->toDateString() : null,
        ];
    }

    /** Position a bar inside the window as left/width percentages, or null if outside. */
    private function bar($start, $end, $winStart, $winEnd, $totalDays)
    {
        if (! $start) {
            return null;
        }
        $s = Carbon::parse($start)->startOfDay();
        $e = $end ? Carbon::parse($end)->startOfDay() : $s->copy();
        if ($e->lt($s)) {
            $e = $s->copy();
        }
        if ($e->lt($winStart) || $s->gt($winEnd)) {
            return null;
        }
        $cs = $s->lt($winStart) ? $winStart->copy() : $s;
        $ce = $e->gt($winEnd) ? $winEnd->copy() : $e;

        return [
            'left' => round($winStart->diffInDays($cs) / $totalDays * 100, 4),
            'width' => round(max(($cs->diffInDays($ce) + 1) / $totalDays * 100, 0.7), 4),
        ];
    }

    private function color($status, $targetDate = null)
    {
        // Overdue: not complete and target_date is in the past
        if ($status !== 'complete' && $targetDate) {
            $target = Carbon::parse($targetDate)->startOfDay();
            if ($target->lt(Carbon::now()->startOfDay())) {
                return '#ef4444'; // Red for overdue
            }
        }

        return match ($status) {
            'complete' => '#22c55e',
            'progress' => '#3b82f6',
            'pending' => '#9ca3af',
            'declined', 'cancelled', 'aborted' => '#ef4444',
            default => '#9ca3af',
        };
    }

    private function progressLabel($status)
    {
        return match ($status) {
            'complete' => '100%',
            'progress' => 'In Progress',
            'pending' => '0%',
            'declined' => 'Declined',
            'cancelled' => 'Cancelled',
            'aborted' => 'Aborted',
            default => ucfirst($status),
        };
    }

    public function render()
    {
        return view('livewire.dashboard.gantt-view');
    }
}
