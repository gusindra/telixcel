<?php

namespace App\Console\Commands;

use App\Models\Billing;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectAssistance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assistance:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assist project base on the contract, quotation, order';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // HANLDE CONTRACT AND UPDATE PROJECT
        $ex_contracts = Contract::where('status', 'approved')->whereDate('expired_at', '<=', Carbon::now());
        if($ex_contracts->count()>0){
            // CHANGE STATUS CONTRACT & PROJECT
            $ex_contracts->update([
                'status' => 'expired'
            ]);
            foreach($ex_contracts->get() as $contract){
                foreach ($contract->userApproval as $flow){
                    $this->notice($ex_contracts, $flow->user_id, $contract->title. ' is expired');
                }
            }
        }
        // CREATE NOTIFICATION CONTRACT
        $next_contracts = Contract::where('status', 'approved')->whereDate('expired_at', '<=', Carbon::now()->addDays('30'))->get();
        if($next_contracts){
            // create app notif // email notif to admin
            foreach($next_contracts as $contract){
                // $this->info($contract);
                foreach ($contract->userApproval as $flow){
                    $count = Notification::where('user_id', $flow->user_id)->where('status', 'unread')->where('model', 'Contract')->where('model_id', $contract->id)->count();
                    if($count==0){
                        $this->notice($contract, $flow->user_id, 'Contract : '.$contract->title. ' will expire in next 30 days.');
                    }
                }

            }
        }

        // HANDDLE EXPIRED QUOTATION & NOTIF
        $ex_quote = Quotation::whereNotIn('status', ['reviewed', 'expired', 'draft'])->whereDate('date', '<=', Carbon::now());
        foreach($ex_quote->get() as $quote){
            $expiredDate = $quote->expired_date;
            if($expiredDate < Carbon::now()){
                if(!$quote->order){
                    $quotation = Quotation::find($quote->id)->update([
                        'status' => 'expired'
                    ]);
                    if($quotation->userApproval){
                        foreach ($quotation->userApproval as $flow){
                            $this->notice($quotation, $flow->user_id, $quotation->name. ' is expired');
                        }
                    }
                }
            }
        }

        // HANDLE ORDER TYPE SAAS TO GENERATE BILLING / INVOICE
        $invoice = 0;
        $generate_inv = Order::whereIn('status', ['active','unpaid', 'paid'])->where('type', 'saas');
        foreach($generate_inv->get() as $order){
            if($order->lastInvoice->id > 0){
                // Create new Invoice for this month
                $lastInvoice = Carbon::parse($order->lastInvoice->period)->format('m-Y');
                if($lastInvoice != date('m-Y')){
                    Billing::create([
                        'uuid'          => Str::uuid(),
                        'status'        => 'unpaid',
                        'code'          => $order->lastInvoice->no ?? '',
                        'description'   => $order->lastInvoice->name ?? '',
                        'amount'        => $order->lastInvoice->total ?? 0,
                        'user_id'       => $order->lastInvoice->user_id ?? 0,
                        'order_id'      => $order->id,
                        'period'        => date('Y-m-d')
                    ]);
                    $invoice +=1;
                }
            }
        }

        $this->info("Found Contract Expired: {$ex_contracts->count()}!");
        $this->info("Found Contract Going to Expired: {$next_contracts->count()}!");
        $this->info("Found Quotation Going to Expired: {$ex_quote->count()}!");
        $this->info("Invoice Created: {$invoice}!");
    }

    private function notice($model, $user_id, $message){
        Notification::create([
            'type' => 'app',
            'model' => class_basename($model),
            'model_id' => $model->id,
            'notification' => $message,
            'user_id' => $user_id,
            'status' => 'unread'
        ]);
    }
}
