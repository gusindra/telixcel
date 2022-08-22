<?php

namespace App\Http\Livewire\Project;

use App\Models\Project;
use Livewire\Component;

class Edit extends Component
{
    public $project;
    public $templateId;
    public $name;
    public $entity;
    public $status;
    public $type;
    public $uuid;

    public function mount($uuid)
    {
        $this->project = Project::find($uuid);
        $this->name = $this->project->name;
        $this->status = $this->project->status;
        $this->entity = $this->project->entity_party;
        $this->type = $this->project->type;
        $this->roleId = $this->project->id;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'status' => 'required',
            'entity' => 'required',
            'type' => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'name'                  => $this->name,
            'status'                => $this->status,
            'entity_party'          => $this->entity,
            'type'                  => $this->type
        ];
    }

    /**
     * Update Template
     *
     * @return void
     */
    public function update($id)
    {
        $this->validate();
        Project::find($id)->update($this->modelData());
        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.project.edit');
    }
}
