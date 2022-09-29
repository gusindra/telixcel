<?php

namespace App\View\Components;

use Illuminate\View\Component;

class switchableTeam extends Component
{
    public $team = "";
    public $selection;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->selection = [];
        if(auth()->user()->currentTeam){
            $this->selection = auth()->user()->listTeams->where('team_id','!=',1);
        }
        $this->team = auth()->user()->currentTeam;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.switchable-team');
    }
}
