<?php

namespace App\Http\Livewire\Commercial\Contract;

use App\Models\Contract;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Add extends Component
{
    public $modalActionVisible = false;
    public $title;

    public function rules()
    {
        return [
            'title' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        Contract::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function modelData()
    {
        return [
            'title'          => $this->title,
            'user_id'       => Auth::user()->id,
        ];
    }

    public function resetForm()
    {
        $this->title = null;
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
        return view('livewire.commercial.contract.add');
    }
}
