<?php

namespace App\Http\Livewire\Commercial\Item;

use App\Models\CommerceItem;
use Livewire\Component;

class Edit extends Component
{
    public $item;
    public $templateId;
    public $name;
    public $sku;
    public $status;
    public $type;
    public $code;
    public $description;
    public $spec;
    public $price;
    public $unit;
    public $discount;
    public $import;

    public function mount($code)
    {
        $this->item = CommerceItem::find($code);
        $this->name = $this->item->name;
        $this->status = $this->item->status;
        $this->sku = $this->item->sku;
        $this->type = $this->item->type;
        $this->description = $this->item->description;
        $this->spec = $this->item->spec;
        $this->price = $this->item->unit_price;
        $this->unit = $this->item->unit;
        $this->discount = $this->item->general_discount;
        $this->import = $this->item->way_import;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'price' => 'required',
            'sku' => 'required',
            'type' => 'required',
        ];
    }

    public function modelData()
    {
        if($this->type!='nosku'){
            $this->unit = NULL;
        }
        return [
            'name'              => $this->name,
            'status'            => $this->status,
            'sku'               => $this->sku,
            'description'       => $this->description,
            'spec'              => $this->spec,
            'unit_price'        => $this->price,
            'unit'              => $this->unit,
            'general_discount'  => $this->discount,
            'way_import'        => $this->import,
            'type'              => $this->type
        ];
    }

    public function update($id)
    {
        $this->validate();
        CommerceItem::find($id)->update($this->modelData());
        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.commercial.item.edit');
    }
}
