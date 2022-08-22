<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Auth;

class RoleController extends Controller
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

    public function index()
    {
        return view('role.role-table', ['page'=>'role']);
    }

    public function show(Role $role)
    {
        return view('role.role-detail', ['role'=>$role]);
    }
}
