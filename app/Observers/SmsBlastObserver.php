<?php

namespace App\Observers;

use App\Models\BlastMessage;
use App\Models\OrderProduct;
use App\Models\ProductLine;
use App\Models\Quotation;
use App\Models\SaldoUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            $set_price = 0;
            //check logic quotation for sms active here
            if($quote = Quotation::where('client_id', $request->user_id)->whereIn('status', ['reviewed'])->orderBy('id', 'desc')->first()){
                //get price for sms
                $items = OrderProduct::orderBy('id', 'asc')->where('model', 'Quotation')->where('model_id', $quote->id)->get();
                foreach($items as $product){
                    if(Str::contains($product->name, 'SMS NON OTP') && $request->otp == 0){
                        $this->addSaldo($product->price, $request);
                        $set_price = 1;
                    }elseif(Str::contains($product->name, 'SMS OTP') && $request->otp == 1){
                        $this->addSaldo($product->price, $request);
                        $set_price = 1;
                    }
                }
            }else{
                //else run this price
                $master = ProductLine::where('name', 'Telixcel')->first();
                $items = $master->items;

                //check msisdn for $product items
                // CHARGE BY PRODUCT SMS PRICE
                if(count($items)>0){
                    foreach($items as $product){
                        if($product->sku=="SMS"){
                            // ALL SMS Charge this Price
                            $this->addSaldo($product->unit_price, $request);
                            // SaldoUser::create([
                            //     'team_id'       => NULL,
                            //     'currency'      => 'idr',
                            //     'amount'        => $product->unit_price,
                            //     'mutation'      => 'debit',
                            //     'description'   => 'Pemotongan SMS - '.$request->id.' - '.$request->msg_id,
                            //     'user_id'       => $request->user_id,
                            // ]);
                            $set_price = 1;
                        }else{
                            // CHECK SMS BY PHONE NUMBER
                            $b = explode(",",$product->spec);
                            $p = $request->msisdn;
                            if(count($b)>0){
                                foreach($b as $bs){
                                    if (strpos($p, $bs) !== false) {
                                        // Log::debug($product);
                                        // Log::debug($bs);
                                        $this->addSaldo($product->unit_price, $request);

                                        // SaldoUser::create([
                                        //     'team_id'       => NULL,
                                        //     'currency'      => 'idr',
                                        //     'amount'        => $product->unit_price,
                                        //     'mutation'      => 'debit',
                                        //     'description'   => 'Pemotongan SMS - '.$request->id.' - '.$request->msg_id,
                                        //     'user_id'       => $request->user_id,
                                        // ]);
                                        $set_price = 1;
                                    }
                                }
                            }
                        }
                    }
                }

            }

            //IF THEREIS NO BALANCE UPDATE DEFAULT BY SMS PRICE
            if($set_price == 0){
                $this->addSaldo($request->price, $request, $request->currency);
                // SaldoUser::create([
                //     'team_id'       => NULL,
                //     'currency'      => $request->currency,
                //     'amount'        => $request->price,
                //     'mutation'      => 'debit',
                //     'description'   => 'Pemotongan sms - '.$request->id.' - '.$request->msg_id,
                //     'user_id'       => $request->user_id,
                // ]);
            }
        }
    }

    private function addSaldo($price, $request, $currency='idr'){
        SaldoUser::create([
            'team_id'       => NULL,
            'currency'      => $currency,
            'amount'        => $price,
            'mutation'      => 'debit',
            'description'   => 'Cost SMS - '.$request->id.' - '.$request->msg_id,
            'user_id'       => $request->user_id,
        ]);
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


