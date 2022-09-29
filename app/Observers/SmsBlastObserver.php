<?php

namespace App\Observers;

use App\Models\BlastMessage;
use App\Models\OperatorPhoneNumber;
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
                //FIND WAY TO FILTER BY PHONE NUMBER BASE ON OPERATOR
                $phoneNo = $request->msisdn;
                $phoneNo = substr($phoneNo, 0, 5);
                $opn = OperatorPhoneNumber::where('code', $phoneNo)->first();
                foreach($items as $product){
                    if($request->otp == 0 && $product->note == 'SMS NON OTP'){
                        if($opn && $opn->operator == $product->name){
                            $this->addSaldo($product->price, $request);
                            $set_price = 1;
                            // Log::debug('hit non otp single operator');
                        }elseif(Str::contains($product->name, 'SMS NON OTP')){
                            // Default by key SMS NON OTP
                            $this->addSaldo($product->price, $request);
                            $set_price = 1;
                            // Log::debug('hit non otp all operator');
                        }
                    }elseif($request->otp == 1 && $product->note == 'SMS OTP'){
                        //FIND WAY TO FILTER BY PHONE NUMBER BASE ON OPERATOR
                        if($opn && $opn->operator == $product->name){
                            $this->addSaldo($product->price, $request);
                            $set_price = 1;
                            // Log::debug('hit otp single operator');
                        }elseif(Str::contains($product->name, 'SMS OTP')){
                            $this->addSaldo($product->price, $request);
                            $set_price = 1;
                            // Log::debug('hit otp single operator');
                        }
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


