<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Vinkla\Hashids\Facades\Hashids;

class UserController extends Controller
{
    public $user_info;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $this->user_info=auth()->user()->super->first();
            if($this->user_info && $this->user_info->role=='superadmin'){
                return $next($request);
            }
            abort(404);
        });
    }

    public function index()
    {
        return view('user.user-table');
    }

    public function show(User $user)
    {
        if($user->name != 'Admin1'){
            return view('user.user-detail', ['user'=>$user]);
        }
        return redirect('user');
    }

    public function profile(User $user)
    {
        if($user->name != 'Admin'){
            return view('user.user-profile', ['user'=>$user]);
        }
        return redirect('user');
    }

    public function balance(User $user, Request $request)
    {
        if($user->name != 'Admin'){
            return view('user.user-balance', ['user'=>$user, 'team'=>$request->has('team')?$request->team:0]);
        }
        return redirect('user');
    }
}
