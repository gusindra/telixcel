<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class ProjectController extends Controller
{
    public $user_info;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $permission = false;
            $this->user_info = FacadesAuth::user()->super->first();
            $id = array("51", "52", "53", "54", "55");
            if($this->user_info || @$this->user_info->role=='superadmin'){
                $permission = true;
            }elseif(FacadesAuth::user()->role){
                foreach(FacadesAuth::user()->role as $role){
                    // dd($role->role->permission);
                    foreach($role->role->permission as $permission){
                        if (in_array($permission->id, $id)){
                            $permission = true;
                        }
                    }
                }
            }

            if($permission){
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
