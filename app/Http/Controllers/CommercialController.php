<?php

namespace App\Http\Controllers;

use App\Models\CommerceItem;
use App\Models\Contract;
use App\Models\Order;
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
        return view('assistant.commercial.index', ['key'=>'item']);
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
            return view('assistant.commercial.quotation.index', ['key'=>$key]);
        }elseif($key=='contract'){
            return view('assistant.commercial.contract.index', ['key'=>$key]);
        }
        return view('assistant.commercial.index', ['key'=>$key]);
    }

    public function edit($key, $id)
    {
        if($key=='quotation'){
            $data = Quotation::find($id);
            if($data){
                return view('assistant.commercial.quotation.show', ['code'=>$id, 'quote' => $data]);
            }
        }elseif($key=='contract'){
            $data = Contract::find($id);
            if($data){
                return view('assistant.commercial.contract.show', ['code'=>$id, 'contract' => $data]);
            }
        }
        $data = CommerceItem::find($id);
        if($data){
            return view('assistant.commercial.show', ['code'=>$id, 'data' => $data]);
        }
        abort(404);

    }

    public function template($key, $id){
        // return $key;
        if($id=='quotation'){
            $q = Quotation::find($key);
            return view('assistant.commercial.quotation.template', ['data' => $q]);
        }elseif($id=='contract'){
            $c = Contract::find($key);
            return view('assistant.commercial.contract.template', ['code'=>$c]);
        }elseif($id=='invoice'){
            $o = Order::find($key);
            return view('assistant.order.template', ['data'=>$o]);
        }
    }
}
