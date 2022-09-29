<?php

namespace App\Http\Livewire\Project;

use App\Models\Company;
use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Add extends Component
{
    use AuthorizesRequests;
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
        $this->authorize('create', new Project);
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

    private function readCompany()
    {
        if((Auth::user()->super->first() && Auth::user()->super->first()->role == 'superadmin') || (Auth::user()->activeRole)){
            return Company::get();
        }
        return Company::where('user_id', Auth::user()->id)->get();
    }

    public function render()
    {
        return view('livewire.project.add', [
            'companies' => $this->readCompany()
        ]);
    }
}
