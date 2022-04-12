<?php

namespace App\Http\Livewire\Template;

use Livewire\Component;
use App\Models\Input;

class AddInput extends Component
{
    public $endpoint_id;
    public $input_id;
    public $name;
    public $modalVisible = false;
    public $confirmingModalRemoval = false;

    public function mount($endpoint)
    {
        $this->endpoint_id = $endpoint->id ?? '';
    }

    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'name'          => $this->name,
            'endpoint_id'   => $this->endpoint_id
        ];
    }

    public function create()
    {
        $this->validate();
        Input::create($this->modelData());
        $this->modalVisible = false;
        $this->resetForm();
        $this->emit('added');
        $this->input_id = null;
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        Input::find($this->input_id)->update([
            'name' => $this->name
        ]);
        $this->modalVisible = false;

        $this->emit('saved');
    }

    /**
     * The delete function.
     *
     * @return void
     */
    public function delete()
    {
        Input::destroy($this->input_id);
        $this->confirmingModalRemoval = false;

        $this->dispatchBrowserEvent('event-notification', [
            'eventName' => 'Deleted Page',
            'eventMessage' => 'The page (' . $this->input_id . ') has been deleted!',
        ]);
    }

    /**
     * Loads the model data
     * of this component.
     *
     * @return void
     */
    public function loadModel()
    {
        $data = Input::find($this->input_id);
        $this->name = $data->name;
    }

    public function deleteShowModal($id)
    {
        $this->input_id = $id;
        $data = Input::find($this->input_id);
        $this->name = $data->name;
        $this->confirmingModalRemoval = true;
    }

    public function showCreateModal()
    {
        $this->modalVisible = true;
        $this->resetForm();
        $this->input_id = null;
    }

    public function updateShowModal($id)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->input_id = $id;
        $this->input_id = $id;
        $this->modalVisible = true;
        $this->loadModel();
    }

    public function resetForm()
    {
        $this->name = null;
    }

    /**
     * The read function.
     * for Input data
     *
     * @return void
     */
    public function read()
    {
        return Input::orderBy('order', 'asc')->where('endpoint_id', $this->endpoint_id)->get();
    }

    /**
     * render
     *
     * @return void
     */
    public function render()
    {
        return view('livewire.template.add-input', [
            'data' => $this->read(),
        ]);
    }
}
