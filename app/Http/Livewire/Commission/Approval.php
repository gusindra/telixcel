<?php

namespace App\Http\Livewire\Commission;

use App\Models\Client;
use App\Models\Commision;
use App\Models\Order;
use Livewire\Component;

class Approval extends Component
{
    public $input;
    public $order;
    public $commission;

    public function mount($uuid)
    {
        $this->commission = Commision::find($uuid);
        $this->order = Order::find($this->commission->model_id);
        $this->date = $this->order->date;
        $this->input['name'] = $this->order->name ?? '';
        $this->input['no'] = $this->order->no ?? '';
        $this->input['type'] = $this->order->type ?? '';
        $this->input['entity_party'] = $this->order->entity_party ?? '';
        $this->input['customer_type'] = $this->order->customer_type ?? '';
        $this->input['customer_id'] = $this->order->customer_id ?? '';
        $this->input['referrer_id'] = $this->order->referrer_id ?? '';
        $this->input['commision_ratio'] = $this->order->commision_ratio ?? '';
        $this->input['total'] = $this->order->total ?? '';
        $this->input['status'] = $this->commission->status ?? '';
        $this->input['source'] = $this->order->source ?? '';
        $this->input['source_id'] = $this->order->source_id ?? '';
        $this->input['date'] = $this->order->date!='' ? $this->order->date->format('d-F-Y'): '';
        $this->input['total'] = $this->order->total ?? '';
    }

    public function rules()
    {
        return [
            'input' => 'required'
        ];
    }

    public function modelData()
    {
        return [
            'status'    => $this->input['status']
        ];
    }

    public function updateStatus($id)
    {
        Commision::find($id)->update([
            'status' => $this->input['status']
        ]);
        $this->emit('update_status');
        return redirect(request()->header('Referer'));
    }

    public function onChangeModelId()
    {
        if($this->model_id!=0){
            $this->addressed = $this->order->customer;
            $this->addressed_company = $this->addressed->name;
        }else{
            $this->model = NULL;
            $this->model_id = NULL;
            $this->addressed_company = NULL;
            $this->addressed = '';
        }
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function readModelSelection()
    {

        $data = Client::where('user_id', auth()->user()->currentTeam->user_id)->pluck('name', 'uuid');

        return $data;
    }

    public function readClient()
    {
        return $this->order->customer;
    }

    public function readItem()
    {
        $data = [];

        return $data;
    }

    public function render()
    {
        return view('livewire.commission.approval', [
            'model_list' => $this->readModelSelection(),
            'client' => $this->readClient(),
            'data' => $this->readItem(),
        ]);
    }
}
