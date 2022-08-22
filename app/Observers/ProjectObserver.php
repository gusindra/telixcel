<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\FlowProcess;
use App\Models\FlowSetting;
use Illuminate\Support\Facades\Auth;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Project $request)
    {
        //Approval from Admin
    }

    /**
     * Handle the Project "updated" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function updated(Project $request)
    {
        if($request->status == 'submit')
        {
            FlowProcess::create([
                'model'     => 'PROJECT',
                'model_id'  => $request->id,
                'user_id'   => Auth::user()->id,
                'status'    => 'submited'
            ]);

            // FlowProcess::create([
            //     'model'     => 'PROJECT',
            //     'model_id'  => $request->id,
            //     'role_id'   => 1
            // ]);
            // APPROVAL
            $flow = FlowSetting::where('model', 'PROJECT')->where('team_id', auth()->user()->currentTeam->id)->get();

            foreach($flow as $key => $value){
                FlowProcess::create([
                    'model'     => $value->model,
                    'model_id'  => $request->id,
                    'role_id'   => $value->role_id,
                    'task'      => $value->description,
                ]);
            }
        }

    }

    /**
     * Handle the Project "deleted" event.
     *
     * @param  \App\Project  $request
     * @return void
     */
    public function deleted()
    {
        //
    }
}


