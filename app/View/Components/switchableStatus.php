<?php

namespace App\View\Components;

use App\Models\TeamUser;
use Illuminate\View\Component;

class switchableStatus extends Component
{
    public $selection = ['Online', 'Praying', 'Meeting', 'Eating', 'Toileting', 'Maintenance', 'Offline'];
    public $status;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(auth()->user()->currentTeam){
            $team = TeamUser::find(auth()->user()->currentTeam->id);
            if($team)
                $this->status = $team->status;

        }
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function updateStatus()
    {
        // dd(1);
        TeamUser::find(auth()->user()->currentTeam->id)->update([
            'status' => $status
        ]);

        $this->status = $status;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.switchable-status');
    }
}
