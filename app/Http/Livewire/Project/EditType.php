<?php

namespace App\Http\Livewire\Project;

use App\Models\Project;
use Livewire\Component;

class EditType extends Component
{
    public $projectId;
    public $project;

    public function mount($id)
    {
        $this->projectId = $id;
        $this->project = Project::find($id);
    }

    public function modelData()
    {
        return [
            'name'                  => $this->name,
            'status'                => $this->status,
            'entity'                => $this->entity,
            'type'                  => $this->type
        ];
    }
    /**
     * Update Template
     *
     * @return void
     */
    public function save()
    {
        $this->validate();
        Project::find($this->projectId)->update($this->modelData());
        $this->emit('saved');
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        return $this->project;
    }

    public function render()
    {
        return view('livewire.project.edit-type', [
            'project' => $this->read()
        ]);
    }
}
