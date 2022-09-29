<?php

namespace App\Http\Livewire;

use App\Models\RoleUser;
use Livewire\Component;

class SwitchRole extends Component
{
    public $haveRoles = 0;
    /**
     * mount
     *
     * @return void
     */
    public function mount()
    {
        if(auth()->user()->currentTeam && auth()->user()->currentTeam->id){
            $this->haveRoles = count(auth()->user()->role->where('team_id', auth()->user()->currentTeam->id));
        }else{
            //dd(2);
        }
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function updateRole($id)
    {
        $resetState = RoleUser::where('user_id', auth()->user()->id)->get();
        // dd($resetState);
        if($resetState){
            RoleUser::where('user_id', auth()->user()->id)->update([
                'active' => NULL
            ]);

            $role = RoleUser::find($id)->update([
                'active' => 1
            ]);
        }

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.switch-role');
    }
}
