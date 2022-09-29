<?php

namespace App\Observers;

use App\Models\Contract;
use App\Models\FlowProcess;
use App\Models\FlowSetting;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContractObserver
{
    /**
     * Handle the Project "updated" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function updated(Contract $request)
    {
        if($request->status == 'new')
        {
            $request->update(['status' => 'draft']);
        }

        if($request->status == 'submit')
        {
            FlowProcess::create([
                'model'     => 'CONTRACT',
                'model_id'  => $request->id,
                'user_id'   => Auth::user()->id,
                'status'    => 'submited'
            ]);

            $flow = FlowSetting::where('model', 'CONTRACT')->where('team_id', auth()->user()->currentTeam->id)->get();

            // APPROVAL
            foreach($flow as $key => $value){
                FlowProcess::create([
                    'model'     => $value->model,
                    'model_id'  => $request->id,
                    'role_id'   => $value->role_id,
                    'task'      => $value->description,
                ]);
            }

            if($request->status){
                $contract = Contract::find($request->id);
                $contract->update([
                    'original_attachment' => $contract->attachments->sortBy('id')->first()->id
                ]);
            }

        }

        if($request->status == 'approve'){
            $contract = Contract::find($request->id);
            $contract->update([
                'result_attachment' => $contract->attachments->sortByDesc('id')->first()->id
            ]);
        }

        if($request->status == 'approved')
        {
            //check if contract have project
            if($request->model=='PROJECT'){
                Project::find($request->model_id)->update([
                    'status' => 'active'
                ]);
            }
        }

        if($request->status == 'expired')
        {
            //check if contract have project
            if($request->model=='PROJECT'){
                Project::find($request->model_id)->update([
                    'status' => 'inactive'
                ]);
            }
        }
    }
}
