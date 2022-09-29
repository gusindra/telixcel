<?php

namespace App\Http\Livewire\Order;

use Livewire\Component;
use App\Models\CommerceItem;
use App\Models\Input;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Auth;

class Item extends Component
{
    public $input;
    public $data;
    public $products;
    public $item_id;
    public $name;
    public $price;
    public $unit;
    public $qty;
    public $description;
    public $selectedProduct;
    public $tax = 0;
    public $total;
    public $percentage = 100;
    public $modalVisible = false;
    public $modalProductVisible = false;
    public $confirmingModalRemoval = false;

    public function mount($data)
    {
        $this->data = $data;
        $this->products = CommerceItem::where('user_id', $data->user_id)->get();
        $this->tax = $data->vat;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'price' => 'required',
            'qty' => 'required',
            'percentage' => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'model_id'      => $this->data->id,
            'model'         => 'Order',
            'name'          => $this->name,
            'unit'          => $this->unit,
            'qty'           => $this->qty,
            'price'         => $this->price,
            'total_percentage'  => $this->percentage,
            'note'          => $this->description,
            'user_id'       => Auth::user()->id
        ];
    }

    public function create()
    {
        $this->validate();
        $this->modalVisible = false;
        OrderProduct::create($this->modelData());
        $this->resetForm();
        $this->emit('added');
    }

    public function addProduct()
    {
        $this->validate();
        $this->modalProductVisible = false;
        $product = CommerceItem::find($this->selectedProduct);
        OrderProduct::create([
            'model_id'      => $this->data->id,
            'model'         => 'Order',
            'product_id'    => $product->id,
            'name'          => $product->name,
            'price'         => $product->unit_price,
            'qty'           => $this->qty,
            'unit'          => $this->unit,
            'note'          => $this->description,
            'user_id'       => Auth::user()->id
        ]);
        $this->resetForm();
        $this->emit('added');
    }

    public function updateTax()
    {
        // dd($this->tax);
        $order = Order::find($this->data->id);
        $order->update(['vat'=>$this->tax]);
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        OrderProduct::find($this->item_id)->update([
            'name' => $this->name,
            'price' => $this->price,
            'qty' => $this->qty,
            'unit' => $this->unit,
            'note' => $this->description
        ]);
        $this->modalVisible = false;
        $this->emit('saved');
    }

    /**
     * The delete function.
     *
     * @return void
     */
    public function delete()
    {
        $this->confirmingModalRemoval = false;
        OrderProduct::destroy($this->item_id);

        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Deleted Page',
            'eventMessage' => 'The page (' . $this->item_id . ') has been deleted!',
        ]);
    }

    /**
     * Loads the model data
     * of this component.
     *
     * @return void
     */
    public function loadModel()
    {
        $data = OrderProduct::find($this->item_id);
        $this->name = $data->name;
        $this->price = $data->price;
        $this->unit = $data->unit;
        $this->description = $data->note;
    }

    public function deleteShowModal($id)
    {
        $this->item_id = $id;
        $data = OrderProduct::find($this->item_id);
        $this->name = $data->name;
        $this->confirmingModalRemoval = true;
    }

    public function showCreateModal()
    {
        $this->modalVisible = true;
        $this->modalProductVisible = false;
        $this->resetForm();
        $this->item_id = null;
    }

    public function modalProductVisible()
    {
        $this->modalProductVisible = true;
        $this->modalVisible = false;
        $this->resetForm();
        $this->item_id = null;
    }

    public function updateShowModal($id)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->item_id = $id;
        $this->modalVisible = true;
        $this->loadModel();
    }

    public function resetForm()
    {
        $this->name = null;
        $this->price = null;
        $this->unit = null;
        $this->description = null;
        $this->selectedProduct = null;
        $this->percentage = 100;
    }

    /**
     * The read function.
     * for Input data
     *
     * @return void
     */
    public function read()
    {
        $items = OrderProduct::orderBy('id', 'asc')->where('model', 'Order')->where('model_id', $this->data->id)->get();
        $this->total = Order::find($this->data->id)->total;
        return [
            'items' => $items,
            'total' => $this->total
        ];
    }

    public function render()
    {
        return view('livewire.order.item', [
            'data' => $this->read()
        ]);
    }
}
