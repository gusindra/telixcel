<?php

namespace App\Http\Livewire\Setting\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CompanyAdd extends Component
{
    public $modalActionVisible = false;
    public $user;
    public $input;

    public function rules()
    {
        return [
            'input.name' => 'required',
            'input.code' => 'required',
            'input.tax_id' => 'required',
            'input.post_code' => 'required',
            'input.province' => 'required',
            'input.city' => 'required',
            'input.address' => 'required',
            'input.person_in_charge' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        $this->user = Auth::user()->id;
        if(@Auth::user()->super->first()->role == 'superadmin'){
            $this->user = 0;
        }

        Company::create([
            'name'  => $this->input['name'],
            'code'  => $this->input['code'],
            'tax_id'  => $this->input['tax_id'],
            'post_code'  => $this->input['post_code'],
            'province'  => $this->input['province'],
            'city'  => $this->input['city'],
            'address'  => $this->input['address'],
            'person_in_charge'  => $this->input['person_in_charge'],
            'user_id'  => $this->user,
        ]);
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function resetForm()
    {
        $this->input = null;
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
        return view('livewire.setting.company-add');
    }
}
