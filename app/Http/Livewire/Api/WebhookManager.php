<?php

namespace App\Http\Livewire\Api;

use Livewire\Component;
use App\Models\ApiCredential;
use Vinkla\Hashids\Facades\Hashids;


class WebhookManager extends Component
{
    public $api_key;
    public $server_key;
    public $credential;
    public $client;
    public $is_enabled;
    public $data_id;
    public $modalActionVisible = false;
    public $confirmingActionRemoval = false;

    public function rules()
    {
        return [
            'api_key'       => 'required',
            'server_key'    => 'required',
            'credential'    => 'required',
            'client'        => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'api_key'       => $this->api_key,
            'server_key'    => $this->server_key,
            'credential'    => $this->credential,
            'client'        => $this->client,
            'is_enabled'    => $this->is_enabled,
            'user_id'       => auth()->user()->id
        ];
    }

    public function create()
    {
        // dd($this->modelData());
        $this->validate();
        ApiCredential::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('added');
        $this->data_id = null;
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        ApiCredential::find($this->data_id)->update($this->modelData());
        $this->modalActionVisible = false;

        $this->emit('saved');
    }

    /**
     * The delete function.
     *
     * @return void
     */
    public function delete()
    {
        ApiCredential::destroy($this->data_id);
        $this->confirmingActionRemoval = false;
    }

    public function resetForm()
    {
        $this->api_key = null;
        $this->server_key = null;
        $this->credential = null;
        $this->client = null;
        $this->is_enabled = 0;
    }



    public function actionShowModal()
    {
        $this->modalActionVisible = true;
        $this->resetForm();
        $this->data_id = null;
    }

     /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        return ApiCredential::where('user_id', auth()->user()->id)->with('teams')
        ->whereHas('teams', function ($query) {
            $query->where([
                'teams.id' => auth()->user()->currentTeam->id
            ]);
        })->get();
    }

    public function updateShowModal($id)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->data_id = $id;
        $this->modalActionVisible = true;
        $this->loadModel();
    }

    public function deleteShowModal($id)
    {
        $this->data_id = $id;
        $data = ApiCredential::find($this->data_id);
        $this->confirmingActionRemoval = true;
    }

    /**
     * Loads the model data
     * of this component.
     *
     * @return void
     */
    public function loadModel()
    {
        $data = ApiCredential::find($this->data_id);
        $this->api_key      = $data->api_key;
        $this->server_key   = $data->server_key;
        $this->credential   = $data->credential;
        $this->client       = $data->client;
        $this->is_enabled   = $data->is_enabled;
    }

    public function render()
    {
        return view('livewire.api.webhook-manager', [
            'data' => $this->read(),
        ]);
    }
}
