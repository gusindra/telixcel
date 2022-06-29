<?php

namespace App\Http\Livewire\Project;

use App\Models\Project;
use Livewire\Component;

class Orders extends Component
{
    public $project;

    public function mount($id)
    {
        $this->project = Project::find($id);
    }

    public function read()
    {
        return $this->project->orders;
    }

    public function render()
    {
        return view('livewire.project.orders', [
            'orders' => $this->read()
        ]);
    }
}
