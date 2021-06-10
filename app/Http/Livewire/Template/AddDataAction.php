<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Action;
use App\Models\DataAction;

class AddDataAction extends Component
{
    public $action;
    public $actionId;
    public $dataId;
    public $name;
    public $value;
    public $dataArray = [];

    protected $listeners = ['addArrayData', 'read'];

    public function mount($actionId)
    {
        if($actionId){
            $this->action = Action::find($actionId);
            $this->actionId = $this->action->id;
        }
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'value' => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'name'          => $this->name,
            'value'         => $this->value,
            'action_id'     => $this->actionId
        ];
    }

    public function addArrayData($id)
    {
        foreach($this->dataArray as $data){
            DataAction::create([
                'name'          => $data['name'],
                'value'         => $data['value'],
                'action_id'     => $id
            ]);
        }
    }

    public function create()
    {
        $this->validate();
        DataAction::create($this->modelData());
        $this->resetForm();
        $this->emit('added');
        $this->actionId = null;
    }

    public function insert()
    {
        $this->validate();
        array_push($this->dataArray, $this->modelData());
        $this->resetForm();
        $this->emit('insert');
        $this->actionId = null;
    }

    /**
     * The delete function.
     *
     * @return void
     */
    public function delete($id)
    {
        DataAction::destroy($id);

        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Deleted Page',
            'eventMessage' => 'The page (' . $this->actionId . ') has been deleted!',
        ]);
    }

    public function remove($id)
    {
        unset($this->dataArray[$id]);
    }

    public function resetForm()
    {
        $this->name = null;
        $this->value = null;
    }

     /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        return DataAction::where('action_id', $this->actionId)->get();
    }

    public function render()
    {
        return view('livewire.template.add-data-action', [
            'data'  => $this->read(),
            'array' => $this->dataArray,
        ]);
    }
}
