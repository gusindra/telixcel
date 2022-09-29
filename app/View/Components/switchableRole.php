<?php

namespace App\View\Components;

use App\Models\RoleUser;
use Illuminate\View\Component;

class switchableRole extends Component
{
    public $role = "";
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
            $this->selection = auth()->user()->role->where('team_id', auth()->user()->currentTeam->id);
        }
        $this->role = auth()->user()->activeRole ? auth()->user()->activeRole->role->name : "";
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.switchable-role');
    }
}
