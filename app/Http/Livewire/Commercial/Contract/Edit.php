<?php

namespace App\Http\Livewire\Commercial\Contract;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Project;
use Livewire\Component;

class Edit extends Component
{
    public $contract;
    public $input;
    public $original_attachment;
    public $result_attachment;
    public $addressed;

    public function mount($code)
    {
        $this->contract = Contract::find($code);
        $this->input['title'] = $this->contract->title ?? '';
        $this->input['model'] = $this->contract->model ?? '';
        $this->input['model_id'] = $this->contract->model_id ?? '';
        $this->input['signer_email'] = $this->contract->signer_email ?? '';
        $this->input['actived_at'] = $this->contract->actived_at ?? '';
        $this->input['expired_at'] = $this->contract->expired_at ?? '';
        $this->original_attachment = $this->contract->original_attachment ?? '';
        $this->result_attachment = $this->contract->result_attachment ?? '';
        // dd($this->contract->title);
    }

    public function rules()
    {
        return [
            'input.title' => 'required'
        ];
    }

    public function modelData()
    {
        return [
            'title'             => $this->input['title'],
            'signer_email'      => $this->input['signer_email'],
            'expired_at'        => $this->input['expired_at'],
            'actived_at'        => $this->input['actived_at'],
            'model'             => $this->input['model'],
            'model_id'          => $this->input['model_id'],
        ];
    }

    public function update($id)
    {
        $this->validate();
        // dd($this->modelData());
        Contract::find($id)->update($this->modelData());
        $this->emit('saved');
    }

    public function onChangeModel()
    {
        $this->input['model_id'] = '';
        $this->addressed = '';
    }

    public function onChangeModelId()
    {
        if($this->input['model_id']!=0){
            if($this->input['model']=='PROJECT'){
                $this->addressed = Project::find($this->input['model_id']);
                $this->input['model'] = 'Project';
            }else{
                $this->addressed = Client::find($this->input['model_id']);
                $this->input['model'] = 'Client';
            }
            $this->addressed_company = $this->addressed->name;
            // dd($this->input['model']);
        }else{
            $this->input['model'] = NULL;
            $this->input['model_id'] = NULL;
            $this->addressed = '';
        }
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function readModelSelection()
    {
        if($this->input['model']=='PROJECT'){
            $data = Project::pluck('name', 'id');
        }else{
            $data = Client::where('user_id', auth()->user()->currentTeam->user_id)->pluck('name', 'id');
        }
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
        return view('livewire.commercial.contract.edit', [
            'model_list' => $this->readModelSelection(),
            'client' => $this->readClient(),
        ]);
    }
}
