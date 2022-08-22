<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TeamUser;

class AgentStatus extends Component
{
    public $selection = ['Online', 'Praying', 'Meeting', 'Eating', 'Toileting', 'Maintenance', 'Offline'];
    public $status;
    public $team_id;

    /**
     * mount
     *
     * @return void
     */
    public function mount()
    {
        $this->status = 'Online';
        $this->team_id = empty(auth()->user()->currentTeam)?1:auth()->user()->currentTeam->id;
        if($this->team_id){
            $team = TeamUser::where('team_id', $this->team_id)->where('user_id', auth()->user()->id)->first();
            if($team){
                $this->status = $team->status;
            }
        }
    }
    /**
     * The update function.
     *
     * @return void
     */
    public function updateStatus($status)
    {
        // abort(404);
        $teamuser = TeamUser::where('team_id', $this->team_id)->where('user_id', auth()->user()->id)->first();
        if($status == 'Offline'){
            $status = null;
        }
        if($teamuser){
            $teamuser->update([
                'status' => $status
            ]);
        }

        $this->status = $status;
        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.agent-status');
    }
}
