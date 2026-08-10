<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Project;
use App\Models\Task;
use Livewire\Component;

class DashboardOverview extends Component
{
    public $projects;
    public $totalProjects = 0;
    public $totalTasks = 0;
    public $tasksInProgress = 0;
    public $tasksCompleted = 0;
    public $projectStats = [];
    public $selectedProject = null;
    public $tasksByStatus = [];
    public $selectedProjectTasks = [];

    /**
     * When set (e.g. user detail page), the dashboard is scoped to that user's
     * owned or assigned tasks — not the whole team / role type.
     */
    public $forUserId = null;

    public function mount($forUserId = null)
    {
        $this->forUserId = $forUserId;
        $this->loadDashboardData();
    }

    /**
     * Apply task visibility:
     * - forUserId set  → that user's owner_id OR assigned_to
     * - default        → active-role type scope (forMyType)
     */
    private function scopeTasks($query)
    {
        if ($this->forUserId) {
            $uid = $this->forUserId;

            return $query->where(function ($w) use ($uid) {
                $w->where('owner_id', $uid)->orWhere('assigned_to', $uid);
            });
        }

        return $query->forMyType();
    }

    /** Display name: assignee first, then owner, else Unassigned. */
    private function assigneeLabel(Task $task): string
    {
        return $task->assignedTo?->name
            ?? $task->owner?->name
            ?? 'Unassigned';
    }

    public function loadDashboardData()
    {
        $teamId = auth()->user()->currentTeam?->id;

        if (! $teamId) {
            return;
        }

        $projectsQuery = Project::where('team_id', $teamId);

        if ($this->forUserId) {
            // Only projects that contain tasks owned by / assigned to this user.
            $uid = $this->forUserId;
            $projectsQuery->whereHas('tasks', function ($q) use ($uid) {
                $q->where(function ($w) use ($uid) {
                    $w->where('owner_id', $uid)->orWhere('assigned_to', $uid);
                });
            });
        } else {
            // Only projects the user is invited to (Super Admin → all).
            $invited = my_invited_project_ids();
            if ($invited !== null) {
                $projectsQuery->whereIn('id', $invited);
            }
        }

        $this->projects = $projectsQuery
            ->with(['tasks' => fn ($q) => $this->scopeTasks($q)->with(['owner', 'assignedTo'])])
            ->get();

        // Fallback: member-only projects that may not be covered by invited/owner scope.
        // Do not run this when forUserId is set — that view must stay user-scoped.
        if (! $this->forUserId && $this->projects->count() === 0) {
            $this->projects = Project::where('team_id', $teamId)
                ->whereHas('members', fn ($q) => $q->where('users.id', auth()->id()))
                ->with(['tasks' => fn ($q) => $this->scopeTasks($q)->with(['owner', 'assignedTo'])])
                ->get();
        }

        $this->totalProjects = $this->projects->count();

        $allTasks = $this->scopeTasks(
            Task::whereIn('project_id', $this->projects->pluck('id'))
        )->with(['owner', 'assignedTo'])->get();

        $this->totalTasks = $allTasks->count();
        $this->tasksInProgress = $allTasks->where('status', 'progress')->count();
        $this->tasksCompleted = $allTasks->where('status', 'complete')->count();

        $this->projectStats = $this->projects->map(function ($project) {
            $tasks = $project->tasks;

            return [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'total_tasks' => $tasks->count(),
                'completed_tasks' => $tasks->where('status', 'complete')->count(),
                'in_progress_tasks' => $tasks->where('status', 'progress')->count(),
                'pending_tasks' => $tasks->where('status', 'pending')->count(),
                'progress_percentage' => $tasks->count() > 0
                    ? round(($tasks->where('status', 'complete')->count() / $tasks->count()) * 100)
                    : 0,
            ];
        });

        $this->tasksByStatus = [
            'pending' => $allTasks->where('status', 'pending')->count(),
            'in_progress' => $this->tasksInProgress,
            'completed' => $this->tasksCompleted,
        ];
    }

    public function selectProject($projectId)
    {
        $this->selectedProject = $projectId;
        $this->loadProjectTasks($projectId);
    }

    public function loadProjectTasks($projectId, $status = null)
    {
        $this->selectedProject = $projectId;

        $statusArr = ['progress', 'pending'];
        if (! is_null($status)) {
            if ($status == 'all') {
                $statusArr = ['progress', 'pending', 'complete'];
            } else {
                $statusArr = [$status];
            }
        }

        if ($projectId == 0) {
            $base = Task::whereIn('status', $statusArr);
            if ($this->forUserId) {
                $base->whereIn('project_id', $this->projects->pluck('id'));
            } else {
                $invited = my_invited_project_ids();
                $base->when($invited !== null, fn ($q) => $q->whereIn('project_id', $invited));
            }

            $this->selectedProjectTasks = $this->scopeTasks($base)
                ->with(['owner', 'assignedTo'])
                ->orderBy('status', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn (Task $task) => $this->mapTaskRow($task))
                ->toArray();
        } else {
            $project = Project::find($projectId);
            if ($project) {
                $this->selectedProjectTasks = $this->scopeTasks(
                    $project->tasks()->whereIn('status', $statusArr)
                )
                    ->with(['owner', 'assignedTo'])
                    ->orderBy('status', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(fn (Task $task) => $this->mapTaskRow($task))
                    ->toArray();
            }
        }
    }

    private function mapTaskRow(Task $task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'status' => $task->status,
            'priority' => $task->priority ?? 'medium',
            // Prefer assignee (assigned_to); fall back to owner so assign is visible.
            'owner_name' => $this->assigneeLabel($task),
            'target_date' => $task->target_date,
            'created_at' => $task->created_at,
        ];
    }

    public function selectStatus($status)
    {
    }

    public function setStatus($taskId)
    {
        $task = Task::find($taskId);
        if ($task) {
            $from = $task->status;
            if ($from === 'pending') {
                $status = 'complete';
            } elseif ($from === 'progress') {
                $status = 'complete';
            } elseif ($from === 'complete') {
                $status = 'pending';
            } else {
                $status = $from;
            }
            $task->update(['status' => $status]);
        }

        $this->loadDashboardData();

        if ($this->selectedProject !== null) {
            $statusParam = ($this->selectedProject == 0) ? 'all' : null;
            $this->loadProjectTasks($this->selectedProject, $statusParam);
        }
    }

    public function clearSelection()
    {
        $this->selectedProject = null;
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-overview');
    }
}
