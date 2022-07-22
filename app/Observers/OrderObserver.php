<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Billing;
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
        $bill = Billing::where('order_id', $request->id)->get();
        // Log::debug(count($bill).' '.$request->status);
        if($request->status=='unpaid' && count($bill)==0){
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
        }elseif($request->status=='paid'){
            $request->bill->update([
                'status'    => 'paid'
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


