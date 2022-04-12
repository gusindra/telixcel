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
        $value =  $notification->ticket->request->client->id;
        return redirect()->to("/message/?id=" . Hashids::encode($value));
    }
}
