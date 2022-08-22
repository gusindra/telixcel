<?php

namespace App\Http\Livewire\Permission;

use App\Models\FlowSetting;
use App\Models\Role;
use App\Models\Team;
use Livewire\Component;

class Flow extends Component
{
    public $status = ['submit', 'submited', 'approve', 'approved', 'decline', 'released', 'reviewed'];
    public $model;
    public $input;
    public $role;
    public $member;
    public $team;

    public function rules()
    {
        return [
            'input' => 'required',
        ];
    }

    public function mount($model)
    {
        $this->model = $model;
        $this->role = Role::whereIn('team_id', [0, auth()->user()->currentTeam->id])->get();
        $this->team = Team::whereIn('user_id', [auth()->user()->currentTeam->user_id])->get();
    }

    public function modelData()
    {
        $data = [
            'model'         => strtoupper($this->model),
            'after_status'  => $this->input['after_status'],
            'result_status' => $this->input['status'],
            'description'   => $this->input['description'],
            'role_id'       => $this->input['role_id'],
            // 'user_id'       => $this->input['user_id'],
            'team_id'       => $this->input['team_id'],
        ];
        return $data;
    }

    public function addFlow()
    {
        FlowSetting::create($this->modelData());
        $this->emit('saved');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->input['after_status'] = null;
        $this->input['description'] = null;
        $this->input['role_id'] = null;
        $this->input['user_id'] = null;
        $this->input['team_id'] = null;
        $this->input['status'] = null;
    }

    public function deleteFlow($id)
    {
        FlowSetting::find($id)->delete();
        $this->emit('deleted');
        $this->resetForm();
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function readData()
    {
        return FlowSetting::where('model', $this->model)->get();
    }

    public function render()
    {
        return view('livewire.permission.flow', [
            'data' => $this->readData(),
        ]);
    }
}
