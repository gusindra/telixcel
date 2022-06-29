<?php

namespace App\Http\Livewire\Commercial\Quotation;

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
        Quotation::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
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
