<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EditAnswer extends Component
{
    public $template;
    public $templateId;
    public $actionId;
    public $type;
    public $name;
    public $description;
    public $trigger;
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

    public function modelData()
    {
        return [
            'uuid'              => Str::uuid(),
            'type'              => $this->type,
            'name'              => $this->name,
            'description'       => $this->description,
            'trigger_condition' => 'equal',
            'trigger'           => $this->trigger,
            'order'             => $this->orderAction(),
            'template_id'       => $this->templateId,
            'user_id'           => Auth::user()->id,
        ];
    }

    public function orderAction()
    {
        return Template::where('template_id', $this->templateId)->count() + 1;
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
        $this->selectionTemplate = Template::where('id', '!=', $this->templateId)->whereHas('teams', function ($query) {
            $query->where([
                'teams.id' => auth()->user()->currentTeam->id
            ]);
        })->get();
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
        return Template::orderBy('order', 'asc')->where('template_id', $this->templateId)->get();
    }

    public function deleteShowModal($id)
    {
        $this->selectedTemplate = $id;
        $this->confirmingActionRemoval = true;
    }

    public function render()
    {
        return view('livewire.template.edit-answer', [
            'data' => $this->read()
        ]);
    }
}
