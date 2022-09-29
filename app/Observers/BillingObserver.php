<?php

namespace App\Observers;

use App\Models\Billing;
use App\Models\Commision;
use App\Models\FlowProcess;
use App\Models\FlowSetting;
use Illuminate\Support\Str;

class BillingObserver
{
    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Billing $request)
    {
        if(auth()){
            FlowProcess::create([
                'model'     => 'INVOICE',
                'model_id'  => $request->id,
                'user_id'   => auth()->user()->id,
                'status'    => 'submited'
            ]);

            $flow = FlowSetting::where('model', 'INVOICE')->where('team_id', auth()->user()->currentTeam->id)->get();
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
     * Handle the Project "updated" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function updated(Billing $request)
    {
        if($request->status=='paid'){
            $request->order->update([
                'status'    => 'paid'
            ]);
            if($request->commission){
                $total = $request->commission->type == "price" ? $request->commission->ratio : $request->commission->ratio/100 * $request->bill->amount;
                Commision::find($request->id)->update([
                    'total'     => $total,
                    'status'    => 'unpaid'
                ]);
            }
        }

        if($request->status == 'approved')
        {
            //
        }
    }

    /**
     * Handle the Client "deleted" event.
     *
     * @param  \App\Client  $request
     * @return void
     */
    public function deleted( )
    {
        //
    }
}


