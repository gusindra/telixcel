<?php

namespace App\Observers;

use App\Models\Attachment;
use App\Models\Notification;

class AttachmentObserver
{
    /**
     * Handle the SaldoUser "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Attachment $request)
    {
        if($request->model=='order'){
            Notification::create([
                'type' => 'message',
                'model' => 'Order',
                'model_id' => $request->model_id,
                'notification' => 'Konfirmasi pembayaran no '.$request->model_id. ' Source: '.$request->file,
                'user_id' => 0,
                'status' => 'unread'
            ]);
        }
    }

    /**
     * Handle the SaldoUser "deleted" event.
     *
     * @param  \App\SaldoUser  $request
     * @return void
     */
    public function deleted()
    {
        //
    }
}


