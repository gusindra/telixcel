<?php

namespace App\Jobs;

use App\Mail\BalanceAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderTelixcel;

class AssistanceContract implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $type)
    {
        $this->request = $request;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->type=="create_order" || $this->type=="payment_order"){
            $notif = $this->request->notifications('unread')->count();
            $email = 'support@telixcel.com';
            $mail = Mail::to($email);
            if($notif && $notif>0){
                $mail->cc('finance@telixcel.com','mirza@telixcel.com','elly@goldeunion.group');
            }
            $mail->send(new OrderTelixcel($this->request));
        }elseif($this->type=="alert_balance"){
            $email = $this->request->user->email;
            Mail::to($email)->bcc('support@telixcel.com')->send(new BalanceAlert($this->request));
        }
    }
}
