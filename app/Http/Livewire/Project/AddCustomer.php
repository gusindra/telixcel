<?php

namespace App\Http\Livewire\Project;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Support\Str;
use Livewire\Component;

class AddCustomer extends Component
{
    public $customer_name;
    public $customer_address;
    public $contact_id;
    public $project_id;
    public $project;

    // ── Client (many) management ──
    public $clientModalVisible = false;
    public $mode = 'existing';     // 'existing' | 'new'
    public $selectedClient;        // client id to attach (existing)

    // detach confirmation
    public $confirmingDetach = false;
    public $detachId = null;
    public $detachName = '';

    protected $listeners = ['confirmDetachClient'];

    // new client fields
    public $newClient = [
        'title'  => '',
        'sender' => '',
        'name'   => '',
        'phone'  => '',
        'email'  => '',
    ];

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
        if (in_array($propertyName, ['customer_name', 'customer_address'])) {
            $this->validateOnly($propertyName);
        }
    }

    /**
     * Save Party A customer details (existing behaviour).
     */
    public function save()
    {
        $validatedData = $this->validate();
        Project::find($this->project_id)->update($validatedData);
        $this->emit('saved');
    }

    // ── Client popup actions ──

    public function showClientModal()
    {
        $this->resetClientForm();
        $this->clientModalVisible = true;
    }

    public function attachExisting()
    {
        $this->validate(['selectedClient' => 'required|exists:clients,id']);
        $this->project->clients()->syncWithoutDetaching([$this->selectedClient]);
        $this->clientModalVisible = false;
        $this->resetClientForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function createAndAttach()
    {
        $this->validate([
            'newClient.name'  => 'required',
            'newClient.phone' => 'required',
            'newClient.email' => 'nullable|email',
        ]);

        $client = Client::create([
            'uuid'    => (string) Str::uuid(),
            'title'   => $this->newClient['title'],
            'sender'  => $this->newClient['sender'],
            'name'    => $this->newClient['name'],
            'phone'   => $this->newClient['phone'],
            'email'   => $this->newClient['email'],
            'user_id' => auth()->id() ?? 0,
        ]);

        $this->project->clients()->syncWithoutDetaching([$client->id]);
        $this->clientModalVisible = false;
        $this->resetClientForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function confirmDetachClient($clientId)
    {
        $client = Client::find($clientId);
        $this->detachId = $clientId;
        $this->detachName = $client?->name ?? '';
        $this->confirmingDetach = true;
    }

    public function detachClient()
    {
        if ($this->detachId) {
            $this->project->clients()->detach($this->detachId);
        }
        $this->confirmingDetach = false;
        $this->detachId = null;
        $this->emit('refreshLivewireDatatable');
    }

    private function resetClientForm()
    {
        $this->mode = 'existing';
        $this->selectedClient = null;
        $this->newClient = ['title' => '', 'sender' => '', 'name' => '', 'phone' => '', 'email' => ''];
    }

    private function availableClients()
    {
        $attached = $this->project->clients()->pluck('clients.id')->all();

        return Client::whereNotIn('id', $attached)->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.project.add-customer', [
            'availableClients' => $this->availableClients(),
        ]);
    }
}
