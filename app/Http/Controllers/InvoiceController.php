<?php

namespace App\Http\Controllers;

use App\Models\Billing;

class InvoiceController extends Controller
{
    // public $user_info;
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         // Your auth here
    //         $permission = false;
    //         $id = array("ORDER");
    //         $permission = checkPermisissions($id);

    //         if($permission){
    //             return $next($request);
    //         }
    //         abort(404);
    //     });
    // }

    public function index()
    {
        return view('assistant.order.invoice');
    }

    public function show(Billing $invoice)
    {
        return view('assistant.invoice.show', ['invoice'=>$invoice, 'order'=>$invoice->order]);
    }
}
