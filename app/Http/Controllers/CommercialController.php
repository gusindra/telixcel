<?php

namespace App\Http\Controllers;

use App\Models\CommerceItem;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Quotation;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class CommercialController extends Controller
{
    public $user_info;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Your auth here
            $id = array("PRODUCT", "QUOTATION", "CONTRACT");
            $permission = checkPermisissions($id);

            if($permission){
                return $next($request);
            }
            abort(404);
        });
    }

    public function index()
    {
        return view('assistant.commercial.index');
    }

    public function create(Request $request)
    {
        if($request->get('data')=='quotation'){
            return view('assistant.commercial.quotation.create');
        }elseif($request->get('data')=='contract'){
            return view('assistant.commercial.contract.create');
        }else{
            return view('assistant.commercial.create');
        }
    }

    public function show($key)
    {
        if($key=='quotation'){
            return view('assistant.commercial.quotation.index');
        }elseif($key=='contract'){
            return view('assistant.commercial.contract.index');
        }
        return view('assistant.commercial.index');
    }

    public function edit($key, $id)
    {
        if($key=='quotation'){
            $data = Quotation::find($id);
            if($data){
                return view('assistant.commercial.quotation.show', ['code'=>$id, 'quote' => $data]);
            }
        }elseif($key=='contract'){
            return view('assistant.commercial.contract.show', ['code'=>$id]);
        }
        return view('assistant.commercial.show', ['code'=>$id]);
    }

    public function template($key, Quotation $quotation){
        if($key=='quotation'){
            return view('assistant.commercial.quotation.template', ['data' => $quotation]);
        }elseif($key=='contract'){
            return view('assistant.commercial.contract.template', ['code'=>$quotation]);
        }
    }
}
