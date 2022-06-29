<?php

namespace App\Observers;

use App\Jobs\ProcessEmail;
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

        if($request->mutations == 'debit' && ($request->balance <= 10000 || $request->balance <= 100000)){
            ProcessEmail::dispatch($request, 'alert_balance');
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


