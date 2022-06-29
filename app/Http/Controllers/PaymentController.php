<?php

namespace App\Http\Controllers;

use App\Models\Order;

class PaymentController extends Controller
{

    public function index()
    {
        return view('payment.deposit');
    }

    public function topup()
    {
        return view('payment.topup');
    }

    public function invoice(Order $id)
    {
        if(auth()->user()->isClient->uuid!=$id->customer_id)
            abort(404);

        return view('payment.pay', ['order'=>$id]);
    }
}
