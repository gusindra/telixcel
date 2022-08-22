<?php

namespace App\Observers;

use App\Models\BlastMessage;
use App\Models\ProductLine;
use App\Models\SaldoUser;
use Illuminate\Support\Facades\Log;

class SmsBlastObserver
{
    /**
     * Handle the SaldoUser "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(BlastMessage $request)
    {
        if($request->code=="200"){
            //check logic order for sms active here
            // get price for sms

            //else run this price
            $master = ProductLine::where('name', 'Telixcel')->first();

            $items = $master->items;

            //check msisdn for $product items
            $set_price = 0;

            if(count($items)>0){
                foreach($items as $product){
                    if($product->sku=="SMS"){
                        SaldoUser::create([
                            'team_id'       => NULL,
                            'currency'      => 'idr',
                            'amount'        => $product->unit_price,
                            'mutation'      => 'debit',
                            'description'   => 'Pemotongan sms - '.$request->id.' - '.$request->msg_id,
                            'user_id'       => $request->user_id,
                        ]);
                        $set_price = 1;
                    }else{
                        $b = explode(",",$product->spec);
                        $p = $request->msisdn;
                        if(count($b)>0){
                            foreach($b as $bs){
                                if (strpos($p, $bs) !== false) {
                                    // Log::debug($product);
                                    // Log::debug($bs);
                                    SaldoUser::create([
                                        'team_id'       => NULL,
                                        'currency'      => 'idr',
                                        'amount'        => $product->unit_price,
                                        'mutation'      => 'debit',
                                        'description'   => 'Pemotongan sms - '.$request->id.' - '.$request->msg_id,
                                        'user_id'       => $request->user_id,
                                    ]);
                                    $set_price = 1;
                                }
                            }
                        }
                    }
                }
            }

            if($set_price == 0){
                SaldoUser::create([
                    'team_id'       => NULL,
                    'currency'      => $request->currency,
                    'amount'        => $request->price,
                    'mutation'      => 'debit',
                    'description'   => 'Pemotongan sms - '.$request->id.' - '.$request->msg_id,
                    'user_id'       => $request->user_id,
                ]);
            }
        }
    }
    /**
     * Handle the SaldoUser "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function updated(BlastMessage $request)
    {
        if($request->status=='ACCEPTED' && $request->code=="200"){

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


