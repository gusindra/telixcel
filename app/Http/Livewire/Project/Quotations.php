<?php

namespace App\Http\Livewire\Project;

use App\Models\Project;
use Livewire\Component;

class Quotations extends Component
{
    public $project;

    public function mount($id)
    {
        $this->project = Project::find($id);
    }

    public function read()
    {
        return $this->project->quotations;
    }

    public function render()
    {
        return view('livewire.project.quotations', [
            'quotations' => $this->read()
        ]);
    }
}
