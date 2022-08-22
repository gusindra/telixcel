<?php

namespace App\Http\Livewire\Commission;

use App\Models\Client;
use App\Models\CommerceItem;
use App\Models\Order;
use App\Models\Commision;
use App\Models\ProductLine;
use App\Models\Project;
use Livewire\Component;

class Edit extends Component
{
    public $model;
    public $master;
    public $clientId;
    public $type;
    public $selectedCleint;
    public $disabled;
    public $rate;
    public $status;
    public $total;
    public $commission;

    public function mount($model, $data, $disabled=false)
    {
        $this->model = $model;
        if($model=='order'){
            $this->master = Order::find($data->id);
        }elseif($model=='project'){
            $this->master = Project::find($data->id);
        }elseif($model=='product'){
            $this->master = CommerceItem::find($data->id);
        }
        $this->commission = Commision::where('model', $this->model)->where('model_id', $data->id)->first();
        if($this->commission){
            $this->rate = $this->commission->ratio;
            $this->clientId = $this->commission->client_id;
            $this->type = $this->commission->type;
            $this->total = 'Rp'.number_format($this->commission->total);
        }
        $this->disabled = $disabled;
        $this->status = 'draft';
    }

    public function update($id)
    {
        // dd($id);
        $data = Commision::where('model', $this->model)->where('model_id', $id)->first();
        if($data){
            $data->update([
                'type'     => $this->type,
                'ratio'     => $this->rate,
                'status'    => $this->status,
                'client_id' => $this->clientId
            ]);
        }else{
            Commision::create([
                'model'     => $this->model,
                'model_id'  => $id,
                'type'      => $this->type,
                'ratio'     => $this->rate,
                'status'    => $this->status,
                'client_id' => $this->clientId,
            ]);
        }
        $this->emit('saved');
    }

    public function removeAgent()
    {
        // dd($this->commission);
        $this->commission->delete();
        $this->rate = null;
        $this->clientId = null;
        $this->emit('removed');
    }

    public function read()
    {
        $data = Client::where('user_id', auth()->user()->currentTeam->user_id)->pluck('name', 'id');
        return $data;
    }

    public function data()
    {
        return Commision::where('model', $this->model)->where('model_id', $this->master->id)->first();
    }

    public function render()
    {
        return view('livewire.commission.edit', [
            'model_list' => $this->read(),
            'selected_agent' => $this->data()
        ]);
    }
}
