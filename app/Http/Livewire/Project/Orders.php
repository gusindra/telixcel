<?php

namespace App\Http\Livewire\Project;

use App\Models\Order;
use App\Models\Project;
use Livewire\Component;

class Orders extends Component
{
    public $project;
    public $order = [];

    public function mount($id)
    {
        $this->project = Project::find($id);
    }

    public function read()
    {
        $project_id = $this->project->id;
        $quotations = $this->project->quotations;
        $order = Order::where('source', 'PROJECT')->where('source_id', $project_id)->get();
        foreach($quotations as $q){
            $order_quotation = Order::where('source', 'QUOTATION')->where('source_id', $q->id)->get();
            $order = $order->merge($order_quotation);
        }

        return $order;
    }

    public function render()
    {
        return view('livewire.project.orders', [
            'orders' => $this->read()
        ]);
    }
}
