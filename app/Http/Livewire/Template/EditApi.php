<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Endpoint;

class EditApi extends Component
{
    public $data;
    public $template;
    public $templateId;
    public $request;
    public $endpoint;

    public function mount($template)
    {
        $this->template = $template;
        $this->templateId = $this->template->id;
        $this->data = Endpoint::where('template_id',$this->templateId)->first();
        //dd($template->id);
        //$this->data = Endpoint::find($this->templateId);
        $this->endpoint = $this->data->endpoint ?? '';
        $this->request = $this->data->request ?? '';
    }

    public function rules()
    {
        return [
            'endpoint' => 'required',
            'request' => 'required'
        ];
    }

    public function modelData()
    {
        return [
            'request'       => $this->request,
            'endpoint'      => $this->endpoint,
            'template_id'   => $this->templateId,
        ];
    }

    /**
     * Update Trigger
     *
     * @return void
     */
    public function create()
    {
        $this->validate();
        Endpoint::create($this->modelData());
        $this->emit('saved');
    }

    /**
     * Update Trigger
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        //dd($this->templateId);
        $endpoint = Endpoint::where('template_id',$this->templateId)->first();
        $endpoint->update($this->modelData());
        $this->emit('saved');
    }


    public function render()
    {
        return view('livewire.template.edit-api');
    }
}
