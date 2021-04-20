<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Template;

class ShowTemplate extends Component
{
    public $template;
    public $templateId;
    public $name;
    public $description;
    public $uuid;

    public function mount($uuid)
    {
        $this->template = Template::where('uuid', $uuid)->first();
        $this->name = $this->template->name;
        $this->description = $this->template->description;
        $this->templateId = $this->template->id;
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
            'name'          => $this->name,
            'description'   => $this->description
        ];
    }

    /**
     * updateTemplate
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
        return view('livewire.template.show-template')->layout('template.show');
    }
}
