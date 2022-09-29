<?php

namespace App\Http\Controllers;

use App\Models\BlastMessage;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AdminSmsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $user = auth()->user();

            if($user->super->first()){
                if($user->super->first()->role=='superadmin'){
                    return $next($request);
                }
            }
            if((auth()->user()->activeRole && str_contains(auth()->user()->activeRole->role->name, "Admin"))){
                return $next($request);
            }
            abort(404);
        });
    }

    public function updateStatus($id, $status)
    {
        BlastMessage::find($id)->update([
            'status' => $status
        ]);
        return redirect()->back();
    }
}
