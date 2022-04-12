<?php

namespace App\Http\Controllers;

use App\Models\CommerceItem;
use App\Models\Project;
use App\Models\Quotation;
use Auth;

class CommercialController extends Controller
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
        return view('assistant.commercial.index');
    }

    public function show($key)
    {
        if($key=='quotation'){
            $quotation = Quotation::get();
            return view('assistant.commercial.quotation.index', ['data'=>$quotation]);
        }elseif($key=='contract'){
            $contracts = Quotation::get();
            return view('assistant.commercial.contract.index', ['data'=>$contracts]);
        }
        $items = CommerceItem::get();
        return view('assistant.commercial.index', ['data'=>$items]);
    }
}
