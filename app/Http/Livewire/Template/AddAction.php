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
    public $link_attachment;
    public $type;

    public function mount($template)
    {
        $this->template = $template;
        $this->templateId = $this->template->id;
        $this->type = false;

    }

    public function rules()
    {
        if($this->link_attachment==''){
            return [
                'message' => 'required',
            ];
        }else{
            return [
                'link_attachment' => 'required',
            ];
        }
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
            if($this->link_attachment=='')
            {
                $data = [
                    'message'       => $this->message,
                    'order'         => $this->orderAction(),
                    'template_id'   => $this->templateId
                ];
            }else{
                $ext = attachmentExt($this->link_attachment);
                if($ext){
                    $data = [
                        'message'       => $this->link_attachment,
                        'order'         => $this->orderAction(),
                        'template_id'   => $this->templateId,
                        'type'          => $ext
                    ];
                }
            }
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

        if($this->link_attachment==''){
            $data = [
                'message'       => $this->message,
                'is_multidata'  => $this->is_multidata,
                'array_data'    => $this->array_data,
                'type'          => 'text'
            ];
        }else{
            $ext = attachmentExt($this->link_attachment);
            if($ext){
                $data = [
                    'message'       => $this->link_attachment,
                    'is_multidata'  => $this->is_multidata,
                    'array_data'    => $this->array_data,
                    'type'          => $ext
                ];
            }else{
                dd('format false');
            }
        }

        Action::find($this->actionId)->update($data);
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
        $this->link_attachment = null;
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
        $this->type             = false;
        if($data->type=='text'){
            $this->message          = $data->message;
        }else{
            $this->type             = true;
            $this->link_attachment  = $data->message;
            $this->message          = '';
        }
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
