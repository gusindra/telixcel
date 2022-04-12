<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AddError extends Component
{
    public $errorTemplate;
    public $errorUuid;
    public $errorName;
    public $errorId;
    public $errorEnabled;
    public $template;
    public $templateId;
    public $type;
    public $name;
    public $description;
    public $selectionTemplate;
    public $selectedTemplate;
    public $sameTemplate;
    public $modalCreateVisible = false;
    public $modalSelectionVisible = false;
    public $confirmingActionRemoval = false;

    public function mount($template)
    {
        $this->template = Template::with('error')->find($template->id);
        $this->templateId = $this->template->id;
        if($this->template->is_repeat_if_error){
            $this->sameTemplate = true;
        }
        $this->templateId = $this->template->id;
        if($this->template->error_template_id){
            $this->errorTemplate = true;
            $this->errorUuid = $this->template->error->uuid;
            $this->errorName = $this->template->error->name;
            $this->errorId = $this->template->error->id;
            $this->errorEnabled = $this->template->error->is_enabled;
        }
    }

    public function rules()
    {
        return [
            'type' => 'required',
            'name' => 'required',
            'description' => 'required',
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
        $addError = Template::create($this->modelData());
        $this->modalCreateVisible = false;
        $this->resetForm();
        $this->emit('added');

        $parent = Template::find($this->templateId);
        $parent->error_template_id = $addError->id;
        $parent->save();

        $this->errorUuid = $addError->uuid;
        $this->errorName = $addError->name;
        $this->errorId = $addError->id;
    }

    public function selectTemplate()
    {
        $this->modalSelectionVisible = false;
        $choosen = Template::find($this->templateId);
        $choosen->error_template_id = $this->selectedTemplate;
        $choosen->save();
        $this->errorTemplate = true;
        $this->errorId = $choosen->error->id;
        $this->errorUuid = $choosen->error->uuid;
        $this->errorName = $choosen->error->name;
        $this->errorEnabled = $choosen->error->is_enabled;
        $this->resetForm();
        $this->emit('added');
    }

    /**
     * Loads the model data
     * of this component.
     *
     * @return void
     */
    public function loadModel()
    {
        $this->selectionTemplate = Template::where('user_id', Auth::user()->id)->where('id', '!=', $this->templateId)->get();
    }

    /**
     * The delete function.
     *
     * @return void
     */
    public function delete()
    {
        $choosen = Template::find($this->templateId);
        $choosen->error_template_id = NULL;
        $choosen->save();

        $this->confirmingActionRemoval = false;
        $this->errorTemplate = false;

        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Deleted Page',
            'eventMessage' => 'The error template (' . $this->templateId . ') has been removed!',
        ]);
    }

    public function resetForm()
    {
        $this->message = null;
    }

    public function modelData()
    {
        return [
            'uuid'              => Str::uuid(),
            'type'              => $this->type,
            'name'              => $this->name,
            'description'       => $this->description,
            'user_id'           => Auth::user()->id,
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
    }

     /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        return $this->template->error;
    }

    public function deleteShowModal($id)
    {
        $this->selectedTemplate = $id;
        $this->confirmingActionRemoval = true;
    }

    /**
     * setDefaultError
     *
     * @return void
     */
    public function setDefaultError()
    {
        $data = Template::find($this->templateId);
        if($this->sameTemplate){
            $data->is_repeat_if_error = 1;
        }else{
            $data->is_repeat_if_error = NULL;
        }
        $data->save();
    }

    public function render()
    {
        return view('livewire.template.add-error', [
            'data' => $this->read(),
        ]);
    }
}
