<?php

namespace App\Http\Livewire\Role;

use App\Models\Template;
use App\Models\Role;
use Livewire\Component;

class Edit extends Component
{
    public $role;
    public $templateId;
    public $name;
    public $description;
    public $status;
    public $type;
    public $role_for;
    public $uuid;

    public function mount($uuid)
    {
        $this->role = Role::find($uuid);
        $this->name = $this->role->name;
        $this->description = $this->role->description;
        $this->status = $this->role->status;
        $this->type = $this->role->type;
        $this->role_for = $this->role->role_for;
        $this->roleId = $this->role->id;
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
            'status'                => $this->status,
            'type'                  => $this->type,
            'role_for'              => $this->role_for,
        ];
    }

    /**
     * Update Template
     *
     * @return void
     */
    public function update($id)
    {
        $this->validate();
        Role::find($id)->update($this->modelData());
        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.role.edit');
    }
}
