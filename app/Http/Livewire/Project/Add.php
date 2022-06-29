<?php

namespace App\Http\Livewire\Project;

use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Add extends Component
{
    public $modalActionVisible = false;
    public $type;
    public $name;
    public $entity;

    public function rules()
    {
        return [
            'type' => 'required',
            'name' => 'required',
            'entity' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        Project::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function modelData()
    {
        return [
            'type'          => $this->type,
            'name'          => $this->name,
            'entity_party'  => $this->entity,
            'team_id'       => !Auth::user()->currentTeam ? null : Auth::user()->currentTeam->id ,
        ];
    }

    public function resetForm()
    {
        $this->type = null;
        $this->name = null;
        $this->entity = null;
    }

    /**
     * createShowModal
     *
     * @return void
     */
    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function render()
    {
        return view('livewire.project.add');
    }
}
