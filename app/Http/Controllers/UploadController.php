<?php

namespace App\Http\Controllers;

use Auth;

class UploadController extends Controller
{
    public $user_info;
    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     // Your auth here
        //     $this->user_info=Auth::user()->super->first();
        //     if($this->user_info && $this->user_info->role=='superadmin'){
        //         return $next($request);
        //     }
        //     abort(404);
        // });
    }

    public function index()
    {
        return view('upload');
    }
}
