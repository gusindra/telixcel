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
        $team = TeamUser::find(auth()->user()->currentTeam->id);
        $this->status = $team->status;
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function updateStatus($status)
    {
        TeamUser::find(auth()->user()->currentTeam->id)->update([
            'status' => $status
        ]);

        $this->status = $status;
        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.agent-status');
    }
}
