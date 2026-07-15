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

    public function mount()
    {
        $this->loadDashboardData();
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
        $invited = my_invited_project_ids();
        $projectsQuery = Project::where('team_id', $teamId);
        if ($invited !== null) {
            $projectsQuery->whereIn('id', $invited);
        }
        $this->projects = $projectsQuery
            ->with(['tasks' => fn ($q) => $q->forMyType()])
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
        $allTasks = Task::whereIn('project_id', $this->projects->pluck('id'))->forMyType()->get();
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
        $this->selectedProject = $projectId;

        $statusArr = ['progress', 'pending'];
        if(!is_null($status)){
            if($status == 'all'){
                $statusArr = ['progress', 'pending', 'complete'];
            }else{
                $statusArr = [$status];
            }
        }
        if($projectId==0){
            $invited = my_invited_project_ids();
            $this->selectedProjectTasks = [];
            $this->selectedProjectTasks = Task::whereIn('status', $statusArr)
                    ->when($invited !== null, fn ($q) => $q->whereIn('project_id', $invited))
                    ->forMyType()
                    ->orderBy('status', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'status' => $task->status,
                            'priority' => $task->priority ?? 'medium',
                            'owner_name' => $task->owner?->name ?? 'Unassigned',
                            'target_date' => $task->target_date,
                            'created_at' => $task->created_at,
                        ];
                    })
                    ->toArray();
        }else{
            $project = Project::find($projectId);
            if ($project) {
                $this->selectedProjectTasks = $project->tasks()
                    ->whereIn('status', $statusArr)
                    ->forMyType()
                    ->orderBy('status', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'status' => $task->status,
                            'priority' => $task->priority ?? 'medium',
                            'owner_name' => $task->owner?->name ?? 'Unassigned',
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

        // Refresh dashboard stats
        $this->loadDashboardData();

        // Refresh selected project task list if a project is selected
        if ($this->selectedProject !== null) {
            // Keep 'all' filter for All Tasks view
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
