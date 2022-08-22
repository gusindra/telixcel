<?php

namespace App\Observers;

use App\Jobs\ProcessEmail;
use App\Models\Notification;
use App\Models\SaldoUser;

class SaldoUserObserver
{
    /**
     * Handle the SaldoUser "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(SaldoUser $request)
    {
        $last = SaldoUser::where('id', '!=', $request->id)->where('user_id', $request->user_id)->where('team_id', $request->team_id)->orderBy('id', 'desc')->first();

        if($last){
            $amount = $last->balance + $request->amount;
            if($request->mutation == 'debit'){
                $amount = $last->balance - $request->amount;
            }
            $request->update(['balance' => $amount]);
        }else{
            $request->update(['balance' => $request->amount]);
        }

        if($request->mutation == 'debit'){
            $notif_count = Notification::where('model', 'Balance')->where('user_id', $request->user_id)->count();
            if(($notif_count==1 && $request->balance <= 50000) || ($notif_count==0 && $request->balance <= 100000)){
                $notif = Notification::create([
                    'type'          => 'email',
                    'model_id'      => $request->id,
                    'model'         => 'Balance',
                    'notification'  => 'Balance Alert. Your current balance remaining Rp'.number_format($request->balance) ,
                    'user_id'       => $request->user_id,
                    'status'        => 'unread',
                ]);

                if($notif){
                    ProcessEmail::dispatch($request, 'alert_balance');
                }
            }
        }
        if($request->mutation == 'credit'){
            Notification::where('type', 'email')->where('model', 'Balance')->where('user_id', $request->user_id)->delete();
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


