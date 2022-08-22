<?php

namespace App\Http\Controllers;

use App\Models\Commision;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class CommissionController extends Controller
{
    public $user_info;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $permission = false;
            $id = array("COMMISSION");
            $permission = checkPermisissions($id);

            if($permission){
                return $next($request);
            }
            abort(404);
        });
    }

    public function index()
    {
        return view('assistant.order.commission');
    }

    public function show(Commision $commission)
    {
        return view('assistant.order.commission_show', ['data'=>$commission]);
    }
}
