<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Templates extends Component
{
    public $modalActionVisible = false;
    public $type;
    public $name;
    public $description;

    public function rules()
    {
        return [
            'type' => 'required',
            'name' => 'required',
            'description' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        Template::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function modelData()
    {
        return [
            'uuid'          => Str::uuid(),
            'type'          => $this->type,
            'name'          => $this->name,
            'description'   => $this->description,
            'user_id'       => Auth::user()->id,
        ];
    }

    public function resetForm()
    {
        $this->title = null;
        $this->slug = null;
        $this->description = null;
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
        return view('livewire.template.add-template');
    }
}
