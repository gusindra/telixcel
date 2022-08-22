<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class OrderTelixcel extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $orders = $this->order->notifications('unread');
        if($orders->count()>0){
            return $this->markdown('mail.order-confirmation', [
                'redirectUrl' => URL::signedRoute('show.order', [
                    'order' => $this->order->id,
                ])
            ])->subject(__('New Order'));
        }
        return $this->markdown('mail.order-created', [
            'redirectUrl' => URL::signedRoute('show.order', [
                'order' => $this->order->id,
            ])
        ])->subject(__('New Order'));
    }
}
