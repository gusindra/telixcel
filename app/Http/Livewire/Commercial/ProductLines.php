<?php

namespace App\Http\Livewire\Commercial;

use App\Models\CommerceItem;
use App\Models\ProductLine;
use App\Models\Project;
use Livewire\Component;

class ProductLines extends Component
{
    public $master;
    public $product_line;
    public $selected_product_line;
    public $disabled;

    public function mount($model, $data, $disabled=false)
    {
        $this->selected_product_line = '';
        if($model=='product'){
            $this->master = CommerceItem::find($data->id);
        }elseif($model=='project'){
            $this->master = Project::find($data->id);
        }
        if($this->master){
            $line = ProductLine::find($this->master->product_line);
            $this->selected_product_line = $line ? $line->name : '';
        }
        $this->disabled = $disabled;
    }

    public function update()
    {
        $this->master->update([
            'product_line' => $this->product_line
        ]);
    }

    public function addProduct()
    {
        $line = ProductLine::create([
            'name' => $this->product_line,
            'user_id' => auth()->user()->id
        ]);

        $this->master->update([
            'product_line' => $line->id
        ]);

        $this->selected_product_line = $this->product_line;
        $this->product_line = "";

    }

    public function selectProduct($id)
    {
        $this->master->update([
            'product_line' => $id
        ]);
        $line = ProductLine::find($id);

        $this->selected_product_line = $line->name;
        $this->product_line = "";
    }

    public function changeProduct()
    {
        $this->selected_product_line = "";
        $this->product_line = "";
    }

    public function showQuickModal($id)
    {
        $template = ProductLine::find($id);
        $this->product_line = $template->name;
    }

    public function read()
    {
        $data = [];

        if($this->product_line!=''){
            $keyword =  $this->product_line;
            $data['quick'] = ProductLine::where('user_id', auth()->user()->id)->where('name','LIKE',"%{$keyword}%")->get();
        }else{
            $data['quick'] = [];
        }
        return $data;
    }

    public function render()
    {
        return view('livewire.commercial.product-lines', [
            'data' => $this->read(),
            'selected_line' => $this->selected_product_line
        ]);
    }
}
