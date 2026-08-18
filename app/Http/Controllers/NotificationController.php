<?php

namespace App\Http\Controllers;

use App\Models\FlowProcess;
use App\Models\Notification;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notification');
    }

    public function show(Notification $notification)
    {
        $notification->update(array('status' => 'read'));
        if($notification->model=='Ticket'){
            $value =  $notification->ticket->request->client->id;
        }elseif($notification->model=='Order'){
            $order = \App\Models\Order::find($notification->model_id);
            return redirect()->to('/order/'.($order->uuid ?? $notification->model_id));
        }elseif($notification->model=='Invoice'){
            return redirect()->to("/invoice-order/". $notification->model_id);
        }elseif($notification->model=='Balance'){
            return redirect()->to("/payment/deposit/");
        }elseif($notification->model=='Contract'){
            $contract = \App\Models\Contract::find($notification->model_id);
            return redirect()->to('/commercial/contract/'.($contract->uuid ?? $notification->model_id));
        }elseif($notification->model=='FlowProcess'){
            $flow = FlowProcess::find($notification->model_id);
            if($flow){
                if($flow->model=='QUOTATION'){
                    $row = \App\Models\Quotation::find($flow->model_id);
                    return redirect()->to('/commercial/quotation/'.($row->uuid ?? $flow->model_id));
                }elseif($flow->model=='PROJECT'){
                    $project = \App\Models\Project::find($flow->model_id);
                    return redirect()->to('/project/'.($project->uuid ?? $flow->model_id));
                }elseif($flow->model=='CONTRACT'){
                    $row = \App\Models\Contract::find($flow->model_id);
                    return redirect()->to('/commercial/contract/'.($row->uuid ?? $flow->model_id));
                }elseif($flow->model=='ORDER'){
                    $row = \App\Models\Order::find($flow->model_id);
                    return redirect()->to('/order/'.($row->uuid ?? $flow->model_id));
                }elseif($flow->model=='COMMISSION'){
                    $row = \App\Models\Commision::find($flow->model_id);
                    return redirect()->to('/commission/'.($row->uuid ?? $flow->model_id));
                }elseif($flow->model=='INVOICE'){
                    $row = \App\Models\Billing::find($flow->model_id);
                    return redirect()->to('/invoice-order/'.($row->uuid ?? $flow->model_id));
                }
            }
        }else{
            return redirect()->to("/message/?id=" . Hashids::encode($notification->id));
        }
        return redirect('dashboard');
    }

    public function readAll(){
        $notification = Notification::where('user_id', auth()->user()->id)->where('status', 'unread')->update([
            'status' => 'read'
        ]);
        return redirect()->back();
    }
}
