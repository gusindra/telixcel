<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\CommerceItem;
use App\Models\Contract;
use App\Models\Order;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\TeamUser;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AssistanceController extends Controller
{
    public $user_info;

    public function assistance()
    {
        // Check Every Day
        $contract = Contract::whereDate('expired_at', '<=', date('Y-m-d'))->get();
        $quotation = Quotation::whereDate('expired_at', '<=', date('Y-m-d'))->get();
        // Check quote to create new order or expired quotation notice

        $order = Order::whereDate('expired_at', '<=', date('Y-m-d'))->get();
        // Check order to create new billing

        $billing = Billing::whereDate('expired_at', '<=', date('Y-m-d'))->get();
        // check invoice to set status of project

        // Update project base on contract / quotation / order
        $project = Project::find(1);

        return true;
    }

    public function login()
    {
        $teamUser = TeamUser::where('status', '!=', NULL)->where('updated_at', '<', date('Y-m-d H:i:s'))->update([
            'status' => NULL
        ]);
    }


}
