<?php

namespace App\Http\Livewire\Setting\Company;

use Livewire\Component;
use App\Models\Company;
use App\Models\CompanyPayment;
use Illuminate\Support\Facades\Auth;

class PaymentCompanyAdd extends Component
{
    public $company;
    public $item_id;
    public $account_number;
    public $input;
    public $selectedProduct;
    public $modalVisible = false;
    public $modalProductVisible = false;
    public $confirmingModalRemoval = false;

    public function mount($data)
    {
        $this->company = Company::find($data->id);
    }

    public function rules()
    {
        return [
            'input.method' => 'required',
            'input.provider_name' => 'required',
            'input.account_number' => 'required',
            'input.account_name' => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'method'            => $this->input['method'],
            'provider_name'     => $this->input['provider_name'],
            'account_number'    => $this->input['account_number'],
            'account_name'      => $this->input['account_name'],
            'provider_location' => $this->input['provider_location'],
            'notes'             => $this->input['notes'] ?? '',
            'company_id'        => $this->company->id
        ];
    }

    public function create()
    {
        $this->validate();
        $this->modalVisible = false;
        CompanyPayment::create($this->modelData());
        $this->resetForm();
        $this->emit('added');
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        CompanyPayment::find($this->item_id)->update([
            'method'            => $this->input['method'],
            'provider_name'     => $this->input['provider_name'],
            'account_number'    => $this->input['account_number'],
            'account_name'      => $this->input['account_name'],
            'provider_location' => $this->input['provider_location'],
            'notes'             => $this->input['notes'],
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
        CompanyPayment::destroy($this->item_id);

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
        $data = CompanyPayment::find($this->item_id);
        $this->input['method'] = $data->method;
        $this->input['provider_name'] = $data->provider_name;
        $this->input['account_number'] = $data->account_number;
        $this->input['account_name'] = $data->account_name;
        $this->input['provider_location'] = $data->provider_location;
        $this->input['notes'] = $data->notes;
    }

    public function deleteShowModal($id)
    {
        $this->item_id = $id;
        $data = CompanyPayment::find($this->item_id);
        $this->account_number = $data->account_name.' '.$data->account_number;
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
        $this->input['method'] = null;
        $this->input['provider_name'] = null;
        $this->input['account_number'] = null;
        $this->input['account_name'] = null;
        $this->input['provider_location'] = null;
        $this->input['notes'] = null;
    }

    /**
     * The read function.
     * for Input data
     *
     * @return void
     */
    public function read()
    {
        $items = CompanyPayment::where('company_id', $this->company->id)->get();
        return [
            'items' => $items
        ];
    }

    public function render()
    {
        return view('livewire.setting.payment-company-add', [
            'data' => $this->read()
        ]);
    }
}
