<?php

namespace App\Http\Livewire\Setting\Company;

use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use Livewire\Component;

class CompanyEdit extends Component
{
    public $company;
    public $contract;
    public $input;
    public $original_attachment;
    public $result_attachment;
    public $addressed;

    public function mount($company)
    {
        $this->company = Company::find($company->id);
        $this->input['name'] = $this->company->name ?? '';
        $this->input['code'] = $this->company->code ?? '';
        $this->input['tax_id'] = $this->company->tax_id ?? '';
        $this->input['post_code'] = $this->company->post_code ?? '';
        $this->input['province'] = $this->company->province ?? '';
        $this->input['city'] = $this->company->city ?? '';
        $this->input['address'] = $this->company->address ?? '';
        $this->input['logo'] = $this->company->logo ?? '';
        $this->input['person_in_charge'] = $this->company->person_in_charge ?? '';
        $this->original_attachment = $this->company->logo ?? '';
        $this->result_attachment = '';
    }

    public function rules()
    {
        return [
            'input.name' => 'required'
        ];
    }

    public function modelData()
    {
        return [
            'name'  => $this->input['name'],
            'code'  => $this->input['code'],
            'tax_id'  => $this->input['tax_id'],
            'person_in_charge'  => $this->input['person_in_charge']
        ];
    }

    public function update($id)
    {
        $this->validate();
        Company::find($id)->update($this->modelData());
        $this->emit('saved');
    }

    public function onChangeModel()
    {
        $this->input['model_id'] = '';
        $this->addressed = '';
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function readModelSelection()
    {
        $data = array();
        return $data;
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function readClient()
    {
        return $this->addressed;
    }

    public function render()
    {
        return view('livewire.setting.company-edit', [
            'model_list' => $this->readModelSelection(),
            'client' => $this->readClient(),
        ]);
    }
}
