<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\Notification;
use App\Models\Request as Message;

class TicketObserver
{
    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Ticket $request)
    {
        // if status open -> sent from agent :
        if($request->status == 'open'){
            $reply = "We have received your request, your Ticket No ".$request->request_id." has been assigned. Meanwhile, we suggest to use this Ticket No in any further communication for easier reference.";
            $request = Message::create([
                'reply'         => $reply,
                'from'          => $request->created_by,
                'user_id'       => $request->request->user_id,
                'type'          => 'text',
                'template_id'   => null,
                'client_id'     => $request->request->client_id
            ]);
        }else if($request->status == 'waiting'){
            $notif = Notification::create([
                'type'          => 'forward',
                'model_id'      => $request->id,
                'model'         => 'Ticket',
                'notification'  => $request->reasons,
                'user_id'       => $request->forward_to,
                'status'        => 'new',
            ]);
        }
    }

    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function updated(Ticket $request)
    {
        // if status close, sent bot :
        $from = 'bot';
        if($request->status == 'close'){
            $reply = "Your Ticket No ".$request->request_id." has been closed. If you have any enquiries, please do not hesitate to contact us. Thank You!";
        }elseif($request->status == 'handled'){
            $reply = "Your Ticket No ".$request->request_id." has been handled.";
            if($request->solution!=''){
                $reply =  $reply." Note: ".$request->solution;
            }
            $from = $request->updated_by;
        }elseif($request->status == 'open'){
            $reply = "We have received your request and the Ticket No ".$request->request_id." has been opened.";
            $from = $request->updated_by;
        }
        if($request->forward_to == null){
            $request = Message::create([
                'reply'         => $reply,
                'from'          => $from,
                'user_id'       => $request->request->user_id,
                'type'          => 'text',
                'template_id'   => null,
                'client_id'     => $request->request->client_id
            ]);
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     *
     * @param  \App\Ticket  $request
     * @return void
     */
    public function deleted(Ticket $request)
    {
        // if status close, sent bot :
        if($request->forward_to == null){
            $from = 'bot';
            $reply = "Your Ticket ".$request->request_id." has been closed. If you have any enquiries, please do not hesitate to contact us. Thank You!";

            $request = Message::create([
                'reply'         => $reply,
                'from'          => $from,
                'user_id'       => $request->request->user_id,
                'type'          => 'text',
                'template_id'   => null,
                'client_id'     => $request->request->client_id
            ]);
        }else{
            Notification::where('model', 'Ticket')->where('model_id', $request->id)->delete();
        }

    }
}


