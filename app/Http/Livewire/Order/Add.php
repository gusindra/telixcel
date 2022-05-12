<?php

namespace App\Http\Livewire\Order;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Add extends Component
{
    public $modalActionVisible = false;
    public $type;
    public $entity;

    public function rules()
    {
        return [
            'type' => 'required',
            'entity' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        Order::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function modelData()
    {
        return [
            'type'          => $this->type,
            'entity_party'  => $this->entity,
            'user_id'       => Auth::user()->id,
        ];
    }

    public function resetForm()
    {
        $this->type = null;
        $this->entity = null;
    }

    /**
     * createShowModal
     *
     * @return void
     */
    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function render()
    {
        return view('livewire.order.add');
    }
}
