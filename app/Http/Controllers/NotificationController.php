<?php

namespace App\Http\Controllers;

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
            return redirect()->to("/order/" . $notification->model_id);
        }else{
            return redirect('dashboard');
        }
        return redirect()->to("/message/?id=" . Hashids::encode($value));
    }
}
