<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Template;
use App\Models\Action;
use App\Models\DataAction;

class AddAction extends Component
{
    public $template;
    public $templateId;
    public $actionId;
    public $message;
    public $is_multidata;
    public $array_data;
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

    public function modelData()
    {
        $template = Template::find($this->templateId);
        if($template->question && $template->question->type == 'api'){
            $data = [
                'message'       => $this->message,
                'order'         => $this->orderAction(),
                'is_multidata'  => $this->is_multidata,
                'array_data'    => $this->array_data,
                'template_id'   => $this->templateId
            ];
        }else{
            $data = [
                'message'       => $this->message,
                'order'         => $this->orderAction(),
                'template_id'   => $this->templateId
            ];
        }
        return $data;
    }

    public function create()
    {
        $this->validate();

        $action = Action::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('added');
        $this->emit('addArrayData', $action->id);
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
            'message'       => $this->message,
            'is_multidata'  => $this->is_multidata,
            'array_data'    => $this->array_data
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
        $this->message      = $data->message;
        $this->is_multidata = $data->is_multidata;
        $this->array_data   = $data->array_data;
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
