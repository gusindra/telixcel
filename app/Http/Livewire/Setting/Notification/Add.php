<?php

namespace App\Http\Livewire\Setting\Notification;

use App\Models\Notification;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Livewire\Component;

class Add extends Component
{
    public $modalActionVisible = false;
    public $user;
    public $input;
    public $grouptype;
    public $groups = array();

    public function rules()
    {
        return [
            'input.type' => 'required',
            'input.message' => 'required',
            'input.group' => 'required',
            'grouptype' => 'required',
        ];
    }

    public function sendAction()
    {
        $this->validate();
        $this->user = auth()->user()->id;
        if(auth()->user()->super->first()->role == 'superadmin'){
            $this->user = 0;
        }
        if($this->grouptype=='user'){
            foreach($this->input['group'] as $group){
                $this->create($this->input['type'], $this->input['message'], $group);
            }
        }else{
            if($this->grouptype=='role'){
                $roles = RoleUser::whereIn('role_id', $this->input['group'])->groupBy('user_id')->get();
                foreach($roles as $role){
                    $this->create($this->input['type'], $this->input['message'], $role->user_id);
                }
            }elseif($this->grouptype=='team'){
                $teams = TeamUser::whereIn('team_id', $this->input['group'])->groupBy('user_id')->get();
                foreach($teams as $team){
                    $this->create($this->input['type'], $this->input['message'], $team->user_id);
                }
            }
        }
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    private function create($type, $msg, $user){
        Notification::create([
            'type' => $type,
            'model' => null,
            'model_id' => null,
            'notification' => $msg,
            'user_id' => $user,
            'status' => 'unread'
        ]);
    }

    public function resetForm()
    {
        $this->input = null;
        $this->modalActionVisible = false;
        $this->groups = array();
    }


    public function readLists()
    {
        $type = $this->grouptype;
        if($type=='team'){
            $this->groups = Team::get();
        }elseif($type=='role'){
            $this->groups = Role::get();
        }else{
            $this->groups = User::get();
        }
        return $this->groups;
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
        return view('livewire.setting.notification.add', [
            'lists' => $this->readLists()
        ]);
    }
}
