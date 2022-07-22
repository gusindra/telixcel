<?php

namespace App\Http\Livewire\Order;

use Livewire\Component;
use App\Models\Client;
use App\Models\Order;
use App\Models\Project;
use App\Models\Quotation;

class Edit extends Component
{
    public $order;
    public $quote;
    public $quoteNo;
    public $company;
    public $price;
    public $discount;
    public $model;
    public $model_id;
    public $name;
    public $date;
    public $valid_day;
    public $status;
    public $type;
    public $terms;
    public $created_by;
    public $created_role;
    public $addressed;
    public $addressed_name;
    public $addressed_role;
    public $addressed_company;
    public $description;
    public $input;
    public $modalAttach = false;
    public $url;

    public function mount($uuid)
    {
        $this->order = Order::find($uuid);
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
        $this->input['status'] = $this->order->status ?? '';
        $this->input['source'] = $this->order->source ?? '';
        $this->input['source_id'] = $this->order->source_id ?? '';
        $this->input['date'] = $this->order->date ?? '';
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
            'name'              => $this->input['name'],
            'status'            => $this->input['status'],
            'no'                => $this->input['no'],
            'date'              => $this->input['date'],
            'customer_id'       => $this->input['customer_id'],
            'type'              => $this->input['type'],
            'model'             => $this->model,
            'model_id'          => $this->model_id,
            'addressed_company' => $this->addressed_company,
            'description'       => $this->description,
            'created_by'        => $this->created_by,
            'created_role'      => $this->created_role,
            'addressed_name'    => $this->addressed_name,
            'addressed_role'    => $this->addressed_role,
        ];
    }

    public function update($id)
    {
        // dd($id);
        $this->validate();
        // dd($this->modelData());
        $order = Order::find($id)->update($this->modelData());
        $this->emit('saved');
    }

    public function updateStatus($id)
    {
        Order::find($id)->update([
            'status' => $this->input['status']
        ]);
        $this->emit('update_status');
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

    public function actionShowModal($url)
    {
        $this->url = $url;
        $this->modalAttach = true;
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
        $data[0] = $this->order->items->count();
        $data[1] = $this->order->total;

        return $data;
    }

    public function render()
    {
        return view('livewire.order.edit', [
            'model_list' => $this->readModelSelection(),
            'client' => $this->readClient(),
            'data' => $this->readItem(),
        ]);
    }
}
