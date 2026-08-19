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
        $key = request()->segment(2);
        if ($commission->uuid && $key !== $commission->uuid && ctype_digit((string) $key)) {
            return redirect()->route('show.commission', array_merge(['commission' => $commission], request()->query()));
        }

        return view('assistant.order.commission_show', ['data' => $commission]);
    }
}
