<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Auth;

class ProjectController extends Controller
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
        return view('assistant.project.index');
    }

    public function show(Project $project)
    {
        return view('assistant.project.show', ['project'=>$project]);
    }
}
