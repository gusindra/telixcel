<?php

namespace App\Http\Livewire\Payment;

use Livewire\Component;
use App\Models\Notification;
use App\Models\Order;

class History extends Component
{
    public $orders;
    public $selected;
    public $modalDetail = false;

    public function mount()
    {
        $this->orders = null;
        if(auth()->user()->isClient){
            $this->orders = Order::whereIn('status', ['unpaid', 'paid'])->where('customer_id', auth()->user()->isClient->uuid)->get();
        }
    }

    public function cancelTransaction(){
        Order::find($this->selected)->update([
            'status' => 'cancel'
        ]);
        $this->emit('cancel');
        $this->modalDetail = false;
    }

    public function actionShowModal($id)
    {
        $this->selected = $id;
        $this->modalDetail = true;
    }

    public function read()
    {
        if(auth()->user()->isClient){
            return Order::where('status', 'unpaid')->where('entity_party', '1')->where('customer_id', auth()->user()->isClient->uuid)->get();
        }
        return [];
    }

    public function render()
    {
        return view('livewire.payment.history', [
            'list_orders' => $this->read()
        ]);
    }
}
