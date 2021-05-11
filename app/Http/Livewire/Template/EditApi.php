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
        $this->data = Endpoint::where('template_id',$template->id)->first();
        $this->endpoint = $this->data->endpoint ?? '';
        $this->request = $this->data->request ?? '';
        $this->templateId = $this->template->id;
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
        Endpoint::find($this->templateId)->update($this->modelData());
        $this->emit('saved');
    }


    public function render()
    {
        return view('livewire.template.edit-api');
    }
}
