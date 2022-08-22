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

class AssistanceController extends Controller
{
    public $user_info;

    public function index()
    {
        // Check Every Day
        $contract = Contract::whereDate('expired_at', '<=', date('Y-m-d'))->get();
        $quotation = Quotation::whereDate('expired_at', '<=', date('Y-m-d'))->get();
        $order = Order::whereDate('expired_at', '<=', date('Y-m-d'))->get();

        return true;
    }


}
