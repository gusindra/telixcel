<?php

namespace App\Http\Livewire\Project;

use App\Models\Project;
use Livewire\Component;

class EditType extends Component
{
    public $projectId;
    public $project;
    public $disabled;
    public $referrer_name;
    public $status;
    public $entity;
    public $type;

    public function rules()
    {
        return [
            'referrer_name' => 'required'
        ];
    }

    public function mount($id, $disabled)
    {
        $this->projectId = $id;
        $this->disabled = $disabled;
        $this->project = Project::find($id);
        $this->referrer_name = $this->project->referrer_name;
    }

    public function modelData()
    {
        return [
            'referrer_name' => $this->referrer_name,
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
