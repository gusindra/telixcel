<?php

namespace App\Http\Livewire\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Quotation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class QuotationToOrder extends Component
{
    use AuthorizesRequests;
    public $quotation;

    public function mount($id)
    {
        $this->quotation = Quotation::find($id);
    }

    public function modelData()
    {
        $data = [
            'type'          => 'referral',
            'status'        => 'draft',
            'user_id'       => auth()->user()->id,
        ];

        //Quotation to Order
        if($this->quotation){
            $data['source']      = 'QUOTATION';
            $data['source_id']   = $this->quotation->id;
            if($this->quotation->model == 'PROJECT')
                $data['entity_party']   = $this->quotation->project->entity_party;
            $data['name']   = 'QUOTATION '.$this->quotation->title;
            $data['no']   = $this->quotation->quote_no;
            $data['date']   = date('Y-m-d H:i:s');
        }
        return $data;
    }

    public function convert()
    {
        $this->authorize('create', new Order);
        $order = Order::create($this->modelData());

        // add order item
        if($order && $this->quotation->items){
            foreach($this->quotation->items as $item){
                OrderProduct::create([
                    'model_id'      => $order->id,
                    'model'         => 'Order',
                    'product_id'    => $item->id,
                    'name'          => $item->name,
                    'price'         => $item->price,
                    'qty'           => $item->qty,
                    'unit'          => $item->unit,
                    'note'          => $item->note,
                    'user_id'       => auth()->user()->id
                ]);
            }
        }

        return redirect(route('show.order', $order->id));
    }

    public function render()
    {
        return view('livewire.order.quotation-to-order');
    }
}
