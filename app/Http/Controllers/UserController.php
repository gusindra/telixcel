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
            if(($this->user_info && $this->user_info->role=='superadmin') || (auth()->user()->activeRole && str_contains(auth()->user()->activeRole->role->name, "Admin"))){
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
        if ($redirect = $this->redirectLegacyId($user, 'user.show')) {
            return $redirect;
        }
        if($user->name != 'Admin1'){
            return view('user.user-detail', ['user'=>$user]);
        }
        return redirect('user');
    }

    public function profile(User $user)
    {
        if ($redirect = $this->redirectLegacyId($user, 'user.show.profile')) {
            return $redirect;
        }
        if($user->name != 'Admin'){
            return view('user.user-profile', ['user'=>$user]);
        }
        return redirect('user');
    }

    public function balance(User $user, Request $request)
    {
        if ($redirect = $this->redirectLegacyId($user, 'user.show.balance')) {
            return $redirect;
        }
        return view('user.user-balance', ['user'=>$user, 'team'=>$request->has('team')?$request->team:0]);
    }

    private function redirectLegacyId(User $user, string $route)
    {
        $key = request()->segment(2);
        if ($user->uuid && $key !== $user->uuid && ctype_digit((string) $key)) {
            return redirect()->route($route, array_merge(['user' => $user], request()->query()));
        }

        return null;
    }
}
