<?php

namespace App\Http\Livewire\Project;

use App\Models\Project;
use Livewire\Component;

class AddCustomer extends Component
{
    public $customer_name;
    public $customer_address;
    public $contact_id;
    public $project_id;
    public $project;

    public function mount($id)
    {
        $this->project_id = $id;
        $this->project = Project::find($id);
        $this->customer_name = $this->project->customer_name;
        $this->customer_address = $this->project->customer_address;
        $this->contact_id = $this->project->contact_id;
    }

    public function rules()
    {
        return [
            'customer_name' => 'required',
            'customer_address' => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'customer_name'     => $this->customer_name,
            'customer_address'  => $this->customer_address
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Update Template
     *
     * @return void
     */
    public function save()
    {
        $validatedData = $this->validate();
        Project::find($this->project_id)->update($validatedData);
        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.project.add-customer');
    }
}
