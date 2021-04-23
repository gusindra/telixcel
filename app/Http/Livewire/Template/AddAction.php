<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Template;
use App\Models\Action;

class AddAction extends Component
{
    public $template;
    public $templateId;
    public $actionId;
    public $message;
    public $modalActionVisible = false;
    public $confirmingActionRemoval = false;

    public function mount($template)
    {
        $this->template = $template;
        $this->templateId = $this->template->id;
    }

    public function rules()
    {
        return [
            'message' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        Action::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('added');
        $this->actionId = null;
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        Action::find($this->actionId)->update([
            'message' => $this->message
        ]);
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
        Action::destroy($this->actionId);
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
            'message'       => $this->message,
            'order'         => $this->orderAction(),
            'template_id'   => $this->templateId
        ];
    }

    public function orderAction()
    {
        return Action::where('template_id', $this->templateId)->count() + 1;
    }

    public function actionShowModal()
    {
        $this->modalActionVisible = true;
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
        return Action::orderBy('order', 'asc')->where('template_id', $this->templateId)->get();
    }

    public function updateShowModal($id)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->actionId = $id;
        $this->modalActionVisible = true;
        $this->loadModel();
    }

    /**
     * Loads the model data
     * of this component.
     *
     * @return void
     */
    public function loadModel()
    {
        $data = Action::find($this->actionId);
        $this->message = $data->message;
    }

    public function deleteShowModal($id)
    {
        $this->actionId = $id;
        $data = Action::find($this->actionId);
        $this->message = $data->message;
        $this->confirmingActionRemoval = true;
    }

    public function render()
    {
        return view('livewire.template.add-action', [
            'data' => $this->read(),
        ]);
    }
}
