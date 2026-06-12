<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Project;
use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectTasksTable extends Component
{
    use WithPagination;

    public $searchQuery = '';
    public $filterStatus = 'all';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'searchQuery' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearchQuery()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $teamId = auth()->user()->currentTeam?->id;

        $query = Project::where('team_id', $teamId);

        // Apply search filter
        if (!empty($this->searchQuery)) {
            $query->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        // Apply status filter
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Get projects with their tasks
        $projects = $query->with('tasks')->paginate($this->perPage);

        // Calculate task counts for each project
        $projects->each(function ($project) {
            $project->task_count = $project->tasks->count();
            $project->completed_count = $project->tasks->where('status', 'completed')->count();
            $project->in_progress_count = $project->tasks->where('status', 'in_progress')->count();
        });

        return view('livewire.dashboard.project-tasks-table', [
            'projects' => $projects,
        ]);
    }
}
