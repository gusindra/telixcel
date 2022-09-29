<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Billing;
use App\Models\Commision;
use App\Models\FlowProcess;
use App\Models\FlowSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Project "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Order $request)
    {
        if($request->status=='unpaid'){
            Billing::create([
                'uuid'          => Str::uuid(),
                'status'        => $request->status,
                'code'          => $request->no,
                'description'   => $request->name,
                'amount'        => $request->total,
                'user_id'       => $request->user_id,
                'order_id'      => $request->id,
                'period'        => $request->date->format('m/Y')
            ]);
        }
    }

    /**
     * Handle the Project "updated" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function updated(Order $request)
    {
        //Log::debug($request->status);
        if($request->status=='unpaid'){
            $bill = Billing::where('order_id', $request->id)->get();
            if(count($bill)==0){
                Billing::create([
                    'uuid'          => Str::uuid(),
                    'status'        => $request->status,
                    'code'          => $request->no,
                    'description'   => $request->name,
                    'amount'        => $request->total,
                    'user_id'       => $request->user_id,
                    'order_id'      => $request->id,
                    'period'        => $request->date->format('Y-m-d')
                ]);
            }
        }elseif($request->status=='paid'){
            // $request->bill->update([
            //     'status'    => 'paid'
            // ]);
            // Log::debug($request->commission);
            // if($request->commission){
            //     $total = $request->commission->type == "price" ? $request->commission->ratio : $request->commission->ratio/100 * $request->bill->amount;
            //     Commision::find($request->commission->id)->update([
            //         'total'     => $total,
            //         'status'    => 'unpaid'
            //     ]);
            // }
        }elseif($request->status == 'submit'){
            FlowProcess::create([
                'model'     => 'ORDER',
                'model_id'  => $request->id,
                'user_id'   => Auth::user()->id,
                'status'    => 'submited'
            ]);

            $flow = FlowSetting::where('model', 'ORDER')->where('team_id', auth()->user()->currentTeam->id)->get();
            foreach($flow as $key => $value){
                FlowProcess::create([
                    'model'     => $value->model,
                    'model_id'  => $request->id,
                    'role_id'   => $value->role_id,
                    'task'      => $value->description,
                ]);
            }

            // FlowProcess::create([
            //     'model'     => 'ORDER',
            //     'model_id'  => $request->id,
            //     'role_id'   => 1,
            //     'task'      => '1st Approver'
            // ]);
        }

        // if($request->status == 'approved')
        // {
        //     FlowProcess::create([
        //         'model'     => 'ORDER',
        //         'model_id'  => $request->id,
        //         'role_id'   => 1,
        //         'task'      => 'Releasor'
        //     ]);
        // }
        if($request->status == 'approved')
        {
            Order::find($request->id)->update([
                'status'    => 'unpaid'
            ]);
        }


    }

    /**
     * Handle the Project "deleted" event.
     *
     * @param  \App\Project  $request
     * @return void
     */
    public function deleted(Order $request)
    {
        $bill = Billing::where('order_id', $request->order_id)->delete();
    }
}


