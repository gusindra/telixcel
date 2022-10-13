<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Template;

class EditTemplate extends Component
{
    public $template;
    public $templateId;
    public $name;
    public $description;
    public $is_enabled;
    public $is_waiting;
    public $uuid;

    public function mount($uuid)
    {
        $this->template = Template::with('question')->where('uuid', $uuid)->first();
        $this->name = $this->template ? $this->template->name :'';
        $this->description = $this->template ?$this->template->description:'';
        $this->is_enabled = $this->template ?$this->template->is_enabled:'';
        $this->is_waiting = $this->template ?$this->template->is_wait_for_chat:'';
        $this->templateId = $this->template ?$this->template->id:'';
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'name'                  => $this->name,
            'description'           => $this->description,
            'is_enabled'            => $this->is_enabled,
            'is_wait_for_chat'      => $this->is_waiting,
        ];
    }

    /**
     * Update Template
     *
     * @return void
     */
    public function updateTemplate()
    {
        $this->validate();
        Template::find($this->templateId)->update($this->modelData());
        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.template.edit-template')
                    ->layout('template.show');
    }
}
