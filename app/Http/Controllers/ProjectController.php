<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class ProjectController extends Controller
{
    public $user_info;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $permission = false;
            $id = array("PROJECT");
            $permission = checkPermisissions($id);

            if($permission){
                return $next($request);
            }
            abort(404);
        });
    }

    public function index(Request $request)
    {
        if($request->has('companyid') && $request->has('v')){
            return view('main-side.user-project', [
                'user' => Company::find($request->get('companyid'))
            ]);
        }
        if($request->has('v')){
            return view('main-side.project');
        }
        return view('assistant.project.index');
    }

    public function show(Request $request, Project $project)
    {
        if($request->has('v')){
            return view('main-side.project-details', [
                'user' => Company::find($project->party_b),
                'project'=>$project
            ]);
        }
        return view('assistant.project.show', ['project'=>$project]);
    }
}
