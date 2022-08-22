<?php

namespace App\Http\Livewire\Commercial\Item;

use App\Models\CommerceItem;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Add extends Component
{
    public $modalActionVisible = false;
    public $type;
    public $name;
    public $sku;
    public $price;

    public function rules()
    {
        return [
            'type' => 'required',
            'name' => 'required',
            'sku' => 'required',
            'price' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        CommerceItem::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function modelData()
    {
        return [
            'type'          => $this->type,
            'name'          => $this->name,
            'sku'           => $this->sku,
            'unit_price'    => $this->price,
            'user_id'       => Auth::user()->id,
        ];
    }

    public function resetForm()
    {
        $this->type = null;
        $this->name = null;
        $this->sku = null;
        $this->price = null;
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
        return view('livewire.commercial.item.add');
    }
}
