<?php

namespace App\Observers;

use App\Models\OrderProduct;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrderProductObserver
{
    /**
     * Handle the Project "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(OrderProduct $request)
    {
        $order = Order::find($request->model_id);

        $order->update([
            'total' => $order->total + ($request->price * $request->qty)
        ]);
    }

    /**
     * Handle the Project "deleted" event.
     *
     * @param  \App\Project  $request
     * @return void
     */
    public function deleted(OrderProduct $request)
    {
        $order = Order::find($request->model_id);
        if(count($order->items)==0){
            $order->update([
                'total' => 0
            ]);
        }else{
            $total = $order->total - ($request->price * $request->qty);
            $order->update([
                'total' => $total >0 ? $total : 0
            ]);
        }
    }
}


