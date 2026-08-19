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
            $id = array("PROJECT");
            $permission = checkPermisissions($id);

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
        $key = request()->segment(2);
        if ($project->uuid && $key !== $project->uuid && ctype_digit((string) $key)) {
            return redirect()->route('project.show', array_merge(
                ['project' => $project],
                request()->query()
            ));
        }

        return view('assistant.project.show', ['project' => $project]);
    }
}
