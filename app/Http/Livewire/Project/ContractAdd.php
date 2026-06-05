<?php

namespace App\Http\Livewire\Project;

use App\Models\Contract;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Create a contract at the PROJECT level (mirrors Order\Add).
 * A contract here is NOT required to belong to a client — client_id stays null.
 */
class ContractAdd extends Component
{
    public $modalActionVisible = false;

    public $title;
    public $signer_email;

    /** Scope: model = 'PROJECT', source = project id (passed from the project page). */
    public $model;
    public $source;

    public function mount($source = null, $model = null)
    {
        $this->source = $source;
        $this->model = $model;
    }

    public function rules()
    {
        return [
            'title'        => 'required|string',
            'signer_email' => 'nullable|email',
        ];
    }

    public function create()
    {
        $this->validate();

        Contract::create([
            'title'        => $this->title,
            'status'       => 'draft',
            'model'        => $this->model ?: 'PROJECT',
            'model_id'     => $this->source,
            'client_id'    => null, // contract can live outside a client
            'signer_email' => $this->signer_email,
            'user_id'      => Auth::id(),
        ]);

        $this->modalActionVisible = false;
        $this->reset(['title', 'signer_email']);
        $this->emit('refreshLivewireDatatable');
    }

    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function render()
    {
        return view('livewire.project.contract-add');
    }
}
