<?php

namespace App\Http\Livewire\Commercial\Quotation;

use App\Models\OrderProduct;
use App\Models\Quotation;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Add extends Component
{
    public $modalActionVisible = false;
    public $type;
    public $title;
    public $date;
    public $valid_day;
    public $model;
    public $source;

    public function rules()
    {
        return [
            'type' => 'required',
            'title' => 'required',
            'date' => 'required',
            'valid_day' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        $quotation = Quotation::create($this->modelData());

        // New quotation follows the latest existing quotation: copy its line items.
        $this->copyItemsFromLatest($quotation);

        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    /**
     * Copy line items from the most recent prior quotation into the new one.
     * When model+source are set (e.g. created from a project), scope to that entity.
     * Otherwise fall back to the globally latest quotation — so standalone creation
     * also benefits from auto-fill.
     */
    private function copyItemsFromLatest(Quotation $new): void
    {
        $query = Quotation::where('id', '!=', $new->id)->orderBy('id', 'desc');

        if ($this->model && $this->source) {
            // Prefer items from the same entity (project / order).
            $query->where('model', $this->model)->where('model_id', $this->source);
        }

        $latest = $query->first();

        if (! $latest) {
            return;
        }

        $items = OrderProduct::where('model', 'Quotation')
            ->where('model_id', $latest->id)
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        foreach ($items as $item) {
            OrderProduct::create([
                'name'             => $item->name,
                'model'            => 'Quotation',
                'model_id'         => $new->id,
                'product_id'       => $item->product_id,
                'qty'              => $item->qty,
                'unit'             => $item->unit,
                'price'            => $item->price,
                'total_percentage' => $item->total_percentage,
                'note'             => $item->note,
                'user_id'          => Auth::user()->id,
            ]);
        }
    }

    public function modelData()
    {
        $data = [
            'type'          => $this->type,
            'title'          => $this->title,
            'valid_day'     => $this->valid_day,
            'date'          => $this->date,
            'user_id'       => Auth::user()->id,
        ];
        if($this->model && $this->source){
            $data['model']      = $this->model;
            $data['model_id']   = $this->source;
        }
        return $data;
    }

    public function resetForm()
    {
        $this->type = null;
        $this->title = null;
        $this->date = null;
        $this->valid_day = null;
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
        return view('livewire.commercial.quotation.add');
    }
}
