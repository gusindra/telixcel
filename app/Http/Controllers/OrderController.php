<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class OrderController extends Controller
{
    public $user_info;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $permission = false;
            $id = array("ORDER");
            $permission = checkPermisissions($id);

            if($permission){
                return $next($request);
            }
            abort(404);
        });
    }

    public function index()
    {
        return view('assistant.order.index');
    }

    public function invoice()
    {
        return view('assistant.order.invoice');
    }

    public function show(Order $order)
    {
        return view('assistant.order.show', ['order'=>$order]);
    }
}
