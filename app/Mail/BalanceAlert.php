<?php

namespace App\Mail;

use App\Models\SaldoUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class BalanceAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $saldo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SaldoUser $saldo)
    {
        $this->saldo = $saldo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.balance-alert', [
            'redirectUrl' => URL::signedRoute('payment.topup')
        ])->subject(__('Balance Alert'));
    }
}
