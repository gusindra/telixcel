<?php

namespace App\Http\Livewire\Project;

use App\Models\Project;
use Livewire\Component;

class Contracts extends Component
{
    public $project;

    public function mount($id)
    {
        $this->project = Project::find($id);
    }

    public function read()
    {
        return $this->project->contracts;
    }

    public function render()
    {
        return view('livewire.project.contracts', [
            'contracts' => $this->read()
        ]);
    }
}
