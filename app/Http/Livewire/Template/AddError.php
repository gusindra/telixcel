<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;

class AddError extends Component
{
    public $template;
    public $templateId;
    public $actionId;
    public $type;
    public $name;
    public $description;
    public $selectionTemplate;
    public $selectedTemplate;
    public $modalCreateVisible = false;
    public $modalSelectionVisible = false;
    public $confirmingActionRemoval = false;

    public function mount($template)
    {
        $this->template = $template;
        $this->templateId = $this->template->id;
    }

    public function rules()
    {
        return [
            'type' => 'required',
            'name' => 'required',
            'description' => 'required',
            'trigger' => 'required',
        ];
    }

    /**
     * The create function.
     *
     * @return void
     */
    public function create()
    {
        $this->validate();
        Template::create($this->modelData());
        $this->modalCreateVisible = false;
        $this->resetForm();
        $this->emit('added');
        $this->actionId = null;
    }

    public function selectTemplate()
    {
        $this->modalSelectionVisible = false;
        $choosen = Template::find($this->selectedTemplate);
        $choosen->template_id = $this->templateId;
        $choosen->save();
        $this->resetForm();
        $this->emit('added');
        $this->actionId = null;

    }

    /**
     * Loads the model data
     * of this component.
     *
     * @return void
     */
    public function loadModel()
    {
        $this->selectionTemplate = Template::where('user_id', Auth::user()->id)->where('id', '!=', $this->templateId)->where('template_id', NULL)->get();
    }

    /**
     * The delete function.
     *
     * @return void
     */
    public function delete()
    {
        $choosen = Template::find($this->selectedTemplate);
        $choosen->template_id = NULL;
        $choosen->save();

        $this->confirmingActionRemoval = false;

        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Deleted Page',
            'eventMessage' => 'The page (' . $this->actionId . ') has been deleted!',
        ]);
    }

    public function resetForm()
    {
        $this->message = null;
    }

    public function modelData()
    {
        return [
            'type'          => $this->type,
            'name'          => $this->name,
            'description'   => $this->description,
            'description'   => $this->orderAction($this->templateId),
            'template_id'   => $this->templateId
        ];
    }

    public function orderAction()
    {
        return Template::where('template_id', $this->templateId)->count() + 1;
    }

    /**
     * actionShowModal
     *
     * @return void
     */
    public function selectionShowModal()
    {
        $this->modalCreateVisible = false;
        $this->modalSelectionVisible = true;
        $this->resetForm();
        $this->actionId = null;
        $this->loadModel();
    }

    /**
     * actionShowModal
     *
     * @return void
     */
    public function createShowModal()
    {
        $this->modalSelectionVisible = false;
        $this->modalCreateVisible = true;
        $this->resetForm();
        $this->actionId = null;
    }

     /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        return Template::where('error_template_id', $this->templateId)->first();
    }

    public function deleteShowModal($id)
    {
        $this->selectedTemplate = $id;
        $this->confirmingActionRemoval = true;
    }

    public function render()
    {
        return view('livewire.template.add-error', [
            'data' => $this->read()
        ]);
    }
}
