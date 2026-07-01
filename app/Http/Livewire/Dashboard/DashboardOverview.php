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

    /** When set, the dashboard is scoped to one user's owned/assigned tasks (per-user view). */
    public $forUserId = null;

    public function mount($forUserId = null)
    {
        $this->forUserId = $forUserId;
        $this->loadDashboardData();
    }

    /** Apply task visibility: a specific user's owned/assigned tasks, or the active-role type scope. */
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

    public function loadDashboardData()
    {
        $teamId = auth()->user()->currentTeam?->id;

        if (!$teamId) {
            return;
        }
        
        //if(auth()->user()->activeRole && str_contains(auth()->user()->activeRole->role->name, "Super Admin")){
        //    $this->projects = Project::with(['tasks'])

        // Only projects the user is invited to (Super Admin -> all). Tasks then
        // follow the ACTIVE role's type (changes when the user switches role).
        $projectsQuery = Project::where('team_id', $teamId);
        if ($this->forUserId) {
            $uid = $this->forUserId;
            $projectsQuery->whereHas('tasks', fn ($q) => $q->where(fn ($w) => $w->where('owner_id', $uid)->orWhere('assigned_to', $uid)));
        } else {
            $invited = my_invited_project_ids();
            if ($invited !== null) {
                $projectsQuery->whereIn('id', $invited);
            }
        }
        $this->projects = $projectsQuery
            ->with(['tasks' => fn ($q) => $this->scopeTasks($q)])
            ->get();
        // }else{
        //     // Get all projects for the team
        //     $this->projects = Project::whereHas('members', function ($query) {
        //             $query->where('user_id', auth()->user()->id);
        //         })
        //         ->with(['tasks'])
        //         ->get();
        // }

        $this->totalProjects = $this->projects->count();

        // Calculate statistics (invited projects + active role type)
        $allTasks = $this->scopeTasks(Task::whereIn('project_id', $this->projects->pluck('id')))->get();
        $this->totalTasks = $allTasks->count();
        $this->tasksInProgress = $allTasks->where('status', 'progress')->count();
        $this->tasksCompleted = $allTasks->where('status', 'complete')->count();

        // Build project statistics
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

        // Get tasks by status
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
        $statusArr = ['progress', 'pending'];
        if(!is_null($status)){
            if($status == 'all'){
                $statusArr = ['progress', 'pending', 'complete'];
            }else{
                $statusArr = [$status];
            }
        }
        if($projectId==0){
            $base = Task::whereIn('status', ['progress', 'pending']);
            if ($this->forUserId) {
                $base->whereIn('project_id', $this->projects->pluck('id'));
            } else {
                $invited = my_invited_project_ids();
                $base->when($invited !== null, fn ($q) => $q->whereIn('project_id', $invited));
            }
            $this->selectedProjectTasks = $this->scopeTasks($base)
                    ->orderBy('status', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'status' => $task->status,
                            'priority' => $task->priority ?? 'medium',
                            'owner_name' => $task->assignedTo?->name ?? $task->owner?->name ?? 'Unassigned',
                            'target_date' => $task->target_date,
                            'created_at' => $task->created_at,
                        ];
                    })
                    ->toArray();
        }else{
            $project = Project::find($projectId);
            if ($project) {
                $this->selectedProjectTasks = $this->scopeTasks($project->tasks()->whereIn('status', $statusArr))
                    ->orderBy('status', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'status' => $task->status,
                            'priority' => $task->priority ?? 'medium',
                            'owner_name' => $task->assignedTo?->name ?? $task->owner?->name ?? 'Unassigned',
                            'target_date' => $task->target_date,
                            'created_at' => $task->created_at,
                        ];
                    })
                    ->toArray();
            }
        }
        
    }

    public function selectStatus($status){

    }

    public function setStatus($taskId)
    {
        //dd($taskId);

        $task = Task::find($taskId);
        if ($task) {
            $from = $task->status;
            if ($from === 'pending') {
                $status = 'complete';
            }elseif($from === 'progress') {
                $status = 'complete';
            }elseif($from === 'complete') {
                $status = 'pending';
            }
            $task->update(['status' => $status]);
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
