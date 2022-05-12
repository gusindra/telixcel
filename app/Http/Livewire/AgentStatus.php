<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TeamUser;

class AgentStatus extends Component
{
    public $selection = ['Online', 'Praying', 'Meeting', 'Eating', 'Toileting', 'Maintenance', 'Offline'];
    public $status;


    /**
     * mount
     *
     * @return void
     */
    public function mount()
    {
        if(auth()->user()->currentTeam){
            $team = TeamUser::where('team_id', auth()->user()->currentTeam->id)->where('user_id', auth()->user()->id)->first();
            if($team)
                $this->status = $team->status;
        }
    }
    /**
     * The update function.
     *
     * @return void
     */
    public function updateStatus($status)
    {
        abort(404);
        $teamuser = TeamUser::where('team_id', auth()->user()->currentTeam->id)->where('user_id', auth()->user()->id)->first();
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
