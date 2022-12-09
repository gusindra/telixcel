<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Auth;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public $user_info;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $this->user_info=Auth::user()->super->first();
            if($this->user_info && $this->user_info->role=='superadmin'){
                return $next($request);
            }
            abort(404);
        });
    }

    public function index(Request $request)
    {
        if(empty(auth()->user()->currentTeam)){
            return redirect()->route('teams.create');
        }
        if($request->has('v')){
            return view('main-side.billing');
        }
        return view('billing');

    }
}
